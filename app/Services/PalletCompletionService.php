<?php

namespace App\Services;

use App\Models\Pallet;
use App\Mail\ProcessingIntakeCompleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PalletCompletionService
{
    private static array $knownTables = [
        'ecommerce_items' => true,
        'refurbishing_items' => true,
        'universal_waste_items' => true,
        'data_destruction_items' => true,
        'it_assets_items' => true,
    ];

    public function syncForPallet(?string $palletNumber): bool
    {
        $palletNumber = trim((string) $palletNumber);

        if ($palletNumber === '') {
            return false;
        }

        $pallets = Pallet::whereRaw('TRIM(barcode_number) = ?', [$palletNumber])->get();

        if ($pallets->isEmpty()) {
            return false;
        }

        $updated = false;

        foreach ($pallets as $pallet) {
            if ($this->syncPallet($pallet)) {
                $updated = true;
            }
        }

        return $updated;
    }

    public function syncPallet(Pallet $pallet): bool
    {
        $evaluation = $this->evaluatePallet($pallet);
        $oldStatus = $pallet->status;
        $targetStatus = $evaluation['target_status'];

        if ($oldStatus !== $targetStatus) {
            $pallet->status = $targetStatus;
            $pallet->save();

            if ($oldStatus !== 'Completed' && $targetStatus === 'Completed') {
                $this->sendCompletionEmail($pallet);
            }

            return true;
        }

        return false;
    }

    public function shouldBeCompleted(Pallet $pallet): bool
    {
        $evaluation = $this->evaluatePallet($pallet);
        return $evaluation['should_complete'];
    }

    public function evaluatePallet(Pallet $pallet): array
    {
        $barcode = trim((string) ($pallet->barcode_number ?? ''));

        $totalQuantity = max(0, (int) ($pallet->estimated_count ?? 0));
        $grossWeight = max(0.0, (float) ($pallet->gross_weight ?? 0));
        $tareWeight = max(0.0, (float) ($pallet->tare_weight ?? 0));
        $totalWeight = max(0.0, $grossWeight - $tareWeight);
        if ($totalWeight <= 0 && ((float) ($pallet->net_weight ?? 0) > 0 || (float) ($pallet->reuse_weight ?? 0) > 0)) {
            $totalWeight = max(0.0, (float) ($pallet->net_weight ?? 0) + (float) ($pallet->reuse_weight ?? 0));
        }

        $processingScrapQuantity = max(0, (int) ($pallet->reuse_quantity ?? 0));
        $processingScrapWeight = max(0.0, (float) ($pallet->reuse_weight ?? 0));

        $assignedQuantity = 0;
        $assignedWeight = 0.0;
        $intakeReuseQuantity = 0;
        $intakeReuseWeight = 0.0;
        $intakeScrapQuantity = 0;
        $intakeScrapWeight = 0.0;

        foreach (array_keys(self::$knownTables) as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $query = DB::table($table)
                ->whereNull('deleted_at');

            if (DB::getSchemaBuilder()->hasColumn($table, 'status')) {
                $query->where(function ($sq) {
                    $sq->whereNull('status')
                       ->orWhere('status', '!=', 'Draft');
                });
            }

            $query->where(function ($q) use ($pallet, $barcode) {
                    $hasCondition = false;
                    if ($pallet->exists && $pallet->id) {
                        $q->where('pallet_id', $pallet->id);
                        $hasCondition = true;
                    }
                    if ($barcode !== '') {
                        if ($hasCondition) {
                            $q->orWhere(function ($sub) use ($barcode) {
                                $sub->whereNull('pallet_id')
                                    ->whereNotNull('pallet_number')
                                    ->whereRaw('TRIM(pallet_number) = ?', [$barcode]);
                            });
                        } else {
                            $q->whereNotNull('pallet_number')
                              ->whereRaw('TRIM(pallet_number) = ?', [$barcode]);
                        }
                    }
                });

            $row = $query->selectRaw('
                COALESCE(SUM(quantity), 0) as total_quantity,
                COALESCE(SUM(reuse_quantity), 0) as reuse_quantity,
                COALESCE(SUM(scrap_quantity), 0) as scrap_quantity,
                COALESCE(SUM(weight), 0) as total_weight,
                COALESCE(SUM(reuse_weight), 0) as reuse_weight,
                COALESCE(SUM(scrap_weight), 0) as scrap_weight
            ')->first();

            if ($row) {
                $assignedQuantity += (int) $row->total_quantity;
                $assignedWeight += (float) $row->total_weight;
                $intakeReuseQuantity += (int) $row->reuse_quantity;
                $intakeReuseWeight += (float) $row->reuse_weight;
                $intakeScrapQuantity += (int) $row->scrap_quantity;
                $intakeScrapWeight += (float) $row->scrap_weight;
            }
        }

        $totalReuseQuantity = $intakeReuseQuantity;
        $totalScrapQuantity = $intakeScrapQuantity + $processingScrapQuantity;
        $totalReuseWeight = $intakeReuseWeight;
        $totalScrapWeight = $intakeScrapWeight + $processingScrapWeight;

        $isQtyComplete = ($totalQuantity > 0) && (($totalReuseQuantity + $totalScrapQuantity) >= $totalQuantity);
        $isWeightComplete = ($totalWeight > 0) && (round($totalReuseWeight + $totalScrapWeight, 2) >= round($totalWeight, 2));

        if ($totalQuantity > 0 && $totalWeight > 0) {
            $shouldBeCompleted = $isQtyComplete && $isWeightComplete;
        } elseif ($totalQuantity > 0) {
            $shouldBeCompleted = $isQtyComplete;
        } elseif ($totalWeight > 0) {
            $shouldBeCompleted = $isWeightComplete;
        } else {
            $shouldBeCompleted = false;
        }

        $oldStatus = $pallet->status;
        $targetStatus = $oldStatus ?: 'Received';

        if ($shouldBeCompleted) {
            $targetStatus = 'Completed';
        } else {
            $hasActivity = ($assignedQuantity > 0 || $totalReuseQuantity > 0 || $totalScrapQuantity > 0 || $assignedWeight > 0 || $totalReuseWeight > 0 || $totalScrapWeight > 0);
            if ($oldStatus === 'Completed') {
                $targetStatus = $hasActivity ? 'Processing' : 'Received';
            } elseif ($oldStatus === 'Received' || empty($oldStatus) || $oldStatus === 'pending') {
                if ($hasActivity) {
                    $targetStatus = 'Processing';
                }
            }
        }

        return [
            'should_complete' => $shouldBeCompleted,
            'target_status' => $targetStatus,
            'total_quantity' => $totalQuantity,
            'total_weight' => $totalWeight,
            'reuse_quantity' => $totalReuseQuantity,
            'scrap_quantity' => $totalScrapQuantity,
            'reuse_weight' => $totalReuseWeight,
            'scrap_weight' => $totalScrapWeight,
        ];
    }

    public function syncAll(): int
    {
        $pallets = Pallet::whereNull('deleted_at')->get();
        $count = 0;
        foreach ($pallets as $pallet) {
            if ($this->syncPallet($pallet)) {
                $count++;
            }
        }
        return $count;
    }

    protected static array $sentCompletionGroups = [];

    public static function resetSentCompletionGroups(): void
    {
        self::$sentCompletionGroups = [];
    }

    public function sendCompletionEmail(Pallet $pallet): void
    {
        try {
            // If the pallet has multiple entries (#1, #2, #3...), wait until all entries are completed
            if (!$pallet->areAllEntriesCompleted()) {
                Log::info("Completion email skipped for Pallet #{$pallet->barcode_number} (ID: {$pallet->id}): Waiting for all entries to be completed.");
                return;
            }

            $barcode = trim((string) ($pallet->barcode_number ?? ''));
            $clientId = $pallet->client_id ?? '0';
            $groupKey = "{$barcode}_{$clientId}";

            if (isset(self::$sentCompletionGroups[$groupKey])) {
                Log::info("Completion email already sent in this cycle for Pallet #{$pallet->barcode_number}");
                return;
            }

            if (!$pallet->relationLoaded('client')) {
                $pallet->load('client');
            }

            $client = $pallet->client;
            if ($client && !empty($client->email)) {
                $relatedPallets = $pallet->getRelatedEntries();
                Mail::to($client->email)->send(new ProcessingIntakeCompleted($pallet, $relatedPallets));
                self::$sentCompletionGroups[$groupKey] = true;
                Log::info("Completion email sent to client {$client->email} for Pallet #{$pallet->barcode_number} (Total Entries: {$relatedPallets->count()})");
            } else {
                Log::info("Completion email skipped for Pallet #{$pallet->barcode_number}: Client email not found.");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send completion email for Pallet #{$pallet->id}: " . $e->getMessage());
        }
    }
}


