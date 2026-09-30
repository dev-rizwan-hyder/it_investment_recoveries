<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\Client;
use App\Models\DataDestructionItem;
use App\Models\ItAssetsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EsgReportController extends Controller
{
    // Standard Emission Factors matching it_investment ReportController
    protected $EMISSION_FACTORS = [
        'laptop' => 4.5,
        'desktop' => 5.2,
        'monitor' => 3.8,
        'server' => 8.0,
        'default' => 2.5
    ];

    /**
     * Display ESG Environmental Impact & Sustainability Report for the user.
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
            return view('user.esg-report.index', [
                'client' => null,
                'items' => collect(),
                'results' => $this->getEmptyResults(),
                'noClientLinked' => true,
                'userEmail' => $user->email,
            ]);
        }

        $pallets = Pallet::whereIn('client_id', $clientIds)->get();
        $palletNumbers = $pallets->pluck('barcode_number')->filter()->unique()->all();

        $tables = [
            'ecommerce_items',
            'refurbishing_items',
            'universal_waste_items',
            'data_destruction_items',
            'it_assets_items',
        ];

        $allItems = collect();

        if (!empty($palletNumbers)) {
            foreach ($tables as $tableName) {
                try {
                    $items = DB::table($tableName)
                        ->whereIn('pallet_number', $palletNumbers)
                        ->where(function ($query) {
                            $query->whereRaw("LOWER(TRIM(status)) = ?", ['completed'])
                                  ->orWhereRaw("LOWER(TRIM(status)) LIKE ?", ['%complet%']);
                        })
                        ->get();
                    $allItems = $allItems->concat($items);
                } catch (\Throwable $e) {
                    // Ignore table query errors if optional table missing
                }
            }
        }

        // Map client details onto items if available
        $clientName = $client->name ?? 'Client Name';
        $company = $client->company ?? ($user->company ?? 'No Company Registered');
        $address = $client->address ?? 'Address not available';
        $email = $client->email ?? $user->email;
        $phone = $client->phone ?? '';

        $mappedItems = $allItems->map(function ($item) use ($clientName, $company, $address, $email, $phone) {
            $itemObj = (object) (array) $item;
            $itemObj->client_display_name = $clientName;
            $itemObj->company = $company;
            $itemObj->address = $address;
            $itemObj->email = $email;
            $itemObj->phone = $phone;
            $itemObj->category = !empty($itemObj->category) ? $itemObj->category : 'General Peripherals';
            $itemObj->quantity = isset($itemObj->quantity) && (int)$itemObj->quantity > 0 ? (int)$itemObj->quantity : 1;
            $itemObj->weight = isset($itemObj->weight) ? (float)$itemObj->weight : 0.0;
            return $itemObj;
        });

        $totalUnits = $mappedItems->sum('quantity');
        $totalWeightLbs = (float) $mappedItems->sum('weight');
        $co2AvoidedLbs = 0;

        foreach ($mappedItems as $item) {
            $factor = $this->getEmissionFactor($item->category);
            $co2AvoidedLbs += ((float) ($item->weight ?? 0) * $factor);
        }

        $results = [
            'totalUnits' => $totalUnits,
            'totalWeightLbs' => $totalWeightLbs,
            'co2AvoidedLbs' => $co2AvoidedLbs,
            'treesEquivalent' => round($co2AvoidedLbs / 48),
            'carsOffRoadDays' => round($co2AvoidedLbs / 24.6),
            'homesEnergyDays' => round($co2AvoidedLbs / 30),
            'waterSavedGallons' => round($totalWeightLbs * 0.5),
        ];

        return view('user.esg-report.index', [
            'client' => $client,
            'items' => $mappedItems,
            'results' => $results,
            'noClientLinked' => false,
            'userEmail' => $user->email,
        ]);
    }

    /**
     * Get emission factor based on category keyword match or fallback to default factor.
     */
    protected function getEmissionFactor(?string $category): float
    {
        if (!$category) {
            return $this->EMISSION_FACTORS['default'];
        }

        $catKey = strtolower($category);

        if (str_contains($catKey, 'laptop') || str_contains($catKey, 'notebook')) {
            return $this->EMISSION_FACTORS['laptop'];
        }
        if (str_contains($catKey, 'desktop') || str_contains($catKey, 'pc') || str_contains($catKey, 'computer')) {
            return $this->EMISSION_FACTORS['desktop'];
        }
        if (str_contains($catKey, 'monitor') || str_contains($catKey, 'display') || str_contains($catKey, 'screen')) {
            return $this->EMISSION_FACTORS['monitor'];
        }
        if (str_contains($catKey, 'server')) {
            return $this->EMISSION_FACTORS['server'];
        }

        return $this->EMISSION_FACTORS[$catKey] ?? $this->EMISSION_FACTORS['default'];
    }

    private function getEmptyResults(): array
    {
        return [
            'totalUnits' => 0,
            'totalWeightLbs' => 0.0,
            'co2AvoidedLbs' => 0.0,
            'treesEquivalent' => 0,
            'carsOffRoadDays' => 0,
            'homesEnergyDays' => 0,
            'waterSavedGallons' => 0,
        ];
    }
}

