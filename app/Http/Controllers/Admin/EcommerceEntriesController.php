<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceItem;
use App\Models\Inventory;
use App\Models\Pallet;
use App\Services\ItemReuseAdjustmentService;
use App\Services\PalletCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Picqer\Barcode\BarcodeGeneratorPNG;

class EcommerceEntriesController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $statusFilter = $request->input('statusFilter', '');
        $categoryFilter = $request->input('categoryFilter', '');
        $palletFilter = $request->input('palletFilter', '');

        $query = EcommerceItem::query();

        // Search filters
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%');
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($categoryFilter)) {
            $query->where('category', $categoryFilter);
        }

        if (!empty($palletFilter)) {
            $query->where('pallet_number', $palletFilter);
        }

        $items = $query->latest()->paginate(10, ['*'], 'page', $page);
        $pallets_list = Pallet::orderBy('barcode_number', 'asc')->get();
        $employees = \App\Models\Employee::where('is_resigned', 0)->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = \App\Models\User::orderBy('name')->get();
        }

        // For AJAX requests, return JSON
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
                'stats' => $this->getStats(),
            ]);
        }

        return view('admin.intake.ecommerce-entries', [
            'items' => $items,
            'stats' => $this->getStats(),
            'pallets_list' => $pallets_list,
            'employees' => $employees,
        ]);
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        return [
            ['label' => 'Total Items', 'value' => EcommerceItem::sum('quantity'), 'color' => 'text-slate-800'],
            ['label' => 'Received', 'value' => EcommerceItem::count(), 'color' => 'text-blue-600'],
            ['label' => 'In Progress', 'value' => EcommerceItem::where('status', 'In Progress')->count(), 'color' => 'text-orange-500'],
            ['label' => 'Live Online', 'value' => EcommerceItem::where('status', 'Live Online')->count(), 'color' => 'text-emerald-500'],
            ['label' => 'Completed', 'value' => EcommerceItem::where('status', 'Completed')->count(), 'color' => 'text-purple-500'],
        ];
    }

    /**
     * Show item details modal
     */
    public function showItemDetails($itemId)
    {
        $item = EcommerceItem::find($itemId);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
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
                $palletIntakeEmp = $pallet->intake_employee ?? $pallet->ecommerce_employee ?? null;
                if (empty($item->ecommerce_employee) && empty($item->intake_employee) && !empty($palletIntakeEmp)) {
                    $item->ecommerce_employee = $palletIntakeEmp;
                    $item->intake_employee = $palletIntakeEmp;
                }
            }
        }

        return response()->json(['item' => $item]);
    }

    /**
     * Load edit modal data
     */
    public function loadEditModal($itemId)
    {
        $item = EcommerceItem::find($itemId);
        $pallets_list = Pallet::orderBy('barcode_number', 'asc')->get();

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
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
                $palletIntakeEmp = $pallet->intake_employee ?? $pallet->ecommerce_employee ?? null;
                if (empty($item->ecommerce_employee) && empty($item->intake_employee) && !empty($palletIntakeEmp)) {
                    $item->ecommerce_employee = $palletIntakeEmp;
                    $item->intake_employee = $palletIntakeEmp;
                }
            }
        }

        return response()->json([
            'item' => $item,
            'pallets_list' => $pallets_list,
        ]);
    }

    /**
     * Update item
     */
    public function update(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
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
            'reuse_quantity' => 'required|integer|min:0',
            'scrap_quantity' => 'nullable|integer|min:0',
            'pallet_number' => 'nullable|string',
            'item_placement' => 'nullable|string',
            'receiving_employee' => 'nullable|string|max:255',
            'processing_employee' => 'nullable|string|max:255',
            'ecommerce_employee' => 'nullable|string|max:255',
            'intake_employee' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = EcommerceItem::find($itemId);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $previousPalletNumber = $item->pallet_number;

        // Handle photo uploads
        $photoPaths = $item->photos_paths ?? [];

        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $photo) {
                $photoPaths[] = $photo->store('items', 'public');
            }
        }

        $normalized = ItemReuseAdjustmentService::normalize([
            'quantity' => $request->quantity,
            'reuse_quantity' => $request->reuse_quantity ?? 0,
            'scrap_quantity' => $request->scrap_quantity ?? 0,
            'transferred_quantity' => $item->transferred_quantity ?? 0,
            'weight' => $request->weight ?? 0,
            'reuse_weight' => $request->reuse_weight ?? 0,
            'scrap_weight' => $request->scrap_weight ?? 0,
        ]);
        $sanitizedPalletNumber = trim((string) ($request->pallet_number ?? ''));
        if (in_array(strtolower($sanitizedPalletNumber), ['', 'undefined', 'null'], true)) {
            $sanitizedPalletNumber = null;
        }

        $ecommerceEmpVal = $request->ecommerce_employee ?? $request->intake_employee;

        // Update item
        $item->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'category' => $request->category,
            'sub_category' => $request->sub_category,
            'sub_sub_category' => $request->sub_sub_category,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'sub_sub_category_id' => $request->sub_sub_category_id,
            'condition' => $request->condition,
            'estimated_value' => $request->estimated_value,
            'weight' => $normalized['weight'],
            'reuse_weight' => $normalized['reuse_weight'],
            'scrap_weight' => $normalized['scrap_weight'] ?? 0,
            'quantity' => $normalized['quantity'],
            'reuse_quantity' => $normalized['reuse_quantity'],
            'scrap_quantity' => $normalized['scrap_quantity'] ?? 0,
            'transferred_quantity' => $normalized['transferred_quantity'],
            'pallet_number' => $sanitizedPalletNumber,
            'item_placement' => $request->item_placement,
            'receiving_employee' => $request->receiving_employee,
            'processing_employee' => $request->processing_employee,
            'ecommerce_employee' => $ecommerceEmpVal,
            'intake_employee' => $ecommerceEmpVal,
            'status' => $item->status,
            'notes' => $request->notes,
            'photos_paths' => $photoPaths,
        ]);

        app(PalletCompletionService::class)->syncForPallet($previousPalletNumber);
        app(PalletCompletionService::class)->syncForPallet($sanitizedPalletNumber);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully!',
            'item' => $item->fresh(),
        ]);
    }

    /**
     * Update item status
     */
    public function updateStatus(Request $request, $itemId)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $item = EcommerceItem::find($itemId);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $item->update(['status' => $request->status]);

        // Update inventory records with same barcode
        Inventory::where('barcode', $item->barcode)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => "Status updated to {$request->status} across all records!",
        ]);
    }

    /**
     * Update multiple item statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:ecommerce_items,id',
            'status' => 'required|string|max:100',
        ]);

        $items = EcommerceItem::whereIn('id', $validated['ids'])->get();
        $itemIds = $items->pluck('id');
        $barcodes = $items->pluck('barcode')->filter()->unique()->values();

        EcommerceItem::whereIn('id', $itemIds)->update(['status' => $validated['status']]);

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
    public function updateQuantity(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = EcommerceItem::find($itemId);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        if (is_numeric($request->quantity) && $request->quantity >= 0) {
            $item->update(['quantity' => $request->quantity]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated!',
        ]);
    }

    /**
     * Delete item
     */
    public function delete($itemId)
    {
        $item = EcommerceItem::find($itemId);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully!',
        ]);
    }

    /**
     * Delete multiple items
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:ecommerce_items,id',
        ]);

        $items = EcommerceItem::whereIn('id', $validated['ids'])->get();

        foreach ($items as $item) {
            $item->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "{$items->count()} item(s) deleted successfully!",
        ]);
    }

    /**
     * Delete photo from item
     */
    public function deletePhoto(Request $request, $itemId)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $item = EcommerceItem::find($itemId);

        if (!$item || empty($item->photos_paths)) {
            return response()->json(['error' => 'Item or photos not found'], 404);
        }

        $photoPaths = $item->photos_paths;
        $updatedPaths = array_values(array_filter($photoPaths, fn($p) => $p !== $request->path));

        $item->update(['photos_paths' => $updatedPaths]);

        if (Storage::disk('public')->exists($request->path)) {
            Storage::disk('public')->delete($request->path);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully!',
            'updated_paths' => $updatedPaths,
        ]);
    }

    /**
     * Prepare item for transfer to inventory
     */
    public function prepareToInventory($itemId)
    {
        $item = EcommerceItem::find($itemId);

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
            'device_type' => $item->brand . ' ' . $item->model,
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
            'reuse_quantity' => $item->reuse_quantity ?? 0,
            'scrap_quantity' => $item->scrap_quantity ?? 0,
            'lot_info' => '',
            'serial_number' => $item->serial_number,
            'status' => 'Pending',
            'pallet_info' => $item->pallet_number,
            'device_specs' => '',
            'notes' => $item->notes,
            'operating_system' => '',
            'put_away_location' => $item->item_placement ?? '',
        ];

        return response()->json([
            'item' => $item,
            'inventoryData' => $inventoryData,
        ]);
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
     * Save item to inventory
     */
    public function saveToInventory(Request $request, $itemId)
    {
        $item = EcommerceItem::find($itemId);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $transferredQty = (int) $request->input('transferred_qty', $request->input('quantity', 0));
        
        // Net calculations
        $normalized = ItemReuseAdjustmentService::normalize($item->toArray());
        $netWeight = $normalized['net_weight'];
        $netQuantity = $normalized['net_quantity'];

        if ($transferredQty > $netQuantity) {
            return response()->json([
                'error' => "Only {$netQuantity} item(s) are available after reuse, scrap, and prior transfers.",
            ], 422);
        }

        try {
            // Handle repair photos
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

            Inventory::create([
                'barcode' => $item->barcode,
                'name' => $item->name,
                'brand' => $item->brand,
                'model' => $item->model,
                'weight' => $netWeight,
                'intake_type' => 'E-commerce',
                'photos_paths' => $item->photos_paths ?? [],
                'repair_photos' => $repairPhotosPaths,
                'condition_status' => $item->condition,
                'pallet_number' => $item->pallet_number,
                'estimated_value' => $item->estimated_value,
                'technician' => $request->input('technician', ''),
                'customer_phone' => $request->input('customer_phone', ''),
                'customer_email' => $request->input('customer_email', ''),
                'device_type' => $request->input('device_type', $item->brand . ' ' . $item->model),
                'category' => $request->input('category', $item->category),
                'sub_category' => $request->input('sub_category', $item->sub_category),
                'sub_sub_category' => $request->input('sub_sub_category', $item->sub_sub_category),
                'category_id' => $request->input('category_id', $item->category_id),
                'sub_category_id' => $request->input('sub_category_id', $item->sub_category_id),
                'sub_sub_category_id' => $request->input('sub_sub_category_id', $item->sub_sub_category_id),
                'brand_category' => $request->input('brand_category', ''),
                'price' => $request->input('price', $item->estimated_value),
                'quantity' => $transferredQty,
                'lot_info' => $request->input('lot_info', ''),
                'serial_number' => $request->input('serial_number', $item->serial_number),
                'status' => $request->input('status', 'Pending'),
                'pallet_info' => $request->input('pallet_info', $item->pallet_number),
                'device_specs' => $request->input('device_specs', ''),
                'operating_system' => $request->input('operating_system', ''),
                'notes' => $request->input('notes', $item->notes),
                'put_away_location' => $request->input('put_away_location', $item->item_placement ?? ''),
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
                'message' => "Item #{$item->barcode} successfully added to Inventory!",
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export CSV
     */
    public function exportCsv(Request $request)
    {
        $search = $request->input('search', '');
        $statusFilter = $request->input('statusFilter', '');

        $query = EcommerceItem::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $items = $query->get();

        $fileName = 'inventory_export_' . now()->format('Y-m-d_H-i') . '.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Barcode', 'Name', 'Brand', 'Category', 'Quantity', 'Status', 'Value', 'Received Date'];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->barcode,
                    $item->name,
                    $item->brand,
                    $item->category,
                    $item->quantity,
                    $item->status,
                    $item->estimated_value,
                    $item->created_at ? $item->created_at->format('M d, Y') : 'N/A',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
