<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivedIntakeController extends Controller
{
    /**
     * Display a listing of received intakes (pallets) for the user.
     */
    /**
     * Display a listing of received intakes (pallets) for the user.
     */
    public function index()
    {
        $user = Auth::user();
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();
        $client = Client::whereIn('id', $clientIds)->first();

        if (empty($clientIds)) {
            return view('user.received-intake.index', [
                'pallets' => Pallet::whereRaw('1 = 0')->paginate(10),
                'client' => null,
                'stats' => $this->getEmptyStats(),
            ]);
        }

        $pallets = Pallet::whereIn('client_id', $clientIds)
            ->latest()
            ->paginate(10);

        // Fetch aggregates to calculate remaining items/weight specific to each pallet
        $aggregates = $this->intakeAggregatesForPallets($pallets);

        // Process pallets to inject stats
        $processedPallets = collect($pallets->items())->map(function ($pallet) use ($aggregates) {
            $breakdown = $this->processingBreakdown($pallet, $aggregates[$pallet->id] ?? null);
            $pallet->remaining_quantity = $breakdown['remaining_quantity'];
            $pallet->remaining_weight = $breakdown['remaining_weight'];
            $pallet->intake_breakdown = $breakdown;
            return $pallet;
        });

        // Paginate manually since we mapped the items, or override items in the paginator
        $pallets->setCollection($processedPallets);

        // Attach sub-pallet index (#1, #2, #3...) for barcodes split into multiple pallets
        $this->attachSubPalletIndex($pallets);

        // Calculate stats for all pallets of this client
        $stats = $this->calculateStats($clientIds);

        return view('user.received-intake.index', [
            'pallets' => $pallets,
            'client' => $client,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the details of a specific received intake (pallet).
     */
    /**
     * Display the details of a specific received intake (pallet).
     */
    public function show($id)
    {
        $user = Auth::user();
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();
        $client = Client::whereIn('id', $clientIds)->first();

        if (empty($clientIds)) {
            abort(403, 'Unauthorized access or client profile not found.');
        }

        $pallet = Pallet::whereIn('client_id', $clientIds)->findOrFail($id);

        // Fetch all related pallets (items received under the same barcode)
        $relatedPallets = Pallet::where('barcode_number', $pallet->barcode_number)
            ->whereIn('client_id', $clientIds)
            ->orderBy('id')
            ->get();

        $this->attachSubPalletIndex($relatedPallets);
        $this->attachSubPalletIndex($pallet);

        // Fetch all items from ecommerce, refurbishing, universal waste, data destruction, IT assets
        $barcode = trim($pallet->barcode_number);

        $allEcommerce = DB::table('ecommerce_items')->where('pallet_number', $barcode)->whereNull('deleted_at')->get();
        $allRefurbishing = DB::table('refurbishing_items')->where('pallet_number', $barcode)->whereNull('deleted_at')->get();
        $allUniversal = DB::table('universal_waste_items')->where('pallet_number', $barcode)->whereNull('deleted_at')->get();
        $allDestruction = \App\Models\DataDestructionItem::where('pallet_number', $barcode)->get();
        $allItAssets = \App\Models\ItAssetsItem::where('pallet_number', $barcode)->get();

        // Build pallet lookup map for tagging items with sub-pallet names
        $palletMap = $relatedPallets->keyBy('id');
        $palletIdSet = $relatedPallets->pluck('id')->all();

        // Filter items SPECIFIC to the opened pallet
        $currentPid = $pallet->id;
        $isFirstPallet = ($relatedPallets->first() && $relatedPallets->first()->id === $currentPid);

        $currentPalletFilter = function ($item) use ($currentPid, $isFirstPallet, $palletIdSet) {
            $itemPid = (int) ($item->pallet_id ?? 0);
            if ($itemPid === $currentPid) {
                return true;
            }
            if ($isFirstPallet && ($itemPid === 0 || !in_array($itemPid, $palletIdSet))) {
                return true;
            }
            return false;
        };

        $ecommerceItems = $allEcommerce->filter($currentPalletFilter)->values();
        $refurbishingItems = $allRefurbishing->filter($currentPalletFilter)->values();
        $universalWasteItems = $allUniversal->filter($currentPalletFilter)->values();
        $dataDestructionItems = $allDestruction->filter($currentPalletFilter)->values();
        $itAssetsItems = $allItAssets->filter($currentPalletFilter)->values();

        $pallet->total_processed_items_count = $ecommerceItems->count()
            + $refurbishingItems->count()
            + $universalWasteItems->count()
            + $dataDestructionItems->count()
            + $itAssetsItems->count();

        $pallet->rel_ecommerce = $ecommerceItems;
        $pallet->rel_refurbishing = $refurbishingItems;
        $pallet->rel_universal = $universalWasteItems;
        $pallet->rel_destruction = $dataDestructionItems;
        $pallet->rel_itassets = $itAssetsItems;

        // Tag items with sub-pallet display names for reference
        $tagItemWithPallet = function ($item) use ($palletMap) {
            $pid = (int) ($item->pallet_id ?? 0);
            if ($pid > 0 && isset($palletMap[$pid])) {
                $item->pallet_display_name = $palletMap[$pid]->display_barcode;
            } else {
                $item->pallet_display_name = null;
            }
            return $item;
        };

        $ecommerceItems->transform($tagItemWithPallet);
        $refurbishingItems->transform($tagItemWithPallet);
        $universalWasteItems->transform($tagItemWithPallet);
        $dataDestructionItems->transform($tagItemWithPallet);
        $itAssetsItems->transform($tagItemWithPallet);

        $aggregates = $this->intakeAggregatesForPallets(collect([$pallet]));
        $breakdown = $this->processingBreakdown($pallet, $aggregates[$pallet->id] ?? null);

        return view('user.received-intake.show', [
            'pallet' => $pallet,
            'relatedPallets' => $relatedPallets,
            'breakdown' => $breakdown,
            'ecommerceItems' => $ecommerceItems,
            'refurbishingItems' => $refurbishingItems,
            'universalWasteItems' => $universalWasteItems,
            'dataDestructionItems' => $dataDestructionItems,
            'itAssetsItems' => $itAssetsItems,
        ]);
    }

    private function getEmptyStats(): array
    {
        return [
            'totalPallets' => 0,
            'receivedPalletsCount' => 0,
            'receivedItemsCount' => 0,
            'processingPalletsCount' => 0,
            'processingItemsCount' => 0,
            'completedPalletsCount' => 0,
            'completedItemsCount' => 0,
        ];
    }

    private function calculateStats(array $clientIds): array
    {
        $pallets = Pallet::whereIn('client_id', $clientIds)->get();

        $receivedPallets = $pallets->filter(function ($p) {
            $st = strtolower(trim($p->status ?: 'received'));
            return $st === 'received' || $st === 'pending' || $st === '';
        });

        $processingPallets = $pallets->filter(function ($p) {
            $st = strtolower(trim($p->status ?: ''));
            return str_contains($st, 'progr') || str_contains($st, 'process');
        });

        $completedPallets = $pallets->filter(function ($p) {
            $st = strtolower(trim($p->status ?: ''));
            return str_contains($st, 'complet') || str_contains($st, 'ready') || str_contains($st, 'recycl');
        });

        $receivedItems = $receivedPallets->sum(function ($p) {
            return max(0, (int) ($p->estimated_count ?? 0));
        });

        $processingItems = $processingPallets->sum(function ($p) {
            return max(0, (int) ($p->estimated_count ?? 0));
        });

        $completedItems = $completedPallets->sum(function ($p) {
            return max(0, (int) ($p->estimated_count ?? 0));
        });

        return [
            'totalPallets' => $pallets->count(),
            'receivedPalletsCount' => $receivedPallets->count(),
            'receivedItemsCount' => $receivedItems,
            'processingPalletsCount' => $processingPallets->count(),
            'processingItemsCount' => $processingItems,
            'completedPalletsCount' => $completedPallets->count(),
            'completedItemsCount' => $completedItems,
        ];
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
            $isUniversalWaste = ($table === 'universal_waste_items');

            $rows = DB::table($table)
                ->selectRaw('TRIM(pallet_number) as pallet_number')
                ->selectRaw('COUNT(*) as records')
                ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(reuse_quantity), 0) as reuse_quantity' 
                    : 'COALESCE(SUM(CASE WHEN reuse_quantity > 0 THEN reuse_quantity ELSE quantity END), 0) as reuse_quantity')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(CASE WHEN scrap_quantity > 0 THEN scrap_quantity ELSE quantity END), 0) as scrap_quantity' 
                    : 'COALESCE(SUM(scrap_quantity), 0) as scrap_quantity')
                ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(reuse_weight), 0) as reuse_weight' 
                    : 'COALESCE(SUM(CASE WHEN reuse_weight > 0 THEN reuse_weight ELSE weight END), 0) as reuse_weight')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(CASE WHEN scrap_weight > 0 THEN scrap_weight ELSE weight END), 0) as scrap_weight' 
                    : 'COALESCE(SUM(scrap_weight), 0) as scrap_weight')
                ->whereNotNull('pallet_number')
                ->whereNull('deleted_at')
                ->whereIn(DB::raw('TRIM(pallet_number)'), $barcodes)
                ->groupByRaw('TRIM(pallet_number)')
                ->get();

            foreach ($rows as $row) {
                $barcode = (string) $row->pallet_number;
                if (!isset($aggregates[$barcode])) {
                    $aggregates[$barcode] = $this->emptyProcessingBreakdown(true);
                }

                $sourceBreakdown = $this->normalizeProcessingBreakdown([
                    'records' => (int) $row->records,
                    'total_quantity' => (int) $row->total_quantity,
                    'reuse_quantity' => (int) $row->reuse_quantity,
                    'scrap_quantity' => (int) $row->scrap_quantity,
                    'total_weight' => (float) $row->total_weight,
                    'reuse_weight' => (float) $row->reuse_weight,
                    'scrap_weight' => (float) $row->scrap_weight,
                    'source_label' => $source['label'],
                ], true);

                foreach (['records', 'total_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'reuse_weight', 'scrap_weight'] as $key) {
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

    private function attachSubPalletIndex($pallets)
    {
        $collection = $pallets instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $pallets->getCollection()
            : collect($pallets);

        if ($collection->isEmpty()) {
            return $pallets;
        }

        $barcodes = $collection->pluck('barcode_number')
            ->filter(fn ($b) => is_string($b) && trim($b) !== '')
            ->map(fn ($b) => trim(strtolower($b)))
            ->unique()
            ->values()
            ->all();

        if (empty($barcodes)) {
            return $pallets;
        }

        $allPallets = Pallet::whereIn(DB::raw('TRIM(LOWER(barcode_number))'), $barcodes)
            ->orderBy('id', 'asc')
            ->get(['id', 'barcode_number']);

        $allPalletGroup = $allPallets->groupBy(function ($p) {
            return trim(strtolower($p->barcode_number));
        });

        $collection->transform(function ($pallet) use ($allPalletGroup) {
            $key = trim(strtolower($pallet->barcode_number));
            $group = $allPalletGroup->get($key, collect());
            if ($group->count() > 1) {
                $palletIds = $group->pluck('id')->all();
                $idx = array_search($pallet->id, $palletIds);
                if ($idx !== false) {
                    $pallet->sub_pallet_index = $idx + 1;
                    $pallet->display_barcode = trim($pallet->barcode_number) . ' (#' . ($idx + 1) . ')';
                } else {
                    $pallet->sub_pallet_index = null;
                    $pallet->display_barcode = trim($pallet->barcode_number);
                }
            } else {
                $pallet->sub_pallet_index = null;
                $pallet->display_barcode = trim($pallet->barcode_number);
            }
            return $pallet;
        });

        if ($pallets instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $pallets->setCollection($collection);
            return $pallets;
        }

        return $collection;
    }

    private function intakeAggregatesForPallets($pallets): array
    {
        $collection = $pallets instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $pallets->getCollection()
            : collect($pallets);

        if ($collection->isEmpty()) {
            return [];
        }

        $palletIds = $collection->pluck('id')->all();
        $barcodes = $collection->pluck('barcode_number')->filter()->unique()->all();

        if (empty($palletIds) && empty($barcodes)) {
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
            $isUniversalWaste = ($table === 'universal_waste_items');

            $rows = DB::table($table)
                ->selectRaw('COALESCE(pallet_id, 0) as pallet_id')
                ->selectRaw('TRIM(pallet_number) as pallet_number')
                ->selectRaw('COUNT(*) as records')
                ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(reuse_quantity), 0) as reuse_quantity' 
                    : 'COALESCE(SUM(CASE WHEN reuse_quantity > 0 THEN reuse_quantity ELSE quantity END), 0) as reuse_quantity')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(CASE WHEN scrap_quantity > 0 THEN scrap_quantity ELSE quantity END), 0) as scrap_quantity' 
                    : 'COALESCE(SUM(scrap_quantity), 0) as scrap_quantity')
                ->selectRaw('COALESCE(SUM(weight), 0) as total_weight')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(reuse_weight), 0) as reuse_weight' 
                    : 'COALESCE(SUM(CASE WHEN reuse_weight > 0 THEN reuse_weight ELSE weight END), 0) as reuse_weight')
                ->selectRaw($isUniversalWaste 
                    ? 'COALESCE(SUM(CASE WHEN scrap_weight > 0 THEN scrap_weight ELSE weight END), 0) as scrap_weight' 
                    : 'COALESCE(SUM(scrap_weight), 0) as scrap_weight')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($palletIds, $barcodes) {
                    if (!empty($palletIds)) {
                        $q->whereIn('pallet_id', $palletIds);
                    }
                    if (!empty($barcodes)) {
                        $q->orWhereIn(DB::raw('TRIM(pallet_number)'), $barcodes);
                    }
                })
                ->groupByRaw('COALESCE(pallet_id, 0), TRIM(pallet_number)')
                ->get();

            foreach ($rows as $row) {
                $pid = (int) $row->pallet_id;
                $barcode = (string) $row->pallet_number;

                foreach ($collection as $pallet) {
                    $matches = false;
                    if ($pid > 0 && $pallet->id == $pid) {
                        $matches = true;
                    } elseif ($pid == 0 && trim($pallet->barcode_number) === $barcode) {
                        $matches = true;
                    }

                    if ($matches) {
                        $key = $pallet->id;
                        if (!isset($aggregates[$key])) {
                            $aggregates[$key] = $this->emptyProcessingBreakdown(true);
                        }

                        $sourceBreakdown = $this->normalizeProcessingBreakdown([
                            'records' => (int) $row->records,
                            'total_quantity' => (int) $row->total_quantity,
                            'reuse_quantity' => (int) $row->reuse_quantity,
                            'scrap_quantity' => (int) $row->scrap_quantity,
                            'total_weight' => (float) $row->total_weight,
                            'reuse_weight' => (float) $row->reuse_weight,
                            'scrap_weight' => (float) $row->scrap_weight,
                            'source_label' => $source['label'],
                        ], true);

                        foreach (['records', 'total_quantity', 'reuse_quantity', 'scrap_quantity', 'total_weight', 'reuse_weight', 'scrap_weight'] as $k) {
                            $aggregates[$key][$k] += $sourceBreakdown[$k];
                        }

                        $aggregates[$key]['sources'][] = $sourceBreakdown;
                    }
                }
            }
        }

        foreach ($aggregates as $key => $aggregate) {
            $aggregates[$key] = $this->normalizeProcessingBreakdown($aggregate, true);
        }

        return $aggregates;
    }

    private function intakeAggregateForPallet(array $aggregates, Pallet $pallet): ?array
    {
        return $aggregates[$pallet->id] ?? null;
    }

    private function processingBreakdown(Pallet $pallet, ?array $intakeAggregate = null): array
    {
        if ($intakeAggregate && ($intakeAggregate['records'] ?? 0) > 0) {
            $base = $this->palletBaseBreakdown($pallet);
            $processingScrapQuantity = max(0, (int) ($pallet->reuse_quantity ?? 0));
            $processingScrapWeight = max(0.0, (float) ($pallet->reuse_weight ?? 0));
            $assignedQuantity = max(0, (int) ($intakeAggregate['total_quantity'] ?? 0));
            $assignedWeight = max(0.0, (float) ($intakeAggregate['total_weight'] ?? 0));
            $availableQuantity = max(0, $base['total_quantity'] - $processingScrapQuantity - $assignedQuantity);
            $availableWeight = max(0.0, $base['total_weight'] - $processingScrapWeight - $assignedWeight);

            return $this->normalizeProcessingBreakdown([
                'records' => (int) ($intakeAggregate['records'] ?? 0),
                'intake_records' => (int) ($intakeAggregate['records'] ?? 0),
                'total_quantity' => $base['total_quantity'],
                'reuse_quantity' => (int) ($intakeAggregate['reuse_quantity'] ?? 0),
                'scrap_quantity' => (int) ($intakeAggregate['scrap_quantity'] ?? 0),
                'remaining_quantity' => $availableQuantity,
                'total_weight' => $base['total_weight'],
                'reuse_weight' => (float) ($intakeAggregate['reuse_weight'] ?? 0),
                'scrap_weight' => (float) ($intakeAggregate['scrap_weight'] ?? 0),
                'remaining_weight' => $availableWeight,
                'assigned_quantity' => $assignedQuantity,
                'assigned_weight' => $assignedWeight,
                'pallet_total_quantity' => $base['total_quantity'],
                'pallet_total_weight' => $base['total_weight'],
                'processing_scrap_quantity' => $processingScrapQuantity,
                'processing_scrap_weight' => $processingScrapWeight,
                'intake_scrap_quantity' => (int) ($intakeAggregate['scrap_quantity'] ?? 0),
                'intake_scrap_weight' => (float) ($intakeAggregate['scrap_weight'] ?? 0),
                'sources' => $intakeAggregate['sources'] ?? [],
            ], true);
        }

        return $this->palletFallbackBreakdown($pallet);
    }

    private function palletBaseBreakdown(Pallet $pallet): array
    {
        $grossWeight = max(0.0, (float) ($pallet->gross_weight ?? 0));
        $tareWeight = max(0.0, (float) ($pallet->tare_weight ?? 0));
        $scrapWeight = max(0.0, (float) ($pallet->reuse_weight ?? 0));
        $totalWeight = max(0.0, $grossWeight - $tareWeight);
        $remainingWeight = max(0.0, (float) ($pallet->net_weight ?? 0));

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
        $remainingWeight = max(0.0, (float) ($pallet->net_weight ?? 0));

        return $this->normalizeProcessingBreakdown([
            'records' => 0,
            'total_quantity' => $base['total_quantity'],
            'reuse_quantity' => 0,
            'scrap_quantity' => 0,
            'remaining_quantity' => max(0, (int) ($pallet->estimated_count ?? 0) - max(0, (int) ($pallet->reuse_quantity ?? 0))),
            'total_weight' => $base['total_weight'],
            'reuse_weight' => 0.0,
            'scrap_weight' => 0.0,
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
            'reuse_quantity' => 0,
            'scrap_quantity' => 0,
            'remaining_quantity' => 0,
            'total_weight' => 0.0,
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
        $breakdown['reuse_quantity'] = max(0, (int) $breakdown['reuse_quantity']);
        $breakdown['scrap_quantity'] = max(0, (int) $breakdown['scrap_quantity']);
        $breakdown['total_weight'] = max(0.0, (float) $breakdown['total_weight']);
        $breakdown['reuse_weight'] = max(0.0, (float) $breakdown['reuse_weight']);
        $breakdown['scrap_weight'] = max(0.0, (float) $breakdown['scrap_weight']);
        $breakdown['remaining_quantity'] = $hasExplicitRemainingQuantity
            ? max(0, (int) $breakdown['remaining_quantity'])
            : max(0, $breakdown['total_quantity'] - $breakdown['reuse_quantity'] - $breakdown['scrap_quantity']);
        $breakdown['remaining_weight'] = $hasExplicitRemainingWeight
            ? max(0.0, (float) $breakdown['remaining_weight'])
            : max(0.0, $breakdown['total_weight'] - $breakdown['reuse_weight'] - $breakdown['scrap_weight']);
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

    /**
     * Remove the specified pallet (received intake) from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();

        if (empty($clientIds)) {
            abort(403, 'Unauthorized access.');
        }

        $pallet = Pallet::whereIn('client_id', $clientIds)->findOrFail($id);
        $barcode = $pallet->barcode_number;
        $pallet->delete();

        return redirect()->route('user.received-intake.index')
            ->with('success', 'Received intake pallet #' . $barcode . ' has been moved to trash successfully.');
    }

    /**
     * Remove multiple specified pallets from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();

        if (empty($clientIds)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = $request->input('ids', []);
        $deletedCount = Pallet::whereIn('client_id', $clientIds)
            ->whereIn('id', $ids)
            ->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' received intake pallet(s) moved to trash successfully.',
                'deleted_count' => $deletedCount,
            ]);
        }

        return redirect()->route('user.received-intake.index')
            ->with('success', $deletedCount . ' received intake pallet(s) moved to trash successfully.');
    }
}

