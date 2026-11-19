<?php

namespace App\Console\Commands;

use App\Services\Ebay\EbayOrderSyncService;
use Illuminate\Console\Command;

class SyncEbayOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ebay:sync-orders 
                            {--days=30 : Number of past days to check for orders (up to 90)} 
                            {--order-id= : Fetch and sync a specific eBay Order ID directly}
                            {--dry-run : Simulate sync without modifying inventory or orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch seller orders via eBay Sell Fulfillment API and automatically sync inventory quantities';

    /**
     * Execute the console command.
     */
    public function handle(EbayOrderSyncService $syncService): int
    {
        $this->info('====================================================');
        $this->info('  eBay Automatic Order & Inventory Synchronization  ');
        $this->info('====================================================');

        $dryRun = (bool) $this->option('dry-run');
        $days = (int) $this->option('days');
        $orderId = trim((string) $this->option('order-id'));

        if ($dryRun) {
            $this->warn('DRY-RUN MODE ACTIVE: No inventory quantities or database records will be modified.');
        }

        // 1. Check authorization status
        $this->line('Checking eBay credentials and seller authorization...');
        $authStatus = $syncService->checkAuthorizationStatus();

        if (!$authStatus['authorized']) {
            $this->error('eBay Seller Authorization Error: ' . $authStatus['message']);
            $this->newLine();
            $this->line('<comment>Status:</comment> ' . $authStatus['status']);
            $this->line('<comment>Environment:</comment> ' . $authStatus['environment']);
            $this->line('<comment>Required Action:</comment> ' . ($authStatus['required_action'] ?? 'N/A'));
            $this->newLine();
            $this->line('The eBay Sell Fulfillment API requires a Seller OAuth User Token with scope:');
            $this->line('  - https://api.ebay.com/oauth/api_scope/sell.fulfillment');
            $this->line('Once connected via ' . url('/ebay/login') . ' or configured in .env as EBAY_REFRESH_TOKEN,');
            $this->line('automatic inventory deduction will run smoothly every 5 minutes.');
            $this->newLine();

            return Command::FAILURE;
        }

        $this->info('Seller account is authorized (' . $authStatus['environment'] . ').');

        // 2. Obtain User Access Token
        try {
            $accessToken = $syncService->getUserAccessToken();
            $username = $syncService->getAuthenticatedUsername($accessToken);
            if ($username) {
                $this->line("<comment>Authenticated Seller Account:</comment> {$username}");
            }
        } catch (\Throwable $e) {
            $this->error('Failed to obtain user access token: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 3. Fetch Orders from Sell Fulfillment API
        if ($orderId !== '') {
            $this->line("Fetching specific eBay order: {$orderId}...");
            try {
                $order = $syncService->fetchSingleOrderFromApi($accessToken, $orderId);
                $orders = [$order];
            } catch (\Throwable $e) {
                $this->error('Failed to fetch eBay order: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->line("Fetching orders from eBay Sell Fulfillment API (past {$days} days)...");
            try {
                $orders = $syncService->fetchOrdersFromApi($accessToken, $days);
            } catch (\Throwable $e) {
                $this->error('Failed to fetch orders from eBay: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $totalFetched = count($orders);
        $this->info("Successfully fetched {$totalFetched} order(s) from eBay.");

        if ($totalFetched === 0) {
            $this->line('No orders found to sync.');
            return Command::SUCCESS;
        }

        // 4. Process Orders and sync inventory
        $this->line('Processing orders and updating inventory...');
        $summary = $syncService->processOrders($orders, $dryRun);

        // 5. Display Summary
        $this->newLine();
        $this->info('----------------- Sync Summary -----------------');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Orders Fetched', $summary['total_fetched']],
                ['Orders Deducted (Sold Stock)', $summary['deducted']],
                ['Orders Restored (Cancelled/Refunded)', $summary['restored']],
                ['Orders Already Processed (Skipped)', $summary['already_synced']],
                ['Orders Cancelled on eBay (Skipped)', $summary['cancelled']],
                ['Orders with Unmatched Items', $summary['unmatched']],
                ['Errors', $summary['errors']],
            ]
        );

        if (!empty($summary['details'])) {
            $detailRows = [];
            foreach (array_slice($summary['details'], 0, 15) as $d) {
                $detailRows[] = [
                    $d['orderId'] ?? 'N/A',
                    $d['action'] ?? 'N/A',
                    isset($d['deducted_items']) ? count($d['deducted_items']) . ' items' : (isset($d['restored_items']) ? count($d['restored_items']) . ' restored' : ($d['message'] ?? '')),
                ];
            }
            if (count($detailRows) > 0) {
                $this->newLine();
                $this->line('<comment>Recent Order Actions:</comment>');
                $this->table(['Order ID', 'Action', 'Details'], $detailRows);
            }
        }

        $this->info('eBay order inventory synchronization completed.');
        return Command::SUCCESS;
    }
}
