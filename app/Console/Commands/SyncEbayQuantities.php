<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\InventoryController;

class SyncEbayQuantities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ebay:sync-quantities 
                            {--only-synced : Check/sync quantity ONLY for products already imported in local inventory (does not import new items)} 
                            {--check-only : Inspect and display quantity differences without saving any changes to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically sync active eBay listing quantities and statuses to local inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $onlySynced = (bool) $this->option('only-synced');
        $checkOnly = (bool) $this->option('check-only');

        $this->info('====================================================');
        $this->info('        eBay Active Listing Quantity Check          ');
        $this->info('====================================================');

        if ($checkOnly) {
            $this->warn('CHECK-ONLY MODE: No database quantities or records will be modified.');
        }
        if ($onlySynced) {
            $this->line('<comment>Scope:</comment> ONLY checking already-synced products in your inventory.');
        } else {
            $this->line('<comment>Scope:</comment> All active eBay listings (will also import new items).');
        }

        try {
            $controller = app(InventoryController::class);
            $request = new Request([
                'force_resync' => false,
                'only_synced' => $onlySynced,
                'check_only' => $checkOnly,
            ]);
            $response = $controller->fetchFromEbay($request);
            $data = json_decode($response->getContent(), true);

            if (isset($data['success']) && $data['success']) {
                $this->info($data['message'] ?? 'Quantity check complete.');

                $changes = $data['quantity_changes'] ?? [];
                if (!empty($changes)) {
                    $rows = [];
                    foreach ($changes as $c) {
                        $diff = $c['new_qty'] - $c['old_qty'];
                        $diffText = $diff === 0 ? 'Match' : ($diff > 0 ? "+{$diff}" : "{$diff}");
                        $rows[] = [
                            $c['barcode'] ?? 'N/A',
                            \Illuminate\Support\Str::limit($c['name'] ?? 'Product', 42),
                            $c['old_qty'],
                            $c['new_qty'],
                            $diffText,
                            $c['status'],
                        ];
                    }

                    $this->newLine();
                    $this->table(['eBay Item ID', 'Product Name', 'Local Qty', 'eBay Qty', 'Difference', 'Status'], $rows);
                } else {
                    $this->line('No already synced items found matching active listings.');
                }
            } else {
                $this->error("Sync returned error: " . ($data['message'] ?? 'Unknown error'));
                if (!empty($data['reasons']) && is_array($data['reasons'])) {
                    $this->newLine();
                    $this->warn('Detailed eBay API Diagnostics:');
                    foreach ($data['reasons'] as $r) {
                        $this->line(" - <fg=yellow>{$r}</>");
                    }
                }
                return Command::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error('Failed to sync eBay quantities: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
