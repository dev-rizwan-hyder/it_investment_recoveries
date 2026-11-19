<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItAssetsItem;
use App\Models\Pallet;
use App\Models\Inventory;
use App\Services\ItemReuseAdjustmentService;
use App\Services\PalletCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ItAssetsEntriesController extends Controller
{
    /**
     * Display all IT Assets Items with filtering and pagination
     */
    public function index()
    {
        $pallets_list = Pallet::orderBy('barcode_number', 'asc')->get();
        $employees = \App\Models\Employee::where('is_resigned', 0)->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = \App\Models\User::orderBy('name')->get();
        }
        return view('admin.intake.it-assets-entries', compact('pallets_list', 'employees'));
    }

    /**
     * Get paginated items with filtering
     */
    public function loadData(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('statusFilter', '');
        $palletFilter = $request->get('palletFilter', '');
        $categoryFilter = $request->get('categoryFilter', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        $query = ItAssetsItem::with(['pallet.client']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%')
                    ->orWhere('serial_number', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%')
                    ->orWhere('pallet_number', 'like', '%' . $search . '%')
                    ->orWhereHas('pallet.client', function ($cq) use ($search) {
                        $cq->where('company', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($palletFilter)) {
            $query->where('pallet_number', $palletFilter);
        }

        if (!empty($categoryFilter)) {
            $query->where('category', $categoryFilter);
        }

        $items = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        $items->getCollection()->transform(function ($item) {
            $pallet = $item->pallet;
            if (!$pallet && $item->pallet_number) {
                $pallet = \App\Models\Pallet::withTrashed()->with('client')->where('barcode_number', $item->pallet_number)->first();
            }
            $item->company_name = $pallet && $pallet->client ? ($pallet->client->company ?: $pallet->client->name) : 'N/A';
            $item->client_name = $pallet && $pallet->client ? ($pallet->client->name ?: 'N/A') : 'N/A';
            $item->pallet_job_no = $pallet ? ($pallet->job_no ?? null) : null;
            return $item;
        });

        return response()->json([
            'items' => $items->items(),
            'total' => $items->total(),
            'per_page' => $items->perPage(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
        ]);
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        $stats = [
            ['label' => 'Draft', 'value' => ItAssetsItem::where('status', 'Draft')->count(), 'color' => 'text-slate-500'],
            ['label' => 'Received', 'value' => ItAssetsItem::whereIn('status', ['Recieved', 'Received'])->count(), 'color' => 'text-emerald-600'],
            ['label' => 'In Progress', 'value' => ItAssetsItem::where('status', 'In Progress')->count(), 'color' => 'text-amber-500'],
            ['label' => 'Completed', 'value' => ItAssetsItem::where('status', 'Completed')->count(), 'color' => 'text-purple-500'],
        ];

        return response()->json($stats);
    }

    /**
     * Generate a new barcode for inventory items (AJAX)
     */
    public function generateInventoryBarcode()
    {
        try {
            $number = 'INV-' . mt_rand(1000000000, 9999999999);
            $generator = new BarcodeGeneratorPNG();

            $barcodeData = $generator->getBarcode($number, $generator::TYPE_CODE_128, 2, 30);
            $base64 = base64_encode($barcodeData);

            return response()->json([
                'success' => true,
                'number' => $number,
                'base64' => 'data:image/png;base64,' . $base64
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show item details modal
     */
    public function showItemDetails($itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);
            if (!empty($item->pallet_number)) {
                $pallet = Pallet::where('barcode_number', $item->pallet_number)->first();
                if ($pallet) {
                    if (empty($item->receiving_employee) && !empty($pallet->receiving_employee)) {
                        $item->receiving_employee = $pallet->receiving_employee;
                    }
                    if (empty($item->processing_employee) && !empty($pallet->processing_employee)) {
                        $item->processing_employee = $pallet->processing_employee;
                    }
                    $palletIntakeEmp = $pallet->intake_employee ?? $pallet->it_assets_employee ?? null;
                    if (empty($item->it_assets_employee) && empty($item->intake_employee) && !empty($palletIntakeEmp)) {
                        $item->it_assets_employee = $palletIntakeEmp;
                        $item->intake_employee = $palletIntakeEmp;
                    }
                }
            }
            return response()->json(['success' => true, 'item' => $item]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }
    }

    /**
     * Load edit modal data
     */
    public function loadEditModal($itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);
            if (!empty($item->pallet_number)) {
                $pallet = Pallet::where('barcode_number', $item->pallet_number)->first();
                if ($pallet) {
                    if (empty($item->receiving_employee) && !empty($pallet->receiving_employee)) {
                        $item->receiving_employee = $pallet->receiving_employee;
                    }
                    if (empty($item->processing_employee) && !empty($pallet->processing_employee)) {
                        $item->processing_employee = $pallet->processing_employee;
                    }
                    $palletIntakeEmp = $pallet->intake_employee ?? $pallet->it_assets_employee ?? null;
                    if (empty($item->it_assets_employee) && empty($item->intake_employee) && !empty($palletIntakeEmp)) {
                        $item->it_assets_employee = $palletIntakeEmp;
                        $item->intake_employee = $palletIntakeEmp;
                    }
                }
            }
            $pallets = Pallet::orderBy('barcode_number', 'asc')->get();

            return response()->json([
                'success' => true,
                'item' => $item,
                'pallets' => $pallets
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load edit form: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update item
     */
    public function update(Request $request, $itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);
            $previousPalletNumber = $item->pallet_number;

            $validated = $request->validate([
                'name' => 'required|string',
                'brand' => 'nullable|string',
                'model' => 'nullable|string',
                'serial_number' => 'nullable|string',
                'category' => 'required|string',
                'sub_category' => 'nullable|string|max:255',
                'sub_sub_category' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer',
                'sub_category_id' => 'nullable|integer',
                'sub_sub_category_id' => 'nullable|integer',
                'condition' => 'nullable|string',
                'estimated_value' => 'required|numeric|min:0',
                'weight' => 'nullable|numeric|min:0',
                'reuse_weight' => 'nullable|numeric|min:0',
                'scrap_weight' => 'nullable|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'reuse_quantity' => 'nullable|integer|min:0',
                'scrap_quantity' => 'nullable|integer|min:0',
                'pallet_number' => 'nullable|string',
                'item_placement' => 'nullable|string',
                'receiving_employee' => 'nullable|string|max:255',
                'processing_employee' => 'nullable|string|max:255',
                'it_assets_employee' => 'nullable|string|max:255',
                'intake_employee' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            $normalized = ItemReuseAdjustmentService::normalize([
                'quantity' => $validated['quantity'],
                'reuse_quantity' => $validated['reuse_quantity'] ?? 0,
                'scrap_quantity' => $validated['scrap_quantity'] ?? 0,
                'transferred_quantity' => $item->transferred_quantity ?? 0,
                'weight' => $validated['weight'] ?? 0,
                'reuse_weight' => $validated['reuse_weight'] ?? 0,
                'scrap_weight' => $validated['scrap_weight'] ?? 0,
            ]);
            $sanitizedPalletNumber = trim((string) ($validated['pallet_number'] ?? ''));
            if (in_array(strtolower($sanitizedPalletNumber), ['', 'undefined', 'null'], true)) {
                $sanitizedPalletNumber = $item->pallet_number;
            }

            // Handle file uploads during update (saving directly to DB as base64 data URIs)
            $newPhotos = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    if ($photo->isValid()) {
                        $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType();
                        $base64Data = base64_encode(file_get_contents($photo->getRealPath()));
                        $dataUri = 'data:' . $mimeType . ';base64,' . $base64Data;
                        $newPhotos[] = $dataUri;
                    }
                }
            }
            $existingPhotos = $item->photos_paths ?? [];
            if (!is_array($existingPhotos)) {
                $existingPhotos = [];
            }
            $updatedPhotos = array_merge($existingPhotos, $newPhotos);

            // Determine status based on net quantity
            $newStatus = $item->status;
            $netQuantity = (int)$normalized['quantity'] - ((int)($normalized['reuse_quantity'] ?? 0) + (int)($normalized['scrap_quantity'] ?? 0) + (int)($normalized['transferred_quantity'] ?? 0));
            
            if ($netQuantity <= 0) {
                $newStatus = 'Completed';
            }

            $empVal = $validated['it_assets_employee'] ?? $request->it_assets_employee ?? $validated['intake_employee'] ?? $request->intake_employee;

            $item->update([
                'name' => $validated['name'],
                'brand' => $validated['brand'] ?? '',
                'model' => $validated['model'] ?? '',
                'serial_number' => $validated['serial_number'] ?? '',
                'category' => $validated['category'],
                'sub_category' => $validated['sub_category'] ?? '',
                'sub_sub_category' => $validated['sub_sub_category'] ?? '',
                'category_id' => $validated['category_id'] ?? null,
                'sub_category_id' => $validated['sub_category_id'] ?? null,
                'sub_sub_category_id' => $validated['sub_sub_category_id'] ?? null,
                'condition' => $validated['condition'] ?? $item->condition,
                'estimated_value' => $validated['estimated_value'],
                'weight' => $normalized['weight'],
                'reuse_weight' => $normalized['reuse_weight'],
                'scrap_weight' => $normalized['scrap_weight'] ?? 0,
                'quantity' => $normalized['quantity'],
                'reuse_quantity' => $normalized['reuse_quantity'],
                'scrap_quantity' => $normalized['scrap_quantity'] ?? 0,
                'transferred_quantity' => $normalized['transferred_quantity'],
                'pallet_number' => $sanitizedPalletNumber,
                'item_placement' => $validated['item_placement'] ?? '',
                'receiving_employee' => $validated['receiving_employee'] ?? $request->receiving_employee,
                'processing_employee' => $validated['processing_employee'] ?? $request->processing_employee,
                'it_assets_employee' => $empVal,
                'intake_employee' => $empVal,
                'status' => $newStatus,
                'notes' => $validated['notes'] ?? '',
                'photos_paths' => $updatedPhotos,
            ]);
            
            $item->refresh();

            app(PalletCompletionService::class)->syncForPallet($previousPalletNumber);
            app(PalletCompletionService::class)->syncForPallet($sanitizedPalletNumber);

            return response()->json([
                'success' => true,
                'message' => 'IT Assets item updated successfully!',
                'item' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update status inline
     */
    public function updateStatus(Request $request, $itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);
            $newStatus = $request->get('status');

            $item->update(['status' => $newStatus]);

            // Update inventory records with same barcode
            Inventory::where('barcode', $item->barcode)->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Status updated to {$newStatus}!",
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update multiple item statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:it_assets_items,id',
                'status' => 'required|string|max:100',
            ]);

            $items = ItAssetsItem::whereIn('id', $validated['ids'])->get();
            $itemIds = $items->pluck('id');
            $barcodes = $items->pluck('barcode')->filter()->unique()->values();

            ItAssetsItem::whereIn('id', $itemIds)->update(['status' => $validated['status']]);

            if ($barcodes->isNotEmpty()) {
                Inventory::whereIn('barcode', $barcodes)->update(['status' => $validated['status']]);
            }

            return response()->json([
                'success' => true,
                'message' => "{$items->count()} item(s) updated to {$validated['status']}!",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update quantity inline
     */
    public function updateQuantity(Request $request, $itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);
            $quantity = $request->get('quantity');

            if (is_numeric($quantity) && $quantity >= 0) {
                $updateData = ['quantity' => $quantity];
                
                $netQuantity = (int)$quantity - ((int)($item->reuse_quantity ?? 0) + (int)($item->scrap_quantity ?? 0) + (int)($item->transferred_quantity ?? 0));
                
                if ($netQuantity <= 0) {
                    $updateData['status'] = 'Completed';
                }
                
                ItAssetsItem::where('id', $itemId)->update($updateData);
                return response()->json(['success' => true, 'message' => 'Quantity updated']);
            }

            return response()->json(['success' => false, 'message' => 'Invalid quantity'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete photo
     */
    public function deletePhoto(Request $request, $itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);
            $photoPath = $request->get('photo_path');

            $photos = $item->photos_paths ?? [];

            if (($key = array_search($photoPath, $photos)) !== false) {
                unset($photos[$key]);

                if (!str_starts_with($photoPath, 'data:')) {
                    $fullPath = public_path($photoPath);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                    if (Storage::disk('public')->exists($photoPath)) {
                        Storage::disk('public')->delete($photoPath);
                    }
                }

                $updatedPhotos = array_values($photos);
                $item->update(['photos_paths' => $updatedPhotos]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully',
                    'photos' => $updatedPhotos
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Photo not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete item
     */
    public function delete(Request $request, $itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from IT Assets.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete item: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete multiple items
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:it_assets_items,id',
            ]);

            $items = ItAssetsItem::whereIn('id', $validated['ids'])->get();

            foreach ($items as $item) {
                $item->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "{$items->count()} item(s) removed from IT Assets.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete selected items: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Prepare item data for inventory modal
     */
    public function prepareToInventory($itemId)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);

            $normalized = ItemReuseAdjustmentService::normalize($item->toArray());
            $netWeight = $normalized['net_weight'];
            $netQuantity = $normalized['net_quantity'];

            $categoryHierarchy = [
                'category' => $item->category,
                'sub_category' => $item->sub_category,
                'sub_sub_category' => $item->sub_sub_category,
                'category_id' => $item->category_id,
                'sub_category_id' => $item->sub_category_id,
                'sub_sub_category_id' => $item->sub_sub_category_id,
            ];

            if ($item->category_id) {
                $category = \App\Store\Models\Category::find($item->category_id);
                if ($category) {
                    $hierarchy = $category->getHierarchy();
                    $categoryHierarchy = array_merge($categoryHierarchy, $hierarchy);
                }
            }

            $inventoryData = [
                'item_id' => $item->id,
                'technician' => '',
                'customer_phone' => '',
                'customer_email' => '',
                'device_type' => trim(($item->brand ?? '') . ' ' . ($item->model ?? '')),
                'category' => $categoryHierarchy['category'],
                'sub_category' => $categoryHierarchy['sub_category'] ?? '',
                'sub_sub_category' => $categoryHierarchy['sub_sub_category'] ?? '',
                'category_id' => $categoryHierarchy['category_id'] ?? null,
                'sub_category_id' => $categoryHierarchy['sub_category_id'] ?? null,
                'sub_sub_category_id' => $categoryHierarchy['sub_sub_category_id'] ?? null,
                'brand_category' => '',
                'price' => $item->estimated_value,
                'quantity' => $netQuantity,
                'reuse_quantity' => $item->reuse_quantity ?? 0,
                'scrap_quantity' => $item->scrap_quantity ?? 0,
                'lot_info' => '',
                'serial_number' => $item->serial_number,
                'status' => 'Pending',
                'pallet_info' => $item->pallet_number,
                'device_specs' => '',
                'notes' => ($item->notes ?? ''),
                'operating_system' => '',
                'put_away_location' => $item->item_placement ?? '',
                'net_weight' => $netWeight,
                'net_quantity' => $netQuantity,
            ];

            return response()->json(['success' => true, 'item' => $item, 'inventoryData' => $inventoryData]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save item to inventory
     */
    public function saveToInventory($itemId, Request $request)
    {
        try {
            $item = ItAssetsItem::findOrFail($itemId);

            $validated = $request->validate([
                'technician' => 'nullable|string',
                'customer_phone' => 'nullable|string',
                'customer_email' => 'nullable|string',
                'device_type' => 'nullable|string',
                'category' => 'required|string',
                'brand_category' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'lot_info' => 'nullable|string',
                'serial_number' => 'nullable|string',
                'status' => 'required|string',
                'pallet_info' => 'nullable|string',
                'device_specs' => 'nullable|string',
                'operating_system' => 'nullable|string',
                'put_away_location' => 'nullable|string',
                'notes' => 'nullable|string',
                'reuse_quantity' => 'nullable|integer|min:0',
                'transferred_qty' => 'nullable|integer|min:0',
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'barcode' => 'nullable|string',
            ]);

            $repairPhotosPaths = [];
            if ($request->hasFile('repair_photos')) {
                foreach ($request->file('repair_photos') as $photo) {
                    if ($photo && $photo->isValid()) {
                        try {
                            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                            $photo->move(public_path('repair'), $filename);
                            $repairPhotosPaths[] = 'repair/' . $filename;
                        } catch (\Exception $e) {
                            \Log::warning('Failed to upload repair photo: ' . $e->getMessage());
                        }
                    }
                }
            }

            $transferredQty = (int) $request->input('transferred_qty', $request->input('quantity', 0));
            $normalized = ItemReuseAdjustmentService::normalize($item->toArray());

            if ($transferredQty > $normalized['net_quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$normalized['net_quantity']} item(s) are available after reuse, scrap, and prior transfers.",
                ], 422);
            }

            Inventory::create([
                'barcode' => $request->input('barcode') ?? $item->barcode,
                'name' => $item->name,
                'brand' => $item->brand,
                'model' => $item->model,
                'weight' => $normalized['net_weight'] ?? 0,
                'intake_type' => 'IT Assets',
                'photos_paths' => $item->photos_paths ?? [],
                'repair_photos' => $repairPhotosPaths,
                'condition_status' => $item->condition,
                'pallet_number' => $item->pallet_number,
                'estimated_value' => $item->estimated_value,
                'technician' => $validated['technician'] ?? '',
                'customer_phone' => $validated['customer_phone'] ?? '',
                'customer_email' => $validated['customer_email'] ?? '',
                'device_type' => $validated['device_type'] ?? '',
                'category' => $validated['category'],
                'sub_category' => $request->input('sub_category', $item->sub_category),
                'sub_sub_category' => $request->input('sub_sub_category', $item->sub_sub_category),
                'category_id' => $request->input('category_id', $item->category_id),
                'sub_category_id' => $request->input('sub_category_id', $item->sub_category_id),
                'sub_sub_category_id' => $request->input('sub_sub_category_id', $item->sub_sub_category_id),
                'brand_category' => $validated['brand_category'] ?? '',
                'price' => $validated['price'] ?? $item->estimated_value,
                'quantity' => $transferredQty,
                'lot_info' => $validated['lot_info'] ?? '',
                'serial_number' => $validated['serial_number'] ?? $item->serial_number,
                'status' => $validated['status'] ?? 'Pending',
                'pallet_info' => $validated['pallet_info'] ?? '',
                'device_specs' => $validated['device_specs'] ?? '',
                'operating_system' => $validated['operating_system'] ?? '',
                'notes' => ($validated['notes'] ?? $item->notes),
                'put_away_location' => $validated['put_away_location'] ?? '',
                'reuse_quantity' => $validated['reuse_quantity'] ?? 0,
            ]);

            $postTransfer = ItemReuseAdjustmentService::normalize(array_merge(
                $item->toArray(),
                ['transferred_quantity' => (int) ($item->transferred_quantity ?? 0) + $transferredQty]
            ));

            $item->forceFill([
                'transferred_quantity' => $postTransfer['transferred_quantity'],
                'status' => $postTransfer['net_quantity'] <= 0 ? 'Completed' : $item->status,
            ])->save();

            app(PalletCompletionService::class)->syncForPallet($item->pallet_number);

            return response()->json([
                'success' => true,
                'message' => "Item #{$item->barcode} successfully added to inventory!",
                'item' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('statusFilter', '');
        $palletFilter = $request->get('palletFilter', '');
        $categoryFilter = $request->get('categoryFilter', '');

        $query = ItAssetsItem::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%')
                    ->orWhere('serial_number', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%');
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($palletFilter)) {
            $query->where('pallet_number', $palletFilter);
        }

        if (!empty($categoryFilter)) {
            $query->where('category', $categoryFilter);
        }

        $items = $query->get();

        $fileName = 'IT_Assets_Export_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = ['Barcode', 'Name', 'Category', 'Quantity', 'Status', 'Weight', 'Value', 'Pallet', 'Serial Numbers', 'Received At'];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->barcode,
                    $item->name,
                    $item->category,
                    $item->quantity,
                    $item->status,
                    $item->weight,
                    $item->estimated_value,
                    $item->pallet_number,
                    $item->serial_number,
                    $item->created_at ? $item->created_at->format('M d, Y H:i') : 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
