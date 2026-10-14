<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EsgReportController extends Controller
{
    // Standard Emission Factors matching sustainability report standards
    protected $EMISSION_FACTORS = [
        'laptop' => 4.5,
        'desktop' => 5.2,
        'monitor' => 3.8,
        'server' => 8.0,
        'default' => 2.5
    ];

    /**
     * Display ESG Environmental Impact & Sustainability Report with 2-step pallet selection wizard.
     */
    public function index(Request $request)
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
                'pallets' => collect(),
                'items' => collect(),
                'noClientLinked' => true,
                'userEmail' => $user->email,
            ]);
        }

        $pallets = Pallet::whereIn('client_id', $clientIds)->latest()->get();
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
                        ->get();
                    $allItems = $allItems->concat($items);
                } catch (\Throwable $e) {
                    // Ignore missing table errors gracefully
                }
            }
        }

        // Map client metadata onto item records
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
            $itemObj->pallet_number = $itemObj->pallet_number ?? '';
            $itemObj->created_at_date = isset($itemObj->created_at) ? date('Y-m-d', strtotime($itemObj->created_at)) : '';
            return $itemObj;
        });

        // Format pallet list summary for client selection wizard: Group by barcode_number so each Pallet # appears only ONCE
        $groupedPallets = $pallets->groupBy(function ($item) {
            return trim(strtolower($item->barcode_number));
        });

        $palletsData = collect();

        foreach ($groupedPallets as $barcodeKey => $palletGroup) {
            $firstPallet = $palletGroup->first();
            $rawBarcode = $firstPallet->barcode_number;

            $palletItems = $mappedItems->where('pallet_number', $rawBarcode);
            $totalUnits = $palletItems->sum('quantity');
            $totalWeight = $palletItems->sum('weight');

            if ($totalUnits == 0) {
                $totalUnits = (int) $palletGroup->sum('estimated_count');
            }

            if ($totalWeight == 0) {
                $groupWeight = 0;
                foreach ($palletGroup as $p) {
                    $w = (float) $p->gross_weight - (float) ($p->tare_weight ?? 0);
                    if ($w <= 0) {
                        $w = (float) $p->gross_weight;
                    }
                    $groupWeight += $w;
                }
                $totalWeight = $groupWeight;
            }

            $palletsData->push([
                'id' => $firstPallet->id,
                'barcode' => $rawBarcode,
                'date' => $firstPallet->created_at ? $firstPallet->created_at->format('Y-m-d') : '',
                'formatted_date' => $firstPallet->created_at ? $firstPallet->created_at->format('M d, Y') : 'N/A',
                'status' => $firstPallet->status ?: 'Received',
                'location' => $firstPallet->put_away_location ?: 'Unassigned',
                'estimated_count' => (int) $palletGroup->sum('estimated_count'),
                'total_units' => $totalUnits,
                'total_weight' => round($totalWeight, 2),
                'items_count' => count($palletItems),
                'records_count' => count($palletGroup),
            ]);
        }

        return view('user.esg-report.index', [
            'client' => $client,
            'pallets' => $palletsData,
            'items' => $mappedItems,
            'emissionFactors' => $this->EMISSION_FACTORS,
            'noClientLinked' => false,
            'userEmail' => $user->email,
        ]);
    }
}
