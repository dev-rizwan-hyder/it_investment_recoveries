<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefurbishingItem;
use App\Models\Inventory;
use App\Models\InventoryShippingRate;
use App\Models\Pallet;
use App\Services\ItemReuseAdjustmentService;
use App\Services\PalletCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Picqer\Barcode\BarcodeGeneratorPNG;

class RefurbishingEntriesController extends Controller
{
    /**
     * Display refurbishing items list (page load or AJAX)
     */
    public function index(Request $request)
    {
        $query = RefurbishingItem::query();

        // Text Search (Name, Barcode, Brand, Model)
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%')
                    ->orWhere('brand', 'like', '%' . $request->search . '%')
                    ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->has('statusFilter') && !empty($request->statusFilter)) {
            $query->where('status', $request->statusFilter);
        }

        // Pallet Filter
        if ($request->has('palletFilter') && !empty($request->palletFilter)) {
            $query->where('pallet_number', $request->palletFilter);
        }

        // Category Filter
        if ($request->has('categoryFilter') && !empty($request->categoryFilter)) {
            $query->where('category', $request->categoryFilter);
        }

        $items = $query->latest()->paginate(10);
        $stats = $this->getStats();

        if ($request->ajax()) {
            return response()->json([
                'items' => $items->items(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                ],
                'stats' => $stats,
            ]);
        }

        return view('admin.intake.refurbishing-entries', [
            'items' => $items,
            'stats' => $stats,
            'pallets_list' => Pallet::orderBy('barcode_number', 'asc')->get(),
            'employees' => \App\Models\Employee::where('is_resigned', 0)->orderBy('name')->get()->isNotEmpty() 
                ? \App\Models\Employee::where('is_resigned', 0)->orderBy('name')->get() 
                : \App\Models\User::orderBy('name')->get(),
        ]);
    }

    /**
     * Get statistics
     */
    private function getStats()
    {
        return [
            ['label' => 'Total items', 'value' => RefurbishingItem::sum('quantity'), 'color' => 'text-slate-800'],
            ['label' => 'Refurb Intake', 'value' => RefurbishingItem::count(), 'color' => 'text-amber-600'],
            ['label' => 'In Progress', 'value' => RefurbishingItem::where('status', 'In Progress')->count(), 'color' => 'text-orange-500'],
            ['label' => 'Added to Inventory', 'value' => RefurbishingItem::sum('reuse_quantity'), 'color' => 'text-emerald-600'],
            ['label' => 'Completed', 'value' => RefurbishingItem::where('status', 'Completed')->count(), 'color' => 'text-purple-500'],
        ];
    }

    /**
     * Show item details
     */
    public function showItemDetails($itemId)
    {
        $item = RefurbishingItem::find($itemId);
        if (!$item && request()->ajax()) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        if ($item && !empty($item->pallet_number)) {
            $pallet = Pallet::where('barcode_number', $item->pallet_number)->first();
            if ($pallet) {
                if (empty($item->receiving_employee) && !empty($pallet->receiving_employee)) {
                    $item->receiving_employee = $pallet->receiving_employee;
                }
                if (empty($item->processing_employee) && !empty($pallet->processing_employee)) {
                    $item->processing_employee = $pallet->processing_employee;
                }
                $palletIntakeEmp = $pallet->intake_employee ?? $pallet->refurbishing_employee ?? null;
                if (empty($item->refurbishing_employee) && empty($item->intake_employee) && !empty($palletIntakeEmp)) {
                    $item->refurbishing_employee = $palletIntakeEmp;
                    $item->intake_employee = $palletIntakeEmp;
                }
            }
        }
        return response()->json($item);
    }

    /**
     * Load edit modal data
     */
    public function loadEditModal($itemId)
    {
        $item = RefurbishingItem::find($itemId);
        if (!$item) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Item not found'], 404);
            }
            abort(404);
        }
        if (!empty($item->pallet_number)) {
            $pallet = Pallet::where('barcode_number', $item->pallet_number)->first();
            if ($pallet) {
                if (empty($item->receiving_employee) && !empty($pallet->receiving_employee)) {
                    $item->receiving_employee = $pallet->receiving_employee;
                }
                if (empty($item->processing_employee) && !empty($pallet->processing_employee)) {
                    $item->processing_employee = $pallet->processing_employee;
                }
                $palletIntakeEmp = $pallet->intake_employee ?? $pallet->refurbishing_employee ?? null;
                if (empty($item->refurbishing_employee) && empty($item->intake_employee) && !empty($palletIntakeEmp)) {
                    $item->refurbishing_employee = $palletIntakeEmp;
                    $item->intake_employee = $palletIntakeEmp;
                }
            }
        }
        
        if (request()->ajax()) {
            return response()->json([
                'item' => $item,
                'pallets_list' => Pallet::orderBy('barcode_number', 'asc')->get(),
            ]);
        }

        return abort(404);
    }

    /**
     * Update refurbishing item
     */
    public function update($itemId, Request $request)
    {
        \Log::info('RefurbishingEntries update called', [
            'itemId' => $itemId,
            'isAjax' => $request->ajax(),
            'headers' => $request->headers->all(),
        ]);

        $item = RefurbishingItem::find($itemId);
        if (!$item) {
            \Log::warning('Item not found', ['itemId' => $itemId]);
            return response()->json(['error' => 'Item not found'], 404);
        }

        $previousPalletNumber = $item->pallet_number;

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'brand' => 'nullable|string|max:100',
                'model' => 'nullable|string|max:100',
                'serial_number' => 'nullable|string|max:100',
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
                'notes' => 'nullable|string',
                'item_placement' => 'nullable|string',
                'receiving_employee' => 'nullable|string|max:255',
                'processing_employee' => 'nullable|string|max:255',
                'refurbishing_employee' => 'nullable|string|max:255',
                'intake_employee' => 'nullable|string|max:255',
                'photos_to_delete' => 'nullable|array',
                'new_photos' => 'nullable',
                'new_photos.*' => 'nullable|file|image|max:5120',
            ]);
            
            \Log::info('Validation passed for item update', ['itemId' => $itemId]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed', ['itemId' => $itemId, 'errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        // Handle photo uploads
        $photoPaths = $item->photos_paths ?? [];
        if (is_string($photoPaths)) {
            $decodedPaths = json_decode($photoPaths, true);
            $photoPaths = is_array($decodedPaths) ? $decodedPaths : [];
        }
        
        // Normalize current paths for reliable comparisons (legacy data may include leading slash)
        $photoPaths = array_map(function ($p) {
            return is_string($p) ? ltrim($p, '/') : $p;
        }, $photoPaths);

        // Remove photos marked for deletion
        if ($request->has('photos_to_delete')) {
            foreach ($request->photos_to_delete as $pathToDelete) {
                $normalizedDeletePath = ltrim((string) $pathToDelete, '/');

                if (Storage::disk('public')->exists($normalizedDeletePath)) {
                    Storage::disk('public')->delete($normalizedDeletePath);
                } elseif (file_exists(public_path($normalizedDeletePath))) {
                    @unlink(public_path($normalizedDeletePath));
                }

                $photoPaths = array_filter($photoPaths, function ($p) use ($normalizedDeletePath) {
                    return ltrim((string) $p, '/') !== $normalizedDeletePath;
                });
            }
        }

        // Add new photos
        $rawUploadedFiles = $request->file('new_photos');
        $uploadedPhotos = collect(Arr::wrap($rawUploadedFiles))
            ->flatten()
            ->filter(fn($file) => $file instanceof UploadedFile)
            ->values();

        \Log::info('Edit upload payload debug', [
            'itemId' => $itemId,
            'hasFile_new_photos' => $request->hasFile('new_photos'),
            'allFiles_keys' => array_keys($request->allFiles()),
            'uploaded_count' => $uploadedPhotos->count(),
        ]);

        if ($uploadedPhotos->count() > 0) {
            $destinationPath = public_path('refurbishing');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $uploadErrors = [];
            foreach ($uploadedPhotos as $photo) {
                if (!$photo->isValid()) {
                    $uploadErrors[] = $photo->getClientOriginalName() . ' (error code: ' . $photo->getError() . ')';
                    continue;
                }

                $filename = uniqid() . '_' . preg_replace('/\s+/', '_', $photo->getClientOriginalName());
                $photo->move($destinationPath, $filename);
                $photoPaths[] = 'refurbishing/' . $filename;
            }

            if (!empty($uploadErrors)) {
                return response()->json([
                    'message' => 'Some images failed to upload',
                    'errors' => ['new_photos' => $uploadErrors],
                ], 422);
            }
        }

        // Keep only unique non-empty string paths
        $photoPaths = array_values(array_unique(array_filter($photoPaths, function ($p) {
            return is_string($p) && trim($p) !== '';
        })));

        \Log::info('Updating item with data', [
            'itemId' => $itemId,
            'data' => array_keys($validated),
        ]);

        $sanitizedPalletNumber = trim((string) ($validated['pallet_number'] ?? ''));
        if (in_array(strtolower($sanitizedPalletNumber), ['', 'undefined', 'null'], true)) {
            $sanitizedPalletNumber = null;
        }

        $normalized = ItemReuseAdjustmentService::normalize([
            'quantity' => $validated['quantity'],
            'reuse_quantity' => $validated['reuse_quantity'] ?? 0,
            'scrap_quantity' => $validated['scrap_quantity'] ?? 0,
            'transferred_quantity' => $item->transferred_quantity ?? 0,
            'weight' => $validated['weight'] ?? 0,
            'reuse_weight' => $validated['reuse_weight'] ?? 0,
            'scrap_weight' => $validated['scrap_weight'] ?? 0,
        ]);

        $refurbEmpVal = $request->refurbishing_employee ?? $request->intake_employee;

        $item->update([
            'name' => $validated['name'],
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'serial_number' => $validated['serial_number'],
            'category' => $validated['category'],
            'sub_category' => $validated['sub_category'] ?? '',
            'sub_sub_category' => $validated['sub_sub_category'] ?? '',
            'category_id' => $validated['category_id'] ?? null,
            'sub_category_id' => $validated['sub_category_id'] ?? null,
            'sub_sub_category_id' => $validated['sub_sub_category_id'] ?? null,
            'condition' => $validated['condition'],
            'estimated_value' => $validated['estimated_value'],
            'weight' => $normalized['weight'],
            'reuse_weight' => $normalized['reuse_weight'],
            'scrap_weight' => $normalized['scrap_weight'] ?? 0,
            'quantity' => $normalized['quantity'],
            'reuse_quantity' => $normalized['reuse_quantity'] ?? 0,
            'scrap_quantity' => $normalized['scrap_quantity'] ?? 0,
            'transferred_quantity' => $normalized['transferred_quantity'],
            'pallet_number' => $sanitizedPalletNumber,
            'notes' => $validated['notes'],
            'item_placement' => $validated['item_placement'],
            'receiving_employee' => $validated['receiving_employee'] ?? $request->receiving_employee,
            'processing_employee' => $request->processing_employee,
            'refurbishing_employee' => $refurbEmpVal,
            'intake_employee' => $refurbEmpVal,
            'status' => $item->status,
            'photos_paths' => array_values($photoPaths),
        ]);

        app(PalletCompletionService::class)->syncForPallet($previousPalletNumber);
        app(PalletCompletionService::class)->syncForPallet($sanitizedPalletNumber);

        \Log::info('Item updated successfully', ['itemId' => $itemId, 'name' => $item->name]);

        // Always return JSON for both AJAX and regular requests
        return response()->json([
            'message' => 'Item updated successfully!',
            'item' => $item,
            'isAjax' => $request->ajax(),
            'uploaded_photos_count' => $uploadedPhotos->count(),
        ]);
    }

    /**
     * Update item status
     */
    public function updateStatus($itemId, Request $request)
    {
        $item = RefurbishingItem::find($itemId);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $newStatus = $request->input('status');
        $item->update(['status' => $newStatus]);

        // Sync to Inventory records
        Inventory::where('barcode', $item->barcode)->update(['status' => $newStatus]);

        if ($request->ajax()) {
            return response()->json(['message' => "Status updated to {$newStatus} across all records!"]);
        }

        return redirect()->back()->with('message', "Status updated to {$newStatus}!");
    }

    /**
     * Update multiple item statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:refurbishing_items,id',
            'status' => 'required|string|max:100',
        ]);

        $items = RefurbishingItem::whereIn('id', $validated['ids'])->get();
        $itemIds = $items->pluck('id');
        $barcodes = $items->pluck('barcode')->filter()->unique()->values();

        RefurbishingItem::whereIn('id', $itemIds)->update(['status' => $validated['status']]);

        if ($barcodes->isNotEmpty()) {
            Inventory::whereIn('barcode', $barcodes)->update(['status' => $validated['status']]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$items->count()} item(s) updated to {$validated['status']} across all records!",
        ]);
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($itemId, Request $request)
    {
        $item = RefurbishingItem::find($itemId);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $newQuantity = $request->input('quantity');
        if (is_numeric($newQuantity) && $newQuantity >= 0) {
            $item->update(['quantity' => (int)$newQuantity]);
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Quantity updated successfully!', 'quantity' => $item->quantity]);
        }

        return redirect()->back();
    }

    /**
     * Delete photo from item
     */
    public function deletePhoto($itemId, Request $request)
    {
        $item = RefurbishingItem::find($itemId);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $photoPath = ltrim((string) $request->input('photo_path'), '/');
        
        if (Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        } elseif (file_exists(public_path($photoPath))) {
            @unlink(public_path($photoPath));
        }

        $photoPaths = array_filter($item->photos_paths, function ($p) use ($photoPath) {
            return ltrim((string) $p, '/') !== $photoPath;
        });
        $item->update(['photos_paths' => array_values($photoPaths)]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Photo deleted successfully!']);
        }

        return redirect()->back();
    }

    /**
     * Delete item
     */
    public function delete($itemId)
    {
        try {
            $item = RefurbishingItem::find($itemId);
            if (!$item) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'Item not found'], 404);
                }
                abort(404);
            }

            // Delete the item
            $item->delete();

            if (request()->ajax()) {
                return response()->json(['message' => 'Item deleted successfully!']);
            }

            return redirect()->back()->with('message', 'Item deleted successfully!');
        } catch (\Exception $e) {
            $errorMsg = 'Failed to delete item: ' . $e->getMessage();
            if (request()->ajax()) {
                return response()->json(['error' => $errorMsg], 422);
            }
            return redirect()->back()->with('error', $errorMsg);
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
                'ids.*' => 'integer|exists:refurbishing_items,id',
            ]);

            $items = RefurbishingItem::whereIn('id', $validated['ids'])->get();

            foreach ($items as $item) {
                $item->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "{$items->count()} item(s) deleted successfully!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete selected items: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Prepare to transfer to inventory
     */
    public function prepareToInventory($itemId)
    {
        $item = RefurbishingItem::find($itemId);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $normalized = ItemReuseAdjustmentService::normalize($item->toArray());
        $netWeight = $normalized['net_weight'];
        $netQuantity = $normalized['net_quantity'];

        // Get category hierarchy from categories table
        $categoryHierarchy = [
            'category' => $item->category,
            'sub_category' => $item->sub_category,
            'sub_sub_category' => $item->sub_sub_category,
            'category_id' => $item->category_id,
            'sub_category_id' => $item->sub_category_id,
            'sub_sub_category_id' => $item->sub_sub_category_id,
        ];

        // Fetch from categories table if IDs are stored
        if ($item->category_id) {
            $category = \App\Store\Models\Category::find($item->category_id);
            if ($category) {
                $hierarchy = $category->getHierarchy();
                $categoryHierarchy = array_merge($categoryHierarchy, $hierarchy);
            }
        }

        $inventoryData = [
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
            'net_quantity' => $netQuantity,
            'net_weight' => $netWeight,
            'lot_info' => '',
            'serial_number' => $item->serial_number,
            'status' => 'Pending',
            'pallet_info' => $item->pallet_number,
            'device_specs' => '',
            'notes' => $item->notes,
            'operating_system' => '',
            'put_away_location' => $item->item_placement ?? '',
        ];

        if (request()->ajax()) {
            return response()->json([
                'item' => $item,
                'inventoryData' => $inventoryData,
                'netWeight' => $netWeight,
                'netQuantity' => $netQuantity,
            ]);
        }

        return response()->json($inventoryData);
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
     * Save to inventory
     */
    public function saveToInventory($itemId, Request $request)
    {
        try {
            $item = RefurbishingItem::find($itemId);
            if (!$item) {
                return response()->json(['error' => 'Item not found'], 404);
            }

            $validated = $request->validate([
                'technician' => 'nullable|string',
                'customer_phone' => 'nullable|string',
                'customer_email' => 'nullable|email',
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
                'notes' => 'nullable|string',
                'operating_system' => 'nullable|string',
                'put_away_location' => 'nullable|string',
                'transferred_qty' => 'nullable|integer|min:0',
                'barcode' => 'nullable|string',
                'selected_shippo_rates' => 'nullable|string',
                'shippo_request_payload' => 'nullable|string',
                'parcel_length' => 'nullable|numeric|min:0',
                'parcel_width' => 'nullable|numeric|min:0',
                'parcel_height' => 'nullable|numeric|min:0',
                'parcel_weight' => 'nullable|numeric|min:0',
                'distance_unit' => 'nullable|string|max:10',
                'mass_unit' => 'nullable|string|max:10',
            ]);

            // Handle repair photos upload
            $repairPhotosPaths = [];
            if ($request->hasFile('repair_photos')) {
                foreach ($request->file('repair_photos') as $photo) {
                    try {
                        // Store directly in public/repair folder
                        $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                        $photo->move(public_path('repair'), $filename);
                        $repairPhotosPaths[] = 'repair/' . $filename;
                    } catch (\Exception $e) {
                        \Log::warning('Failed to upload repair photo: ' . $e->getMessage());
                    }
                }
            }

            $transferredQty = (int) $request->input('transferred_qty', $request->input('quantity', 0));
            $normalized = ItemReuseAdjustmentService::normalize($item->toArray());
            $netWeight = $normalized['net_weight'];
            $netQuantity = $normalized['net_quantity'];

            if ($transferredQty > $netQuantity) {
                return response()->json([
                    'success' => false,
                    'error' => "Only {$netQuantity} item(s) are available after reuse, scrap, and prior transfers.",
                ], 422);
            }

            // Create inventory record with transferred quantity
            $inventory = Inventory::create([
                'barcode' => $request->input('barcode') ?? $item->barcode,
                'name' => $item->name,
                'brand' => $item->brand ?? '',
                'model' => $item->model ?? '',
                'weight' => $netWeight,
                'intake_type' => 'Refurbishing',
                'photos_paths' => $item->photos_paths ?? [],
                'repair_photos' => $repairPhotosPaths,
                'condition_status' => $item->condition ?? 'Unknown',
                'pallet_number' => $item->pallet_number ?? '',
                'estimated_value' => $item->estimated_value ?? 0,
                'quantity' => $transferredQty,
                'reuse_quantity' => 0,  // New inventory items start with 0 reuse quantity
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
                'price' => $validated['price'] ?? $item->estimated_value ?? 0,
                'lot_info' => $validated['lot_info'] ?? '',
                'serial_number' => $validated['serial_number'] ?? $item->serial_number ?? '',
                'status' => $validated['status'] ?? 'Pending',
                'pallet_info' => $validated['pallet_info'] ?? '',
                'device_specs' => $validated['device_specs'] ?? '',
                'operating_system' => $validated['operating_system'] ?? '',
                'notes' => ($validated['notes'] ?? $item->notes ?? ''),
                'put_away_location' => $validated['put_away_location'] ?? '',
            ]);
            
            if (!$inventory) {
                throw new \Exception('Failed to create inventory record');
            }

            // Save parcel dimensions only at transfer time (no live rate fetch here).
            InventoryShippingRate::create([
                'inventory_id' => $inventory->id,
                'refurbishing_item_id' => $item->id,
                'parcel_length' => $request->filled('parcel_length') ? (float) $request->input('parcel_length') : null,
                'parcel_width' => $request->filled('parcel_width') ? (float) $request->input('parcel_width') : null,
                'parcel_height' => $request->filled('parcel_height') ? (float) $request->input('parcel_height') : null,
                'parcel_weight' => $request->filled('parcel_weight') ? (float) $request->input('parcel_weight') : null,
                'distance_unit' => $request->input('distance_unit') ?: null,
                'mass_unit' => $request->input('mass_unit') ?: null,
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

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Item #{$item->barcode} successfully added to inventory!",
                    'item' => $item->fresh(),
                ]);
            }

            return redirect()->back()->with('message', "Item #{$item->barcode} successfully added to inventory!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $errorMsg = implode(', ', $errors);
            \Log::error('Validation error in saveToInventory: ' . $errorMsg);
            
            if (request()->ajax()) {
                return response()->json(['success' => false, 'error' => 'Validation Error: ' . $errorMsg], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            $errorMsg = 'Error adding to inventory: ' . $e->getMessage();
            \Log::error('saveToInventory error: ' . $errorMsg);
            
            if (request()->ajax()) {
                return response()->json(['success' => false, 'error' => $errorMsg], 422);
            }
            return redirect()->back()->with('error', $errorMsg);
        }
    }

    /**
     * Get shipping rates from Shippo
     */
    public function getShippoRates(Request $request)
    {
        $validated = $request->validate([
            'length' => 'required|numeric|min:0.1',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'weight' => 'required|numeric|min:0.1',
            'distance_unit' => 'nullable|in:in,cm,ft,m,mm,yd',
            'mass_unit' => 'nullable|in:lb,oz,g,kg',
            'to_name' => 'nullable|string|max:120',
            'to_street1' => 'required|string|max:120',
            'to_city' => 'required|string|max:80',
            'to_state' => 'required|string|max:80',
            'to_zip' => 'required|string|max:20',
            'to_country' => 'required|string|size:2',
        ]);

        $shippoKey = env('SHIPPO_API_KEY');
        if (!$shippoKey || str_contains($shippoKey, 'xxxxxxxx')) {
            return response()->json(['error' => 'Shippo API key is not configured.'], 422);
        }

        $fromAddress = [
            'name' => env('SHIPPO_FROM_NAME', 'IT Investment Recoveries'),
            'street1' => env('SHIPPO_FROM_STREET1', '1000 W Mississippi Ave'),
            'city' => env('SHIPPO_FROM_CITY', 'Denver'),
            'state' => env('SHIPPO_FROM_STATE', 'CO'),
            'zip' => env('SHIPPO_FROM_ZIP', '80223'),
            'country' => env('SHIPPO_FROM_COUNTRY', 'US'),
        ];

        $shipmentPayload = [
            'address_from' => $fromAddress,
            'address_to' => [
                'name' => $validated['to_name'] ?? 'Customer',
                'street1' => $validated['to_street1'],
                'city' => $validated['to_city'],
                'state' => $validated['to_state'],
                'zip' => $validated['to_zip'],
                'country' => strtoupper($validated['to_country']),
            ],
            'parcels' => [[
                'length' => (string) $validated['length'],
                'width' => (string) $validated['width'],
                'height' => (string) $validated['height'],
                'distance_unit' => $validated['distance_unit'] ?? 'in',
                'weight' => (string) $validated['weight'],
                'mass_unit' => $validated['mass_unit'] ?? 'lb',
            ]],
            'async' => false,
        ];

        $response = Http::withHeaders([
                'Authorization' => 'ShippoToken ' . $shippoKey,
            ])
            ->acceptJson()
            ->post('https://api.goshippo.com/shipments/', $shipmentPayload);

        if (!$response->successful()) {
            $shippoBody = $response->json();
            $shippoError = 'Failed to fetch rates from Shippo.';
            if (is_array($shippoBody)) {
                if (!empty($shippoBody['detail'])) {
                    $shippoError = $shippoBody['detail'];
                } elseif (!empty($shippoBody['error'])) {
                    $shippoError = is_string($shippoBody['error']) ? $shippoBody['error'] : json_encode($shippoBody['error']);
                } elseif (!empty($shippoBody['messages']) && is_array($shippoBody['messages'])) {
                    $shippoError = collect($shippoBody['messages'])->map(function ($m) {
                        return is_array($m) ? ($m['text'] ?? json_encode($m)) : (string) $m;
                    })->implode(', ');
                }
            }
            return response()->json([
                'error' => $shippoError,
                'shippo' => $shippoBody,
            ], 422);
        }

        $rates = collect($response->json('rates', []))->map(function ($rate) {
            return [
                'provider' => $rate['provider'] ?? '',
                'servicelevel' => $rate['servicelevel']['name'] ?? '',
                'amount' => $rate['amount'] ?? '',
                'currency' => $rate['currency'] ?? '',
                'estimated_days' => $rate['estimated_days'] ?? null,
                'duration_terms' => $rate['duration_terms'] ?? '',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'rates' => $rates,
        ]);
    }

    /**
     * Export CSV
     */
    public function exportCsv(Request $request)
    {
        $query = RefurbishingItem::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%')
                    ->orWhere('brand', 'like', '%' . $request->search . '%')
                    ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('statusFilter') && !empty($request->statusFilter)) {
            $query->where('status', $request->statusFilter);
        }

        if ($request->has('palletFilter') && !empty($request->palletFilter)) {
            $query->where('pallet_number', $request->palletFilter);
        }

        $items = $query->get();

        $fileName = 'Refurbishing_Inventory_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Barcode', 'Name', 'Brand', 'Model', 'Serial Number', 'Category', 'Quantity', 'Status', 'Weight (lbs)', 'Value ($)', 'Pallet #', 'Received At'];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->barcode,
                    $item->name,
                    $item->brand,
                    $item->model,
                    $item->serial_number,
                    $item->category,
                    $item->quantity,
                    $item->status,
                    $item->weight,
                    number_format($item->estimated_value, 2),
                    $item->pallet_number ?: 'N/A',
                    $item->created_at ? $item->created_at->format('M d, Y H:i') : 'N/A',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
