<?php

namespace App\Services;

use App\Models\Client;
use App\Models\SalesInvoice;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SquareInvoiceService
{
    private string $environment;
    private string $baseUrl;
    private ?string $token;
    private ?string $locationId;

    public function __construct()
    {
        $this->environment = (string) config('services.square.environment', env('SQUARE_ENVIRONMENT', 'sandbox'));
        $this->token = config('services.square.token', env('SQUARE_ACCESS_TOKEN'));
        $this->locationId = config('services.square.location_id', env('SQUARE_LOCATION_ID', ''));

        $this->baseUrl = $this->environment === 'production'
            ? 'https://connect.squareup.com'
            : 'https://connect.squareupsandbox.com';
    }

    /**
     * Check if Square is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->token) && !empty($this->locationId);
    }

    /**
     * Get pre-configured HTTP pending request for Square API v2.
     */
    private function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->token)
            ->withHeaders([
                'Square-Version' => '2024-01-18',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(25);
    }

    /**
     * Create and Send (Publish) Square Invoice for a Laravel SalesInvoice.
     */
    public function createAndSendInvoice(SalesInvoice $invoice): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'message' => 'Square API credentials are not configured in environment.',
                ];
            }

            if (empty($invoice->customer_email)) {
                return [
                    'success' => false,
                    'message' => 'Customer email address is required to send a Square Invoice.',
                ];
            }

            if ($invoice->items->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Invoice must have at least one line item.',
                ];
            }

            // 1. Obtain or Create Square Customer ID
            $customerId = $this->getOrCreateSquareCustomer($invoice);
            if (!$customerId) {
                return [
                    'success' => false,
                    'message' => 'Failed to resolve or create customer record in Square.',
                ];
            }

            // 2. Create Square Order
            $orderId = $this->createSquareOrder($invoice);
            if (!$orderId) {
                return [
                    'success' => false,
                    'message' => 'Failed to create Order in Square.',
                ];
            }

            // 3. Create Square Invoice Draft
            $squareInvoice = $this->createSquareInvoiceDraft($invoice, $orderId, $customerId);
            if (!$squareInvoice || empty($squareInvoice['id'])) {
                return [
                    'success' => false,
                    'message' => 'Failed to create Draft Invoice in Square.',
                ];
            }

            $squareInvoiceId = $squareInvoice['id'];
            $version = (int) ($squareInvoice['version'] ?? 0);

            // 4. Publish Square Invoice (Triggers Square Email Delivery)
            $publishedInvoice = $this->publishSquareInvoice($squareInvoiceId, $version);

            $finalInvoice = $publishedInvoice ?: $squareInvoice;
            $publicUrl = $finalInvoice['public_url'] ?? null;
            $status = (string) ($finalInvoice['status'] ?? 'UNPAID');

            // 5. Update Laravel SalesInvoice with Square references
            $invoice->update([
                'square_invoice_id' => $squareInvoiceId,
                'square_order_id' => $orderId,
                'square_customer_id' => $customerId,
                'square_public_url' => $publicUrl ?: "https://square.link/i/{$squareInvoiceId}",
                'square_status' => $status ?: 'UNPAID',
                'square_sent_at' => now(),
                'status' => SalesInvoice::STATUS_UNPAID,
                'payment_method' => SalesInvoice::PAYMENT_METHOD_SQUARE_CARD,
            ]);

            Log::info('Square Invoice created and published successfully', [
                'sales_invoice_id' => $invoice->id,
                'square_invoice_id' => $squareInvoiceId,
                'public_url' => $invoice->square_public_url,
            ]);

            return [
                'success' => true,
                'square_invoice_id' => $squareInvoiceId,
                'public_url' => $invoice->square_public_url,
                'status' => $invoice->square_status,
                'message' => "Invoice {$invoice->invoice_number} sent via Square successfully.",
            ];
        } catch (\Throwable $e) {
            Log::error('Square Invoice creation failed', [
                'sales_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Square Invoice error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sync invoice status directly from Square API.
     */
    public function syncInvoiceStatus(SalesInvoice $invoice): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'message' => 'Square API credentials are not configured in environment.',
                ];
            }

            if (!$invoice->hasSquareInvoice()) {
                return [
                    'success' => false,
                    'message' => 'This invoice does not have a linked Square Invoice ID.',
                ];
            }

            $response = $this->http()->get("/v2/invoices/{$invoice->square_invoice_id}");
            $data = $response->json();

            if (!$response->successful() || !empty($data['errors'])) {
                $errMsg = $this->formatErrors($data['errors'] ?? [['detail' => "HTTP {$response->status()} error"]]);
                return [
                    'success' => false,
                    'message' => 'Failed to retrieve invoice from Square: ' . $errMsg,
                ];
            }

            $squareInvoice = $data['invoice'] ?? null;
            if (!$squareInvoice) {
                return [
                    'success' => false,
                    'message' => 'Square Invoice not found in Square response.',
                ];
            }

            $squareStatus = (string) ($squareInvoice['status'] ?? 'UNKNOWN');
            $publicUrl = $squareInvoice['public_url'] ?? $invoice->square_public_url;

            $updateData = [
                'square_status' => $squareStatus,
                'square_public_url' => $publicUrl ?: $invoice->square_public_url,
            ];

            if (strtoupper($squareStatus) === 'PAID') {
                $updateData['status'] = SalesInvoice::STATUS_PAID;
                if (!$invoice->paid_at) {
                    $updateData['paid_at'] = now();
                }
            } elseif (strtoupper($squareStatus) === 'CANCELED') {
                $updateData['status'] = SalesInvoice::STATUS_UNPAID;
            } elseif (strtoupper($squareStatus) === 'UNPAID' || strtoupper($squareStatus) === 'SCHEDULED') {
                $updateData['status'] = SalesInvoice::STATUS_UNPAID;
            }

            $invoice->update($updateData);

            return [
                'success' => true,
                'square_status' => $squareStatus,
                'status' => $invoice->status_label,
                'message' => "Invoice status synced: {$squareStatus}.",
            ];
        } catch (\Throwable $e) {
            Log::error('Sync Square Invoice error', [
                'sales_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error syncing status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel Square Invoice.
     */
    public function cancelInvoice(SalesInvoice $invoice): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'message' => 'Square API credentials are not configured in environment.',
                ];
            }

            if (!$invoice->hasSquareInvoice()) {
                return [
                    'success' => false,
                    'message' => 'No linked Square Invoice found.',
                ];
            }

            // Get current version first
            $getResp = $this->http()->get("/v2/invoices/{$invoice->square_invoice_id}");
            $getData = $getResp->json();

            if (!$getResp->successful() || empty($getData['invoice'])) {
                return [
                    'success' => false,
                    'message' => 'Could not find invoice on Square to cancel.',
                ];
            }

            $version = (int) ($getData['invoice']['version'] ?? 0);

            $cancelResp = $this->http()->post("/v2/invoices/{$invoice->square_invoice_id}/cancel", [
                'version' => $version,
            ]);
            $cancelData = $cancelResp->json();

            if (!$cancelResp->successful() || !empty($cancelData['errors'])) {
                return [
                    'success' => false,
                    'message' => 'Failed to cancel Square invoice: ' . $this->formatErrors($cancelData['errors'] ?? []),
                ];
            }

            $invoice->update([
                'square_status' => 'CANCELED',
                'status' => SalesInvoice::STATUS_UNPAID,
            ]);

            return [
                'success' => true,
                'message' => "Square Invoice {$invoice->invoice_number} has been canceled.",
            ];
        } catch (\Throwable $e) {
            Log::error('Cancel Square Invoice error', [
                'sales_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error canceling invoice: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get or create Square Customer ID.
     */
    private function getOrCreateSquareCustomer(SalesInvoice $invoice): ?string
    {
        $client = $invoice->client;
        if ($client && !empty($client->square_customer_id)) {
            return $client->square_customer_id;
        }

        if (!empty($invoice->square_customer_id)) {
            return $invoice->square_customer_id;
        }

        $email = trim((string) $invoice->customer_email);
        $nameParts = explode(' ', trim((string) $invoice->customer_name), 2);
        $givenName = $nameParts[0] ?? 'Valued';
        $familyName = $nameParts[1] ?? 'Customer';

        // 1. Try to search by email first
        try {
            $searchResp = $this->http()->post('/v2/customers/search', [
                'query' => [
                    'filter' => [
                        'email_address' => [
                            'exact' => $email,
                        ],
                    ],
                ],
            ]);

            $searchData = $searchResp->json();
            if ($searchResp->successful() && !empty($searchData['customers'][0]['id'])) {
                $customerId = $searchData['customers'][0]['id'];

                if ($client && $customerId) {
                    $client->update(['square_customer_id' => $customerId]);
                }

                return $customerId;
            }
        } catch (\Throwable $e) {
            Log::warning('Square customer search failed, attempting create', ['error' => $e->getMessage()]);
        }

        // 2. Create customer if search yielded nothing
        try {
            $payload = [
                'idempotency_key' => Str::uuid()->toString(),
                'given_name' => $givenName,
                'family_name' => $familyName,
                'email_address' => $email,
                'reference_id' => $client ? "client-{$client->id}" : "invoice-{$invoice->id}",
            ];

            if (!empty($invoice->customer_phone)) {
                $payload['phone_number'] = $invoice->customer_phone;
            }
            if (!empty($invoice->customer_company)) {
                $payload['company_name'] = $invoice->customer_company;
            }

            $createResp = $this->http()->post('/v2/customers', $payload);
            $createData = $createResp->json();

            if (!$createResp->successful() || empty($createData['customer']['id'])) {
                Log::error('Failed to create Square Customer', [
                    'errors' => $this->formatErrors($createData['errors'] ?? [])
                ]);
                return null;
            }

            $customerId = $createData['customer']['id'];

            if ($client && $customerId) {
                $client->update(['square_customer_id' => $customerId]);
            }

            return $customerId;
        } catch (\Throwable $e) {
            Log::error('Exception creating Square customer', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create Square Order.
     */
    private function createSquareOrder(SalesInvoice $invoice): ?string
    {
        $lineItems = [];
        foreach ($invoice->items as $item) {
            $categoryName = mb_substr(trim((string) ($item->invoice_category ?: 'Item')), 0, 500);
            $description = mb_substr(trim((string) ($item->description ?: '')), 0, 500);

            $lineItem = [
                'name' => $categoryName ?: 'Service Item',
                'quantity' => (string) max(1, (int) $item->quantity),
                'base_price_money' => [
                    'amount' => (int) round(((float) $item->unit_price) * 100),
                    'currency' => 'USD',
                ],
            ];

            if (!empty($description)) {
                $lineItem['note'] = $description;
            }

            $lineItems[] = $lineItem;
        }

        $taxes = [];
        if ((float) $invoice->tax_amount > 0) {
            $taxes[] = [
                'name' => 'Sales Tax',
                'percentage' => (string) number_format((float) $invoice->tax_rate, 2, '.', ''),
                'scope' => 'ORDER',
            ];
        }

        $orderPayload = [
            'idempotency_key' => Str::uuid()->toString(),
            'order' => [
                'location_id' => $this->locationId,
                'reference_id' => $invoice->invoice_number,
                'line_items' => $lineItems,
            ],
        ];

        if (!empty($taxes)) {
            $orderPayload['order']['taxes'] = $taxes;
        }

        $response = $this->http()->post('/v2/orders', $orderPayload);
        $data = $response->json();

        if (!$response->successful() || empty($data['order']['id'])) {
            Log::error('Square Order creation errors', [
                'errors' => $this->formatErrors($data['errors'] ?? [])
            ]);
            return null;
        }

        return $data['order']['id'];
    }

    /**
     * Create Draft Square Invoice.
     */
    private function createSquareInvoiceDraft(SalesInvoice $invoice, string $orderId, string $customerId): ?array
    {
        $dueDate = ($invoice->due_date ?: $invoice->service_date ?: now())->format('Y-m-d');
        $saleOrServiceDate = $invoice->service_date?->format('Y-m-d');

        $invoiceNumberCandidates = [
            $invoice->invoice_number,
            $invoice->invoice_number . '-' . substr((string) time(), -4),
            null, // Omit to let Square auto-generate a unique invoice number if candidates conflict
        ];

        foreach ($invoiceNumberCandidates as $candidateNumber) {
            try {
                $invoiceBody = [
                    'location_id' => $this->locationId,
                    'order_id' => $orderId,
                    'primary_recipient' => [
                        'customer_id' => $customerId,
                    ],
                    'payment_requests' => [
                        [
                            'request_type' => 'BALANCE',
                            'due_date' => $dueDate,
                            'automatic_payment_source' => 'NONE',
                        ],
                    ],
                    'accepted_payment_methods' => [
                        'card' => true,
                        'square_gift_card' => false,
                        'bank_account' => true,
                        'buy_now_pay_later' => false,
                        'cash_app_pay' => true,
                    ],
                    'title' => $invoice->invoice_title ?: $invoice->invoice_number,
                    'delivery_method' => 'EMAIL',
                ];

                if (!empty($candidateNumber)) {
                    $invoiceBody['invoice_number'] = $candidateNumber;
                }
                if (!empty($invoice->message)) {
                    $invoiceBody['description'] = $invoice->message;
                }
                if (!empty($saleOrServiceDate)) {
                    $invoiceBody['sale_or_service_date'] = $saleOrServiceDate;
                }

                $response = $this->http()->post('/v2/invoices', [
                    'idempotency_key' => Str::uuid()->toString(),
                    'invoice' => $invoiceBody,
                ]);

                $data = $response->json();

                if (!$response->successful() || !empty($data['errors'])) {
                    $errorStr = $this->formatErrors($data['errors'] ?? []);
                    if (str_contains($errorStr, 'already used at this location') || str_contains($errorStr, 'invoice_number')) {
                        Log::warning("Square invoice_number '{$candidateNumber}' collision. Retrying with fallback...");
                        continue;
                    }
                    Log::error('Square Invoice Draft creation errors', ['errors' => $errorStr]);
                    return null;
                }

                return $data['invoice'] ?? null;
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                if (str_contains($msg, 'already used at this location') || str_contains($msg, 'invoice_number')) {
                    Log::warning("Square invoice_number '{$candidateNumber}' collision caught in exception. Retrying with fallback...");
                    continue;
                }
                throw $e;
            }
        }

        return null;
    }

    /**
     * Publish Square Invoice.
     */
    private function publishSquareInvoice(string $squareInvoiceId, int $version): ?array
    {
        $response = $this->http()->post("/v2/invoices/{$squareInvoiceId}/publish", [
            'idempotency_key' => Str::uuid()->toString(),
            'version' => $version,
        ]);

        $data = $response->json();

        if (!$response->successful() || !empty($data['errors'])) {
            Log::error('Square Invoice Publish errors', [
                'errors' => $this->formatErrors($data['errors'] ?? [])
            ]);
            return null;
        }

        return $data['invoice'] ?? null;
    }

    /**
     * Format Square API error array into readable string.
     */
    private function formatErrors(array $errors): string
    {
        return implode(', ', array_map(function ($error) {
            if (is_array($error)) {
                return $error['detail'] ?? ($error['message'] ?? ($error['code'] ?? 'Unknown Square error'));
            }
            if (is_object($error) && method_exists($error, 'getDetail')) {
                return $error->getDetail() ?: ($error->getMessage() ?? 'Unknown Square error');
            }
            return (string) $error;
        }, $errors));
    }
}
