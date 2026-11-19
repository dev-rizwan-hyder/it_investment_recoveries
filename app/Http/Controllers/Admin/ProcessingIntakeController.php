<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecyclingPalletReceipt;
use App\Mail\ProcessingIntakeCompleted;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ProcessingIntakeController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        $employees = Employee::where('is_resigned', 0)->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = User::orderBy('name')->get();
        }

        return view('admin.intake.processing', compact('clients', 'employees'));
    }

    public function getStats()
    {
        $palletTotals = DB::table('pallets')
            ->whereNull('deleted_at')
            ->selectRaw('
                COUNT(*) as total_pallets,
                COALESCE(SUM(CASE WHEN status = "Processing" THEN 1 ELSE 0 END), 0) as processing_count,
                COALESCE(SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END), 0) as completed_count,
                COALESCE(SUM(estimated_count), 0) as total_estimated,
                COALESCE(SUM(reuse_quantity), 0) as total_reuse_qty,
                COALESCE(SUM(GREATEST(0, gross_weight - tare_weight)), 0) as total_net_weight,
                COALESCE(SUM(reuse_weight), 0) as total_reuse_weight
            ')->first();

        $assignedQty = 0;
        $assignedWeight = 0;
        $sources = [
            'ecommerce_items',
            'refurbishing_items',
            'universal_waste_items',
            'data_destruction_items',
            'it_assets_items',
        ];

        foreach ($sources as $table) {
            if ($this->hasTable($table)) {
                $res = DB::table($table)
                    ->whereNull('deleted_at')
                    ->whereNotNull('pallet_number')
                    ->where('pallet_number', '!=', '')
                    ->selectRaw('COALESCE(SUM(quantity), 0) as qty, COALESCE(SUM(weight), 0) as wght')
                    ->first();
                $assignedQty += (int) ($res->qty ?? 0);
                $assignedWeight += (float) ($res->wght ?? 0);
            }
        }

        $totalPallets = (int) ($palletTotals->total_pallets ?? 0);
        $processingCount = (int) ($palletTotals->processing_count ?? 0);
        $completedCount = (int) ($palletTotals->completed_count ?? 0);
        $totalEstimated = (int) ($palletTotals->total_estimated ?? 0);
        $totalReuseQty = (int) ($palletTotals->total_reuse_qty ?? 0);
        $totalNetWeight = (float) ($palletTotals->total_net_weight ?? 0);
        $totalReuseWeight = (float) ($palletTotals->total_reuse_weight ?? 0);

        $totalItems = max(0, $totalEstimated - $totalReuseQty - $assignedQty);
        $totalWeight = max(0.0, $totalNetWeight - $totalReuseWeight - $assignedWeight);

        return response()->json([
            'success' => true,
            'stats' => [
                'totalPallets' => $totalPallets,
                'processingCount' => $processingCount,
                'completedCount' => $completedCount,
                'totalItems' => $totalItems,
                'totalWeight' => $totalWeight,
            ]
        ]);
    }

    public function getPallets(Request $request)
    {
        $query = Pallet::with('client');

        if ($request->search) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('barcode_number', 'like', "%{$search}%")
                    ->orWhere('job_no', 'like', "%{$search}%")
                    ->orWhere('put_away_location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('company', 'like', "%{$search}%");
                    });

                try {
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
                        $q->orWhereDate('created_at', $search);
                    } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $search)) {
                        $dateObj = \Carbon\Carbon::createFromFormat('m/d/Y', $search);
                        $q->orWhereDate('created_at', $dateObj->format('Y-m-d'));
                    } elseif (strtotime($search) !== false && preg_match('/[a-zA-Z]/', $search)) {
                        $dateObj = \Carbon\Carbon::parse($search);
                        $q->orWhereDate('created_at', $dateObj->format('Y-m-d'));
                    }
                } catch (\Throwable $e) {
                    // Ignore parse errors
                }
            });
        }

        if ($request->statusFilter && $request->statusFilter !== 'All Statuses') {
            $query->where('status', $request->statusFilter);
        }

        if ($request->filled('dateFilter')) {
            $query->whereDate('created_at', $request->dateFilter);
        }

        $pallets = $query->orderBy('created_at', 'desc')->orderBy('id', 'asc')->paginate(10);
        
        $barcodeGenerator = new BarcodeGeneratorSVG();
        $palletIds = collect($pallets->items())->pluck('id')->all();
        $aggregates = $this->intakeAggregatesForPalletIds($palletIds);

        $data = array_map(function ($pallet) use ($barcodeGenerator, $aggregates) {
            $palletAggregate = $this->intakeAggregateForPallet($aggregates, $pallet);
            $breakdown = $this->processingBreakdown($pallet, $palletAggregate);
            $palletData = $pallet->toArray();

            $photos = $this->normalizePhotos($pallet->photos_paths);
            // Only thumbnail the 1st photo for the table listing row thumbnail to keep CPU/payload ultra lightweight
            if (!empty($photos)) {
                $palletData['photos_paths'] = [
                    $this->createThumbnailFromDataUri($photos[0], 100)
                ];
            } else {
                $palletData['photos_paths'] = [];
            }

            $palletData['barcode_svg'] = $this->generateBarcodeSvg($barcodeGenerator, $pallet->barcode_number);
            $palletData['remaining_quantity'] = $breakdown['remaining_quantity'];
            $palletData['remaining_weight'] = $breakdown['remaining_weight'];
            $palletData['intake_breakdown'] = $breakdown;

            return $palletData;
        }, $pallets->items());

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $pallets->currentPage(),
                'last_page' => $pallets->lastPage(),
                'total' => $pallets->total(),
            ]
        ]);
    }

    public function getPalletDetails($id)
    {
        $pallet = Pallet::with('client')->findOrFail($id);
        $barcodeGenerator = new BarcodeGeneratorSVG();
        $barcodeSvg = $this->generateBarcodeSvg($barcodeGenerator, $pallet->barcode_number);

        $relatedModels = Pallet::with('client')
            ->where('barcode_number', $pallet->barcode_number)
            ->when($pallet->client_id, function ($query) use ($pallet) {
                $query->where('client_id', $pallet->client_id);
            })
            ->orderBy('id', 'asc')
            ->get();

        $palletIds = $relatedModels->pluck('id')->all();
        $aggregates = $this->intakeAggregatesForPalletIds($palletIds);

        $allBarcodePhotos = $this->collectAllPhotosForBarcode($pallet->barcode_number);

        $relatedPallets = $relatedModels->map(function ($relatedPallet) use ($barcodeGenerator, $aggregates, $allBarcodePhotos) {
            $aggregate = $this->intakeAggregateForPallet($aggregates, $relatedPallet);
            $breakdown = $this->processingBreakdown($relatedPallet, $aggregate);
            $relatedData = $relatedPallet->toArray();
            $relatedData['photos_paths'] = $this->normalizePhotos($relatedPallet->photos_paths);
            $relatedData['all_barcode_photos'] = $allBarcodePhotos;
            $relatedData['barcode_svg'] = $this->generateBarcodeSvg($barcodeGenerator, $relatedPallet->barcode_number);
            $relatedData['remaining_quantity'] = $breakdown['remaining_quantity'];
            $relatedData['remaining_weight'] = $breakdown['remaining_weight'];
            $relatedData['intake_breakdown'] = $breakdown;

            return $relatedData;
        })->values();

        // Compute totals across all related pallets
        $allAggregated = $this->combinedAggregateForPallets($relatedModels, $aggregates);
        $totals = $this->processingTotals($relatedModels, $allAggregated);
        $palletAggregate = $this->intakeAggregateForPallet($aggregates, $pallet);
        $breakdown = $this->processingBreakdown($pallet, $palletAggregate);
        $palletOwnPhotos = $this->normalizePhotos($pallet->photos_paths);

        return response()->json([
            'success' => true,
            'data' => array_merge($pallet->toArray(), [
                'photos_paths' => $palletOwnPhotos,
                'all_barcode_photos' => $allBarcodePhotos,
                'barcode_svg' => $barcodeSvg,
                'remaining_quantity' => $breakdown['remaining_quantity'],
                'remaining_weight' => $breakdown['remaining_weight'],
                'intake_breakdown' => $breakdown,
                'related_pallets' => $relatedPallets,
                'totals' => $totals,
            ])
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $pallet = Pallet::with('client')->findOrFail($id);
        $oldStatus = $pallet->status;
        $newStatus = $request->status;

        $pallet->update(['status' => $newStatus]);

        if ($oldStatus !== 'Completed' && $newStatus === 'Completed') {
            $this->sendCompletionEmail($pallet);
        }

        return response()->json(['success' => true]);
    }

    public function updatePallet(Request $request, $id)
    {
        // 1. Find the pallet or fail with 404
        $pallet = \App\Models\Pallet::with('client')->findOrFail($id);
        $oldStatus = $pallet->status;

        $request->validate([
            'photos_to_delete' => 'nullable|array',
            'photos_to_delete.*' => 'nullable|string',
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'nullable',
        ]);

        // 2. Weight Calculation Logic
        $gross = (float) ($request->editGrossWeight ?? 0);
        $tare = (float) ($request->editTareWeight ?? 0);
        $grossNetWeight = max(0, $gross - $tare);
        $scrapWeight = (float) ($request->has('editReuseWeight') && $request->editReuseWeight !== null ? $request->editReuseWeight : ($pallet->reuse_weight ?? 0));
        $netWeight = max(0, $grossNetWeight - $scrapWeight);

        // 3. Item Calculation Logic (Estimated - Scrap Quantity)
        $estimated = max(0, (int) ($request->editEstimatedCount ?? 0));
        $scrapQty = (int) ($request->has('editReuseQuantity') && $request->editReuseQuantity !== null ? $request->editReuseQuantity : ($pallet->reuse_quantity ?? 0));

        // Note: We calculate this for data integrity. 
        $netItems = max(0, $estimated - $scrapQty);

        $status = $request->editStatus ?: $pallet->status;

        $photoPaths = $this->normalizePhotos($pallet->photos_paths);

        foreach ((array) $request->input('photos_to_delete', []) as $pathToDelete) {
            $normalizedDeletePath = ltrim(str_replace('\\', '/', (string) $pathToDelete), '/');
            $this->deletePalletPhotoFile($normalizedDeletePath);

            $photoPaths = array_filter($photoPaths, function ($path) use ($pathToDelete, $normalizedDeletePath) {
                return $path !== $pathToDelete && ltrim(str_replace('\\', '/', (string) $path), '/') !== $normalizedDeletePath;
            });
        }

        if ($request->hasFile('new_photos')) {
            $uploadedPhotos = $request->file('new_photos');
            if ($uploadedPhotos instanceof \Illuminate\Http\UploadedFile) {
                $uploadedPhotos = [$uploadedPhotos];
            }

            $photoPaths = array_merge(
                $photoPaths,
                $this->savePalletPhotos($pallet->barcode_number, $uploadedPhotos)
            );
        }

        $photoPaths = array_values(array_unique(array_filter($photoPaths)));

        // 4. Update the Database using the "edit" prefixed keys from your JS formData
        $updateData = [
            'status' => $status,
            'client_id' => $request->editVendor,
            'gross_weight' => $gross,
            'tare_weight' => $tare,
            'reuse_weight' => $scrapWeight,
            'net_weight' => $netWeight,
            'estimated_count' => $estimated,
            'reuse_quantity' => $scrapQty,
            'description' => $request->editDescription,
            'put_away_location' => $request->editLocation,
            'marketing_reference' => $request->editMarketingReference,
            'notes' => $request->editNotes,
            'receiving_employee' => $request->editReceivingEmployee ?? $request->receiving_employee,
            'processing_employee' => $request->editProcessingEmployee ?? $request->processing_employee,
            'job_no' => $request->editJobNo ?? ($request->job_no ?? $request->jobNo),
            'photos_paths' => $photoPaths,
        ];

        if ($request->filled('editIntakeDate')) {
            $currentTime = $pallet->created_at ? \Carbon\Carbon::parse($pallet->created_at) : now();
            $updateData['created_at'] = \Carbon\Carbon::parse($request->editIntakeDate)->setTimeFrom($currentTime);
        }

        $pallet->update($updateData);
        app(\App\Services\PalletCompletionService::class)->syncPallet($pallet);
        $pallet->refresh();

        if ($oldStatus !== 'Completed' && $pallet->status === 'Completed') {
            $this->sendCompletionEmail($pallet);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pallet updated successfully!',
            'net_weight' => $netWeight,
            'net_items' => $netItems,
            'photos_paths' => $photoPaths,
        ]);
    }

    public function deletePallet($id)
    {
        try {
            $pallet = Pallet::findOrFail($id);
            $pallet->delete();
            return response()->json(['success' => true, 'message' => 'Pallet deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting pallet: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting pallet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pallets,id',
            'status' => 'required|string|max:100',
        ]);

        $newStatus = $validated['status'];
        if ($newStatus === 'Completed') {
            $pallets = Pallet::with('client')->whereIn('id', $validated['ids'])->get();
            $updatedCount = 0;
            $oldStatuses = [];
            foreach ($pallets as $pallet) {
                $oldStatuses[$pallet->id] = $pallet->status;
                $pallet->update(['status' => 'Completed']);
                $updatedCount++;
            }

            // Group pallets by (barcode_number, client_id) to verify all entries and prevent duplicate emails
            $processedGroups = [];
            foreach ($pallets as $pallet) {
                $groupKey = trim((string) ($pallet->barcode_number ?? '')) . '_' . ($pallet->client_id ?? '0');
                if (isset($processedGroups[$groupKey])) {
                    continue;
                }
                $processedGroups[$groupKey] = true;

                $groupPallets = $pallets->filter(function ($p) use ($pallet) {
                    return trim((string) ($p->barcode_number ?? '')) === trim((string) ($pallet->barcode_number ?? ''))
                        && (string) ($p->client_id ?? '0') === (string) ($pallet->client_id ?? '0');
                });
                $anyTransitioned = $groupPallets->contains(fn ($p) => ($oldStatuses[$p->id] ?? '') !== 'Completed');

                if ($anyTransitioned && $pallet->areAllEntriesCompleted()) {
                    $this->sendCompletionEmail($pallet);
                }
            }
        } else {
            $updatedCount = Pallet::whereIn('id', $validated['ids'])
                ->update(['status' => $newStatus]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} pallet(s) updated to {$newStatus}.",
        ]);
    }


    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pallets,id',
        ]);

        $deletedCount = Pallet::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} pallet(s) deleted successfully.",
        ]);
    }

    public function bulkSendEmail(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pallets,id',
        ]);

        $pallets = Pallet::with('client')->whereIn('id', $validated['ids'])->get();

        // Group pallets by client_id so each client gets ONE email
        $grouped = $pallets->groupBy('client_id');

        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $details = [];

        foreach ($grouped as $clientId => $clientPallets) {
            $client = $clientPallets->first()->client;

            if (!$client || empty($client->email)) {
                $skipped += $clientPallets->count();
                $details[] = [
                    'client' => $client->name ?? 'Unknown',
                    'status' => 'skipped',
                    'reason' => 'No email address',
                ];
                continue;
            }

            try {
                Mail::to($client->email)->send(new RecyclingPalletReceipt($clientPallets));
                $sent += $clientPallets->count();
                $details[] = [
                    'client' => $client->name,
                    'email' => $client->email,
                    'status' => 'sent',
                    'pallets' => $clientPallets->count(),
                ];
            } catch (\Throwable $e) {
                $failed += $clientPallets->count();
                Log::error('Bulk email failed for client.', [
                    'client_id' => $clientId,
                    'email' => $client->email,
                    'error' => $e->getMessage(),
                ]);
                $details[] = [
                    'client' => $client->name,
                    'email' => $client->email,
                    'status' => 'failed',
                    'reason' => $e->getMessage(),
                ];
            }
        }

        $totalClients = $grouped->count();
        $sentClients = collect($details)->where('status', 'sent')->count();

        return response()->json([
            'success' => $sent > 0,
            'message' => "Email sent to {$sentClients} client(s) covering {$sent} pallet(s)." .
                ($skipped > 0 ? " {$skipped} pallet(s) skipped (no email)." : '') .
                ($failed > 0 ? " {$failed} pallet(s) failed." : ''),
            'details' => $details,
        ]);
    }

    public function getClients()
    {
        return response()->json(['success' => true, 'data' => Client::orderBy('name')->get()]);
    }

    public function getMarketingRefs()
    {
        return response()->json(['success' => true, 'data' => ['Facebook', 'Google', 'Referral', 'Direct Mail', 'Returning Client']]);
    }

    private function generateBarcodeSvg(BarcodeGeneratorSVG $barcodeGenerator, $barcodeNumber)
    {
        if (empty($barcodeNumber)) {
            return '';
        }

        try {
            $barcodeSvg = $barcodeGenerator->getBarcode(
                $barcodeNumber,
                $barcodeGenerator::TYPE_CODE_128,
                1.5,
                55
            );
            $svgStart = strpos($barcodeSvg, '<svg');

            return $svgStart === false ? '' : substr($barcodeSvg, $svgStart);
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function remainingPalletQuantity(Pallet $pallet): int
    {
        return max(0, (int) ($pallet->estimated_count ?? 0) - $this->processingScrapQuantity($pallet));
    }

    private function remainingPalletWeight(Pallet $pallet): float
    {
        $currentNetWeight = max(0, (float) ($pallet->net_weight ?? 0));
        $reuseWeight = $this->processingScrapWeight($pallet);
        $grossWeight = max(0, (float) ($pallet->gross_weight ?? 0));
        $tareWeight = max(0, (float) ($pallet->tare_weight ?? 0));
        $originalNetWeight = max(0, $grossWeight - $tareWeight);

        if ($reuseWeight > 0 && $originalNetWeight > 0 && abs($currentNetWeight - $originalNetWeight) < 0.01) {
            return max(0, $currentNetWeight - $reuseWeight);
        }

        return $currentNetWeight;
    }

    private function processingScrapQuantity(Pallet $pallet): int
    {
        return max(0, (int) ($pallet->reuse_quantity ?? 0));
    }

    private function processingScrapWeight(Pallet $pallet): float
    {
        return max(0.0, (float) ($pallet->reuse_weight ?? 0));
    }

    private function intakeAggregatesForBarcodes(array $barcodes): array
    {
        $barcodes = collect($barcodes)
            ->filter(fn ($barcode) => is_string($barcode) && trim($barcode) !== '')
            ->map(fn ($barcode) => trim($barcode))
            ->unique()
            ->values()
            ->all();

        if (empty($barcodes)) {
            return [];
        }

        $aggregates = [];
        $sources = [
            ['table' => 'ecommerce_items', 'label' => 'E-commerce'],
            ['table' => 'refurbishing_items', 'label' => 'Refurbishing'],
            ['table' => 'universal_waste_items', 'label' => 'Universal Waste'],
            ['table' => 'data_destruction_items', 'label' => 'Data Destruction'],
            ['table' => 'it_assets_items', 'label' => 'IT Assets'],
        ];

        foreach ($sources as $source) {
            $table = $source['table'];
            if (!$this->hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)
                ->whereNull('deleted_at')
                ->selectRaw('pallet_number as pallet_number')
                ->selectRaw('COUNT(*) as records')
                ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
                ->selectRaw('COALESCE(SUM(reuse_quantity), 0) as reuse_quantity')
                ->selectRaw('COALESCE(SUM(scrap_quantity), 0) as scrap_quantity')
                ->selectRaw('COALESCE(SUM(GREATEST(0, quantity - reuse_quantity - scrap_quantity)), 0) as transferred_quantity')
                ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
                ->selectRaw('COALESCE(SUM(reuse_weight), 0) as reuse_weight')
                ->selectRaw('COALESCE(SUM(scrap_weight), 0) as scrap_weight')
                ->selectRaw('COALESCE(SUM(GREATEST(0.0, weight - reuse_weight - scrap_weight)), 0) as transferred_weight')
                ->whereNotNull('pallet_number')
                ->whereIn('pallet_number', $barcodes)
                ->groupBy('pallet_number')
                ->get();

            foreach ($rows as $row) {
                $barcode = (string) $row->pallet_number;
                if (!isset($aggregates[$barcode])) {
                    $aggregates[$barcode] = $this->emptyProcessingBreakdown(true);
                }

                $sourceBreakdown = $this->normalizeProcessingBreakdown([
                    'records' => (int) $row->records,
                    'total_quantity' => (int) $row->total_quantity,
                    'transferred_quantity' => (int) $row->transferred_quantity,
                    'reuse_quantity' => (int) $row->reuse_quantity,
                    'scrap_quantity' => (int) $row->scrap_quantity,
                    'total_weight' => (float) $row->total_weight,
                    'transferred_weight' => (float) $row->transferred_weight,
                    'reuse_weight' => (float) $row->reuse_weight,
                    'scrap_weight' => (float) $row->scrap_weight,
                    'source_label' => $source['label'],
                ], true);

                foreach (['records', 'total_quantity', 'transferred_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'transferred_weight', 'reuse_weight', 'scrap_weight'] as $key) {
                    $aggregates[$barcode][$key] += $sourceBreakdown[$key];
                }

                $aggregates[$barcode]['sources'][] = $sourceBreakdown;
            }
        }

        foreach ($aggregates as $barcode => $aggregate) {
            $aggregates[$barcode] = $this->normalizeProcessingBreakdown($aggregate, true);
        }

        return $aggregates;
    }

    /**
     * Build per-pallet-ID intake aggregates.
     * Returns ['by_id' => [palletId => aggregate, ...]]
     */
    private function intakeAggregatesForPalletIds(array $palletIds): array
    {
        $palletIds = array_filter($palletIds);

        if (empty($palletIds)) {
            return ['by_id' => []];
        }

        $aggregates = ['by_id' => []];
        $sources = [
            ['table' => 'ecommerce_items', 'label' => 'E-commerce'],
            ['table' => 'refurbishing_items', 'label' => 'Refurbishing'],
            ['table' => 'universal_waste_items', 'label' => 'Universal Waste'],
            ['table' => 'data_destruction_items', 'label' => 'Data Destruction'],
            ['table' => 'it_assets_items', 'label' => 'IT Assets'],
        ];

        // First, collect barcodes for these pallet IDs (for legacy fallback)
        $pallets = Pallet::whereIn('id', $palletIds)->get(['id', 'barcode_number']);
        $palletBarcodeMap = $pallets->pluck('barcode_number', 'id')->all();

        foreach ($sources as $source) {
            $table = $source['table'];
            if (!$this->hasTable($table)) {
                continue;
            }
            $hasPalletIdCol = $this->hasColumn($table, 'pallet_id');
            if ($hasPalletIdCol) {
                // Query items that have pallet_id set (new items)
                $rows = DB::table($table)
                    ->whereNull('deleted_at')
                    ->selectRaw('pallet_id')
                    ->selectRaw('COUNT(*) as records')
                    ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
                    ->selectRaw('COALESCE(SUM(reuse_quantity), 0) as reuse_quantity')
                    ->selectRaw('COALESCE(SUM(scrap_quantity), 0) as scrap_quantity')
                    ->selectRaw('COALESCE(SUM(GREATEST(0, quantity - reuse_quantity - scrap_quantity)), 0) as transferred_quantity')
                    ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
                    ->selectRaw('COALESCE(SUM(reuse_weight), 0) as reuse_weight')
                    ->selectRaw('COALESCE(SUM(scrap_weight), 0) as scrap_weight')
                    ->selectRaw('COALESCE(SUM(GREATEST(0.0, weight - reuse_weight - scrap_weight)), 0) as transferred_weight')
                    ->whereNotNull('pallet_id')
                    ->whereIn('pallet_id', $palletIds)
                    ->groupBy('pallet_id')
                    ->get();

                foreach ($rows as $row) {
                    $pid = (int) $row->pallet_id;
                    if (!isset($aggregates['by_id'][$pid])) {
                        $aggregates['by_id'][$pid] = $this->emptyProcessingBreakdown(true);
                    }

                    $sourceBreakdown = $this->normalizeProcessingBreakdown([
                        'records' => (int) $row->records,
                        'total_quantity' => (int) $row->total_quantity,
                        'transferred_quantity' => (int) $row->transferred_quantity,
                        'reuse_quantity' => (int) $row->reuse_quantity,
                        'scrap_quantity' => (int) $row->scrap_quantity,
                        'total_weight' => (float) $row->total_weight,
                        'transferred_weight' => (float) $row->transferred_weight,
                        'reuse_weight' => (float) $row->reuse_weight,
                        'scrap_weight' => (float) $row->scrap_weight,
                        'source_label' => $source['label'],
                    ], true);

                    foreach (['records', 'total_quantity', 'transferred_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'transferred_weight', 'reuse_weight', 'scrap_weight'] as $key) {
                        $aggregates['by_id'][$pid][$key] += $sourceBreakdown[$key];
                    }
                    $aggregates['by_id'][$pid]['sources'][] = $sourceBreakdown;
                }

                // Also pick up legacy items (pallet_id IS NULL) that match barcodes
                $barcodes = array_filter(array_unique(array_values($palletBarcodeMap)));
                if (!empty($barcodes)) {
                    $legacyRows = DB::table($table)
                        ->whereNull('deleted_at')
                        ->selectRaw('pallet_number')
                        ->selectRaw('COUNT(*) as records')
                        ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
                        ->selectRaw('COALESCE(SUM(reuse_quantity), 0) as reuse_quantity')
                        ->selectRaw('COALESCE(SUM(scrap_quantity), 0) as scrap_quantity')
                        ->selectRaw('COALESCE(SUM(GREATEST(0, quantity - reuse_quantity - scrap_quantity)), 0) as transferred_quantity')
                        ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
                        ->selectRaw('COALESCE(SUM(reuse_weight), 0) as reuse_weight')
                        ->selectRaw('COALESCE(SUM(scrap_weight), 0) as scrap_weight')
                        ->selectRaw('COALESCE(SUM(GREATEST(0.0, weight - reuse_weight - scrap_weight)), 0) as transferred_weight')
                        ->whereNull('pallet_id')
                        ->whereNotNull('pallet_number')
                        ->whereIn('pallet_number', $barcodes)
                        ->groupBy('pallet_number')
                        ->get();

                    // Assign legacy items to the first pallet with that barcode
                    foreach ($legacyRows as $row) {
                        $legacyBarcode = trim((string) $row->pallet_number);
                        // Find the first pallet ID matching this barcode
                        $targetPid = null;
                        foreach ($palletBarcodeMap as $pid => $bc) {
                            if (trim((string) $bc) === $legacyBarcode) {
                                $targetPid = $pid;
                                break;
                            }
                        }
                        if (!$targetPid) {
                            continue;
                        }

                        if (!isset($aggregates['by_id'][$targetPid])) {
                            $aggregates['by_id'][$targetPid] = $this->emptyProcessingBreakdown(true);
                        }

                        $sourceBreakdown = $this->normalizeProcessingBreakdown([
                            'records' => (int) $row->records,
                            'total_quantity' => (int) $row->total_quantity,
                            'transferred_quantity' => (int) $row->transferred_quantity,
                            'reuse_quantity' => (int) $row->reuse_quantity,
                            'scrap_quantity' => (int) $row->scrap_quantity,
                            'total_weight' => (float) $row->total_weight,
                            'transferred_weight' => (float) $row->transferred_weight,
                            'reuse_weight' => (float) $row->reuse_weight,
                            'scrap_weight' => (float) $row->scrap_weight,
                            'source_label' => $source['label'] . ' (legacy)',
                        ], true);

                        foreach (['records', 'total_quantity', 'transferred_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'transferred_weight', 'reuse_weight', 'scrap_weight'] as $key) {
                            $aggregates['by_id'][$targetPid][$key] += $sourceBreakdown[$key];
                        }
                        $aggregates['by_id'][$targetPid]['sources'][] = $sourceBreakdown;
                    }
                }
            } else {
                // No pallet_id column — fall back to barcode-based grouping (same as old logic)
                // Assign all items with matching barcode to the first pallet ID
                $barcodes = array_filter(array_unique(array_values($palletBarcodeMap)));
                if (empty($barcodes)) {
                    continue;
                }

                $rows = DB::table($table)
                    ->whereNull('deleted_at')
                    ->selectRaw('pallet_number')
                    ->selectRaw('COUNT(*) as records')
                    ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
                    ->selectRaw('COALESCE(SUM(reuse_quantity), 0) as reuse_quantity')
                    ->selectRaw('COALESCE(SUM(scrap_quantity), 0) as scrap_quantity')
                    ->selectRaw('COALESCE(SUM(GREATEST(0, quantity - reuse_quantity - scrap_quantity)), 0) as transferred_quantity')
                    ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
                    ->selectRaw('COALESCE(SUM(reuse_weight), 0) as reuse_weight')
                    ->selectRaw('COALESCE(SUM(scrap_weight), 0) as scrap_weight')
                    ->selectRaw('COALESCE(SUM(GREATEST(0.0, weight - reuse_weight - scrap_weight)), 0) as transferred_weight')
                    ->whereNotNull('pallet_number')
                    ->whereIn('pallet_number', $barcodes)
                    ->groupBy('pallet_number')
                    ->get();

                foreach ($rows as $row) {
                    $legacyBarcode = trim((string) $row->pallet_number);
                    $targetPid = null;
                    foreach ($palletBarcodeMap as $pid => $bc) {
                        if (trim((string) $bc) === $legacyBarcode) {
                            $targetPid = $pid;
                            break;
                        }
                    }
                    if (!$targetPid) {
                        continue;
                    }

                    if (!isset($aggregates['by_id'][$targetPid])) {
                        $aggregates['by_id'][$targetPid] = $this->emptyProcessingBreakdown(true);
                    }

                    $sourceBreakdown = $this->normalizeProcessingBreakdown([
                        'records' => (int) $row->records,
                        'total_quantity' => (int) $row->total_quantity,
                        'transferred_quantity' => (int) $row->transferred_quantity,
                        'reuse_quantity' => (int) $row->reuse_quantity,
                        'scrap_quantity' => (int) $row->scrap_quantity,
                        'total_weight' => (float) $row->total_weight,
                        'transferred_weight' => (float) $row->transferred_weight,
                        'reuse_weight' => (float) $row->reuse_weight,
                        'scrap_weight' => (float) $row->scrap_weight,
                        'source_label' => $source['label'],
                    ], true);

                    foreach (['records', 'total_quantity', 'transferred_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'transferred_weight', 'reuse_weight', 'scrap_weight'] as $key) {
                        $aggregates['by_id'][$targetPid][$key] += $sourceBreakdown[$key];
                    }
                    $aggregates['by_id'][$targetPid]['sources'][] = $sourceBreakdown;
                }
            }
        }

        // Normalize all per-pallet aggregates
        foreach ($aggregates['by_id'] as $pid => $aggregate) {
            $aggregates['by_id'][$pid] = $this->normalizeProcessingBreakdown($aggregate, true);
        }

        return $aggregates;
    }

    /**
     * Combine per-pallet aggregates into a single aggregate for totals display.
     */
    private function combinedAggregateForPallets($pallets, array $aggregates): ?array
    {
        $combined = $this->emptyProcessingBreakdown(true);
        $hasSources = false;

        foreach ($pallets as $pallet) {
            $agg = $this->intakeAggregateForPallet($aggregates, $pallet);
            if ($agg && ($agg['records'] ?? 0) > 0) {
                $hasSources = true;
                foreach (['records', 'total_quantity', 'transferred_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'transferred_weight', 'reuse_weight', 'scrap_weight'] as $key) {
                    $combined[$key] += $agg[$key] ?? 0;
                }
                if (!empty($agg['sources'])) {
                    $combined['sources'] = array_merge($combined['sources'], $agg['sources']);
                }
            }
        }

        if (!$hasSources) {
            return null;
        }

        return $this->normalizeProcessingBreakdown($combined, true);
    }

    private function intakeAggregateForPallet(array $aggregates, Pallet $pallet): ?array
    {
        // Look up by pallet ID first (precise per-pallet aggregates)
        $palletId = $pallet->id;
        if ($palletId && isset($aggregates['by_id'][$palletId])) {
            return $aggregates['by_id'][$palletId];
        }

        // Fall back to barcode-based aggregate (for backward compatibility)
        $barcode = trim((string) ($pallet->barcode_number ?? ''));
        if ($barcode !== '' && isset($aggregates[$barcode])) {
            return $aggregates[$barcode];
        }

        return null;
    }

    private function processingBreakdown(Pallet $pallet, ?array $intakeAggregate = null): array
    {
        if ($intakeAggregate && ($intakeAggregate['records'] ?? 0) > 0) {
            $base = $this->palletBaseBreakdown($pallet);
            $processingScrapQuantity = $this->processingScrapQuantity($pallet);
            $processingScrapWeight = $this->processingScrapWeight($pallet);
            $assignedQuantity = max(0, (int) ($intakeAggregate['total_quantity'] ?? 0));
            $assignedWeight = max(0.0, (float) ($intakeAggregate['total_weight'] ?? 0));
            $availableQuantity = max(0, $base['total_quantity'] - $processingScrapQuantity - $assignedQuantity);
            $availableWeight = max(0.0, $base['total_weight'] - $processingScrapWeight - $assignedWeight);

            $intakeReuseQuantity = max(0, (int) ($intakeAggregate['reuse_quantity'] ?? 0));
            $intakeReuseWeight = max(0.0, (float) ($intakeAggregate['reuse_weight'] ?? 0));
            $intakeScrapQuantity = max(0, (int) ($intakeAggregate['scrap_quantity'] ?? 0));
            $intakeScrapWeight = max(0.0, (float) ($intakeAggregate['scrap_weight'] ?? 0));

            $transferredQuantity = max(0, (int) ($intakeAggregate['transferred_quantity'] ?? max(0, $assignedQuantity - $intakeReuseQuantity - $intakeScrapQuantity)));
            $transferredWeight = max(0.0, (float) ($intakeAggregate['transferred_weight'] ?? max(0.0, $assignedWeight - $intakeReuseWeight - $intakeScrapWeight)));

            $totalScrapQuantity = $intakeScrapQuantity + $processingScrapQuantity;
            $totalScrapWeight = $intakeScrapWeight + $processingScrapWeight;

            $totalQuantity = $base['total_quantity'];
            $totalWeight = $base['total_weight'];
            $totalReuseQuantity = $intakeReuseQuantity;
            $totalReuseWeight = $intakeReuseWeight;

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

            if ($shouldBeCompleted) {
                if ($pallet->status !== 'Completed') {
                    $pallet->status = 'Completed';
                    if ($pallet->exists) {
                        DB::table('pallets')->where('id', $pallet->id)->update(['status' => 'Completed']);
                        $this->sendCompletionEmail($pallet);
                    }
                }
            } else {
                if ($pallet->status === 'Completed') {
                    $hasActivity = ($assignedQuantity > 0 || $totalReuseQuantity > 0 || $totalScrapQuantity > 0 || $assignedWeight > 0 || $totalReuseWeight > 0 || $totalScrapWeight > 0);
                    $newStatus = $hasActivity ? 'Processing' : 'Received';
                    $pallet->status = $newStatus;
                    if ($pallet->exists) {
                        DB::table('pallets')->where('id', $pallet->id)->update(['status' => $newStatus]);
                    }
                } elseif ($pallet->status === 'Received' || empty($pallet->status) || $pallet->status === 'pending') {
                    $hasActivity = ($assignedQuantity > 0 || $totalReuseQuantity > 0 || $totalScrapQuantity > 0 || $assignedWeight > 0 || $totalReuseWeight > 0 || $totalScrapWeight > 0);
                    if ($hasActivity) {
                        $pallet->status = 'Processing';
                        if ($pallet->exists) {
                            DB::table('pallets')->where('id', $pallet->id)->update(['status' => 'Processing']);
                        }
                    }
                }
            }

            return $this->normalizeProcessingBreakdown([
                'records' => (int) ($intakeAggregate['records'] ?? 0),
                'intake_records' => (int) ($intakeAggregate['records'] ?? 0),
                'total_quantity' => $base['total_quantity'],
                'transferred_quantity' => $transferredQuantity,
                'reuse_quantity' => $intakeReuseQuantity,
                'scrap_quantity' => $totalScrapQuantity,
                'remaining_quantity' => $availableQuantity,
                'total_weight' => $base['total_weight'],
                'transferred_weight' => $transferredWeight,
                'reuse_weight' => $intakeReuseWeight,
                'scrap_weight' => $totalScrapWeight,
                'remaining_weight' => $availableWeight,
                'assigned_quantity' => $assignedQuantity,
                'assigned_weight' => $assignedWeight,
                'pallet_total_quantity' => $base['total_quantity'],
                'pallet_total_weight' => $base['total_weight'],
                'processing_scrap_quantity' => $processingScrapQuantity,
                'processing_scrap_weight' => $processingScrapWeight,
                'intake_scrap_quantity' => $intakeScrapQuantity,
                'intake_scrap_weight' => $intakeScrapWeight,
                'sources' => $intakeAggregate['sources'] ?? [],
            ], true);
        }

        return $this->palletFallbackBreakdown($pallet);
    }

    private function processingTotals($relatedPallets, ?array $intakeAggregate = null): array
    {
        if ($intakeAggregate && ($intakeAggregate['records'] ?? 0) > 0) {
            $base = $this->palletBaseTotals($relatedPallets);
            $processingScrap = $this->processingScrapTotals($relatedPallets);
            $assignedQuantity = max(0, (int) ($intakeAggregate['total_quantity'] ?? 0));
            $assignedWeight = max(0.0, (float) ($intakeAggregate['total_weight'] ?? 0));
            $availableQuantity = max(0, $base['total_quantity'] - $processingScrap['quantity'] - $assignedQuantity);
            $availableWeight = max(0.0, $base['total_weight'] - $processingScrap['weight'] - $assignedWeight);

            $intakeReuseQuantity = max(0, (int) ($intakeAggregate['reuse_quantity'] ?? 0));
            $intakeReuseWeight = max(0.0, (float) ($intakeAggregate['reuse_weight'] ?? 0));
            $intakeScrapQuantity = max(0, (int) ($intakeAggregate['scrap_quantity'] ?? 0));
            $intakeScrapWeight = max(0.0, (float) ($intakeAggregate['scrap_weight'] ?? 0));

            $transferredQuantity = max(0, (int) ($intakeAggregate['transferred_quantity'] ?? max(0, $assignedQuantity - $intakeReuseQuantity - $intakeScrapQuantity)));
            $transferredWeight = max(0.0, (float) ($intakeAggregate['transferred_weight'] ?? max(0.0, $assignedWeight - $intakeReuseWeight - $intakeScrapWeight)));

            $totalScrapQuantity = $intakeScrapQuantity + $processingScrap['quantity'];
            $totalScrapWeight = $intakeScrapWeight + $processingScrap['weight'];

            $totals = $this->normalizeProcessingBreakdown([
                'records' => $relatedPallets->count(),
                'intake_records' => (int) ($intakeAggregate['records'] ?? 0),
                'total_quantity' => $base['total_quantity'],
                'transferred_quantity' => $transferredQuantity,
                'reuse_quantity' => $intakeReuseQuantity,
                'scrap_quantity' => $totalScrapQuantity,
                'remaining_quantity' => $availableQuantity,
                'total_weight' => $base['total_weight'],
                'transferred_weight' => $transferredWeight,
                'reuse_weight' => $intakeReuseWeight,
                'scrap_weight' => $totalScrapWeight,
                'remaining_weight' => $availableWeight,
                'assigned_quantity' => $assignedQuantity,
                'assigned_weight' => $assignedWeight,
                'pallet_total_quantity' => $base['total_quantity'],
                'pallet_total_weight' => $base['total_weight'],
                'processing_scrap_quantity' => $processingScrap['quantity'],
                'processing_scrap_weight' => $processingScrap['weight'],
                'intake_scrap_quantity' => $intakeScrapQuantity,
                'intake_scrap_weight' => $intakeScrapWeight,
                'sources' => $intakeAggregate['sources'] ?? [],
            ], true);
            $totals['records'] = $relatedPallets->count();
            $totals['intake_records'] = (int) ($intakeAggregate['records'] ?? 0);
            return $totals;
        }

        $totals = $this->emptyProcessingBreakdown(false);
        $totals['records'] = $relatedPallets->count();
        $totals['intake_records'] = 0;

        foreach ($relatedPallets as $relatedPallet) {
            $breakdown = $this->palletFallbackBreakdown($relatedPallet);
            foreach (['total_quantity', 'transferred_quantity', 'reuse_quantity', 'scrap_quantity', 'remaining_quantity', 'total_weight', 'transferred_weight', 'reuse_weight', 'scrap_weight', 'remaining_weight'] as $key) {
                $totals[$key] += $breakdown[$key];
            }
        }

        return $this->normalizeProcessingBreakdown($totals, false);
    }

    private function processingScrapTotals($relatedPallets): array
    {
        $totals = ['quantity' => 0, 'weight' => 0.0];

        foreach ($relatedPallets as $relatedPallet) {
            $totals['quantity'] += $this->processingScrapQuantity($relatedPallet);
            $totals['weight'] += $this->processingScrapWeight($relatedPallet);
        }

        return $totals;
    }

    private function palletBaseTotals($relatedPallets): array
    {
        $totals = [
            'total_quantity' => 0,
            'total_weight' => 0.0,
        ];

        foreach ($relatedPallets as $relatedPallet) {
            $base = $this->palletBaseBreakdown($relatedPallet);
            $totals['total_quantity'] += $base['total_quantity'];
            $totals['total_weight'] += $base['total_weight'];
        }

        return $totals;
    }

    private function palletBaseBreakdown(Pallet $pallet): array
    {
        $grossWeight = max(0.0, (float) ($pallet->gross_weight ?? 0));
        $tareWeight = max(0.0, (float) ($pallet->tare_weight ?? 0));
        $scrapWeight = $this->processingScrapWeight($pallet);
        $totalWeight = max(0.0, $grossWeight - $tareWeight);
        $remainingWeight = $this->remainingPalletWeight($pallet);

        if ($totalWeight <= 0 && ($remainingWeight > 0 || $scrapWeight > 0)) {
            $totalWeight = $remainingWeight + $scrapWeight;
        }

        return [
            'total_quantity' => max(0, (int) ($pallet->estimated_count ?? 0)),
            'total_weight' => $totalWeight,
        ];
    }

    private function palletFallbackBreakdown(Pallet $pallet): array
    {
        $base = $this->palletBaseBreakdown($pallet);
        $remainingWeight = $this->remainingPalletWeight($pallet);
        $remainingQuantity = $this->remainingPalletQuantity($pallet);
        $processingScrapQuantity = $this->processingScrapQuantity($pallet);
        $processingScrapWeight = $this->processingScrapWeight($pallet);

        $totalQuantity = $base['total_quantity'];
        $totalWeight = $base['total_weight'];
        $totalReuseQuantity = 0;
        $totalReuseWeight = 0.0;
        $totalScrapQuantity = $processingScrapQuantity;
        $totalScrapWeight = $processingScrapWeight;

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

        if ($shouldBeCompleted) {
            if ($pallet->status !== 'Completed') {
                $pallet->status = 'Completed';
                if ($pallet->exists) {
                    DB::table('pallets')->where('id', $pallet->id)->update(['status' => 'Completed']);
                    $this->sendCompletionEmail($pallet);
                }
            }
        } else {
            if ($pallet->status === 'Completed') {
                $hasActivity = ($processingScrapQuantity > 0 || $processingScrapWeight > 0);
                $newStatus = $hasActivity ? 'Processing' : 'Received';
                $pallet->status = $newStatus;
                if ($pallet->exists) {
                    DB::table('pallets')->where('id', $pallet->id)->update(['status' => $newStatus]);
                }
            }
        }

        return $this->normalizeProcessingBreakdown([
            'records' => 0,
            'total_quantity' => $base['total_quantity'],
            'transferred_quantity' => 0,
            'reuse_quantity' => 0,
            'scrap_quantity' => $processingScrapQuantity,
            'remaining_quantity' => $remainingQuantity,
            'total_weight' => $base['total_weight'],
            'transferred_weight' => 0.0,
            'reuse_weight' => 0.0,
            'scrap_weight' => $processingScrapWeight,
            'remaining_weight' => $remainingWeight,
            'assigned_quantity' => 0,
            'assigned_weight' => 0.0,
            'pallet_total_quantity' => $base['total_quantity'],
            'pallet_total_weight' => $base['total_weight'],
            'sources' => [],
        ], false);
    }

    private function emptyProcessingBreakdown(bool $hasIntakeRecords): array
    {
        return [
            'records' => 0,
            'intake_records' => 0,
            'total_quantity' => 0,
            'transferred_quantity' => 0,
            'reuse_quantity' => 0,
            'scrap_quantity' => 0,
            'remaining_quantity' => 0,
            'total_weight' => 0.0,
            'transferred_weight' => 0.0,
            'reuse_weight' => 0.0,
            'scrap_weight' => 0.0,
            'remaining_weight' => 0.0,
            'assigned_quantity' => 0,
            'assigned_weight' => 0.0,
            'pallet_total_quantity' => 0,
            'pallet_total_weight' => 0.0,
            'has_intake_records' => $hasIntakeRecords,
            'sources' => [],
        ];
    }

    private function normalizeProcessingBreakdown(array $breakdown, bool $hasIntakeRecords): array
    {
        $hasExplicitRemainingQuantity = array_key_exists('remaining_quantity', $breakdown)
            && $breakdown['remaining_quantity'] !== null
            && $breakdown['remaining_quantity'] !== '';
        $hasExplicitRemainingWeight = array_key_exists('remaining_weight', $breakdown)
            && $breakdown['remaining_weight'] !== null
            && $breakdown['remaining_weight'] !== '';

        $breakdown = array_merge($this->emptyProcessingBreakdown($hasIntakeRecords), $breakdown);
        $breakdown['records'] = (int) $breakdown['records'];
        $breakdown['intake_records'] = (int) ($breakdown['intake_records'] ?: $breakdown['records']);
        $breakdown['total_quantity'] = max(0, (int) $breakdown['total_quantity']);
        $breakdown['transferred_quantity'] = max(0, (int) ($breakdown['transferred_quantity'] ?? 0));
        $breakdown['reuse_quantity'] = max(0, (int) $breakdown['reuse_quantity']);
        $breakdown['scrap_quantity'] = max(0, (int) $breakdown['scrap_quantity']);
        $breakdown['total_weight'] = max(0.0, (float) $breakdown['total_weight']);
        $breakdown['transferred_weight'] = max(0.0, (float) ($breakdown['transferred_weight'] ?? 0));
        $breakdown['reuse_weight'] = max(0.0, (float) $breakdown['reuse_weight']);
        $breakdown['scrap_weight'] = max(0.0, (float) $breakdown['scrap_weight']);
        $breakdown['remaining_quantity'] = $hasExplicitRemainingQuantity
            ? max(0, (int) $breakdown['remaining_quantity'])
            : max(0, $breakdown['total_quantity'] - $breakdown['transferred_quantity'] - $breakdown['reuse_quantity'] - $breakdown['scrap_quantity']);
        $breakdown['remaining_weight'] = $hasExplicitRemainingWeight
            ? max(0.0, (float) $breakdown['remaining_weight'])
            : max(0.0, $breakdown['total_weight'] - $breakdown['transferred_weight'] - $breakdown['reuse_weight'] - $breakdown['scrap_weight']);
        $breakdown['assigned_quantity'] = max(0, (int) ($breakdown['assigned_quantity'] ?? 0));
        $breakdown['assigned_weight'] = max(0.0, (float) ($breakdown['assigned_weight'] ?? 0));
        $breakdown['pallet_total_quantity'] = max(0, (int) ($breakdown['pallet_total_quantity'] ?? $breakdown['total_quantity']));
        $breakdown['pallet_total_weight'] = max(0.0, (float) ($breakdown['pallet_total_weight'] ?? $breakdown['total_weight']));
        $breakdown['estimated_count'] = $breakdown['total_quantity'];
        $breakdown['net_items'] = $breakdown['remaining_quantity'];
        $breakdown['gross_weight'] = $breakdown['total_weight'];
        $breakdown['tare_weight'] = 0.0;
        $breakdown['net_weight'] = $breakdown['remaining_weight'];
        $breakdown['has_intake_records'] = $hasIntakeRecords;

        return $breakdown;
    }

    private function normalizePhotos($photos)
    {
        if (is_string($photos)) {
            $photos = json_decode($photos, true) ?: [];
        }

        return is_array($photos) ? array_values(array_filter($photos)) : [];
    }

    private function collectAllPhotosForBarcodes(array $barcodes): array
    {
        $barcodes = array_values(array_unique(array_filter($barcodes)));
        if (empty($barcodes)) {
            return [];
        }

        $photosByBarcode = array_fill_keys($barcodes, []);

        $pallets = Pallet::whereIn('barcode_number', $barcodes)->whereNotNull('photos_paths')->get(['id', 'barcode_number', 'photos_paths']);
        foreach ($pallets as $p) {
            $bc = trim((string) $p->barcode_number);
            if (isset($photosByBarcode[$bc])) {
                foreach ($this->normalizePhotos($p->photos_paths) as $url) {
                    if (!in_array($url, $photosByBarcode[$bc], true)) {
                        $photosByBarcode[$bc][] = $url;
                    }
                }
            }
        }

        $tables = [
            'ecommerce_items',
            'refurbishing_items',
            'universal_waste_items',
            'data_destruction_items',
            'it_assets_items',
        ];

        foreach ($tables as $table) {
            if (!$this->hasTable($table)) {
                continue;
            }

            try {
                $items = DB::table($table)
                    ->select('pallet_number', 'photos_paths')
                    ->whereNotNull('photos_paths')
                    ->whereIn('pallet_number', $barcodes)
                    ->get();

                foreach ($items as $item) {
                    $bc = trim((string) $item->pallet_number);
                    if ($bc !== '' && isset($photosByBarcode[$bc])) {
                        foreach ($this->normalizePhotos($item->photos_paths) as $url) {
                            if (!in_array($url, $photosByBarcode[$bc], true)) {
                                $photosByBarcode[$bc][] = $url;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore missing table or column error
            }
        }

        return $photosByBarcode;
    }

    private function collectAllPhotosForBarcode(string $barcodeNumber): array
    {
        $allPhotos = [];

        $pallets = Pallet::where('barcode_number', $barcodeNumber)->whereNotNull('photos_paths')->get(['photos_paths']);
        foreach ($pallets as $p) {
            foreach ($this->normalizePhotos($p->photos_paths) as $url) {
                if (!in_array($url, $allPhotos, true)) {
                    $allPhotos[] = $url;
                }
            }
        }

        $tables = [
            'ecommerce_items',
            'refurbishing_items',
            'universal_waste_items',
            'data_destruction_items',
            'it_assets_items',
        ];

        foreach ($tables as $table) {
            if (!$this->hasTable($table)) {
                continue;
            }

            try {
                $items = DB::table($table)
                    ->select('photos_paths')
                    ->whereNotNull('photos_paths')
                    ->where('pallet_number', $barcodeNumber)
                    ->get();

                foreach ($items as $item) {
                    foreach ($this->normalizePhotos($item->photos_paths) as $url) {
                        if (!in_array($url, $allPhotos, true)) {
                            $allPhotos[] = $url;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore missing table/column
            }
        }

        return $allPhotos;
    }

    private function savePalletPhotos($barcode, $photos)
    {
        $photoPaths = [];

        if (empty($photos)) {
            return $photoPaths;
        }

        // Flatten any nested arrays/structures of UploadedFile objects
        $flattened = [];
        $arrayIter = is_array($photos) ? $photos : [$photos];
        array_walk_recursive($arrayIter, function ($item) use (&$flattened) {
            if ($item instanceof \Illuminate\Http\UploadedFile) {
                $flattened[] = $item;
            }
        });

        if (empty($flattened)) {
            return $photoPaths;
        }

        foreach ($flattened as $photo) {
            if (!$photo->isValid()) {
                continue;
            }

            try {
                $photoPaths[] = $this->convertPhotoToBase64($photo);
            } catch (\Throwable $e) {
                \Log::error('Processing pallet photo base64 conversion failed.', [
                    'barcode' => $barcode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $photoPaths;
    }

    /**
     * Convert an uploaded photo into an optimized Base64 data URI string.
     */
    private function convertPhotoToBase64(\Illuminate\Http\UploadedFile $photo): string
    {
        $realPath = $photo->getRealPath();
        $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType() ?: 'image/jpeg';

        try {
            if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                $rawContent = file_get_contents($realPath);
                $img = @imagecreatefromstring($rawContent);

                if ($img !== false) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $maxDimension = 1600;

                    if ($width > $maxDimension || $height > $maxDimension) {
                        if ($width >= $height) {
                            $newWidth = $maxDimension;
                            $newHeight = (int) round(($height / $width) * $maxDimension);
                        } else {
                            $newHeight = $maxDimension;
                            $newWidth = (int) round(($width / $height) * $maxDimension);
                        }

                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($img);
                        $img = $resized;
                    }

                    ob_start();
                    if ($mimeType === 'image/png') {
                        imagepng($img, null, 6);
                    } elseif ($mimeType === 'image/webp') {
                        imagewebp($img, null, 80);
                    } else {
                        imagejpeg($img, null, 82);
                        $mimeType = 'image/jpeg';
                    }
                    $compressedData = ob_get_clean();
                    imagedestroy($img);

                    if (!empty($compressedData)) {
                        return 'data:' . $mimeType . ';base64,' . base64_encode($compressedData);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Photo optimization failed, falling back to raw base64: ' . $e->getMessage());
        }

        $base64Data = base64_encode(file_get_contents($realPath));
        return 'data:' . $mimeType . ';base64,' . $base64Data;
    }

    /**
     * Create a lightweight low-resolution Base64 data URI thumbnail for table listings.
     */
    private function createThumbnailFromDataUri(string $dataUri, int $maxDim = 120): string
    {
        if (empty($dataUri) || !str_starts_with($dataUri, 'data:image/')) {
            return $dataUri;
        }

        try {
            $parts = explode(',', $dataUri, 2);
            if (count($parts) < 2) {
                return $dataUri;
            }

            $rawData = base64_decode($parts[1]);
            if (!$rawData) {
                return $dataUri;
            }

            if (!function_exists('imagecreatefromstring') || !function_exists('imagejpeg')) {
                return $dataUri;
            }

            $img = @imagecreatefromstring($rawData);
            if (!$img) {
                return $dataUri;
            }

            $width = imagesx($img);
            $height = imagesy($img);

            if ($width <= $maxDim && $height <= $maxDim) {
                imagedestroy($img);
                return $dataUri;
            }

            if ($width >= $height) {
                $newWidth = $maxDim;
                $newHeight = (int) round(($height / $width) * $maxDim);
            } else {
                $newHeight = $maxDim;
                $newWidth = (int) round(($width / $height) * $maxDim);
            }

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($img);

            ob_start();
            imagejpeg($resized, null, 70);
            $compressedData = ob_get_clean();
            imagedestroy($resized);

            if (!empty($compressedData)) {
                return 'data:image/jpeg;base64,' . base64_encode($compressedData);
            }
        } catch (\Throwable $e) {
            // Fallback to original
        }

        return $dataUri;
    }

    private function deletePalletPhotoFile($path)
    {
        $normalizedPath = ltrim(str_replace('\\', '/', (string) $path), '/');

        if (strpos($normalizedPath, 'pallets/') !== 0) {
            return;
        }

        $absolutePath = public_path($normalizedPath);
        $palletRoot = realpath(public_path('pallets'));
        $targetPath = realpath($absolutePath);

        if ($palletRoot && $targetPath && strpos($targetPath, $palletRoot . DIRECTORY_SEPARATOR) === 0 && is_file($targetPath)) {
            @unlink($targetPath);
        }
    }

    public function exportCsv(Request $request)
    {
        $query = Pallet::with('client');

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('barcode_number', 'like', "%{$search}%")
                    ->orWhere('job_no', 'like', "%{$search}%")
                    ->orWhere('put_away_location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('company', 'like', "%{$search}%");
                    });

                try {
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
                        $q->orWhereDate('created_at', $search);
                    } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $search)) {
                        $dateObj = \Carbon\Carbon::createFromFormat('m/d/Y', $search);
                        $q->orWhereDate('created_at', $dateObj->format('Y-m-d'));
                    } elseif (strtotime($search) !== false && preg_match('/[a-zA-Z]/', $search)) {
                        $dateObj = \Carbon\Carbon::parse($search);
                        $q->orWhereDate('created_at', $dateObj->format('Y-m-d'));
                    }
                } catch (\Throwable $e) {
                    // Ignore parse errors
                }
            });
        }

        if ($request->has('statusFilter') && !empty($request->statusFilter) && $request->statusFilter !== 'All Statuses') {
            $query->where('status', $request->statusFilter);
        }

        if ($request->has('dateFilter') && !empty($request->dateFilter)) {
            $query->whereDate('created_at', $request->dateFilter);
        }

        $pallets = $query->latest()->get();

        $fileName = 'Processing_Intake_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Barcode #', 'Job No', 'Client', 'Company', 'Status',
            'Total Items', 'Remaining Items', 'Transferred Items', 'Reuse Items', 'Scrap Items',
            'Total Weight (lbs)', 'Remaining Weight (lbs)', 'Transferred Weight (lbs)', 'Reuse Weight (lbs)', 'Scrap Weight (lbs)',
            'Put Away Location', 'Marketing Reference', 'Receiving Employee', 'Processing Employee', 'Notes', 'Created At'
        ];

        $aggregates = $this->intakeAggregatesForBarcodes($pallets->pluck('barcode_number')->all());

        $callback = function () use ($pallets, $columns, $aggregates) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($pallets as $pallet) {
                $breakdown = $this->processingBreakdown($pallet, $this->intakeAggregateForPallet($aggregates, $pallet));
                fputcsv($file, [
                    $pallet->barcode_number,
                    $pallet->job_no ?: 'N/A',
                    $pallet->client ? $pallet->client->name : 'N/A',
                    $pallet->client ? ($pallet->client->company ?: 'N/A') : 'N/A',
                    $pallet->status,
                    $breakdown['total_quantity'],
                    $breakdown['remaining_quantity'],
                    $breakdown['transferred_quantity'],
                    $breakdown['reuse_quantity'],
                    $breakdown['scrap_quantity'],
                    number_format($breakdown['total_weight'], 2),
                    number_format($breakdown['remaining_weight'], 2),
                    number_format($breakdown['transferred_weight'], 2),
                    number_format($breakdown['reuse_weight'], 2),
                    number_format($breakdown['scrap_weight'], 2),
                    $pallet->put_away_location ?: 'N/A',
                    $pallet->marketing_reference ?: 'N/A',
                    $pallet->receiving_employee ?: 'N/A',
                    $pallet->processing_employee ?: 'N/A',
                    $pallet->notes ?: '',
                    $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private static array $knownTables = [
        'ecommerce_items' => true,
        'refurbishing_items' => true,
        'universal_waste_items' => true,
        'data_destruction_items' => true,
        'it_assets_items' => true,
        'pallets' => true,
    ];

    private static array $schemaCache = [
        'col_ecommerce_items_pallet_id' => true,
        'col_refurbishing_items_pallet_id' => true,
        'col_universal_waste_items_pallet_id' => true,
        'col_data_destruction_items_pallet_id' => true,
        'col_it_assets_items_pallet_id' => true,
    ];

    private function hasTable(string $table): bool
    {
        return self::$knownTables[$table] ?? (self::$schemaCache["table_{$table}"] ??= DB::getSchemaBuilder()->hasTable($table));
    }

    private function hasColumn(string $table, string $column): bool
    {
        $key = "col_{$table}_{$column}";
        return self::$schemaCache[$key] ?? (self::$schemaCache[$key] = DB::getSchemaBuilder()->hasColumn($table, $column));
    }

    private function sendCompletionEmail(Pallet $pallet): void
    {
        app(\App\Services\PalletCompletionService::class)->sendCompletionEmail($pallet);
    }
}

