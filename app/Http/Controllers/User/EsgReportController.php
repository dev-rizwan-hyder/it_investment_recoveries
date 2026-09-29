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
                'metrics' => $this->getEmptyMetrics(),
                'noClientLinked' => true,
                'userEmail' => $user->email,
            ]);
        }

        $pallets = Pallet::whereIn('client_id', $clientIds)->get();
        $palletNumbers = $pallets->pluck('barcode_number')->filter()->unique()->all();

        $totalIntakesCount = $pallets->count();
        $totalDataDestructionCount = DataDestructionItem::whereIn('pallet_number', $palletNumbers)->count();
        $totalItAssetsCount = ItAssetsItem::whereIn('pallet_number', $palletNumbers)->count();

        $universalWasteQuery = DB::table('universal_waste_items')->whereIn('pallet_number', $palletNumbers);
        $totalUniversalWasteCount = (clone $universalWasteQuery)->count();
        $universalWasteWeightLbs = (float) ((clone $universalWasteQuery)->sum('weight') ?: 0.0);

        // Estimate weights across all categories
        $palletGrossWeight = (float) $pallets->sum(function ($p) {
            $gross = (float) ($p->gross_weight ?? 0);
            $tare = (float) ($p->tare_weight ?? 0);
            return max(0, $gross - $tare);
        });

        $dataDestructionWeightLbs = (float) DataDestructionItem::whereIn('pallet_number', $palletNumbers)->sum('weight') ?: 0.0;
        $itAssetsWeightLbs = (float) ItAssetsItem::whereIn('pallet_number', $palletNumbers)->sum('weight') ?: 0.0;

        $totalRecycledLbs = max($palletGrossWeight, $universalWasteWeightLbs + $dataDestructionWeightLbs + $itAssetsWeightLbs);
        if ($totalRecycledLbs <= 0 && ($totalIntakesCount > 0 || $totalDataDestructionCount > 0)) {
            $totalRecycledLbs = ($totalIntakesCount * 250) + ($totalDataDestructionCount * 15) + ($totalItAssetsCount * 25);
        }

        $totalRecycledTons = round($totalRecycledLbs / 2000, 2);

        // ESG Calculation Coefficients (EPA WARM model benchmarks for e-waste & IT recycling)
        // 1 lb of recycled e-waste saves approx 1.44 lbs CO2 equivalent
        $co2SavedLbs = round($totalRecycledLbs * 1.44, 2);
        $co2SavedMetricTons = round($co2SavedLbs / 2204.62, 2);

        // Trees equivalent: 1 mature tree absorbs ~48 lbs CO2/year
        $treesSaved = (int) round($co2SavedLbs / 48);

        // Energy saved: 1 lb recycled electronics saves ~1.8 kWh energy
        $kwhSaved = round($totalRecycledLbs * 1.8, 1);

        // Landfill Diversion Rate (percentage diverted from landfills through reuse and zero-landfill recycling)
        $landfillDiversionRate = $totalRecycledLbs > 0 ? 100.0 : 0.0;

        // Material Recovery Estimates
        $metalsRecoveredLbs = round($totalRecycledLbs * 0.45, 1); // ~45% metals (steel, copper, aluminum)
        $plasticsRecoveredLbs = round($totalRecycledLbs * 0.30, 1); // ~30% plastics
        $preciousMetalsGrams = round($totalRecycledLbs * 0.08, 1); // gold/silver/palladium trace recovery

        $metrics = [
            'totalIntakesCount' => $totalIntakesCount,
            'totalDataDestructionCount' => $totalDataDestructionCount,
            'totalItAssetsCount' => $totalItAssetsCount,
            'totalUniversalWasteCount' => $totalUniversalWasteCount,
            'totalRecycledLbs' => number_format($totalRecycledLbs, 1),
            'totalRecycledTons' => number_format($totalRecycledTons, 2),
            'co2SavedMetricTons' => number_format($co2SavedMetricTons, 2),
            'co2SavedLbs' => number_format($co2SavedLbs, 1),
            'treesSaved' => number_format($treesSaved),
            'kwhSaved' => number_format($kwhSaved, 1),
            'landfillDiversionRate' => $landfillDiversionRate,
            'metalsRecoveredLbs' => number_format($metalsRecoveredLbs, 1),
            'plasticsRecoveredLbs' => number_format($plasticsRecoveredLbs, 1),
            'preciousMetalsGrams' => number_format($preciousMetalsGrams, 1),
            'reportDate' => now()->format('F d, Y'),
        ];

        return view('user.esg-report.index', [
            'client' => $client,
            'metrics' => $metrics,
            'noClientLinked' => false,
            'userEmail' => $user->email,
        ]);
    }

    private function getEmptyMetrics(): array
    {
        return [
            'totalIntakesCount' => 0,
            'totalDataDestructionCount' => 0,
            'totalItAssetsCount' => 0,
            'totalUniversalWasteCount' => 0,
            'totalRecycledLbs' => '0.0',
            'totalRecycledTons' => '0.00',
            'co2SavedMetricTons' => '0.00',
            'co2SavedLbs' => '0.0',
            'treesSaved' => '0',
            'kwhSaved' => '0.0',
            'landfillDiversionRate' => 0.0,
            'metalsRecoveredLbs' => '0.0',
            'plasticsRecoveredLbs' => '0.0',
            'preciousMetalsGrams' => '0.0',
            'reportDate' => now()->format('F d, Y'),
        ];
    }
}
