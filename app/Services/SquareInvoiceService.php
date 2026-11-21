<?php

namespace App\Services;

use App\Models\Client;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Square\Customers\Requests\CreateCustomerRequest;
use Illuminate\Support\Facades\Mail;
use Square\Customers\Requests\SearchCustomersRequest;
use Square\Environments;
use Square\Invoices\Requests\CancelInvoiceRequest;
use Square\Invoices\Requests\CreateInvoiceRequest;
use Square\Invoices\Requests\GetInvoicesRequest;
use Square\Invoices\Requests\PublishInvoiceRequest;
use Square\Invoices\Requests\UpdateInvoiceRequest;
use Square\SquareClient;
use Square\Types\CreateOrderRequest;
use Square\Types\Customer;
use Square\Types\CustomerFilter;
use Square\Types\CustomerQuery;
use Square\Types\CustomerTextFilter;
use Square\Types\Invoice;
use Square\Types\InvoiceAcceptedPaymentMethods;
use Square\Types\InvoicePaymentReminder;
use Square\Types\InvoicePaymentRequest;
use Square\Types\InvoiceRecipient;
use Square\Types\Money;
use Square\Types\Order;
use Square\Types\OrderLineItem;
use Square\Types\OrderLineItemTax;

class SquareInvoiceService
{
    private SquareClient $client;
    private string $locationId;
    private string $environment;

    public function __construct()
    {
        $this->environment = env('SQUARE_ENVIRONMENT', 'sandbox');
        $this->locationId = env('SQUARE_LOCATION_ID', '');

        $token = env('SQUARE_ACCESS_TOKEN') ?: 'sandbox_placeholder_token';

        $this->client = new SquareClient(
            token: $token,
            options: [
                'baseUrl' => $this->environment === 'production'
                    ? Environments::Production->value
                    : Environments::Sandbox->value,
            ]
        );
    }

    /**
     * Check if Square is configured.
     */
    public function isConfigured(): bool
    {
        return !empty(env('SQUARE_ACCESS_TOKEN')) && !empty($this->locationId);
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

            // 3. Create Square Invoice
            $squareInvoice = $this->createSquareInvoiceDraft($invoice, $orderId, $customerId);
            if (!$squareInvoice || !$squareInvoice->getId()) {
                return [
                    'success' => false,
                    'message' => 'Failed to create Draft Invoice in Square.',
                ];
            }

            $squareInvoiceId = $squareInvoice->getId();
            $version = $squareInvoice->getVersion() ?? 0;

            // 4. Publish Square Invoice (Triggers Square Email Delivery)
            $publishedInvoice = $this->publishSquareInvoice($squareInvoiceId, $version);

            $finalInvoice = $publishedInvoice ?? $squareInvoice;
            $publicUrl = method_exists($finalInvoice, 'getPublicUrl') ? $finalInvoice->getPublicUrl() : null;
            $status = method_exists($finalInvoice, 'getStatus') ? (string) $finalInvoice->getStatus() : 'UNPAID';

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
                'public_url' => $publicUrl,
            ]);

            return [
                'success' => true,
                'square_invoice_id' => $squareInvoiceId,
                'public_url' => $invoice->square_public_url,
                'status' => $invoice->square_status,
                'message' => "Invoice {$invoice->invoice_number} sent via Square successfully.",
            ];
        } catch (\Exception $e) {
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
            if (!$invoice->hasSquareInvoice()) {
                return [
                    'success' => false,
                    'message' => 'This invoice does not have a linked Square Invoice ID.',
                ];
            }

            $getReq = new GetInvoicesRequest([
                'invoiceId' => $invoice->square_invoice_id,
            ]);
            $response = $this->client->invoices->get($getReq);
            if ($response->getErrors()) {
                return [
                    'success' => false,
                    'message' => 'Failed to retrieve invoice from Square: ' . $this->formatErrors($response->getErrors()),
                ];
            }

            $squareInvoice = $response->getInvoice();
            if (!$squareInvoice) {
                return [
                    'success' => false,
                    'message' => 'Square Invoice not found.',
                ];
            }

            $squareStatus = (string) $squareInvoice->getStatus();
            $publicUrl = method_exists($squareInvoice, 'getPublicUrl') ? $squareInvoice->getPublicUrl() : $invoice->square_public_url;

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
            $invoice->fresh()?->syncLinkedRepairEntry();

            return [
                'success' => true,
                'square_status' => $squareStatus,
                'status' => $invoice->status_label,
                'message' => "Invoice status synced: {$squareStatus}.",
            ];
        } catch (\Exception $e) {
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
            if (!$invoice->hasSquareInvoice()) {
                return [
                    'success' => false,
                    'message' => 'No linked Square Invoice found.',
                ];
            }

            // Get current version first
            $getReq = new GetInvoicesRequest([
                'invoiceId' => $invoice->square_invoice_id,
            ]);
            $getResp = $this->client->invoices->get($getReq);
            if ($getResp->getErrors() || !$getResp->getInvoice()) {
                return [
                    'success' => false,
                    'message' => 'Could not find invoice on Square to cancel.',
                ];
            }

            $version = $getResp->getInvoice()->getVersion() ?? 0;

            $cancelReq = new CancelInvoiceRequest([
                'invoiceId' => $invoice->square_invoice_id,
                'version' => $version,
            ]);

            $response = $this->client->invoices->cancel($cancelReq);

            if ($response->getErrors()) {
                return [
                    'success' => false,
                    'message' => 'Failed to cancel Square invoice: ' . $this->formatErrors($response->getErrors()),
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
        } catch (\Exception $e) {
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
     * Send Square Payment Reminder for an invoice with custom message text.
     */
    public function sendPaymentReminder(SalesInvoice $invoice, string $reminderMessage, ?string $recipientEmail = null): array
    {
        try {
            $targetEmail = trim((string) ($recipientEmail ?: $invoice->customer_email));
            if (empty($targetEmail)) {
                return [
                    'success' => false,
                    'message' => 'Customer email address is required to send the payment reminder.',
                ];
            }

            // If not yet sent to Square, publish it first so we have the Square payment link
            if (!$invoice->hasSquareInvoice()) {
                $publishResult = $this->createAndSendInvoice($invoice);
                if (!$publishResult['success']) {
                    return [
                        'success' => false,
                        'message' => 'Could not generate Square invoice link: ' . $publishResult['message'],
                    ];
                }
                $invoice->refresh();
            }

            // Sync status to verify if it is already paid
            if ($invoice->hasSquareInvoice()) {
                $this->syncInvoiceStatus($invoice);
                $invoice->refresh();
            }

            if ($invoice->isSquarePaid()) {
                return [
                    'success' => false,
                    'message' => "Invoice {$invoice->invoice_number} is already marked as paid. Reminder was not sent.",
                ];
            }

            // Attempt to record payment reminder in Square API
            try {
                if ($this->isConfigured() && $invoice->square_invoice_id) {
                    $getReq = new GetInvoicesRequest([
                        'invoiceId' => $invoice->square_invoice_id,
                    ]);
                    $getResp = $this->client->invoices->get($getReq);
                    if ($getResp && !$getResp->getErrors()) {
                        $currentInvoice = $getResp->getInvoice();
                        if ($currentInvoice) {
                            $version = $currentInvoice->getVersion() ?? 0;
                            $paymentRequests = $currentInvoice->getPaymentRequests() ?? [];
                            if (!empty($paymentRequests)) {
                                $firstReq = $paymentRequests[0];
                                $existingReminders = $firstReq->getReminders() ?? [];
                                $existingReminders[] = new InvoicePaymentReminder([
                                    'relativeScheduledDays' => 0,
                                    'message' => mb_substr($reminderMessage, 0, 500),
                                ]);
                                $firstReq->setReminders($existingReminders);

                                $updateReq = new UpdateInvoiceRequest([
                                    'invoiceId' => $invoice->square_invoice_id,
                                    'invoice' => new Invoice([
                                        'version' => $version,
                                        'paymentRequests' => [$firstReq],
                                    ]),
                                    'idempotencyKey' => Uuid::uuid4()->toString(),
                                ]);
                                $this->client->invoices->update($updateReq);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::info('Square API reminder recording note: ' . $e->getMessage());
            }

            // Build and send the reminder email
            $paymentUrl = $invoice->square_public_url ?: "https://square.link/i/{$invoice->square_invoice_id}";
            $invoiceNumber = $invoice->invoice_number;
            $customerName = $invoice->customer_name ?: 'Valued Customer';
            $dueDate = $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: 'Upon receipt');
            $totalFormatted = '$' . number_format((float) $invoice->total, 2);
            $cleanMessage = trim($reminderMessage);

            $htmlContent = $this->renderReminderEmailHtml(
                invoice: $invoice,
                customerName: $customerName,
                invoiceNumber: $invoiceNumber,
                dueDate: $dueDate,
                totalFormatted: $totalFormatted,
                paymentUrl: $paymentUrl,
                customMessage: $cleanMessage
            );

            Mail::html($htmlContent, function ($message) use ($targetEmail, $invoiceNumber) {
                $message->to($targetEmail)
                    ->subject("Payment Reminder: Invoice #{$invoiceNumber} - IT INVESTMENT RECOVERIES");
            });

            // Update SalesInvoice with reminder tracking
            $invoice->update([
                'square_reminded_at' => now(),
                'square_reminder_count' => ((int) $invoice->square_reminder_count) + 1,
                'square_last_reminder_message' => $cleanMessage,
            ]);

            Log::info('Square payment reminder sent successfully', [
                'sales_invoice_id' => $invoice->id,
                'recipient' => $targetEmail,
                'reminder_count' => $invoice->square_reminder_count,
            ]);

            return [
                'success' => true,
                'message' => "Payment reminder sent to {$targetEmail} successfully.",
                'reminded_at' => $invoice->square_reminded_at?->format('M d, Y h:i A'),
                'reminder_count' => $invoice->square_reminder_count,
            ];
        } catch (\Throwable $e) {
            Log::error('Send Square Payment Reminder failed', [
                'sales_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error sending payment reminder: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Render HTML content for the payment reminder email.
     */
    private function renderReminderEmailHtml(
        SalesInvoice $invoice,
        string $customerName,
        string $invoiceNumber,
        string $dueDate,
        string $totalFormatted,
        string $paymentUrl,
        string $customMessage
    ): string {
        $safeName = htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
        $safeInvoice = htmlspecialchars($invoiceNumber, ENT_QUOTES, 'UTF-8');
        $safeDueDate = htmlspecialchars($dueDate, ENT_QUOTES, 'UTF-8');
        $safeTotal = htmlspecialchars($totalFormatted, ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($paymentUrl, ENT_QUOTES, 'UTF-8');
        $messageParagraphs = nl2br(htmlspecialchars($customMessage, ENT_QUOTES, 'UTF-8'));

        $itemsRows = '';
        foreach ($invoice->items as $item) {
            $cat = htmlspecialchars($item->invoice_category ?: $item->product_name ?: 'Item', ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars($item->description ?: '', ENT_QUOTES, 'UTF-8');
            $qty = (int) $item->quantity;
            $price = '$' . number_format((float) $item->unit_price, 2);
            $itemTotal = '$' . number_format((float) $item->total, 2);

            $itemsRows .= "
                <tr>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b;\">
                        <strong>{$cat}</strong>
                        " . ($desc ? "<br><span style=\"color: #64748b; font-size: 11px;\">{$desc}</span>" : "") . "
                    </td>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #475569; text-align: center;\">{$qty}</td>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #475569; text-align: right;\">{$price}</td>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #0f172a; text-align: right; font-weight: bold;\">{$itemTotal}</td>
                </tr>
            ";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder for Invoice #{$safeInvoice}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #334155;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="background-color: #0f172a; padding: 28px 32px; text-align: left;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="color: #38bdf8; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;">Payment Reminder</div>
                                        <div style="color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">IT INVESTMENT RECOVERIES</div>
                                    </td>
                                    <td align="right">
                                        <div style="display: inline-block; background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                            Due: {$safeDueDate}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            <div style="background-color: #f8fafc; border-left: 4px solid #2563eb; border-radius: 8px; padding: 18px 20px; margin-bottom: 24px; font-size: 14px; line-height: 1.6; color: #334155;">
                                {$messageParagraphs}
                            </div>
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin-bottom: 28px; text-align: center;">
                                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #166534; margin-bottom: 4px;">Total Balance Due</div>
                                <div style="font-size: 32px; font-weight: 900; color: #15803d; letter-spacing: -1px; margin-bottom: 4px;">{$safeTotal}</div>
                                <div style="font-size: 12px; color: #166534; font-weight: 600;">Invoice #{$safeInvoice} &bull; Due {$safeDueDate}</div>
                            </div>
                            <div style="text-align: center; margin-bottom: 28px;">
                                <a href="{$safeUrl}" target="_blank" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 16px 36px; border-radius: 12px; font-size: 15px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                    Pay Online with Square &rarr;
                                </a>
                                <p style="margin-top: 10px; font-size: 11px; color: #64748b;">
                                    Secure online payment via Credit Card, Debit Card, or Cash App Pay.
                                </p>
                            </div>
                            <div style="margin-bottom: 24px;">
                                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 8px;">Invoice Summary</div>
                                <table width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                    <thead>
                                        <tr style="background-color: #f8fafc;">
                                            <th style="padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Item / Service</th>
                                            <th style="padding: 10px 12px; text-align: center; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Qty</th>
                                            <th style="padding: 10px 12px; text-align: right; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Price</th>
                                            <th style="padding: 10px 12px; text-align: right; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$itemsRows}
                                    </tbody>
                                </table>
                            </div>
                            <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                                If the button above does not work, copy and paste this link into your browser:<br>
                                <a href="{$safeUrl}" style="color: #2563eb; word-break: break-all;">{$safeUrl}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
                            <p style="margin: 0 0 4px 0; font-weight: bold; color: #334155;">IT INVESTMENT RECOVERIES</p>
                            <p style="margin: 0;">Have questions about your invoice? Reply to this email or call us directly.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
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
            $searchReq = new SearchCustomersRequest([
                'query' => new CustomerQuery([
                    'filter' => new CustomerFilter([
                        'emailAddress' => new CustomerTextFilter([
                            'exact' => $email,
                        ]),
                    ]),
                ]),
            ]);

            $searchResp = $this->client->customers->search($searchReq);
            if (!$searchResp->getErrors() && $searchResp->getCustomers() && count($searchResp->getCustomers()) > 0) {
                /** @var Customer $customer */
                $customer = $searchResp->getCustomers()[0];
                $customerId = $customer->getId();

                if ($client && $customerId) {
                    $client->update(['square_customer_id' => $customerId]);
                }

                return $customerId;
            }
        } catch (\Exception $e) {
            Log::warning('Square customer search failed, attempting create', ['error' => $e->getMessage()]);
        }

        // 2. Create customer if search yielded nothing
        try {
            $createReq = new CreateCustomerRequest([
                'idempotencyKey' => Uuid::uuid4()->toString(),
                'givenName' => $givenName,
                'familyName' => $familyName,
                'emailAddress' => $email,
                'phoneNumber' => $invoice->customer_phone ?: null,
                'companyName' => $invoice->customer_company ?: null,
                'referenceId' => $client ? "client-{$client->id}" : "invoice-{$invoice->id}",
            ]);

            $createResp = $this->client->customers->create($createReq);
            if ($createResp->getErrors()) {
                Log::error('Failed to create Square Customer', ['errors' => $this->formatErrors($createResp->getErrors())]);
                return null;
            }

            $customer = $createResp->getCustomer();
            $customerId = $customer?->getId();

            if ($client && $customerId) {
                $client->update(['square_customer_id' => $customerId]);
            }

            return $customerId;
        } catch (\Exception $e) {
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

            $lineItems[] = new OrderLineItem([
                'name' => $categoryName ?: 'Service Item',
                'quantity' => (string) max(1, (int) $item->quantity),
                'note' => $description ?: null,
                'basePriceMoney' => new Money([
                    'amount' => (int) round(((float) $item->unit_price) * 100),
                    'currency' => 'USD',
                ]),
            ]);
        }

        $taxes = [];
        if ((float) $invoice->tax_amount > 0) {
            $taxes[] = new OrderLineItemTax([
                'name' => 'Sales Tax',
                'percentage' => (string) number_format((float) $invoice->tax_rate, 2, '.', ''),
                'scope' => 'ORDER',
            ]);
        }

        $order = new Order([
            'locationId' => $this->locationId,
            'referenceId' => $invoice->invoice_number,
            'lineItems' => $lineItems,
            'taxes' => $taxes,
        ]);

        $orderReq = new CreateOrderRequest([
            'order' => $order,
            'idempotencyKey' => Uuid::uuid4()->toString(),
        ]);

        $response = $this->client->orders->create($orderReq);
        if ($response->getErrors()) {
            Log::error('Square Order creation errors', ['errors' => $this->formatErrors($response->getErrors())]);
            return null;
        }

        return $response->getOrder()?->getId();
    }

    /**
     * Create Draft Square Invoice.
     */
    private function createSquareInvoiceDraft(SalesInvoice $invoice, string $orderId, string $customerId): ?Invoice
    {
        $dueDate = ($invoice->due_date ?: $invoice->service_date ?: now())->format('Y-m-d');
        $saleOrServiceDate = $invoice->service_date?->format('Y-m-d');

        $paymentRequest = new InvoicePaymentRequest([
            'requestType' => 'BALANCE',
            'dueDate' => $dueDate,
            'automaticPaymentSource' => 'NONE',
        ]);

        $recipient = new InvoiceRecipient([
            'customerId' => $customerId,
        ]);

        $acceptedPaymentMethods = new InvoiceAcceptedPaymentMethods([
            'card' => true,
            'squareGiftCard' => false,
            'bankAccount' => true,
            'buyNowPayLater' => false,
            'cashAppPay' => true,
        ]);

        $invoiceNumberCandidates = [
            $invoice->invoice_number,
            $invoice->invoice_number . '-' . substr((string) time(), -4),
            null, // Omit to let Square auto-generate a unique invoice number if candidates conflict
        ];

        foreach ($invoiceNumberCandidates as $candidateNumber) {
            try {
                $invoiceData = new Invoice([
                    'locationId' => $this->locationId,
                    'orderId' => $orderId,
                    'primaryRecipient' => $recipient,
                    'paymentRequests' => [$paymentRequest],
                    'acceptedPaymentMethods' => $acceptedPaymentMethods,
                    'invoiceNumber' => $candidateNumber,
                    'title' => $invoice->invoice_title ?: $invoice->invoice_number,
                    'description' => $invoice->message ?: null,
                    'deliveryMethod' => 'EMAIL',
                    'saleOrServiceDate' => $saleOrServiceDate,
                ]);

                $createReq = new CreateInvoiceRequest([
                    'invoice' => $invoiceData,
                    'idempotencyKey' => Uuid::uuid4()->toString(),
                ]);

                $response = $this->client->invoices->create($createReq);

                if ($response->getErrors()) {
                    $errorStr = $this->formatErrors($response->getErrors());
                    if (str_contains($errorStr, 'already used at this location') || str_contains($errorStr, 'invoice_number')) {
                        Log::warning("Square invoice_number '{$candidateNumber}' collision. Retrying with fallback...");
                        continue;
                    }
                    Log::error('Square Invoice Draft creation errors', ['errors' => $errorStr]);
                    return null;
                }

                return $response->getInvoice();
            } catch (\Exception $e) {
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
    private function publishSquareInvoice(string $squareInvoiceId, int $version): ?Invoice
    {
        $publishReq = new PublishInvoiceRequest([
            'invoiceId' => $squareInvoiceId,
            'version' => $version,
            'idempotencyKey' => Uuid::uuid4()->toString(),
        ]);

        $response = $this->client->invoices->publish($publishReq);
        if ($response->getErrors()) {
            Log::error('Square Invoice Publish errors', ['errors' => $this->formatErrors($response->getErrors())]);
            return null;
        }

        return $response->getInvoice();
    }

    /**
     * Format Square SDK error array into readable string.
     */
    private function formatErrors(array $errors): string
    {
        return implode(', ', array_map(function ($error) {
            if (is_object($error) && method_exists($error, 'getDetail')) {
                return $error->getDetail() ?: ($error->getMessage() ?? 'Unknown Square error');
            }
            if (is_array($error)) {
                return $error['detail'] ?? ($error['message'] ?? 'Unknown Square error');
            }
            return (string) $error;
        }, $errors));
    }
}
