<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pallet;
use App\Models\DataDestructionItem;
use App\Models\ItAssetsItem;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();
        $client = Client::whereIn('id', $clientIds)->first();

        $orderQuery = Order::where('user_id', $userId);
        $totalOrders = (clone $orderQuery)->count();
        $activeOrders = (clone $orderQuery)
            ->whereNotIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])
            ->count();

        $recentOrders = (clone $orderQuery)
            ->withCount('items')
            ->latest()
            ->limit(4)
            ->get();

        $totalIntakes = 0;
        $totalDestruction = 0;
        $totalItAssets = 0;
        $recentIntakes = collect();
        $recentDestruction = collect();
        $recentItAssets = collect();

        if (!empty($clientIds)) {
            $totalIntakes = Pallet::whereIn('client_id', $clientIds)->count();
            $palletNumbers = Pallet::whereIn('client_id', $clientIds)->pluck('barcode_number')->filter()->unique()->all();
            
            $totalDestruction = DataDestructionItem::whereIn('pallet_number', $palletNumbers)->count();
            $totalItAssets = ItAssetsItem::whereIn('pallet_number', $palletNumbers)->count();

            $recentIntakes = Pallet::whereIn('client_id', $clientIds)
                ->latest()
                ->limit(4)
                ->get();

            $recentDestruction = DataDestructionItem::whereIn('pallet_number', $palletNumbers)
                ->latest()
                ->limit(4)
                ->get();

            $recentItAssets = ItAssetsItem::whereIn('pallet_number', $palletNumbers)
                ->latest()
                ->limit(4)
                ->get();
        }

        return view('user.dashboard', compact(
            'totalOrders',
            'activeOrders',
            'totalIntakes',
            'totalDestruction',
            'totalItAssets',
            'recentOrders',
            'recentIntakes',
            'recentDestruction',
            'recentItAssets',
            'client'
        ));
    }
}
