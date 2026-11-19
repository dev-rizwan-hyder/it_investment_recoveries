<?php

namespace App\Services\Ebay;

use App\Models\EbayOrder;
use App\Models\EbayOrderLineItem;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EbayOrderSyncService
{
    /**
     * Get configured eBay Client ID.
     */
    public function getClientId(): string
    {
        return (string) (config('services.ebay.client_id') ?: env('EBAY_CLIENT_ID') ?: env('CLIENT_ID', ''));
    }

    /**
     * Get configured eBay Client Secret.
     */
    public function getClientSecret(): string
    {
        return (string) (config('services.ebay.client_secret') ?: env('EBAY_CLIENT_SECRET') ?: env('CLIENT_SECRET', ''));
    }

    /**
     * Get configured eBay RuName (Redirect URL Name).
     */
    public function getRuName(): string
    {
        return (string) (config('services.ebay.ru_name') ?: env('EBAY_RU_NAME') ?: env('RU_NAME') ?: url('/ebay/callback'));
    }

    /**
     * Get configured eBay Refresh Token.
     * Looks across env, persistent storage file, Cache, and Session.
     */
    public function getRefreshToken(): ?string
    {
        $token = config('services.ebay.refresh_token') ?: env('EBAY_REFRESH_TOKEN') ?: env('REFRESH_TOKEN');
        if (!empty($token) && strlen(trim($token)) >= 20) {
            return trim($token);
        }

        // If env token was truncated or missing, check persistent storage file
        $tokenFile = storage_path('app/ebay_refresh_token.txt');
        if (\Illuminate\Support\Facades\File::exists($tokenFile)) {
            $fileToken = trim(\Illuminate\Support\Facades\File::get($tokenFile));
            if (!empty($fileToken) && strlen($fileToken) >= 20) {
                return $fileToken;
            }
        }

        $cached = \Illuminate\Support\Facades\Cache::get('ebay_refresh_token');
        if (!empty($cached) && strlen(trim($cached)) >= 20) {
            return trim($cached);
        }

        $sessionToken = Session::get('ebay_refresh_token');
        if (!empty($sessionToken) && strlen(trim($sessionToken)) >= 20) {
            return trim($sessionToken);
        }

        return !empty($token) ? trim($token) : null;
    }

    /**
     * Store eBay Refresh Token persistently across storage, Cache, and Session.
     * Safely quotes the token in .env to preserve special characters such as '#'.
     */
    public function storeRefreshToken(string $token): void
    {
        $token = trim($token);
        if (empty($token)) return;

        // 1. Session
        Session::put('ebay_refresh_token', $token);

        // 2. Cache forever
        \Illuminate\Support\Facades\Cache::forever('ebay_refresh_token', $token);

        // 3. Persistent file in storage/app
        try {
            $tokenFile = storage_path('app/ebay_refresh_token.txt');
            \Illuminate\Support\Facades\File::ensureDirectoryExists(storage_path('app'));
            \Illuminate\Support\Facades\File::put($tokenFile, $token);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not save eBay token to file: ' . $e->getMessage());
        }

        // 4. Try updating .env if writable, safely wrapped in double quotes
        try {
            $envPath = base_path('.env');
            if (\Illuminate\Support\Facades\File::isWritable($envPath)) {
                $content = \Illuminate\Support\Facades\File::get($envPath);
                $escapedToken = addcslashes($token, '"\\$');
                if (preg_match('/^EBAY_REFRESH_TOKEN=.*/m', $content)) {
                    $content = preg_replace('/^EBAY_REFRESH_TOKEN=.*/m', "EBAY_REFRESH_TOKEN=\"{$escapedToken}\"", $content);
                } else {
                    $content .= "\nEBAY_REFRESH_TOKEN=\"{$escapedToken}\"";
                }
                \Illuminate\Support\Facades\File::put($envPath, $content);
            }
        } catch (\Throwable $e) {
            // Silently ignore .env write errors
        }
    }

    /**
     * Determine eBay Base URL based on environment or Client ID.
     */
    public function getBaseUrl(): string
    {
        $apiUrl = env('EBAY_API_URL');
        if (!empty($apiUrl)) {
            return rtrim($apiUrl, '/');
        }
        $clientId = $this->getClientId();
        if (str_contains($clientId, '-SBX-') || str_starts_with($clientId, 'SBX-')) {
            return 'https://api.sandbox.ebay.com';
        }
        $env = env('EBAY_ENV');
        if (!empty($env)) {
            return strtolower($env) === 'production' ? 'https://api.ebay.com' : 'https://api.sandbox.ebay.com';
        }
        return 'https://api.ebay.com';
    }

    /**
     * Check current authorization status and return diagnosis.
     */
    public function checkAuthorizationStatus(): array
    {
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $refreshToken = $this->getRefreshToken();
        $isSandbox = str_contains($this->getBaseUrl(), 'sandbox');

        if (empty($clientId) || empty($clientSecret)) {
            return [
                'authorized' => false,
                'status' => 'missing_app_credentials',
                'message' => 'eBay Client ID or Client Secret is missing in .env configuration.',
                'environment' => $isSandbox ? 'Sandbox' : 'Production',
                'required_action' => 'Set EBAY_CLIENT_ID and EBAY_CLIENT_SECRET in your .env file.',
            ];
        }

        if (empty($refreshToken)) {
            return [
                'authorized' => false,
                'status' => 'missing_seller_refresh_token',
                'message' => 'Seller OAuth refresh token (EBAY_REFRESH_TOKEN) is not configured. Public client credentials cannot access seller orders via eBay Sell Fulfillment API.',
                'environment' => $isSandbox ? 'Sandbox' : 'Production',
                'required_action' => 'Authorize the seller account by completing the eBay OAuth flow via ' . url('/ebay/login') . ' or generate a User Refresh Token in eBay Developer Portal with scope https://api.ebay.com/oauth/api_scope/sell.fulfillment and save it as EBAY_REFRESH_TOKEN in .env.',
            ];
        }

        // Test refresh token exchange
        try {
            $token = $this->getUserAccessToken();
            return [
                'authorized' => true,
                'status' => 'authorized',
                'message' => 'Seller account is authorized and access token was successfully generated.',
                'environment' => $isSandbox ? 'Sandbox' : 'Production',
                'token_preview' => substr($token, 0, 10) . '...',
            ];
        } catch (\Throwable $e) {
            return [
                'authorized' => false,
                'status' => 'token_refresh_failed',
                'message' => 'Failed to refresh eBay seller access token: ' . $e->getMessage(),
                'environment' => $isSandbox ? 'Sandbox' : 'Production',
                'required_action' => 'Reconnect your eBay seller account via ' . url('/ebay/login') . ' to obtain a new refresh token.',
            ];
        }
    }

    /**
     * Obtain a valid User Access Token using the Seller Refresh Token.
     *
     * @throws \Exception
     */
    public function getUserAccessToken(): string
    {
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $refreshToken = $this->getRefreshToken();

        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception('eBay Client ID or Client Secret is not configured in .env.');
        }

        if (empty($refreshToken)) {
            throw new \Exception(
                'eBay Sell Fulfillment API requires seller OAuth authorization. ' .
                'EBAY_REFRESH_TOKEN is not configured in .env or session. ' .
                'Please authorize your seller account via ' . url('/ebay/login') . ' or configure EBAY_REFRESH_TOKEN in .env.'
            );
        }

        $tokenUrl = env('EBAY_OAUTH_URL') ?: ($this->getBaseUrl() . '/identity/v1/oauth2/token');

        // Attempt 1: Refresh without scope (RFC 6749 standard - issues token with all originally granted scopes)
        $response = Http::asForm()
            ->timeout(15)
            ->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ])
            ->post($tokenUrl, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        // Attempt 2: If rejected, fallback to explicit scopes
        if (!$response->successful()) {
            $response = Http::asForm()
                ->timeout(15)
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                ])
                ->post($tokenUrl, [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'scope' => implode(' ', [
                        'https://api.ebay.com/oauth/api_scope',
                        'https://api.ebay.com/oauth/api_scope/sell.inventory',
                        'https://api.ebay.com/oauth/api_scope/sell.account',
                        'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                    ]),
                ]);
        }

        if (!$response->successful()) {
            $errorDesc = $response->json('error_description') ?? $response->body();
            throw new \Exception('Failed to exchange eBay refresh token: ' . $errorDesc);
        }

        $accessToken = $response->json('access_token');
        if (empty($accessToken)) {
            throw new \Exception('eBay token endpoint returned success but no access_token was present.');
        }

        return $accessToken;
    }

    /**
     * Fetch orders from eBay Sell Fulfillment API with pagination.
     *
     * @param string $accessToken
     * @param int $days Number of past days to fetch (0 for all/default filter)
     * @param int $limit Max items per page (1 to 200)
     * @param int $maxPages Maximum pages to retrieve for safety
     * @return array
     * @throws \Exception
     */
    /**
     * Query Trading API GetUser to identify the actual authenticated eBay UserID.
     *
     * @param string $accessToken
     * @return array|null Returns ['userId' => string, 'email' => string, 'status' => string] or null on failure
     */
    public function fetchAuthenticatedUser(string $accessToken): ?array
    {
        try {
            $endpoint = rtrim($this->getBaseUrl(), '/') . '/ws/api.dll';
            $xml = '<?xml version="1.0" encoding="utf-8"?>'
                . '<GetUserRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
                . '  <DetailLevel>ReturnSummary</DetailLevel>'
                . '</GetUserRequest>';

            $response = Http::timeout(15)->withHeaders([
                'X-EBAY-API-CALL-NAME' => 'GetUser',
                'X-EBAY-API-SITEID' => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                'X-EBAY-API-IAF-TOKEN' => $accessToken,
            ])->withBody($xml, 'text/xml')->post($endpoint);

            if ($response->successful()) {
                $xmlObj = @simplexml_load_string($response->body());
                if ($xmlObj && (string)($xmlObj->Ack ?? '') === 'Success' && !empty($xmlObj->User->UserID)) {
                    return [
                        'userId' => (string) $xmlObj->User->UserID,
                        'email'  => (string) ($xmlObj->User->Email ?? ''),
                        'status' => (string) ($xmlObj->User->Status ?? ''),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Trading API GetUser fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get authenticated eBay seller username verified from Trading API GetUser.
     * Uses a token fingerprint for cache association.
     * Never falls back to configured EBAY_SELLER_USERNAME if verification fails.
     */
    public function getAuthenticatedUsername(string $accessToken): ?string
    {
        $fingerprint = hash('sha256', $accessToken);
        $cacheKey = 'ebay_user_fp_' . $fingerprint;

        $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if (!empty($cached)) {
            return $cached;
        }

        $userInfo = $this->fetchAuthenticatedUser($accessToken);
        if ($userInfo && !empty($userInfo['userId'])) {
            \Illuminate\Support\Facades\Cache::put($cacheKey, $userInfo['userId'], 3600);
            return $userInfo['userId'];
        }

        return null;
    }

    /**
     * Fetch a single order by orderId from eBay Sell Fulfillment API or Trading API fallback.
     *
     * @param string $accessToken
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function fetchSingleOrderFromApi(string $accessToken, string $orderId): array
    {
        $baseUrl = $this->getBaseUrl();
        $cleanOrderId = trim($orderId);

        // 1. Try Sell Fulfillment API: GET /sell/fulfillment/v1/order/{orderId}
        Log::info("Fetching single eBay Sell Fulfillment order: {$cleanOrderId}", [
            'endpoint' => $baseUrl . '/sell/fulfillment/v1/order/' . urlencode($cleanOrderId),
        ]);

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(30)
            ->get($baseUrl . '/sell/fulfillment/v1/order/' . urlencode($cleanOrderId));

        if ($response->successful()) {
            return $response->json();
        }

        // 2. Try Sell Fulfillment API: GET /sell/fulfillment/v1/order?orderIds={orderId}
        $queryResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(30)
            ->get($baseUrl . '/sell/fulfillment/v1/order', [
                'orderIds' => $cleanOrderId,
            ]);

        if ($queryResponse->successful()) {
            $orders = $queryResponse->json('orders') ?? [];
            if (!empty($orders)) {
                return $orders[0];
            }
        }

        // 3. Fallback: Try Trading API GetOrders (handles hyphenated and legacy order IDs)
        Log::info("Sell Fulfillment API could not locate order {$cleanOrderId}. Attempting Trading API GetOrders fallback...");
        $tradingOrder = $this->fetchOrderFromTradingApi($accessToken, $cleanOrderId);
        if ($tradingOrder !== null) {
            Log::info("Successfully fetched order {$cleanOrderId} via Trading API GetOrders fallback.");
            return $tradingOrder;
        }

        $body = $response->json() ?? $response->body();
        throw new \Exception("eBay order '{$cleanOrderId}' could not be retrieved from Sell Fulfillment API or Trading API: " . json_encode($body));
    }

    /**
     * Fetch a single order using eBay Trading API GetOrders.
     */
    public function fetchOrderFromTradingApi(string $userToken, string $orderId): ?array
    {
        $baseUrl = $this->getBaseUrl();
        $isSandbox = str_contains($baseUrl, 'sandbox');
        $endpoint = $isSandbox ? 'https://api.sandbox.ebay.com/ws/api.dll' : 'https://api.ebay.com/ws/api.dll';

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
            . '  <OrderIDArray>'
            . '    <OrderID>' . htmlspecialchars($orderId, ENT_XML1) . '</OrderID>'
            . '  </OrderIDArray>'
            . '  <DetailLevel>ReturnAll</DetailLevel>'
            . '</GetOrdersRequest>';

        try {
            $response = Http::timeout(25)->withHeaders([
                'X-EBAY-API-CALL-NAME' => 'GetOrders',
                'X-EBAY-API-SITEID' => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                'X-EBAY-API-IAF-TOKEN' => $userToken,
            ])->withBody($xmlBody, 'text/xml')->post($endpoint);

            if ($response->successful()) {
                $xml = @simplexml_load_string($response->body());
                if ($xml && isset($xml->OrderArray->Order) && count($xml->OrderArray->Order) > 0) {
                    return $this->normalizeTradingOrderToFulfillmentOrder($xml->OrderArray->Order[0]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Trading API GetOrders error for order {$orderId}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch recent seller orders using Trading API GetOrders.
     */
    public function fetchOrdersFromTradingApi(string $userToken, int $days = 30): array
    {
        $baseUrl = $this->getBaseUrl();
        $isSandbox = str_contains($baseUrl, 'sandbox');
        $endpoint = $isSandbox ? 'https://api.sandbox.ebay.com/ws/api.dll' : 'https://api.ebay.com/ws/api.dll';

        $boundedDays = min(max(1, $days), 30);
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
            . '  <NumberOfDays>' . $boundedDays . '</NumberOfDays>'
            . '  <OrderRole>Seller</OrderRole>'
            . '  <DetailLevel>ReturnAll</DetailLevel>'
            . '</GetOrdersRequest>';

        try {
            $response = Http::timeout(30)->withHeaders([
                'X-EBAY-API-CALL-NAME' => 'GetOrders',
                'X-EBAY-API-SITEID' => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                'X-EBAY-API-IAF-TOKEN' => $userToken,
            ])->withBody($xmlBody, 'text/xml')->post($endpoint);

            if ($response->successful()) {
                $xml = @simplexml_load_string($response->body());
                if ($xml && isset($xml->OrderArray->Order)) {
                    $orders = [];
                    foreach ($xml->OrderArray->Order as $orderXml) {
                        $orders[] = $this->normalizeTradingOrderToFulfillmentOrder($orderXml);
                    }
                    return $orders;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Trading API GetOrders batch error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Normalize eBay Trading API XML Order to Fulfillment API format.
     */
    public function normalizeTradingOrderToFulfillmentOrder(\SimpleXMLElement $xmlOrder): array
    {
        $orderId = (string) $xmlOrder->OrderID;
        $orderStatus = (string) ($xmlOrder->OrderStatus ?? 'Completed');
        $cancelStatus = (string) ($xmlOrder->CancelStatus ?? 'None');
        $isCancelled = in_array(strtolower($cancelStatus), ['cancelcomplete', 'cancelpending', 'canceled', 'cancelled'], true)
            || strtolower($orderStatus) === 'cancelled';

        $totalAmount = (float) ($xmlOrder->AmountPaid ?? $xmlOrder->Total ?? 0);
        $subtotal = (float) ($xmlOrder->Subtotal ?? $totalAmount);
        $buyerUserId = (string) ($xmlOrder->BuyerUserID ?? '');
        $createdTime = (string) ($xmlOrder->CreatedTime ?? now()->toIso8601String());

        $lineItems = [];
        if (isset($xmlOrder->TransactionArray->Transaction)) {
            foreach ($xmlOrder->TransactionArray->Transaction as $tx) {
                $lineItemId = (string) ($tx->OrderLineItemID ?? $tx->TransactionID ?? uniqid());
                $legacyItemId = (string) ($tx->Item->ItemID ?? '');
                $sku = (string) ($tx->Item->SKU ?? $tx->Variation->SKU ?? '');
                $title = (string) ($tx->Item->Title ?? 'eBay Product');
                $qty = (int) ($tx->QuantityPurchased ?? 1);
                $price = (float) ($tx->TransactionPrice ?? 0);

                $lineItems[] = [
                    'lineItemId' => $lineItemId,
                    'legacyItemId' => $legacyItemId,
                    'sku' => $sku,
                    'title' => $title,
                    'quantity' => $qty,
                    'lineItemCost' => ['value' => (string) $price],
                    'total' => ['value' => (string) ($price * $qty)],
                ];
            }
        }

        return [
            'orderId' => $orderId,
            'orderFulfillmentStatus' => $orderStatus === 'Completed' ? 'FULFILLED' : 'NOT_STARTED',
            'orderPaymentStatus' => (string) ($xmlOrder->PaidTime ?? '') ? 'PAID' : 'PENDING',
            'cancelStatus' => [
                'cancelState' => $isCancelled ? 'CANCELED' : 'NONE_REQUESTED',
            ],
            'pricingSummary' => [
                'total' => ['value' => (string) $totalAmount, 'currency' => 'USD'],
                'priceSubtotal' => ['value' => (string) $subtotal],
            ],
            'buyer' => [
                'username' => $buyerUserId,
            ],
            'creationDate' => $createdTime,
            'lineItems' => $lineItems,
        ];
    }

    /**
     * Fetch orders from eBay Sell Fulfillment API with pagination and date filters.
     *
     * @param string $accessToken
     * @param int $days Number of past days to fetch (1 to 90)
     * @param int $limit Max items per page (1 to 200)
     * @param int $maxPages Maximum pages to retrieve for safety
     * @return array
     * @throws \Exception
     */
    public function fetchOrdersFromApi(string $accessToken, int $days = 30, int $limit = 50, int $maxPages = 10): array
    {
        // eBay requires ISO 8601 with milliseconds in UTC: YYYY-MM-DDTHH:mm:ss.SSSZ
        // Max date range supported by eBay Sell Fulfillment API is 90 days.
        $filter = null;
        if ($days > 0) {
            $boundedDays = min(max(1, $days), 90);
            $startDate = Carbon::now('UTC')->subDays($boundedDays)->format('Y-m-d\TH:i:s.000\Z');
            $endDate = Carbon::now('UTC')->format('Y-m-d\TH:i:s.000\Z');
            $filter = "creationdate:[{$startDate}..{$endDate}]";
        }

        $orders = $this->fetchPagedOrders($accessToken, $filter, $limit, $maxPages);

        // Fallback 1: If 0 orders were returned with creationdate filter, try lastmodifieddate
        if (empty($orders) && $days > 0) {
            $boundedDays = min(max(1, $days), 90);
            $startDate = Carbon::now('UTC')->subDays($boundedDays)->format('Y-m-d\TH:i:s.000\Z');
            $endDate = Carbon::now('UTC')->format('Y-m-d\TH:i:s.000\Z');
            $modifiedFilter = "lastmodifieddate:[{$startDate}..{$endDate}]";
            Log::info("0 orders with creationdate filter. Attempting lastmodifieddate filter: {$modifiedFilter}");
            $orders = $this->fetchPagedOrders($accessToken, $modifiedFilter, $limit, $maxPages);
        }

        // Fallback 2: If still 0 orders, try without filter (returns latest orders)
        if (empty($orders)) {
            Log::info("Attempting order fetch without filter to check latest orders.");
            $orders = $this->fetchPagedOrders($accessToken, null, $limit, $maxPages);
        }

        // Fallback 3: If still 0 orders, query Trading API GetOrders
        if (empty($orders)) {
            Log::info("Attempting Trading API GetOrders fallback for past {$days} days...");
            $orders = $this->fetchOrdersFromTradingApi($accessToken, $days);
        }

        return $orders;
    }

    /**
     * Helper to execute paginated GET requests against /sell/fulfillment/v1/order.
     */
    private function fetchPagedOrders(string $accessToken, ?string $filter, int $limit, int $maxPages): array
    {
        $baseUrl = $this->getBaseUrl();
        $allOrders = [];
        $offset = 0;
        $page = 1;

        do {
            $queryParams = [
                'limit' => min(max(1, $limit), 200),
                'offset' => $offset,
            ];

            if (!empty($filter)) {
                $queryParams['filter'] = $filter;
            }

            Log::info("Fetching eBay Sell Fulfillment orders (Page {$page}, offset {$offset})...", [
                'endpoint' => $baseUrl . '/sell/fulfillment/v1/order',
                'params' => $queryParams,
            ]);

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(30)
                ->get($baseUrl . '/sell/fulfillment/v1/order', $queryParams);

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->json() ?? $response->body();
                Log::error("eBay Sell Fulfillment API Error [HTTP {$status}]", [
                    'status' => $status,
                    'body' => $body,
                    'offset' => $offset,
                ]);

                if ($status === 403) {
                    throw new \Exception("eBay Sell Fulfillment API Access Denied (HTTP 403). The authorized user account lacks 'sell.fulfillment' scope or is not authorized as a seller.");
                }

                throw new \Exception("eBay Sell Fulfillment API request failed [HTTP {$status}]: " . json_encode($body));
            }

            $data = $response->json();
            $orders = $data['orders'] ?? [];
            $total = (int) ($data['total'] ?? count($orders));
            $orderCount = count($orders);

            Log::info("Received {$orderCount} orders on page {$page} (Total matching: {$total})");

            foreach ($orders as $order) {
                $allOrders[] = $order;
            }

            $hasNext = !empty($data['next']) || (($offset + $limit) < $total);
            $offset += $limit;
            $page++;

            if ($page > $maxPages) {
                Log::warning("Reached maximum page limit ({$maxPages}) while fetching eBay orders. Stopping pagination.");
                break;
            }

        } while ($hasNext && $orderCount > 0);

        return $allOrders;
    }

    /**
     * Find local Inventory item corresponding to an eBay order line item.
     * Checks legacyItemId (Item ID), formatted barcodes, and SKU.
     */
    public function findInventoryItem(array $lineItem): ?Inventory
    {
        $legacyItemId = trim((string) ($lineItem['legacyItemId'] ?? ''));
        $sku = trim((string) ($lineItem['sku'] ?? ''));

        // 1. Try matching legacyItemId directly if present
        if ($legacyItemId !== '') {
            // Direct exact match
            $item = Inventory::where('barcode', $legacyItemId)->first();
            if ($item) {
                return $item;
            }

            // REST format exact match (v1|{legacyItemId}|0)
            $item = Inventory::where('barcode', 'v1|' . $legacyItemId . '|0')->first();
            if ($item) {
                return $item;
            }

            // Barcode containing legacyItemId surrounded by pipes
            $item = Inventory::where('barcode', 'LIKE', '%|' . $legacyItemId . '|%')
                ->orWhere('barcode', 'LIKE', 'v1|' . $legacyItemId . '%')
                ->orWhere('barcode', 'LIKE', '%' . $legacyItemId)
                ->first();
            if ($item) {
                return $item;
            }
        }

        // 2. Try matching SKU if present
        if ($sku !== '') {
            $item = Inventory::where('barcode', $sku)
                ->orWhere('model', $sku)
                ->first();
            if ($item) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Check whether an eBay order is cancelled or refunded.
     */
    public function isOrderCancelledOrRefunded(array $order): bool
    {
        $cancelStatus = $order['cancelStatus'] ?? [];
        $cancelState = strtoupper((string) ($cancelStatus['cancelState'] ?? ''));
        if (in_array($cancelState, ['CANCELED', 'CANCELLED'], true)) {
            return true;
        }

        $paymentStatus = strtoupper((string) ($order['orderPaymentStatus'] ?? ''));
        if (in_array($paymentStatus, ['REFUNDED', 'FAILED'], true)) {
            return true;
        }

        // Check line items if all are refunded or cancelled
        $lineItems = $order['lineItems'] ?? [];
        if (!empty($lineItems)) {
            $allRefunded = true;
            foreach ($lineItems as $li) {
                $status = strtoupper((string) ($li['lineItemFulfillmentStatus'] ?? ''));
                $hasRefunds = !empty($li['refunds']);
                if (!$hasRefunds && $status !== 'CANCELED' && $status !== 'CANCELLED') {
                    $allRefunded = false;
                    break;
                }
            }
            if ($allRefunded) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process all fetched orders and update local inventory safely.
     *
     * @param array $orders Array of eBay order objects
     * @param bool $dryRun If true, preview changes without touching database
     * @return array Summary of processed items
     */
    public function processOrders(array $orders, bool $dryRun = false): array
    {
        $summary = [
            'total_fetched' => count($orders),
            'processed' => 0,
            'deducted' => 0,
            'restored' => 0,
            'already_synced' => 0,
            'unmatched' => 0,
            'cancelled' => 0,
            'errors' => 0,
            'details' => [],
        ];

        foreach ($orders as $order) {
            $ebayOrderId = (string) ($order['orderId'] ?? '');
            if (empty($ebayOrderId)) {
                continue;
            }

            try {
                $result = $this->processSingleOrder($order, $dryRun);
                $summary['details'][] = $result;

                switch ($result['action']) {
                    case 'deducted':
                        $summary['deducted']++;
                        $summary['processed']++;
                        break;
                    case 'restored':
                        $summary['restored']++;
                        $summary['processed']++;
                        break;
                    case 'skipped_already_synced':
                        $summary['already_synced']++;
                        break;
                    case 'skipped_cancelled':
                        $summary['cancelled']++;
                        break;
                    case 'unmatched':
                        $summary['unmatched']++;
                        break;
                    default:
                        $summary['processed']++;
                        break;
                }
            } catch (\Throwable $e) {
                $summary['errors']++;
                Log::error("Error processing eBay order #{$ebayOrderId}: " . $e->getMessage(), [
                    'orderId' => $ebayOrderId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $summary['details'][] = [
                    'orderId' => $ebayOrderId,
                    'action' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $summary;
    }

    /**
     * Process a single eBay order inside a database transaction.
     */
    public function processSingleOrder(array $order, bool $dryRun = false): array
    {
        $ebayOrderId = (string) ($order['orderId'] ?? '');
        $fulfillmentStatus = (string) ($order['orderFulfillmentStatus'] ?? 'NOT_STARTED');
        $paymentStatus = (string) ($order['orderPaymentStatus'] ?? 'PENDING');
        $cancelStatus = $order['cancelStatus'] ?? [];
        $cancelState = (string) ($cancelStatus['cancelState'] ?? 'NONE_REQUESTED');
        $totalAmount = (float) ($order['pricingSummary']['total']['value'] ?? 0);
        $currency = (string) ($order['pricingSummary']['total']['currency'] ?? 'USD');
        $buyerUsername = (string) ($order['buyer']['username'] ?? null);
        $ebayCreatedAt = !empty($order['creationDate']) ? Carbon::parse($order['creationDate']) : null;
        $ebayModifiedAt = !empty($order['lastModifiedDate']) ? Carbon::parse($order['lastModifiedDate']) : null;

        $isCancelled = $this->isOrderCancelledOrRefunded($order);

        // In dry run, simulate without database lock
        if ($dryRun) {
            return $this->simulateOrderProcessing($order, $isCancelled);
        }

        return DB::transaction(function () use (
            $order,
            $ebayOrderId,
            $fulfillmentStatus,
            $paymentStatus,
            $cancelState,
            $totalAmount,
            $currency,
            $buyerUsername,
            $ebayCreatedAt,
            $ebayModifiedAt,
            $isCancelled
        ) {
            /** @var EbayOrder $ebayOrder */
            $ebayOrder = EbayOrder::firstOrNew(['ebay_order_id' => $ebayOrderId]);

            // CASE 1: Order was already processed and deducted previously
            if ($ebayOrder->exists && $ebayOrder->inventory_deducted) {
                // Check if the order is now cancelled or refunded
                if ($isCancelled && !$ebayOrder->inventory_restored) {
                    return $this->restoreOrderInventory($ebayOrder, $order, $cancelState, $paymentStatus);
                }

                // If cancelled and already restored
                if ($isCancelled && $ebayOrder->inventory_restored) {
                    return [
                        'orderId' => $ebayOrderId,
                        'action' => 'skipped_already_synced',
                        'message' => 'Order was cancelled and inventory was already restored.',
                    ];
                }

                // Still active and already deducted: prevent duplicate deduction!
                return [
                    'orderId' => $ebayOrderId,
                    'action' => 'skipped_already_synced',
                    'message' => 'Order already processed and inventory already deducted. Skipped to prevent duplicate deduction.',
                ];
            }

            // CASE 2: Order is cancelled or refunded before we ever deducted inventory
            if ($isCancelled) {
                $ebayOrder->order_fulfillment_status = $fulfillmentStatus;
                $ebayOrder->order_payment_status = $paymentStatus;
                $ebayOrder->cancel_state = $cancelState;
                $ebayOrder->total_amount = $totalAmount;
                $ebayOrder->currency = $currency;
                $ebayOrder->buyer_username = $buyerUsername;
                $ebayOrder->ebay_created_at = $ebayCreatedAt;
                $ebayOrder->ebay_modified_at = $ebayModifiedAt;
                $ebayOrder->inventory_deducted = false;
                $ebayOrder->inventory_restored = false;
                $ebayOrder->sync_status = 'cancelled';
                $ebayOrder->sync_notes = 'Order is cancelled/refunded. No inventory deduction made.';
                $ebayOrder->raw_data = $order;
                $ebayOrder->save();

                return [
                    'orderId' => $ebayOrderId,
                    'action' => 'skipped_cancelled',
                    'message' => 'Order is cancelled or refunded on eBay. Inventory not deducted.',
                ];
            }

            // CASE 3: Active/paid order needs inventory deduction
            $lineItems = $order['lineItems'] ?? [];
            if (empty($lineItems)) {
                $ebayOrder->sync_status = 'no_line_items';
                $ebayOrder->raw_data = $order;
                $ebayOrder->save();

                return [
                    'orderId' => $ebayOrderId,
                    'action' => 'skipped_empty',
                    'message' => 'No line items present in eBay order payload.',
                ];
            }

            $deductedLineItems = [];
            $unmatchedLineItems = [];
            $matchedInventoryMap = [];

            foreach ($lineItems as $lineItem) {
                $lineItemId = (string) ($lineItem['lineItemId'] ?? uniqid());
                $legacyItemId = (string) ($lineItem['legacyItemId'] ?? '');
                $sku = (string) ($lineItem['sku'] ?? '');
                $title = (string) ($lineItem['title'] ?? 'eBay Product');
                $quantityPurchased = max(1, (int) ($lineItem['quantity'] ?? 1));
                $unitPrice = isset($lineItem['lineItemCost']['value']) ? (float) $lineItem['lineItemCost']['value'] : null;
                $totalPrice = isset($lineItem['total']['value']) ? (float) $lineItem['total']['value'] : ($unitPrice ? $unitPrice * $quantityPurchased : null);

                $inventory = $this->findInventoryItem($lineItem);

                if ($inventory) {
                    // Lock the inventory row for update
                    $inventory = Inventory::lockForUpdate()->find($inventory->id);

                    $beforeQty = (int) $inventory->quantity;
                    $newQty = max(0, $beforeQty - $quantityPurchased);
                    $inventory->quantity = $newQty;

                    // Compute available quantity: quantity - reuse_quantity
                    $availableQty = max(0, $newQty - (int) ($inventory->reuse_quantity ?? 0));

                    // Rule: Mark products as Sold only when available quantity reaches zero
                    if ($availableQty === 0) {
                        $inventory->status = 'Sold';
                    } elseif ($inventory->status === 'Sold') {
                        $inventory->status = 'In Stock';
                    }

                    $inventory->save();

                    EbayOrderLineItem::updateOrCreate(
                        [
                            'ebay_order_id' => $ebayOrderId,
                            'line_item_id' => $lineItemId,
                        ],
                        [
                            'legacy_item_id' => $legacyItemId,
                            'sku' => $sku,
                            'title' => $title,
                            'quantity_purchased' => $quantityPurchased,
                            'quantity_deducted' => $quantityPurchased,
                            'quantity_restored' => 0,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice,
                            'inventory_id' => $inventory->id,
                            'status' => 'deducted',
                        ]
                    );

                    $deductedLineItems[] = [
                        'line_item_id' => $lineItemId,
                        'legacy_item_id' => $legacyItemId,
                        'inventory_id' => $inventory->id,
                        'name' => $inventory->name,
                        'quantity_deducted' => $quantityPurchased,
                        'quantity_before' => $beforeQty,
                        'quantity_after' => $newQty,
                        'available_after' => $availableQty,
                        'status' => $inventory->status,
                    ];

                    $matchedInventoryMap[$lineItemId] = $inventory;
                } else {
                    // Record unmatched item
                    EbayOrderLineItem::updateOrCreate(
                        [
                            'ebay_order_id' => $ebayOrderId,
                            'line_item_id' => $lineItemId,
                        ],
                        [
                            'legacy_item_id' => $legacyItemId,
                            'sku' => $sku,
                            'title' => $title,
                            'quantity_purchased' => $quantityPurchased,
                            'quantity_deducted' => 0,
                            'quantity_restored' => 0,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice,
                            'inventory_id' => null,
                            'status' => 'not_found',
                        ]
                    );

                    $unmatchedLineItems[] = [
                        'line_item_id' => $lineItemId,
                        'legacy_item_id' => $legacyItemId,
                        'sku' => $sku,
                        'title' => $title,
                        'quantity' => $quantityPurchased,
                    ];

                    Log::warning("eBay Order #{$ebayOrderId}: No matching local inventory found for Item ID '{$legacyItemId}' / SKU '{$sku}'");
                }
            }

            // Sync with local Order table if line items were matched
            $localOrderId = null;
            if (!empty($deductedLineItems)) {
                $localOrderId = $this->createOrUpdateLocalOrder($order, $ebayOrderId, $matchedInventoryMap);
            }

            // Determine sync status
            $syncStatus = 'processed';
            if (empty($deductedLineItems)) {
                $syncStatus = 'unmatched';
            } elseif (!empty($unmatchedLineItems)) {
                $syncStatus = 'partially_matched';
            }

            $ebayOrder->order_fulfillment_status = $fulfillmentStatus;
            $ebayOrder->order_payment_status = $paymentStatus;
            $ebayOrder->cancel_state = $cancelState;
            $ebayOrder->total_amount = $totalAmount;
            $ebayOrder->currency = $currency;
            $ebayOrder->buyer_username = $buyerUsername;
            $ebayOrder->ebay_created_at = $ebayCreatedAt;
            $ebayOrder->ebay_modified_at = $ebayModifiedAt;
            $ebayOrder->inventory_deducted = !empty($deductedLineItems);
            $ebayOrder->inventory_deducted_at = !empty($deductedLineItems) ? now() : null;
            $ebayOrder->local_order_id = $localOrderId;
            $ebayOrder->sync_status = $syncStatus;
            $ebayOrder->sync_notes = !empty($unmatchedLineItems)
                ? 'Unmatched items: ' . json_encode($unmatchedLineItems)
                : 'Inventory successfully deducted';
            $ebayOrder->raw_data = $order;
            $ebayOrder->save();

            Log::info("eBay Order #{$ebayOrderId} processed successfully. Deducted: " . count($deductedLineItems) . ", Unmatched: " . count($unmatchedLineItems));

            return [
                'orderId' => $ebayOrderId,
                'action' => !empty($deductedLineItems) ? 'deducted' : 'unmatched',
                'deducted_items' => $deductedLineItems,
                'unmatched_items' => $unmatchedLineItems,
                'local_order_id' => $localOrderId,
            ];
        });
    }

    /**
     * Restore inventory for a cancelled or refunded order.
     */
    private function restoreOrderInventory(EbayOrder $ebayOrder, array $order, string $cancelState, string $paymentStatus): array
    {
        $restoredItems = [];
        $lineItems = $ebayOrder->lineItems()->where('quantity_deducted', '>', 0)->get();

        foreach ($lineItems as $lineItem) {
            if ($lineItem->inventory_id) {
                $inventory = Inventory::lockForUpdate()->find($lineItem->inventory_id);
                if ($inventory) {
                    $beforeQty = (int) $inventory->quantity;
                    $restoredQty = (int) $lineItem->quantity_deducted;
                    $newQty = $beforeQty + $restoredQty;
                    $inventory->quantity = $newQty;

                    // Re-calculate available quantity
                    $availableQty = max(0, $newQty - (int) ($inventory->reuse_quantity ?? 0));
                    if ($availableQty > 0 && $inventory->status === 'Sold') {
                        $inventory->status = 'In Stock';
                    }

                    $inventory->save();

                    $lineItem->quantity_restored = $restoredQty;
                    $lineItem->status = 'restored';
                    $lineItem->save();

                    $restoredItems[] = [
                        'line_item_id' => $lineItem->line_item_id,
                        'inventory_id' => $inventory->id,
                        'name' => $inventory->name,
                        'quantity_restored' => $restoredQty,
                        'quantity_before' => $beforeQty,
                        'quantity_after' => $newQty,
                        'new_status' => $inventory->status,
                    ];
                }
            }
        }

        $ebayOrder->cancel_state = $cancelState;
        $ebayOrder->order_payment_status = $paymentStatus;
        $ebayOrder->inventory_restored = true;
        $ebayOrder->inventory_restored_at = now();
        $ebayOrder->sync_status = 'cancelled_and_restored';
        $ebayOrder->sync_notes = 'Order was cancelled/refunded. Inventory was restored to stock.';
        $ebayOrder->raw_data = $order;
        $ebayOrder->save();

        Log::info("eBay Order #{$ebayOrder->ebay_order_id} cancelled/refunded. Restored items: " . count($restoredItems));

        return [
            'orderId' => $ebayOrder->ebay_order_id,
            'action' => 'restored',
            'restored_items' => $restoredItems,
            'message' => 'Inventory restored due to cancellation or refund.',
        ];
    }

    /**
     * Create or update local Order and OrderItem records for store tracking.
     */
    private function createOrUpdateLocalOrder(array $order, string $ebayOrderId, array $matchedInventoryMap): ?int
    {
        try {
            $orderNumber = 'EBAY-' . $ebayOrderId;
            $pricing = $order['pricingSummary'] ?? [];
            $subtotal = (float) ($pricing['priceSubtotal']['value'] ?? 0);
            $shipping = (float) ($pricing['deliveryCost']['value'] ?? 0);
            $total = (float) ($pricing['total']['value'] ?? ($subtotal + $shipping));
            $buyer = $order['buyer'] ?? [];
            $shippingStep = $order['fulfillmentStartPlans'][0]['shippingStep'] ?? [];
            $shipTo = $shippingStep['shipTo'] ?? [];

            $customerName = trim(($shipTo['fullName'] ?? '') ?: ($buyer['username'] ?? 'eBay Customer'));
            $customerEmail = $buyer['buyerRegistrationAddress']['email'] ?? 'ebay-buyer@order.ebay.com';
            $customerPhone = $shipTo['primaryPhone']['phoneNumber'] ?? null;

            // Build shipping address string
            $address = $shipTo['contactAddress'] ?? [];
            $addressLines = array_filter([
                $address['addressLine1'] ?? null,
                $address['addressLine2'] ?? null,
                $address['city'] ?? null,
                $address['stateOrProvince'] ?? null,
                $address['postalCode'] ?? null,
                $address['countryCode'] ?? null,
            ]);
            $shippingAddress = !empty($addressLines) ? implode(', ', $addressLines) : 'eBay Delivery Address';

            // Find an admin user or first user for local order owner
            $userId = DB::table('users')->where('role', 'admin')->value('id') ?? DB::table('users')->value('id') ?? 1;

            $localOrder = Order::firstOrCreate(
                ['order_number' => $orderNumber],
                [
                    'user_id' => $userId,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                    'status' => Order::STATUS_ONLINE_PURCHASED,
                    'payment_method' => 'ebay',
                    'payment_id' => $ebayOrderId,
                    'payment_status' => Order::PAYMENT_STATUS_PAID,
                    'fulfillment_type' => 'shipping',
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'shipping_address' => $shippingAddress,
                    'notes' => "eBay Sold Order #{$ebayOrderId} (Buyer: " . ($buyer['username'] ?? 'N/A') . ")",
                ]
            );

            // Add order items
            $lineItems = $order['lineItems'] ?? [];
            foreach ($lineItems as $lineItem) {
                $lineItemId = (string) ($lineItem['lineItemId'] ?? '');
                $inventory = $matchedInventoryMap[$lineItemId] ?? null;
                $qty = max(1, (int) ($lineItem['quantity'] ?? 1));
                $unitPrice = (float) ($lineItem['lineItemCost']['value'] ?? 0);
                $totalPrice = (float) ($lineItem['total']['value'] ?? ($unitPrice * $qty));

                OrderItem::firstOrCreate(
                    [
                        'order_id' => $localOrder->id,
                        'product_id' => $inventory ? $inventory->id : 0,
                    ],
                    [
                        'product_name' => $inventory ? $inventory->name : ($lineItem['title'] ?? 'eBay Product'),
                        'product_type' => 'inventory',
                        'device_type' => $inventory?->device_type ?: 'eBay',
                        'location' => $inventory?->put_away_location ?: 'N/A',
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]
                );
            }

            return $localOrder->id;
        } catch (\Throwable $e) {
            Log::warning("Could not create local Order record for eBay order #{$ebayOrderId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Dry run simulation of single order processing.
     */
    private function simulateOrderProcessing(array $order, bool $isCancelled): array
    {
        $ebayOrderId = (string) ($order['orderId'] ?? '');
        $existingOrder = EbayOrder::where('ebay_order_id', $ebayOrderId)->first();

        if ($existingOrder && $existingOrder->inventory_deducted) {
            if ($isCancelled && !$existingOrder->inventory_restored) {
                return [
                    'orderId' => $ebayOrderId,
                    'action' => 'restored',
                    'simulated' => true,
                    'message' => '[Dry Run] Order cancelled on eBay; would restore previously deducted inventory.',
                ];
            }

            return [
                'orderId' => $ebayOrderId,
                'action' => 'skipped_already_synced',
                'simulated' => true,
                'message' => '[Dry Run] Order already processed; would skip deduction.',
            ];
        }

        if ($isCancelled) {
            return [
                'orderId' => $ebayOrderId,
                'action' => 'skipped_cancelled',
                'simulated' => true,
                'message' => '[Dry Run] Order is cancelled/refunded; would not deduct inventory.',
            ];
        }

        $lineItems = $order['lineItems'] ?? [];
        $deductions = [];
        $unmatched = [];

        foreach ($lineItems as $lineItem) {
            $inventory = $this->findInventoryItem($lineItem);
            $qty = max(1, (int) ($lineItem['quantity'] ?? 1));

            if ($inventory) {
                $before = (int) $inventory->quantity;
                $after = max(0, $before - $qty);
                $availableAfter = max(0, $after - (int) ($inventory->reuse_quantity ?? 0));
                $newStatus = $availableAfter === 0 ? 'Sold' : 'In Stock';

                $deductions[] = [
                    'inventory_id' => $inventory->id,
                    'barcode' => $inventory->barcode,
                    'name' => $inventory->name,
                    'qty_deduct' => $qty,
                    'before' => $before,
                    'after' => $after,
                    'available_after' => $availableAfter,
                    'status_after' => $newStatus,
                ];
            } else {
                $unmatched[] = [
                    'legacyItemId' => $lineItem['legacyItemId'] ?? null,
                    'sku' => $lineItem['sku'] ?? null,
                    'title' => $lineItem['title'] ?? null,
                ];
            }
        }

        return [
            'orderId' => $ebayOrderId,
            'action' => !empty($deductions) ? 'deducted' : 'unmatched',
            'simulated' => true,
            'deducted_items' => $deductions,
            'unmatched_items' => $unmatched,
        ];
    }
}
