<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniversalWasteController extends Controller
{
    /**
     * Display a listing of universal waste items for the user.
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
            return view('user.universal-waste.index', [
                'items' => collect(),
                'client' => null,
                'stats' => $this->getEmptyStats(),
                'noClientLinked' => true,
                'userEmail' => $user->email,
            ]);
        }

        $palletNumbers = Pallet::whereIn('client_id', $clientIds)
            ->pluck('barcode_number')
            ->filter()
            ->unique()
            ->all();

        if (empty($palletNumbers)) {
            return view('user.universal-waste.index', [
                'items' => DB::table('universal_waste_items')->whereRaw('1 = 0')->paginate(10),
                'client' => $client,
                'stats' => $this->getEmptyStats(),
                'noClientLinked' => false,
                'userEmail' => $user->email,
            ]);
        }

        $items = DB::table('universal_waste_items')
            ->whereIn('pallet_number', $palletNumbers)
            ->orderBy('id', 'desc')
            ->paginate(10);

        $stats = $this->calculateStats($palletNumbers);

        return view('user.universal-waste.index', [
            'items' => $items,
            'client' => $client,
            'stats' => $stats,
            'noClientLinked' => false,
            'userEmail' => $user->email,
        ]);
    }

    /**
     * Display details of a specific universal waste item / certificate.
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

        $item = DB::table('universal_waste_items')
            ->whereIn('pallet_number', $palletNumbers)
            ->where('id', $id)
            ->first();

        if (!$item) {
            abort(404, 'Universal waste item record not found.');
        }

        $pallet = Pallet::where('barcode_number', $item->pallet_number)->first();
        $client = Client::whereIn('id', $clientIds)->first();

        return view('user.universal-waste.show', [
            'item' => $item,
            'pallet' => $pallet,
            'client' => $client,
        ]);
    }

    private function getEmptyStats(): array
    {
        return [
            'totalItems' => 0,
            'totalWeight' => 0.0,
            'processingCount' => 0,
            'completedCount' => 0,
            'certificatesCount' => 0,
        ];
    }

    private function calculateStats(array $palletNumbers): array
    {
        if (empty($palletNumbers)) {
            return $this->getEmptyStats();
        }

        $query = DB::table('universal_waste_items')->whereIn('pallet_number', $palletNumbers);

        $totalItems = (clone $query)->count();
        $totalWeight = (clone $query)->sum('weight') ?: 0.0;
        $processingCount = (clone $query)->whereIn('status', ['In Progress', 'Processing', 'Pending'])->count();
        $completedCount = (clone $query)->whereIn('status', ['Completed', 'Recycled', 'Processed'])->count();

        return [
            'totalItems' => $totalItems,
            'totalWeight' => (float) $totalWeight,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
            'certificatesCount' => $completedCount > 0 ? $completedCount : max(1, $totalItems),
        ];
    }
}
