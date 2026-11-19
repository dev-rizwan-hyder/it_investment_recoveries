<?php

namespace App\Console\Commands;

use App\Models\EbayInventorySync;
use App\Models\Inventory;
use App\Services\Ebay\EbayInventorySyncService;
use Illuminate\Console\Command;

class SyncInventoryQuantityToEbay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ebay:sync-inventory-quantity 
                            {inventory_id? : The specific local Inventory ID to synchronize to eBay}
                            {--all : Synchronize all inventory items that have an eBay listing or SKU}
                            {--retry-failed : Retry all previously failed eBay inventory sync records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually or batch synchronize local Laravel inventory quantities to corresponding eBay listings';

    /**
     * Execute the console command.
     */
    public function handle(EbayInventorySyncService $syncService): int
    {
        $this->info('====================================================');
        $this->info('   Laravel -> eBay Inventory Quantity Sync Command  ');
        $this->info('====================================================');

        $inventoryId = $this->argument('inventory_id');
        $all = (bool) $this->option('all');
        $retryFailed = (bool) $this->option('retry-failed');

        if (!$inventoryId && !$all && !$retryFailed) {
            $this->error('Please specify an inventory_id or provide the --all or --retry-failed flag.');
            $this->line('Example: php artisan ebay:sync-inventory-quantity 479');
            $this->line('Example: php artisan ebay:sync-inventory-quantity --all');
            $this->line('Example: php artisan ebay:sync-inventory-quantity --retry-failed');
            return Command::FAILURE;
        }

        if ($retryFailed) {
            $failedRecords = EbayInventorySync::where('status', 'failed')->get();
            $this->line("Found {$failedRecords->count()} previously failed synchronization records to retry.");

            $recovered = 0;
            $stillFailed = 0;

            foreach ($failedRecords as $rec) {
                try {
                    $this->line("Retrying sync for Inventory #{$rec->inventory_id}...");
                    $newRecord = $syncService->syncInventoryQuantityToEbay($rec->inventory_id, $rec->order_id);
                    if ($newRecord->status === 'success') {
                        $recovered++;
                        $this->info("✓ Inventory #{$rec->inventory_id} successfully synchronized (Qty: {$newRecord->synced_quantity}).");
                    } else {
                        $stillFailed++;
                        $this->warn("✗ Inventory #{$rec->inventory_id} still failed: {$newRecord->error_message}");
                    }
                } catch (\Throwable $e) {
                    $stillFailed++;
                    $this->error("✗ Inventory #{$rec->inventory_id} exception: {$e->getMessage()}");
                }
            }

            $this->newLine();
            $this->info("Recovery complete. Recovered: {$recovered}, Remaining Failed: {$stillFailed}");
            return Command::SUCCESS;
        }

        if ($inventoryId) {
            $inventory = Inventory::find((int) $inventoryId);
            if (!$inventory) {
                $this->error("Inventory #{$inventoryId} not found in database.");
                return Command::FAILURE;
            }

            $this->line("Synchronizing Inventory #{$inventory->id} ('{$inventory->name}')...");
            try {
                $syncRecord = $syncService->syncInventoryQuantityToEbay($inventory->id);
                $this->displaySyncRecord($syncRecord);
                return $syncRecord->status === 'success' ? Command::SUCCESS : Command::FAILURE;
            } catch (\Throwable $e) {
                $this->error("Sync failed: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // Process all candidate inventory items
        $candidates = Inventory::where(function ($q) {
            $q->where('intake_type', 'eBay')
                ->orWhere('barcode', 'LIKE', 'v1|%')
                ->orWhere('barcode', 'REGEXP', '^[0-9]{9,14}$')
                ->orWhereNotNull('model');
        })->get();

        $this->line("Found {$candidates->count()} candidate inventory items with possible eBay links.");

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($candidates as $inv) {
            try {
                $syncRecord = $syncService->syncInventoryQuantityToEbay($inv->id);
                if ($syncRecord->status === 'success') {
                    $successCount++;
                    $this->info("✓ [{$inv->id}] {$inv->name}: Synced (Qty: {$syncRecord->synced_quantity})");
                } elseif ($syncRecord->status === 'skipped_no_mapping') {
                    $skippedCount++;
                    $this->line("- [{$inv->id}] {$inv->name}: Skipped (no mapping)");
                } else {
                    $failedCount++;
                    $this->warn("✗ [{$inv->id}] {$inv->name}: Failed ({$syncRecord->error_message})");
                }
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("✗ [{$inv->id}] {$inv->name}: Exception - {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Sync completed. Success: {$successCount}, Skipped: {$skippedCount}, Failed: {$failedCount}");

        return Command::SUCCESS;
    }

    protected function displaySyncRecord($record): void
    {
        $this->table(
            ['Field', 'Value'],
            [
                ['Sync Record ID', $record->id],
                ['Inventory ID', $record->inventory_id],
                ['SKU', $record->sku ?? 'N/A'],
                ['eBay Item ID', $record->ebay_item_id ?? 'N/A'],
                ['eBay Offer ID', $record->ebay_offer_id ?? 'N/A'],
                ['Synced Quantity', $record->synced_quantity],
                ['Status', $record->status],
                ['Sync Mode', $record->sync_mode ?? 'N/A'],
                ['Error Message', $record->error_message ?? 'None'],
                ['Last Attempt', $record->last_attempt_at?->toIso8601String() ?? 'N/A'],
            ]
        );
    }
}
