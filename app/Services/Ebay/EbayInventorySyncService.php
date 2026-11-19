<?php

namespace App\Services\Ebay;

use App\Models\EbayInventorySync;
use App\Models\Inventory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EbayInventorySyncService
{
    protected EbayOrderSyncService $orderSyncService;

    public function __construct(EbayOrderSyncService $orderSyncService)
    {
        $this->orderSyncService = $orderSyncService;
    }

    /**
     * Get underlying EbayOrderSyncService instance.
     */
    public function getOrderSyncService(): EbayOrderSyncService
    {
        return $this->orderSyncService;
    }

    /**
     * Resolve eBay identifiers (SKU, Offer ID, Listing/Item ID) for an Inventory item.
     * Checks previous sync history, barcode, model, notes, and eBay Inventory API.
     *
     * @param Inventory $inventory
     * @param string|null $accessToken
     * @return array{sku: ?string, ebay_offer_id: ?string, ebay_item_id: ?string}
     */
    public function resolveEbayMapping(Inventory $inventory, ?string $accessToken = null): array
    {
        $mapping = [
            'sku' => null,
            'ebay_offer_id' => null,
            'ebay_item_id' => null,
        ];

        // 1. Check if we already have a previous sync or discovered mapping for this inventory item
        $lastSync = EbayInventorySync::where('inventory_id', $inventory->id)
            ->where(function ($q) {
                $q->whereNotNull('sku')->orWhereNotNull('ebay_item_id');
            })
            ->orderByDesc('id')
            ->first();

        if ($lastSync) {
            $mapping['sku'] = $lastSync->sku;
            $mapping['ebay_offer_id'] = $lastSync->ebay_offer_id;
            $mapping['ebay_item_id'] = $lastSync->ebay_item_id;
        }

        // 2. Check if this inventory item was previously linked via eBay orders
        if (empty($mapping['sku']) || empty($mapping['ebay_item_id'])) {
            $lastOrderLine = \App\Models\EbayOrderLineItem::where('inventory_id', $inventory->id)
                ->orderByDesc('id')
                ->first();
            if ($lastOrderLine) {
                $mapping['sku'] = $mapping['sku'] ?: ($lastOrderLine->sku ?: null);
                $mapping['ebay_item_id'] = $mapping['ebay_item_id'] ?: ($lastOrderLine->legacy_item_id ?: null);
            }
        }

        // 3. Extract eBay Item ID and SKU from barcode
        $barcode = trim((string) ($inventory->barcode ?? ''));
        $isIntakeEbay = strtolower((string) ($inventory->intake_type ?? '')) === 'ebay';

        if (!empty($barcode)) {
            if (preg_match('/(?:v1\|)?(\d{9,14})(?:\|0)?/', $barcode, $m)) {
                $mapping['ebay_item_id'] = $mapping['ebay_item_id'] ?: $m[1];
            } elseif (empty($mapping['sku'])) {
                // If intake_type is eBay, or barcode explicitly uses SKU/EBAY prefix
                if ($isIntakeEbay || preg_match('/^(?:SKU[-_]|EBAY[-_])/i', $barcode)) {
                    $mapping['sku'] = $barcode;
                }
            }
        }

        // 4. Extract from model if intake_type is eBay or model starts with SKU prefix
        $model = trim((string) ($inventory->model ?? ''));
        if (empty($mapping['sku']) && !empty($model)) {
            if ($isIntakeEbay || preg_match('/^(?:SKU[-_]|EBAY[-_])/i', $model)) {
                $mapping['sku'] = $model;
            }
        }

        // 5. Extract from notes if present
        $notes = (string) ($inventory->notes ?? '');
        if (!empty($notes)) {
            if (empty($mapping['ebay_item_id']) && preg_match('/eBay Item ID:\s*(?:v1\|)?(\d{9,14})/i', $notes, $mId)) {
                $mapping['ebay_item_id'] = $mId[1];
            }
            if (empty($mapping['ebay_offer_id']) && preg_match('/(?:eBay )?Offer ID:\s*([a-zA-Z0-9_\-]+)/i', $notes, $mOff)) {
                $mapping['ebay_offer_id'] = $mOff[1];
            }
            if (empty($mapping['sku']) && preg_match('/(?:eBay )?SKU:\s*([a-zA-Z0-9_\-]+)/i', $notes, $mSku)) {
                $mapping['sku'] = $mSku[1];
            }
        }

        // 5. If we have SKU but missing Offer ID, query eBay Inventory API to discover Offer ID
        if (!empty($mapping['sku']) && empty($mapping['ebay_offer_id']) && !empty($accessToken)) {
            try {
                $discovered = $this->findOfferBySku($accessToken, $mapping['sku']);
                if ($discovered) {
                    $mapping['ebay_offer_id'] = $discovered['offerId'] ?? null;
                    if (!empty($discovered['listingId'])) {
                        $mapping['ebay_item_id'] = $discovered['listingId'];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Could not discover eBay offer by SKU '{$mapping['sku']}': " . $e->getMessage());
            }
        }

        return $mapping;
    }

    /**
     * Query eBay Inventory API to find active offer details for a given SKU.
     */
    public function findOfferBySku(string $accessToken, string $sku): ?array
    {
        $baseUrl = $this->orderSyncService->getBaseUrl();

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(15)
            ->get($baseUrl . '/sell/inventory/v1/offer', [
                'sku' => $sku,
                'limit' => 5,
            ]);

        if ($response->successful()) {
            $offers = $response->json('offers') ?? [];
            if (!empty($offers)) {
                // Prefer published offer
                foreach ($offers as $off) {
                    if (strtoupper($off['status'] ?? '') === 'PUBLISHED') {
                        return [
                            'offerId' => $off['offerId'] ?? null,
                            'listingId' => $off['listing']['listingId'] ?? null,
                            'status' => $off['status'] ?? null,
                        ];
                    }
                }

                // Fallback to first offer
                $first = $offers[0];
                return [
                    'offerId' => $first['offerId'] ?? null,
                    'listingId' => $first['listing']['listingId'] ?? null,
                    'status' => $first['status'] ?? null,
                ];
            }
        }

        return null;
    }

    /**
     * Synchronize the latest available Laravel inventory quantity of a product to eBay.
     *
     * @param int $inventoryId
     * @param int|null $orderId
     * @return EbayInventorySync
     * @throws \Exception
     */
    public function syncInventoryQuantityToEbay(int $inventoryId, ?int $orderId = null, int $attempts = 1): EbayInventorySync
    {
        $lockKey = "ebay_sync_inventory_lock_{$inventoryId}";

        return Cache::lock($lockKey, 30)->block(15, function () use ($inventoryId, $orderId, $attempts) {
            return $this->performSyncInventoryQuantityToEbay($inventoryId, $orderId, $attempts);
        });
    }

    /**
     * Perform the inventory quantity sync to eBay.
     */
    protected function performSyncInventoryQuantityToEbay(int $inventoryId, ?int $orderId = null, int $attempts = 1): EbayInventorySync
    {
        // 1. Fetch fresh inventory record from database to ensure latest balance
        /** @var Inventory|null $inventory */
        $inventory = Inventory::find($inventoryId);

        if (!$inventory) {
            Log::warning("eBay inventory sync skipped: Inventory ID #{$inventoryId} not found in database.");

            return EbayInventorySync::create([
                'inventory_id' => $inventoryId,
                'order_id' => $orderId,
                'synced_quantity' => 0,
                'status' => 'failed',
                'attempts' => $attempts,
                'error_message' => "Inventory item #{$inventoryId} not found in database.",
                'last_attempt_at' => now(),
            ]);
        }

        // 2. Compute exact latest available quantity from actual Laravel inventory balance
        // Available quantity = quantity - reuse_quantity (clamped to >= 0)
        $availableQuantity = max(0, (int) $inventory->quantity - (int) ($inventory->reuse_quantity ?? 0));
        $targetQuantity = $availableQuantity;

        // Ensure status reflects stock state
        if ($targetQuantity === 0 && $inventory->status !== 'Sold') {
            $inventory->status = 'Sold';
            $inventory->save();
        } elseif ($targetQuantity > 0 && $inventory->status === 'Sold') {
            $inventory->status = 'In Stock';
            $inventory->save();
        }

        // 3. Resolve initial local eBay mapping (SKU, Offer ID, Listing Item ID)
        $mapping = $this->resolveEbayMapping($inventory, null);
        $sku = $mapping['sku'];
        $offerId = $mapping['ebay_offer_id'];
        $ebayItemId = $mapping['ebay_item_id'];

        if (empty($sku) && empty($ebayItemId)) {
            Log::info("eBay inventory sync skipped for item #{$inventoryId} ('{$inventory->name}'): No associated eBay SKU or Listing ID found.");

            return EbayInventorySync::create([
                'inventory_id' => $inventory->id,
                'order_id' => $orderId,
                'synced_quantity' => $targetQuantity,
                'status' => 'skipped_no_mapping',
                'attempts' => $attempts,
                'error_message' => 'Item is not linked to an eBay listing or SKU.',
                'last_attempt_at' => now(),
            ]);
        }

        // 4. Obtain OAuth User Access Token (refreshes if needed)
        $accessToken = null;
        try {
            $accessToken = $this->orderSyncService->getUserAccessToken();
        } catch (\Throwable $e) {
            Log::error("eBay authorization error during inventory sync for item #{$inventoryId}: " . $e->getMessage());

            return EbayInventorySync::create([
                'inventory_id' => $inventory->id,
                'order_id' => $orderId,
                'sku' => $sku,
                'ebay_item_id' => $ebayItemId,
                'ebay_offer_id' => $offerId,
                'synced_quantity' => $targetQuantity,
                'status' => 'failed',
                'attempts' => $attempts,
                'error_message' => 'OAuth token acquisition failed: ' . $e->getMessage(),
                'last_attempt_at' => now(),
            ]);
        }

        // Re-resolve with access token to discover missing Offer ID if needed
        if (!empty($sku) && empty($offerId)) {
            $mapping = $this->resolveEbayMapping($inventory, $accessToken);
            $offerId = $mapping['ebay_offer_id'];
            $ebayItemId = $mapping['ebay_item_id'] ?: $ebayItemId;
        }

        // 5. Execute eBay API Update
        $baseUrl = $this->orderSyncService->getBaseUrl();
        $syncMode = null;
        $requestPayload = null;
        $responsePayload = null;
        $syncSuccess = false;
        $errorMessage = null;

        // Approach A: If SKU is present, use official eBay Inventory API bulkUpdatePriceQuantity
        if (!empty($sku)) {
            $syncMode = 'inventory_api';
            $requestItem = [
                'sku' => $sku,
                'shipToLocationAvailability' => [
                    'quantity' => $targetQuantity,
                ],
            ];

            if (!empty($offerId)) {
                $requestItem['offers'] = [
                    [
                        'offerId' => (string) $offerId,
                        'availableQuantity' => $targetQuantity,
                    ],
                ];
            }

            $requestPayload = [
                'requests' => [$requestItem],
            ];

            Log::info("Sending eBay Inventory API bulkUpdatePriceQuantity for SKU '{$sku}'", [
                'inventory_id' => $inventory->id,
                'target_quantity' => $targetQuantity,
                'payload' => $requestPayload,
            ]);

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(20)
                ->post($baseUrl . '/sell/inventory/v1/bulk_update_price_quantity', $requestPayload);

            $responsePayload = $response->json() ?? ['body' => $response->body()];
            $httpStatus = $response->status();

            // Handle 401 Unauthorized (expired token refresh & retry once)
            if ($httpStatus === 401) {
                Log::info("eBay token expired during bulkUpdatePriceQuantity. Refreshing token and retrying...");
                $accessToken = $this->orderSyncService->getUserAccessToken();
                $response = Http::withToken($accessToken)
                    ->acceptJson()
                    ->timeout(20)
                    ->post($baseUrl . '/sell/inventory/v1/bulk_update_price_quantity', $requestPayload);
                $responsePayload = $response->json() ?? ['body' => $response->body()];
                $httpStatus = $response->status();
            }

            $isAuthError = in_array($httpStatus, [401, 403], true);
            $isRateLimitError = ($httpStatus === 429);
            $isServerError = ($httpStatus >= 500 && $httpStatus <= 599);

            if ($response->successful()) {
                $firstResp = $responsePayload['responses'][0] ?? null;
                $respCode = (int) ($firstResp['statusCode'] ?? $httpStatus);

                if ($respCode >= 200 && $respCode < 300) {
                    $syncSuccess = true;
                } else {
                    $errors = $firstResp['errors'] ?? [];
                    $errorMessage = !empty($errors) ? json_encode($errors) : "Item update rejected with status {$respCode}";
                    if ($respCode === 429) {
                        $isRateLimitError = true;
                    }
                    if ($respCode >= 500 && $respCode <= 599) {
                        $isServerError = true;
                    }
                }
            } else {
                $errorMessage = "HTTP {$httpStatus}: " . json_encode($responsePayload);
            }
        }

        // Approach B: Fallback to eBay Trading API ReviseInventoryStatus
        // CRITICAL PRODUCTION SAFETY AUDIT RULE:
        // Do NOT use Trading API fallback for authentication failures, rate limits, or temporary server errors!
        // Only use fallback if:
        //  1. The item has no Inventory API SKU (legacy listing with only numeric Item ID), OR
        //  2. Inventory API explicitly reported that the SKU or Offer does not exist in the Inventory API catalog
        $canFallbackToTrading = false;
        if (!$syncSuccess && !empty($ebayItemId)) {
            if (empty($sku)) {
                $canFallbackToTrading = true;
            } elseif (!$isAuthError && !$isRateLimitError && !$isServerError) {
                $errStr = strtolower((string) $errorMessage);
                $isUnmapped = ($httpStatus === 404)
                    || str_contains($errStr, 'not found')
                    || str_contains($errStr, '25710') // Item not found for sku
                    || str_contains($errStr, '25804') // Offer not found
                    || str_contains($errStr, '25002'); // No offer found for SKU
                if ($isUnmapped) {
                    $canFallbackToTrading = true;
                }
            }
        }

        if ($canFallbackToTrading) {
            Log::info("Using eBay Trading API ReviseInventoryStatus fallback for Item ID '{$ebayItemId}' (Qty: {$targetQuantity})...");

            $tradingResult = $this->updateQuantityViaTradingApi($accessToken, $ebayItemId, $targetQuantity);
            $syncMode = 'trading_api';
            $requestPayload = $tradingResult['request'];
            $responsePayload = $tradingResult['response'];
            $syncSuccess = $tradingResult['success'];
            $errorMessage = $tradingResult['error'] ?? null;
        }

        // 6. Record Synchronization Result
        $status = $syncSuccess ? 'success' : 'failed';
        $now = now();

        $syncAttributes = [
            'sku' => $sku,
            'ebay_item_id' => $ebayItemId,
            'ebay_offer_id' => $offerId,
            'synced_quantity' => $targetQuantity,
            'status' => $status,
            'sync_mode' => $syncMode,
            'attempts' => $attempts,
            'last_attempt_at' => $now,
            'last_success_at' => $syncSuccess ? $now : null,
            'error_message' => $errorMessage,
            'request_payload' => $requestPayload,
            'response_payload' => $responsePayload,
        ];

        if ($orderId) {
            $syncRecord = EbayInventorySync::updateOrCreate(
                ['inventory_id' => $inventory->id, 'order_id' => $orderId],
                $syncAttributes
            );
        } else {
            $syncRecord = EbayInventorySync::create(array_merge(
                ['inventory_id' => $inventory->id, 'order_id' => null],
                $syncAttributes
            ));
        }

        if ($syncSuccess) {
            Log::info("eBay quantity synchronization succeeded for Inventory #{$inventory->id} (eBay Qty: {$targetQuantity}, Mode: {$syncMode})");
        } else {
            Log::error("eBay quantity synchronization failed for Inventory #{$inventory->id}: {$errorMessage}");
            // Throw exception if this failure is retryable (e.g. rate limit or server error)
            if ($this->isRetryableError($errorMessage)) {
                throw new \Exception("Temporary eBay API failure during quantity sync: {$errorMessage}");
            }
        }

        return $syncRecord;
    }

    /**
     * Fallback to update listing quantity via eBay Trading API ReviseInventoryStatus.
     */
    protected function updateQuantityViaTradingApi(string $userToken, string $ebayItemId, int $quantity): array
    {
        $baseUrl = $this->orderSyncService->getBaseUrl();
        $isSandbox = str_contains($baseUrl, 'sandbox');
        $endpoint = $isSandbox ? 'https://api.sandbox.ebay.com/ws/api.dll' : 'https://api.ebay.com/ws/api.dll';

        $rawNumericId = preg_match('/(\d{9,14})/', $ebayItemId, $m) ? $m[1] : $ebayItemId;

        // If quantity is 0, attempt ReviseInventoryStatus; if rejected because listing must end, try EndFixedPriceItem
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
            . '  <InventoryStatus>'
            . '    <ItemID>' . htmlspecialchars($rawNumericId, ENT_XML1) . '</ItemID>'
            . '    <Quantity>' . max(0, $quantity) . '</Quantity>'
            . '  </InventoryStatus>'
            . '</ReviseInventoryStatusRequest>';

        try {
            $response = Http::timeout(20)->withHeaders([
                'X-EBAY-API-CALL-NAME' => 'ReviseInventoryStatus',
                'X-EBAY-API-SITEID' => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                'X-EBAY-API-IAF-TOKEN' => $userToken,
            ])->withBody($xmlBody, 'text/xml')->post($endpoint);

            $xml = @simplexml_load_string($response->body());
            $ack = (string) ($xml->Ack ?? '');
            $isSuccess = in_array(strtolower($ack), ['success', 'warning'], true);

            if ($isSuccess) {
                return [
                    'success' => true,
                    'request' => ['itemId' => $rawNumericId, 'quantity' => $quantity],
                    'response' => ['ack' => $ack, 'raw' => $response->body()],
                ];
            }

            $errorMsg = (string) ($xml->Errors->LongMessage ?? $xml->Errors->ShortMessage ?? 'Trading API ReviseInventoryStatus call failed');
            $errorCode = (string) ($xml->Errors->ErrorCode ?? '');

            // Zero-quantity handling with Out-of-Stock control:
            // When Out-of-Stock Control is active on the seller account, ReviseInventoryStatus with quantity=0
            // succeeds with Ack=Success/Warning, keeping the listing active in out-of-stock mode.
            // When restocked (quantity > 0), ReviseInventoryStatus automatically reactivates the listing.
            // ONLY if eBay explicitly rejects quantity=0 because Out-of-Stock control is disabled (e.g. error 21916564/21916560),
            // does it fall back to EndFixedPriceItem.
            $isOosDisabled = $quantity === 0 && (
                in_array($errorCode, ['21916564', '21916560', '21919028'], true)
                || stripos($errorMsg, 'greater than 0') !== false
                || (stripos($errorMsg, 'out of stock') !== false && stripos($errorMsg, 'disabled') !== false)
            );

            if ($isOosDisabled) {
                Log::info("Trading API requires ending listing for 0 quantity on Item #{$rawNumericId} because Out-of-Stock control is disabled. Calling EndFixedPriceItem...");
                return $this->endListingViaTradingApi($userToken, $rawNumericId);
            }

            if ($quantity > 0 && (in_array($errorCode, ['21916799', '17'], true) || stripos($errorMsg, 'ended') !== false)) {
                Log::warning("eBay listing Item #{$rawNumericId} is ended on eBay; it cannot be revised via ReviseInventoryStatus and requires relisting.");
            }

            return [
                'success' => false,
                'request' => ['itemId' => $rawNumericId, 'quantity' => $quantity],
                'response' => ['ack' => $ack, 'raw' => $response->body()],
                'error' => $errorMsg,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'request' => ['itemId' => $rawNumericId, 'quantity' => $quantity],
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * End a listing via Trading API when quantity drops to 0 and Out-of-Stock control is off.
     */
    protected function endListingViaTradingApi(string $userToken, string $ebayItemId): array
    {
        $baseUrl = $this->orderSyncService->getBaseUrl();
        $isSandbox = str_contains($baseUrl, 'sandbox');
        $endpoint = $isSandbox ? 'https://api.sandbox.ebay.com/ws/api.dll' : 'https://api.ebay.com/ws/api.dll';

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<EndFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
            . '  <ItemID>' . htmlspecialchars($ebayItemId, ENT_XML1) . '</ItemID>'
            . '  <EndingReason>NotAvailable</EndingReason>'
            . '</EndFixedPriceItemRequest>';

        try {
            $response = Http::timeout(20)->withHeaders([
                'X-EBAY-API-CALL-NAME' => 'EndFixedPriceItem',
                'X-EBAY-API-SITEID' => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                'X-EBAY-API-IAF-TOKEN' => $userToken,
            ])->withBody($xmlBody, 'text/xml')->post($endpoint);

            $xml = @simplexml_load_string($response->body());
            $ack = (string) ($xml->Ack ?? '');
            $isSuccess = in_array(strtolower($ack), ['success', 'warning'], true);

            return [
                'success' => $isSuccess,
                'request' => ['itemId' => $ebayItemId, 'action' => 'EndFixedPriceItem'],
                'response' => ['ack' => $ack, 'raw' => $response->body()],
                'error' => $isSuccess ? null : (string) ($xml->Errors->LongMessage ?? 'EndFixedPriceItem failed'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'request' => ['itemId' => $ebayItemId, 'action' => 'EndFixedPriceItem'],
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Determine if an error message represents a temporary, retryable condition.
     */
    protected function isRetryableError(?string $error): bool
    {
        if (empty($error)) return false;

        $retryablePatterns = [
            'timeout',
            'timed out',
            'rate limit',
            'too many requests',
            '429',
            '500',
            '502',
            '503',
            '504',
            'connection reset',
            'service unavailable',
            'internal error',
        ];

        $lower = strtolower($error);
        foreach ($retryablePatterns as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
