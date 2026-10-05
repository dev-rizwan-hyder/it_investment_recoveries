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

        $stats = $this->calculateStats($palletNumbers, $clientIds);

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

    /**
     * Remove the specified IT asset item.
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

        $palletNumbers = Pallet::whereIn('client_id', $clientIds)->pluck('barcode_number')->filter()->unique()->all();
        $item = ItAssetsItem::whereIn('pallet_number', $palletNumbers)->findOrFail($id);
        $name = $item->primary_name;
        $item->delete();

        return redirect()->route('user.it-assets.index')
            ->with('success', 'IT Asset entry "' . $name . '" moved to trash successfully.');
    }

    /**
     * Remove multiple specified IT asset items.
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

        $palletNumbers = Pallet::whereIn('client_id', $clientIds)->pluck('barcode_number')->filter()->unique()->all();

        $ids = $request->input('ids', []);
        $deletedCount = ItAssetsItem::whereIn('pallet_number', $palletNumbers)
            ->whereIn('id', $ids)
            ->delete();

        return redirect()->route('user.it-assets.index')
            ->with('success', $deletedCount . ' IT Asset item(s) moved to trash successfully.');
    }

    private function getEmptyStats(): array
    {
        return [
            'receivedCount' => 0,
            'processingCount' => 0,
            'completedCount' => 0,
        ];
    }

    private function calculateStats(array $palletNumbers, array $clientIds = []): array
    {
        if (empty($palletNumbers)) {
            return $this->getEmptyStats();
        }

        $items = ItAssetsItem::whereIn('pallet_number', $palletNumbers)->get(['status']);

        $receivedCount = 0;
        $processingCount = 0;
        $completedCount = 0;

        foreach ($items as $item) {
            $rawStatus = strtolower(trim($item->status ?? ''));
            if (str_contains($rawStatus, 'complet') || str_contains($rawStatus, 'ready')) {
                $completedCount++;
            } elseif (str_contains($rawStatus, 'progr') || str_contains($rawStatus, 'process')) {
                $processingCount++;
            } else {
                $receivedCount++;
            }
        }

        return [
            'receivedCount' => $receivedCount,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
        ];
    }
}
