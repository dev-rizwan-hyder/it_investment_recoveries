<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\DataDestructionItem;
use App\Models\ItAssetsItem;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();
        $client = Client::whereIn('id', $clientIds)->first();

        $totalIntakes = 0;
        $totalItAssets = 0;
        $totalDestruction = 0;
        $totalUniversalWaste = 0;
        $co2SavedTons = '0.00';
        $recentIntakes = collect();
        $recentDestruction = collect();
        $recentUniversalWaste = collect();

        if (!empty($clientIds)) {
            $totalIntakes = Pallet::whereIn('client_id', $clientIds)->count();
            $palletNumbers = Pallet::whereIn('client_id', $clientIds)->pluck('barcode_number')->filter()->unique()->all();

            if (!empty($palletNumbers)) {
                $totalItAssets = ItAssetsItem::whereIn('pallet_number', $palletNumbers)->count();
                $totalDestruction = DataDestructionItem::whereIn('pallet_number', $palletNumbers)->count();
                $totalUniversalWaste = DB::table('universal_waste_items')->whereIn('pallet_number', $palletNumbers)->count();

                $recentDestruction = DataDestructionItem::whereIn('pallet_number', $palletNumbers)
                    ->latest()
                    ->limit(4)
                    ->get();

                $recentUniversalWaste = DB::table('universal_waste_items')
                    ->whereIn('pallet_number', $palletNumbers)
                    ->latest('id')
                    ->limit(4)
                    ->get();
            }

            // Calculate ESG Carbon Offset metric
            $palletGrossWeight = (float) Pallet::whereIn('client_id', $clientIds)->get()->sum(function ($p) {
                return max(0, (float)($p->gross_weight ?? 0) - (float)($p->tare_weight ?? 0));
            });
            $destWeight = !empty($palletNumbers) ? (float) DataDestructionItem::whereIn('pallet_number', $palletNumbers)->sum('weight') : 0.0;
            $uwWeight = !empty($palletNumbers) ? (float) DB::table('universal_waste_items')->whereIn('pallet_number', $palletNumbers)->sum('weight') : 0.0;
            
            $totalLbs = max($palletGrossWeight, $destWeight + $uwWeight);
            if ($totalLbs <= 0 && ($totalIntakes > 0 || $totalDestruction > 0 || $totalUniversalWaste > 0)) {
                $totalLbs = ($totalIntakes * 250) + ($totalDestruction * 15) + ($totalUniversalWaste * 20);
            }
            $co2SavedTons = number_format(($totalLbs * 1.44) / 2204.62, 2);

            $recentIntakes = Pallet::whereIn('client_id', $clientIds)
                ->latest()
                ->limit(4)
                ->get();
        }

        return view('user.dashboard', compact(
            'totalIntakes',
            'totalItAssets',
            'totalDestruction',
            'totalUniversalWaste',
            'co2SavedTons',
            'recentIntakes',
            'recentDestruction',
            'recentUniversalWaste',
            'client'
        ));
    }
}
