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

        // Fetch aggregates to calculate remaining items/weight
        $aggregates = $this->intakeAggregatesForBarcodes($pallets->pluck('barcode_number')->all());

        // Process pallets to inject stats
        $processedPallets = collect($pallets->items())->map(function ($pallet) use ($aggregates) {
            $breakdown = $this->processingBreakdown($pallet, $this->intakeAggregateForPallet($aggregates, $pallet));
            $pallet->remaining_quantity = $breakdown['remaining_quantity'];
            $pallet->remaining_weight = $breakdown['remaining_weight'];
            $pallet->intake_breakdown = $breakdown;
            return $pallet;
        });

        // Paginate manually since we mapped the items, or override items in the paginator
        $pallets->setCollection($processedPallets);

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

        // Fetch all items from ecommerce, refurbishing, universal waste, and data destruction
        $barcode = trim($pallet->barcode_number);

        $ecommerceItems = DB::table('ecommerce_items')->where('pallet_number', $barcode)->get();
        $refurbishingItems = DB::table('refurbishing_items')->where('pallet_number', $barcode)->get();
        $universalWasteItems = DB::table('universal_waste_items')->where('pallet_number', $barcode)->get();
        $dataDestructionItems = DB::table('data_destruction_items')->where('pallet_number', $barcode)->get();

        $aggregate = $this->intakeAggregateForPallet(
            $this->intakeAggregatesForBarcodes([$pallet->barcode_number]),
            $pallet
        );
        $breakdown = $this->processingBreakdown($pallet, $aggregate);

        return view('user.received-intake.show', [
            'pallet' => $pallet,
            'relatedPallets' => $relatedPallets,
            'breakdown' => $breakdown,
            'ecommerceItems' => $ecommerceItems,
            'refurbishingItems' => $refurbishingItems,
            'universalWasteItems' => $universalWasteItems,
            'dataDestructionItems' => $dataDestructionItems,
        ]);
    }

    private function getEmptyStats(): array
    {
        return [
            'totalPallets' => 0,
            'processingCount' => 0,
            'completedCount' => 0,
            'totalItems' => 0,
            'totalWeight' => 0.0,
        ];
    }

    private function calculateStats(array $clientIds): array
    {
        $pallets = Pallet::whereIn('client_id', $clientIds)->get();
        $aggregates = $this->intakeAggregatesForBarcodes($pallets->pluck('barcode_number')->all());

        $totalItems = 0;
        $totalWeight = 0.0;

        foreach ($pallets as $pallet) {
            $breakdown = $this->processingBreakdown($pallet, $this->intakeAggregateForPallet($aggregates, $pallet));
            $totalItems += $breakdown['remaining_quantity'];
            $totalWeight += $breakdown['remaining_weight'];
        }

        return [
            'totalPallets' => $pallets->count(),
            'processingCount' => $pallets->where('status', 'Processing')->count(),
            'completedCount' => $pallets->where('status', 'Completed')->count(),
            'totalItems' => $totalItems,
            'totalWeight' => $totalWeight,
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

    private function intakeAggregateForPallet(array $aggregates, Pallet $pallet): ?array
    {
        $barcode = trim((string) ($pallet->barcode_number ?? ''));
        return $barcode === '' ? null : ($aggregates[$barcode] ?? null);
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
}
