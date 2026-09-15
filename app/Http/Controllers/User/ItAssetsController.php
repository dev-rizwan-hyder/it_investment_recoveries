<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ItAssetsItem;
use App\Models\Pallet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItAssetsController extends Controller
{
    /**
     * Display a listing of IT assets items for the user.
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
            return view('user.it-assets.index', [
                'items' => ItAssetsItem::whereRaw('1 = 0')->paginate(10),
                'client' => null,
                'stats' => $this->getEmptyStats(),
                'noClientLinked' => true,
                'userEmail' => $user->email,
            ]);
        }

        $palletNumbers = Pallet::whereIn('client_id', $clientIds)->pluck('barcode_number')->filter()->unique()->all();

        $items = ItAssetsItem::whereIn('pallet_number', $palletNumbers)
            ->latest()
            ->paginate(10);

        $stats = $this->calculateStats($palletNumbers);

        return view('user.it-assets.index', [
            'items' => $items,
            'client' => $client,
            'stats' => $stats,
            'noClientLinked' => false,
            'userEmail' => $user->email,
        ]);
    }

    /**
     * Display the details of a specific IT asset item.
     */
    public function show($id)
    {
        $user = Auth::user();
        $clientIds = Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();

        if (empty($clientIds)) {
            abort(403, 'Unauthorized access or client profile not found.');
        }

        $palletNumbers = Pallet::whereIn('client_id', $clientIds)->pluck('barcode_number')->filter()->unique()->all();

        $item = ItAssetsItem::whereIn('pallet_number', $palletNumbers)->findOrFail($id);

        return view('user.it-assets.show', [
            'item' => $item,
        ]);
    }

    private function getEmptyStats(): array
    {
        return [
            'totalItems' => 0,
            'processingCount' => 0,
            'completedCount' => 0,
            'readyCount' => 0,
        ];
    }

    private function calculateStats(array $palletNumbers): array
    {
        if (empty($palletNumbers)) {
            return $this->getEmptyStats();
        }

        $query = ItAssetsItem::whereIn('pallet_number', $palletNumbers);

        return [
            'totalItems' => (clone $query)->count(),
            'processingCount' => (clone $query)->where('status', 'In Progress')->count(),
            'readyCount' => (clone $query)->where('status', 'Ready for Inventory')->count(),
            'completedCount' => (clone $query)->where('status', 'Completed')->count(),
        ];
    }
}
