<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RepairEntry;
use App\Models\Pallet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairOrderController extends Controller
{
    /**
     * Display a listing of repair orders for the authenticated client.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $clientIds = $this->resolveClientIds($user);
        $client = !empty($clientIds) ? Client::whereIn('id', $clientIds)->first() : null;
        $palletNumbers = $this->resolvePalletNumbers($clientIds);

        $query = RepairEntry::query()
            ->with(['client', 'pallet'])
            ->where(function ($q) use ($clientIds, $user, $palletNumbers) {
                if (!empty($clientIds)) {
                    $q->whereIn('user_id', $clientIds);
                }
                $q->orWhereRaw('TRIM(LOWER(customer_email)) = ?', [trim(strtolower($user->email))])
                  ->orWhere('customer_email', $user->email);
                if (!empty($palletNumbers)) {
                    $q->orWhereIn('pallet_info', $palletNumbers);
                }
            })
            ->whereNotIn('status', ['Draft', 'draft'])
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) != 'draft'");

        // Compute metrics for all matching orders before filtering
        $allEntries = (clone $query)->get();
        $stats = [
            'total' => $allEntries->count(),
            'in_progress' => $allEntries->filter(fn($e) => in_array($e->status, [
                RepairEntry::STATUS_REPAIR_SERVICES,
                RepairEntry::STATUS_IN_STORE_RESERVED,
                RepairEntry::STATUS_RECEIVING,
                RepairEntry::STATUS_PROCESSING,
            ]))->count(),
            'ready' => $allEntries->filter(fn($e) => in_array($e->status, [
                RepairEntry::STATUS_READY_FOR_PICKUP,
                RepairEntry::STATUS_READY_FOR_SHIPMENT,
            ]))->count(),
            'completed' => $allEntries->filter(fn($e) => in_array($e->status, [
                RepairEntry::STATUS_COMPLETED,
                RepairEntry::STATUS_SHIPPED,
                RepairEntry::STATUS_IN_STORE_PURCHASED,
                RepairEntry::STATUS_ONLINE_PURCHASED,
            ]))->count(),
        ];

        // Available client pallets for filter
        $clientPallets = $allEntries->pluck('pallet_info')->filter()->unique()->values();

        // Filters
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('device_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%')
                    ->orWhere('customer_email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('serial_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('technician', 'like', '%' . $searchTerm . '%')
                    ->orWhere('item_description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('item_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('pallet_info', 'like', '%' . $searchTerm . '%')
                    ->orWhere('lot_info', 'like', '%' . $searchTerm . '%')
                    ->orWhere('brand_category', 'like', '%' . $searchTerm . '%')
                    ->orWhere('put_away_location', 'like', '%' . $searchTerm . '%')
                    ->orWhere('id', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('pallet') && $request->pallet !== 'all') {
            $query->where('pallet_info', $request->pallet);
        }

        $entries = $query->latest('id')->paginate(10)->withQueryString();

        return view('user.repair-orders.index', [
            'entries' => $entries,
            'client' => $client,
            'stats' => $stats,
            'clientPallets' => $clientPallets,
            'statusChoices' => RepairEntry::statusChoices(),
            'currentStatus' => $request->status ?? 'all',
            'currentPallet' => $request->pallet ?? 'all',
            'searchTerm' => $request->search ?? '',
        ]);
    }

    /**
     * Display detailed view of a single repair order.
     */
    public function show($id)
    {
        $user = Auth::user();
        $clientIds = $this->resolveClientIds($user);
        $palletNumbers = $this->resolvePalletNumbers($clientIds);

        $entry = RepairEntry::with(['client', 'pallet', 'salesInvoices'])->findOrFail($id);

        if (strtolower(trim($entry->status ?? '')) === 'draft') {
            abort(404, 'Repair order not found.');
        }

        $belongsToClient = (!empty($clientIds) && in_array($entry->user_id, $clientIds))
            || (trim(strtolower($entry->customer_email ?? '')) === trim(strtolower($user->email)))
            || (!empty($entry->pallet_info) && in_array($entry->pallet_info, $palletNumbers));

        if (!$belongsToClient) {
            abort(403, 'Unauthorized access to this repair order.');
        }

        return view('user.repair-orders.show', [
            'entry' => $entry,
        ]);
    }

    /**
     * Helper: Resolve Client IDs matching the authenticated user.
     */
    private function resolveClientIds($user): array
    {
        return Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();
    }

    /**
     * Helper: Resolve pallet barcode numbers belonging to client IDs.
     */
    private function resolvePalletNumbers(array $clientIds): array
    {
        if (empty($clientIds)) {
            return [];
        }

        return Pallet::whereIn('client_id', $clientIds)
            ->pluck('barcode_number')
            ->filter()
            ->unique()
            ->all();
    }
}
