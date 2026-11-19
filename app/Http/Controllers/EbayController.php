<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Services\Ebay\EbayOrderSyncService;

class EbayController extends Controller
{
    private function getClientId(): string
    {
        return env('EBAY_CLIENT_ID') ?: env('CLIENT_ID', '');
    }

    private function getClientSecret(): string
    {
        return env('EBAY_CLIENT_SECRET') ?: env('CLIENT_SECRET', '');
    }

    private function getRuName(): string
    {
        return env('EBAY_RU_NAME') ?: env('RU_NAME') ?: url('/ebay/callback');
    }

    private function getRefreshToken(): ?string
    {
        return app(EbayOrderSyncService::class)->getRefreshToken();
    }

    private function getBaseUrl(): string
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

    private function getAuthUrl(): string
    {
        $clientId = $this->getClientId();
        if (str_contains($clientId, '-SBX-') || str_starts_with($clientId, 'SBX-')) {
            return 'https://auth.sandbox.ebay.com';
        }
        $env = env('EBAY_ENV');
        if (!empty($env)) {
            return strtolower($env) === 'production' ? 'https://auth.ebay.com' : 'https://auth.sandbox.ebay.com';
        }
        return 'https://auth.ebay.com';
    }

    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!\Illuminate\Support\Facades\File::exists($envPath)) return;

        $content = \Illuminate\Support\Facades\File::get($envPath);
        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }
        \Illuminate\Support\Facades\File::put($envPath, $content);
    }

    private function normalizeImageUrl(?string $url): ?string
    {
        if (!$url) return null;
        if (str_starts_with($url, 'http://')) {
            $url = 'https://' . substr($url, 7);
        }
        return $url;
    }
    
    private function getUserAccessToken(): string
    {
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $refreshToken = $this->getRefreshToken();

        if (!$refreshToken) {
            throw new \Exception('No eBay refresh token configured in .env or session.');
        }

        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ])
            ->post($this->getBaseUrl() . '/identity/v1/oauth2/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'scope' => implode(' ', [
                    'https://api.ebay.com/oauth/api_scope',
                    'https://api.ebay.com/oauth/api_scope/sell.inventory',
                    'https://api.ebay.com/oauth/api_scope/sell.account',
                    'https://api.ebay.com/oauth/api_scope/sell.fulfillment'
                ])
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to obtain access token: ' . ($response->json('error_description') ?? $response->body()));
        }

        return $response->json('access_token');
    }

    /**
     * Fetch products/inventory from eBay API and render view or JSON.
     */
    public function products(Request $request)
    {
        return $this->browse($request);
    }

    private function fetchTradingSellerItems(string $token, string $baseUrl, int $limit, string $query = ''): array
    {
        $items = [];
        try {
            $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
                . '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
                . '  <ActiveList>'
                . '    <Include>true</Include>'
                . '    <Sort>StartTimeNewest</Sort>'
                . '    <Pagination>'
                . '      <EntriesPerPage>' . min($limit, 100) . '</EntriesPerPage>'
                . '      <PageNumber>1</PageNumber>'
                . '    </Pagination>'
                . '  </ActiveList>'
                . '  <DetailLevel>ReturnAll</DetailLevel>'
                . '</GetMyeBaySellingRequest>';

            $endpoint = str_contains($baseUrl, 'sandbox') ? 'https://api.sandbox.ebay.com/ws/api.dll' : 'https://api.ebay.com/ws/api.dll';

            $response = Http::withHeaders([
                'X-EBAY-API-CALL-NAME' => 'GetMyeBaySelling',
                'X-EBAY-API-SITEID' => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                'X-EBAY-API-IAF-TOKEN' => $token,
            ])->withBody($xmlBody, 'text/xml')->post($endpoint);

            if ($response->successful()) {
                $xml = @simplexml_load_string($response->body());
                if ($xml && isset($xml->ActiveList->ItemArray->Item)) {
                    foreach ($xml->ActiveList->ItemArray->Item as $item) {
                        $title = (string) $item->Title;
                        $itemId = (string) $item->ItemID;

                        if (trim($query) !== '' && stripos($title, $query) === false && stripos($itemId, $query) === false) {
                            continue;
                        }

                        $priceVal = (string) ($item->SellingStatus->CurrentPrice ?? $item->BuyItNowPrice ?? 'N/A');
                        $currency = (string) ($item->SellingStatus->CurrentPrice['currencyID'] ?? 'USD');
                        $qty = (int) ($item->QuantityAvailable ?? $item->Quantity ?? 1);
                        $itemUrl = (string) ($item->ListingDetails->ViewItemURL ?? '');

                        $imagesList = [];
                        if (isset($item->PictureDetails->PictureURL)) {
                            foreach ($item->PictureDetails->PictureURL as $pic) {
                                $picUrl = (string) $pic;
                                if ($picUrl) {
                                    $imagesList[] = ['imageUrl' => $this->normalizeImageUrl($picUrl)];
                                }
                            }
                        }

                        $thumb = !empty($imagesList[0]['imageUrl']) ? $imagesList[0]['imageUrl'] : null;

                        $items[] = [
                            'itemId' => $itemId,
                            'legacyItemId' => $itemId,
                            'title' => $title,
                            'price' => ['value' => $priceVal, 'currency' => $currency],
                            'quantity' => $qty,
                            'condition' => (string) ($item->ConditionDisplayName ?? 'N/A'),
                            'itemWebUrl' => $itemUrl,
                            'images' => $imagesList,
                            'thumbnail' => $thumb,
                            'categories' => isset($item->PrimaryCategory->CategoryName) ? [['categoryName' => (string) $item->PrimaryCategory->CategoryName]] : [],
                            'seller' => ['username' => 'My Connected Account'],
                            'itemLocation' => [],
                            'raw' => json_decode(json_encode($item), true),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Trading API GetMyeBaySelling Exception: ' . $e->getMessage());
        }

        return $items;
    }

    /**
     * Browse and search eBay listings with strict priority & filtering for Connected Seller Account products.
     */
    public function browse(Request $request)
    {
        $query = $request->query('q', '');
        $seller = trim($request->query('seller', env('EBAY_SELLER_USERNAME', Session::get('ebay_seller_username', 'systematictrade'))));
        $limit = (int) $request->query('limit', 24);
        if ($limit <= 0) $limit = 24;
        
        $items = [];
        $error = null;
        $token = null;
        $tokenType = null;
        $notice = null;
        $rawResponse = null;
        $accountConnected = false;
        
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $baseUrl = $this->getBaseUrl();
        $refreshToken = $this->getRefreshToken();

        // 1. If refresh token is available, attempt to fetch user's own connected account products
        if (!empty($refreshToken)) {
            try {
                $tokenResponse = Http::asForm()
                    ->withHeaders([
                        'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    ])
                    ->post($baseUrl . '/identity/v1/oauth2/token', [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken,
                        'scope' => implode(' ', [
                            'https://api.ebay.com/oauth/api_scope',
                            'https://api.ebay.com/oauth/api_scope/sell.inventory',
                            'https://api.ebay.com/oauth/api_scope/sell.account',
                            'https://api.ebay.com/oauth/api_scope/sell.fulfillment'
                        ])
                    ]);

                if ($tokenResponse->successful()) {
                    $token = $tokenResponse->json('access_token');
                    $tokenType = 'refresh_token';
                    $accountConnected = true;
                } else {
                    $error = 'eBay User Token Refresh Failed: ' . ($tokenResponse->json('error_description') ?? 'Please reconnect your eBay account.');
                }
            } catch (\Throwable $e) {
                $error = 'Connection Error: ' . $e->getMessage();
            }

            // Fetch seller products using User Access Token (from both Inventory API and Trading API)
            if ($token) {
                try {
                    // Source A: Trading API Active Listings (GetMyeBaySelling)
                    $tradingItems = $this->fetchTradingSellerItems($token, $baseUrl, $limit, $query);
                    foreach ($tradingItems as $tItem) {
                        $items[] = $tItem;
                    }

                    // Source B: REST Inventory API
                    $offerMap = [];
                    $offersResponse = Http::withToken($token)
                        ->acceptJson()
                        ->get($baseUrl . '/sell/inventory/v1/offer', [
                            'limit' => $limit
                        ]);

                    if ($offersResponse->successful()) {
                        $offers = $offersResponse->json('offers') ?? [];
                        foreach ($offers as $off) {
                            $sku = $off['sku'] ?? null;
                            if ($sku) {
                                $priceVal = $off['pricingSummary']['price']['value'] ?? 'N/A';
                                $curr = $off['pricingSummary']['price']['currency'] ?? 'USD';
                                $listingId = $off['listing']['listingId'] ?? null;
                                $offerMap[$sku] = [
                                    'price' => ['value' => $priceVal, 'currency' => $curr],
                                    'listingId' => $listingId,
                                    'status' => $off['status'] ?? 'N/A',
                                    'itemWebUrl' => $listingId ? 'https://www.ebay.com/itm/' . $listingId : null,
                                ];
                            }
                        }
                    }

                    $inventoryResponse = Http::withToken($token)
                        ->acceptJson()
                        ->get($baseUrl . '/sell/inventory/v1/inventory_item', [
                            'limit' => $limit
                        ]);

                    if ($inventoryResponse->successful()) {
                        $rawResponse = $inventoryResponse->json();
                        $inventoryItems = $rawResponse['inventoryItems'] ?? [];

                        foreach ($inventoryItems as $invItem) {
                            $sku = $invItem['sku'] ?? 'N/A';
                            $product = $invItem['product'] ?? [];
                            $title = $product['title'] ?? $sku;
                            
                            if (trim($query) !== '' && stripos($title, $query) === false && stripos($sku, $query) === false) {
                                continue;
                            }

                            $imagesList = [];
                            if (!empty($product['imageUrls'])) {
                                foreach ($product['imageUrls'] as $imgUrl) {
                                    $imagesList[] = ['imageUrl' => $this->normalizeImageUrl($imgUrl)];
                                }
                            }

                            $thumb = !empty($imagesList[0]['imageUrl']) ? $imagesList[0]['imageUrl'] : null;
                            $qty = $invItem['availability']['shipToLocationAvailability']['quantity'] ?? 1;
                            $cond = $invItem['condition'] ?? 'N/A';

                            $mappedPrice = $offerMap[$sku]['price'] ?? ['value' => 'N/A', 'currency' => 'USD'];
                            $mappedUrl = $offerMap[$sku]['itemWebUrl'] ?? null;

                            $items[] = [
                                'itemId' => $sku,
                                'legacyItemId' => $offerMap[$sku]['listingId'] ?? null,
                                'title' => $title,
                                'price' => $mappedPrice,
                                'quantity' => $qty,
                                'condition' => $cond,
                                'itemWebUrl' => $mappedUrl,
                                'images' => $imagesList,
                                'thumbnail' => $thumb,
                                'categories' => [],
                                'seller' => ['username' => $seller ?: 'My Connected Account'],
                                'itemLocation' => [],
                                'raw' => $invItem,
                            ];
                        }
                    }

                    $isSandbox = str_contains($baseUrl, 'sandbox');
                    if (count($items) > 0) {
                        $notice = 'Displaying ' . count($items) . ' seller listing(s) directly from your connected eBay account (' . ($isSandbox ? 'Sandbox' : 'Production') . ').';
                    }
                } catch (\Throwable $e) {
                    $error = 'Seller API Error: ' . $e->getMessage();
                }
            }
        }

        // 2. Fetch via Client Credentials & strict seller username filter if no refresh token or seller products empty
        if (empty($items)) {
            try {
                $tokenResponse = Http::asForm()
                    ->withHeaders([
                        'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    ])
                    ->post($baseUrl . '/identity/v1/oauth2/token', [
                        'grant_type' => 'client_credentials',
                        'scope' => 'https://api.ebay.com/oauth/api_scope',
                    ]);

                if ($tokenResponse->successful()) {
                    $token = $tokenResponse->json('access_token');
                    $tokenType = $tokenType ?: 'client_credentials';

                    $searchTerm = trim($query) !== '' ? $query : 'a';
                    $apiParams = [
                        'q' => $searchTerm,
                        'sort' => 'newly_listed',
                        'limit' => $limit,
                    ];

                    if (!empty($seller)) {
                        $apiParams['filter'] = 'sellers:{' . $seller . '}';
                    }

                    $browseResponse = Http::withToken($token)
                        ->acceptJson()
                        ->get($baseUrl . '/buy/browse/v1/item_summary/search', $apiParams);

                    if ($browseResponse->successful()) {
                        $rawResponse = $browseResponse->json();
                        $summaries = $rawResponse['itemSummaries'] ?? [];

                        foreach ($summaries as $item) {
                            $imagesList = [];
                            $mainImg = $item['image']['imageUrl'] ?? null;
                            if ($mainImg) {
                                $imagesList[] = ['imageUrl' => $this->normalizeImageUrl($mainImg)];
                            }

                            if (!empty($item['thumbnailImages'])) {
                                foreach ($item['thumbnailImages'] as $tImg) {
                                    if (!empty($tImg['imageUrl'])) {
                                        $imagesList[] = ['imageUrl' => $this->normalizeImageUrl($tImg['imageUrl'])];
                                    }
                                }
                            }

                            $thumb = !empty($imagesList[0]['imageUrl']) ? $imagesList[0]['imageUrl'] : $this->normalizeImageUrl($mainImg);

                            $items[] = [
                                'itemId' => $item['itemId'] ?? 'N/A',
                                'legacyItemId' => $item['legacyItemId'] ?? null,
                                'title' => $item['title'] ?? 'Untitled',
                                'price' => $item['price'] ?? ['value' => 'N/A', 'currency' => 'USD'],
                                'condition' => $item['condition'] ?? 'N/A',
                                'itemWebUrl' => $item['itemWebUrl'] ?? null,
                                'images' => $imagesList,
                                'thumbnail' => $thumb,
                                'categories' => $item['categories'] ?? [],
                                'seller' => $item['seller'] ?? [],
                                'shippingOptions' => $item['shippingOptions'] ?? [],
                                'itemLocation' => $item['itemLocation'] ?? [],
                                'raw' => $item,
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                if (!$error) $error = 'Connection Error: ' . $e->getMessage();
            }
        }

        // 3. Strict Seller Filter Enforcement in PHP (never show other sellers' products if seller filter is requested)
        if (!empty($seller) && !$accountConnected) {
            $targetSeller = strtolower(trim($seller));
            $items = array_values(array_filter($items, function($item) use ($targetSeller) {
                $itemSeller = strtolower($item['seller']['username'] ?? '');
                return str_contains($itemSeller, $targetSeller) || $itemSeller === 'my connected account';
            }));

            $isSandbox = str_contains($baseUrl, 'sandbox');
            if (count($items) > 0) {
                $notice = 'Displaying ' . count($items) . ' listing(s) matching seller username "' . $seller . '" (' . ($isSandbox ? 'Sandbox' : 'Production') . ').';
            } else {
                $notice = 'No eBay products found for seller username "' . $seller . '". Please verify your exact eBay seller username or sign in using "Connect My eBay Account".';
            }
        }

        if ($request->wantsJson() || $request->query('format') === 'json' || $request->ajax()) {
            return response()->json([
                'query' => $query,
                'seller' => $seller,
                'token_type' => $tokenType,
                'account_connected' => $accountConnected,
                'notice' => $notice,
                'error' => $error,
                'total_items' => count($items),
                'items' => $items,
                'results' => array_map(function($item) {
                    $priceStr = is_array($item['price']) && is_numeric($item['price']['value'] ?? null) 
                        ? ' ($' . number_format((float)$item['price']['value'], 2) . ')' 
                        : '';
                    return [
                        'id' => $item['itemId'],
                        'text' => $item['title'] . $priceStr,
                        'item' => $item,
                    ];
                }, $items),
                'raw_response' => $rawResponse,
            ]);
        }

        $authenticatedUser = null;
        if ($token) {
            $syncService = app(EbayOrderSyncService::class);
            $authenticatedUser = $syncService->getAuthenticatedUsername($token);
        }

        return view('ebay.api-products', [
            'query' => $query,
            'seller' => $seller,
            'limit' => $limit,
            'items' => $items,
            'tokenType' => $tokenType,
            'accountConnected' => $accountConnected,
            'authenticatedUser' => $authenticatedUser,
            'expectedSeller' => env('EBAY_SELLER_USERNAME', 'systematictrade'),
            'notice' => $notice,
            'error' => $error,
            'rawResponse' => $rawResponse,
        ]);
    }

    /**
     * Redirect to eBay User Authorization page to connect seller account.
     */
    public function login()
    {
        $clientId = $this->getClientId();
        $ruName = $this->getRuName();
        $authUrl = $this->getAuthUrl();

        $scopes = implode(' ', [
            'https://api.ebay.com/oauth/api_scope',
            'https://api.ebay.com/oauth/api_scope/sell.inventory',
            'https://api.ebay.com/oauth/api_scope/sell.account',
            'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
        ]);

        $url = $authUrl . '/oauth2/authorize'
            . '?client_id=' . $clientId
            . '&redirect_uri=' . urlencode($ruName)
            . '&response_type=code'
            . '&prompt=login'
            . '&scope=' . urlencode($scopes);

        return redirect()->away($url);
    }

    /**
     * Handle OAuth Callback from eBay and save refresh_token to connect account.
     * Verifies the actual eBay UserID via Trading API GetUser.
     * Rejects mismatched accounts and preserves existing tokens if verification fails or account differs.
     */
    public function callback(Request $request)
    {
        $code = $request->code;

        if (!$code) {
            return redirect('/ebay/products')->with('error', 'Authorization Code Missing. Existing connection preserved.');
        }

        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $ruName = $this->getRuName();
        $baseUrl = $this->getBaseUrl();

        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)
            ])
            ->post($baseUrl . '/identity/v1/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $ruName
            ]);

        if (!$response->successful()) {
            return redirect('/ebay/products')->with('error', 'eBay Token Exchange Failed: ' . ($response->json('error_description') ?? $response->body()) . '. Existing connection preserved.');
        }

        $tokens = $response->json();
        $newRefreshToken = $tokens['refresh_token'] ?? null;
        $newAccessToken = $tokens['access_token'] ?? null;

        if (!$newRefreshToken || !$newAccessToken) {
            return redirect('/ebay/products')->with('error', 'eBay Token Exchange returned incomplete tokens. Existing connection preserved.');
        }

        // STEP 1: Verify actual authenticated eBay UserID via Trading API GetUser
        $syncService = app(EbayOrderSyncService::class);
        $userInfo = $syncService->fetchAuthenticatedUser($newAccessToken);
        $actualUserId = $userInfo['userId'] ?? null;
        $expectedSeller = env('EBAY_SELLER_USERNAME', 'systematictrade');

        // CASE 1: Verification failed (API error, invalid XML, or missing UserID)
        // Existing tokens are preserved
        if (empty($actualUserId)) {
            return redirect('/ebay/products')->with('error',
                'Account Verification Failed: Could not verify account identity via eBay Trading API GetUser. Existing connection and token preserved.'
            );
        }

        // CASE 2: Account mismatch (e.g. inves-43, disc-3585 instead of systematictrade)
        // Existing tokens are preserved and NOT overwritten
        if (strtolower($actualUserId) !== strtolower($expectedSeller)) {
            return redirect('/ebay/products')->with('warning',
                "Account Mismatch Rejected: You signed in as '{$actualUserId}', but the expected store account is '{$expectedSeller}'. " .
                "The existing token was preserved and NOT overwritten. Please log out of eBay in your browser, click 'Connect My eBay Account', and sign in with '{$expectedSeller}'."
            );
        }

        // CASE 3: Verified match (actualUserId == expectedSeller)
        $syncService->storeRefreshToken($newRefreshToken);
        $fingerprint = hash('sha256', $newAccessToken);
        \Illuminate\Support\Facades\Cache::put('ebay_user_fp_' . $fingerprint, $actualUserId, 3600);
        Session::put('ebay_seller_username', $actualUserId);
        $this->updateEnv(['EBAY_SELLER_USERNAME' => $actualUserId]);

        return redirect('/ebay/products')->with('success', "eBay Seller Account '{$actualUserId}' connected and verified successfully! Active listings loaded.");
    }

    /**
     * Handle OAuth Cancel from eBay and redirect back to products.
     */
    public function cancel(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('eBay OAuth Cancelled', $request->all());

        return redirect('/ebay/products')->with('info', 'eBay account connection was cancelled.');
    }

    /**
     * Check authorization status for eBay Sell Fulfillment API orders access.
     */
    public function ordersStatus(EbayOrderSyncService $syncService)
    {
        $status = $syncService->checkAuthorizationStatus();
        return response()->json($status);
    }

    /**
     * Trigger automatic order & inventory synchronization via Sell Fulfillment API.
     */
    public function syncOrders(Request $request, EbayOrderSyncService $syncService)
    {
        $authStatus = $syncService->checkAuthorizationStatus();
        if (!$authStatus['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $authStatus['message'],
                'auth_status' => $authStatus,
            ], 422);
        }

        try {
            $days = (int) $request->input('days', 30);
            $dryRun = (bool) $request->input('dry_run', false);
            $token = $syncService->getUserAccessToken();
            $orders = $syncService->fetchOrdersFromApi($token, $days);
            $summary = $syncService->processOrders($orders, $dryRun);

            return response()->json([
                'success' => true,
                'message' => "eBay order sync complete. Total: {$summary['total_fetched']}, Deducted: {$summary['deducted']}, Restored: {$summary['restored']}, Skipped: {$summary['already_synced']}",
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('eBay syncOrders error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'eBay order sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}