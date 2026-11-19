<?php

namespace App\Jobs;

use App\Models\EbayInventorySync;
use App\Services\Ebay\EbayInventorySyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncInventoryToEbayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The ID of the local inventory item to sync.
     */
    public int $inventoryId;

    /**
     * The related website Order ID, if triggered by a checkout.
     */
    public ?int $orderId;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     * Backoff schedule: 10s, 60s, 180s.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 180];

    /**
     * Maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @param int $inventoryId
     * @param int|null $orderId
     */
    public function __construct(int $inventoryId, ?int $orderId = null)
    {
        $this->inventoryId = $inventoryId;
        $this->orderId = $orderId;
        $this->afterCommit = true;
    }

    /**
     * Execute the job.
     */
    public function handle(EbayInventorySyncService $syncService): void
    {
        Log::info("Starting SyncInventoryToEbayJob for Inventory #{$this->inventoryId} (Order #{$this->orderId}, Attempt #{$this->attempts()})");

        $syncService->syncInventoryQuantityToEbay($this->inventoryId, $this->orderId, $this->attempts());
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SyncInventoryToEbayJob permanently failed for Inventory #{$this->inventoryId}: " . $exception->getMessage(), [
            'inventory_id' => $this->inventoryId,
            'order_id' => $this->orderId,
            'exception' => $exception->getTraceAsString(),
        ]);

        $failAttributes = [
            'synced_quantity' => 0,
            'status' => 'failed',
            'attempts' => $this->attempts(),
            'last_attempt_at' => now(),
            'error_message' => 'Job failed after max retries: ' . $exception->getMessage(),
        ];

        if ($this->orderId) {
            EbayInventorySync::updateOrCreate(
                ['inventory_id' => $this->inventoryId, 'order_id' => $this->orderId],
                $failAttributes
            );
        } else {
            EbayInventorySync::create(array_merge(
                ['inventory_id' => $this->inventoryId, 'order_id' => null],
                $failAttributes
            ));
        }
    }
}
