<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryShippingRate;
use App\Models\Pallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Mail\OrderConfirmation;
use App\Services\Ebay\EbayOrderSyncService;
use App\Services\Ebay\EbayInventorySyncService;

class InventoryController extends Controller
{
    private const TAX_RATE = 0.08;

    /**
     * Display inventory listing page
     */
    public function index()
    {
        $pallets_list = Pallet::select('id', 'barcode_number')->orderBy('barcode_number', 'asc')->get();
        return view('admin.inventory.inventory-management', ['pallets_list' => $pallets_list]);
    }

    /**
     * Load inventory data with filters and pagination (AJAX)
     */
    public function loadData(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $statusFilter = $request->get('statusFilter', '');
            $intakeFilter = $request->get('intakeFilter', '');
            $categoryFilter = $request->get('categoryFilter', '');
            $palletFilter = $request->get('palletFilter', '');
            $dateFilter = $request->get('dateFilter', '');
            $page = $request->get('page', 1);

            $query = Inventory::query();

            // Apply filters
            if ($search) {
                $cleanSearch = trim($search);

                // Extract numeric eBay Item ID if an eBay link or item ID was pasted
                $ebayItemId = null;
                if (preg_match('/(?:itm|item|sys\/id|itm\/[^\/]+)\/(\d{9,14})/i', $cleanSearch, $matches)) {
                    $ebayItemId = $matches[1];
                } elseif (preg_match('/^\d{9,14}$/', $cleanSearch)) {
                    $ebayItemId = $cleanSearch;
                }

                $query->where(function ($q) use ($cleanSearch, $ebayItemId) {
                    $q->where('name', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('device_type', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('barcode', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('brand', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('model', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('serial_number', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('notes', 'like', '%' . $cleanSearch . '%')
                        ->orWhere('intake_type', 'like', '%' . $cleanSearch . '%');

                    if ($ebayItemId) {
                        $q->orWhere('barcode', $ebayItemId)
                          ->orWhere('notes', 'like', '%' . $ebayItemId . '%');
                    }
                });
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            if ($intakeFilter) {
                if (strtolower($intakeFilter) === 'denver') {
                    $query->where(function ($q) {
                        $q->where('intake_type', 'Denver')
                          ->orWhere(function ($sub) {
                              $sub->whereIn('intake_type', ['eBay', 'Ebay', 'Ebay Product'])
                                  ->where('sync_status', 1);
                          });
                    });
                } elseif (strtolower($intakeFilter) === 'ebay') {
                    $query->where(function ($q) {
                        $q->whereIn('intake_type', ['eBay', 'Ebay'])
                          ->where(function ($sub) {
                              $sub->whereNull('sync_status')
                                  ->orWhere('sync_status', '!=', 1);
                          });
                    });
                } elseif (strtolower($intakeFilter) === 'both') {
                    $query->where(function ($q) {
                        $q->where('intake_type', 'Denver')
                          ->orWhereIn('intake_type', ['eBay', 'Ebay', 'Ebay Product']);
                    });
                } else {
                    $query->where('intake_type', $intakeFilter);
                }
            }

            if ($categoryFilter) {
                $query->where('category', $categoryFilter);
            }

            if ($request->filled('subCategoryFilter')) {
                $query->where('sub_category', $request->get('subCategoryFilter'));
            }

            if ($request->filled('subSubCategoryFilter')) {
                $query->where('sub_sub_category', $request->get('subSubCategoryFilter'));
            }

            if ($palletFilter) {
                $query->where('pallet_number', $palletFilter);
            }

            if ($dateFilter) {
                $query->whereDate('created_at', $dateFilter);
            }

            $perPage = 15;
            $items = $query->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'items' => $items->items(),
                'total' => $items->total(),
                'per_page' => $perPage,
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get inventory stats
     */
    public function getStats()
    {
        try {
            $stats = [
                ['label' => 'Received', 'value' => Inventory::whereIn('status', ['Recieved', 'Received', 'Pending'])->count(), 'color' => 'text-blue-600'],
                ['label' => 'In Stock', 'value' => Inventory::whereIn('status', ['In Stock', 'Ready'])->count(), 'color' => 'text-emerald-600'],
                ['label' => 'Live Online', 'value' => Inventory::whereIn('status', ['LIVE ONLINE', 'Live Online'])->count(), 'color' => 'text-indigo-600'],
                ['label' => 'Sold', 'value' => Inventory::where('status', 'Sold')->count(), 'color' => 'text-amber-500'],
                ['label' => 'Completed', 'value' => Inventory::whereIn('status', ['COMPLETED', 'Completed'])->count(), 'color' => 'text-purple-500'],
            ];

            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show item details (AJAX)
     */
    public function show($id)
    {
        try {
            $item = Inventory::with('shippingRates')->findOrFail($id);

            // Find any existing sales invoice linked to this inventory item
            $invoiceItem = SalesInvoiceItem::with('invoice')
                ->where('inventory_id', $item->id)
                ->whereHas('invoice')
                ->latest('id')
                ->first();

            $invoice = $invoiceItem?->invoice;

            // Fallback: check if invoice message or item description contains barcode
            if (!$invoice && !empty($item->barcode)) {
                $invoiceItemFallback = SalesInvoiceItem::with('invoice')
                    ->where('description', 'LIKE', '%' . $item->barcode . '%')
                    ->whereHas('invoice')
                    ->latest('id')
                    ->first();
                $invoice = $invoiceItemFallback?->invoice;

                if (!$invoice) {
                    $invoice = SalesInvoice::where('message', 'LIKE', '%' . $item->barcode . '%')
                        ->latest('id')
                        ->first();
                }
            }

            $invoiceData = null;
            if ($invoice) {
                $statusBadgeClass = SalesInvoice::STATUS_BADGE_CLASSES[$invoice->status] ?? 'bg-slate-100 text-slate-700';
                $invoiceData = [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'status' => $invoice->status,
                    'status_label' => ucfirst($invoice->status),
                    'status_badge_class' => $statusBadgeClass,
                    'total' => (float) $invoice->total,
                    'customer_name' => $invoice->customer_name,
                    'service_date' => $invoice->service_date ? $invoice->service_date->format('Y-m-d') : ($invoice->created_at ? $invoice->created_at->format('Y-m-d') : null),
                    'print_url' => route('admin.sales.invoices.print', $invoice->id) . '?embedded=1',
                    'view_url' => route('admin.sales.invoices.show', $invoice->id),
                ];
            }

            $createInvoiceUrl = route('admin.sales.invoices.create', ['inventory_id' => $item->id]);
            $storeProductUrl = 'https://denverdiscountcomputers.com/product/' . $item->id;

            return response()->json([
                'success' => true,
                'item' => $item,
                'has_invoice' => !empty($invoiceData),
                'invoice' => $invoiceData,
                'create_invoice_url' => $createInvoiceUrl,
                'store_product_url' => $storeProductUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Load edit modal data
     */
    public function loadEditModal($id)
    {
        try {
            $item = Inventory::with('shippingRates')->findOrFail($id);
            $pallets_list = Pallet::select('id', 'barcode_number')->orderBy('barcode_number', 'asc')->get();

            return response()->json([
                'success' => true,
                'item' => $item,
                'pallets_list' => $pallets_list
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update inventory item
     */
    public function update(Request $request, $id)
    {
        try {
            $item = Inventory::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'reuse_quantity' => 'nullable|integer|min:0',
                'weight' => 'nullable|numeric|min:0',
                'reuse_weight' => 'nullable|numeric|min:0',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255',
                'category' => 'required|string|max:255',
                'technician' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:255',
                'customer_email' => 'nullable|string|max:255',
                'device_type' => 'nullable|string|max:255',
                'brand_category' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|max:255',
                'lot_info' => 'nullable|string|max:255',
                'pallet_info' => 'nullable|string|max:255',
                'device_specs' => 'nullable|string',
                'operating_system' => 'nullable|string|max:255',
                'put_away_location' => 'nullable|string|max:255',
                'sub_category' => 'nullable|string|max:255',
                'sub_sub_category' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer',
                'sub_category_id' => 'nullable|integer',
                'sub_sub_category_id' => 'nullable|integer',
                'estimated_value' => 'nullable|numeric|min:0',
                'pallet_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'parcel_length' => 'nullable|numeric|min:0',
                'parcel_width' => 'nullable|numeric|min:0',
                'parcel_height' => 'nullable|numeric|min:0',
                'parcel_weight' => 'nullable|numeric|min:0',
                'distance_unit' => 'nullable|string|max:10',
                'mass_unit' => 'nullable|string|max:10',
                'existing_photos_order' => 'nullable|array',
                'repair_photo_order' => 'nullable|array',
                'repair_photo_order.*' => 'nullable|string|max:1500',
                'existing_repair_photos_order' => 'nullable|array',
                'removed_existing_repair_photos' => 'nullable|array',
                'removed_existing_repair_photos.*' => 'nullable|string|max:1024',
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'repair_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            // Check if reuse_quantity exceeds quantity
            $netQty = (int) $validated['quantity'];
            if ($netQty < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reuse quantity cannot exceed total quantity!'
                ], 422);
            }

            // Handle new photos (inventory photos)
            $uploadedPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    if ($photo && $photo->isValid()) {
                        $uploadedPaths[] = $photo->store('inventory-photos', 'public');
                    }
                }
            }

            // Merge with existing photos_paths, respecting client-side order if provided
            $existingPhotos = $this->normalizePhotoPaths($item->photos_paths ?? []);
            if ($request->has('existing_photos_order')) {
                $requestedOrder = $this->normalizePhotoPaths($request->input('existing_photos_order', []));
                $ordered = array_values(array_filter($requestedOrder, function ($p) use ($existingPhotos) {
                    return in_array($p, $existingPhotos, true);
                }));
                $finalPhotos = array_merge($ordered, $uploadedPaths);
            } else {
                $finalPhotos = array_merge($existingPhotos, $uploadedPaths);
            }
            $validated['photos_paths'] = $finalPhotos;

            // Handle repair_photos uploads and ordering (used by edit repair modal)
            $uploadedRepairPaths = [];
            if ($request->hasFile('repair_photos')) {
                foreach ($request->file('repair_photos') as $photo) {
                    if ($photo && $photo->isValid()) {
                        try {
                            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                            $photo->move(public_path('repair'), $filename);
                            $uploadedRepairPaths[] = 'repair/' . $filename;
                        } catch (\Exception $e) {
                            \Log::warning('Failed to store repair photo: ' . $e->getMessage());
                        }
                    }
                }
            }

            $existingRepair = $this->normalizePhotoPaths($item->repair_photos ?? []);
            $requestedRemovedRepairPhotos = $this->normalizePhotoPaths($request->input('removed_existing_repair_photos', []));
            $removedExistingRepairPhotos = array_values(array_unique(array_filter(
                $requestedRemovedRepairPhotos,
                fn ($path) => in_array($path, $existingRepair, true)
            )));

            foreach ($removedExistingRepairPhotos as $removedRepairPhotoPath) {
                $this->deleteStoredPhotoPath($removedRepairPhotoPath);
            }

            $availableExistingRepair = array_values(array_filter(
                $existingRepair,
                fn ($path) => !in_array($path, $removedExistingRepairPhotos, true)
            ));

            if ($request->has('repair_photo_order')) {
                $finalRepair = [];
                foreach ((array) $request->input('repair_photo_order', []) as $token) {
                    if (!is_string($token)) {
                        continue;
                    }

                    if (str_starts_with($token, 'existing:')) {
                        $path = ltrim(substr($token, strlen('existing:')), '/');
                        if (in_array($path, $availableExistingRepair, true) && !in_array($path, $finalRepair, true)) {
                            $finalRepair[] = $path;
                        }
                    }

                    if (str_starts_with($token, 'upload:')) {
                        $uploadIndex = (int) substr($token, strlen('upload:'));
                        if (isset($uploadedRepairPaths[$uploadIndex]) && !in_array($uploadedRepairPaths[$uploadIndex], $finalRepair, true)) {
                            $finalRepair[] = $uploadedRepairPaths[$uploadIndex];
                        }
                    }
                }

                foreach (array_merge($availableExistingRepair, $uploadedRepairPaths) as $repairPhotoPath) {
                    if (!in_array($repairPhotoPath, $finalRepair, true)) {
                        $finalRepair[] = $repairPhotoPath;
                    }
                }
            } elseif ($request->has('existing_repair_photos_order')) {
                $requestedRepairOrder = $this->normalizePhotoPaths($request->input('existing_repair_photos_order', []));
                $orderedRepair = array_values(array_filter($requestedRepairOrder, function ($p) use ($availableExistingRepair) {
                    return in_array($p, $availableExistingRepair, true);
                }));
                $finalRepair = array_merge($orderedRepair, $uploadedRepairPaths);
            } else {
                $finalRepair = array_merge($availableExistingRepair, $uploadedRepairPaths);
            }
            $validated['repair_photos'] = $finalRepair;

            $parcelData = [
                'parcel_length' => $request->filled('parcel_length') ? (float) $request->input('parcel_length') : null,
                'parcel_width' => $request->filled('parcel_width') ? (float) $request->input('parcel_width') : null,
                'parcel_height' => $request->filled('parcel_height') ? (float) $request->input('parcel_height') : null,
                'parcel_weight' => $request->filled('parcel_weight') ? (float) $request->input('parcel_weight') : null,
                'distance_unit' => $request->input('distance_unit') ?: 'in',
                'mass_unit' => $request->input('mass_unit') ?: 'lb',
            ];

            $oldQuantity = (int) $item->quantity;
            $oldReuseQty = (int) ($item->reuse_quantity ?? 0);

            $item->update($validated);

            $newQuantity = (int) $item->quantity;
            $newReuseQty = (int) ($item->reuse_quantity ?? 0);

            // If quantity or reuse quantity changed, directly synchronize to eBay
            if ($oldQuantity !== $newQuantity || $oldReuseQty !== $newReuseQty) {
                try {
                    app(EbayInventorySyncService::class)->syncInventoryQuantityToEbay($item->id);
                } catch (\Throwable $syncEx) {
                    \Log::error("Direct eBay inventory sync failed on update for inventory #{$item->id}: " . $syncEx->getMessage());
                }
            }

            if ($request->filled('parcel_length') || $request->filled('parcel_width') || $request->filled('parcel_height') || $request->filled('parcel_weight')) {
                $shippingRate = InventoryShippingRate::where('inventory_id', $item->id)->latest()->first();
                if ($shippingRate) {
                    $shippingRate->update($parcelData);
                } else {
                    InventoryShippingRate::create(array_merge($parcelData, ['inventory_id' => $item->id]));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Inventory item updated successfully!',
                'item' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $item = Inventory::findOrFail($id);
            $newStatus = $request->get('status');

            $item->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Status changed to {$newStatus}"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove image background using a segmentation service.
     */
    public function removeBackground(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $apiKey = config('services.remove_bg.api_key');
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Background removal API key is not configured. Add REMOVE_BG_API_KEY to your .env file.',
            ], 422);
        }

        $image = $request->file('image');

        try {
            $response = Http::timeout(60)
                ->withHeaders(['X-Api-Key' => $apiKey])
                ->attach(
                    'image_file',
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName() ?: 'repair-photo.png'
                )
                ->post('https://api.remove.bg/v1.0/removebg', [
                    'size' => config('services.remove_bg.size', 'preview'),
                    'format' => 'png',
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json('errors.0.title')
                        ?? $response->json('errors.0.detail')
                        ?? 'Background removal failed.',
                ], $response->status() >= 400 ? $response->status() : 502);
            }

            return response($response->body(), 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Background removal service could not be reached.',
            ], 502);
        }
    }

    /**
     * Delete photo
     */
    public function deletePhoto(Request $request, $id)
    {
        try {
            $item = Inventory::findOrFail($id);
            $photoPath = ltrim((string) $request->get('photo_path'), '/');

            $photos = $this->normalizePhotoPaths($item->photos_paths ?? []);
            $repair = $this->normalizePhotoPaths($item->repair_photos ?? []);

            if (($key = array_search($photoPath, $photos)) !== false) {
                unset($photos[$key]);
                $photos = array_values($photos);

                $this->deleteStoredPhotoPath($photoPath);

                $item->update(['photos_paths' => $photos]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully!'
                ]);
            }

            if (($key = array_search($photoPath, $repair)) !== false) {
                unset($repair[$key]);
                $repair = array_values($repair);

                $this->deleteStoredPhotoPath($photoPath);

                $item->update(['repair_photos' => $repair]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully!'
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Photo not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function normalizePhotoPaths($paths): array
    {
        if (is_string($paths)) {
            $decoded = json_decode($paths, true);
            $paths = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($paths)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) {
            if (!is_string($path)) {
                return null;
            }

            $normalized = ltrim(trim($path), '/');

            return $normalized !== '' ? $normalized : null;
        }, $paths)));
    }

    private function deleteStoredPhotoPath(string $photoPath): void
    {
        $normalizedPath = ltrim(trim($photoPath), '/');
        if ($normalizedPath === '' || str_contains($normalizedPath, '..')) {
            return;
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
            return;
        }

        $publicPath = public_path($normalizedPath);
        $publicRoot = realpath(public_path());
        $realPublicPath = realpath($publicPath);

        if (
            $publicRoot &&
            $realPublicPath &&
            str_starts_with($realPublicPath, $publicRoot . DIRECTORY_SEPARATOR) &&
            is_file($realPublicPath)
        ) {
            @unlink($realPublicPath);
        }
    }

    /**
     * Delete item
     */
    public function delete($id)
    {
        try {
            $item = Inventory::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from inventory successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete multiple inventory items
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'required|integer|distinct',
        ]);

        try {
            $deletedCount = DB::transaction(function () use ($validated) {
                $items = Inventory::whereIn('id', $validated['ids'])->get();

                foreach ($items as $item) {
                    $item->delete();
                }

                return $items->count();
            });

            if ($deletedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching inventory items were found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} inventory item(s) deleted successfully!",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete all inventory items with 0 or negative/null quantity
     */
    public function deleteZeroQuantity(Request $request)
    {
        try {
            $force = $request->boolean('force', false);

            $query = Inventory::where(function ($q) {
                $q->where('quantity', '<=', 0)->orWhereNull('quantity');
            });

            $count = $query->count();

            if ($count === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items with 0 quantity found in inventory.'
                ]);
            }

            if ($force) {
                $query->forceDelete();
            } else {
                $query->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} zero-quantity item(s) removed from inventory successfully!",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all eBay inventory listings
     */
    public function deleteAllEbay(Request $request)
    {
        try {
            $deletedCount = DB::transaction(function () {
                $items = Inventory::where('intake_type', 'eBay')->get();
                $count = 0;
                foreach ($items as $item) {
                    $item->delete();
                    $count++;
                }
                return $count;
            });

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} eBay inventory item(s).",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete all eBay inventory items failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete eBay items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync item to store (update sync_status to 1)
     */
    public function syncToStore($id)
    {
        try {
            $item = Inventory::findOrFail($id);

            // Update sync status, item status, and timestamp
            $item->sync_status = 1;
            $item->status = 'In Stock';
            $item->synced_at = now();

            // When product fetched from eBay is synced to store, intake type becomes Denver
            $isEbay = in_array(strtolower($item->intake_type ?? ''), ['ebay', 'ebay product', 'e-commerce', 'ecommerce', 'denver', ''])
                || str_starts_with((string) $item->barcode, 'v1|');
            if ($isEbay) {
                $item->intake_type = 'Denver';
            }

            $item->save();

            \Log::info("Item synced to store", [
                'item_id' => $id,
                'barcode' => $item->barcode,
                'name' => $item->name,
                'status' => $item->status,
                'intake_type' => $item->intake_type,
            ]);

            $storeProductUrl = 'https://denverdiscountcomputers.com/product/' . $item->id;

            return response()->json([
                'success' => true,
                'message' => 'Item synced to store successfully!',
                'store_product_url' => $storeProductUrl,
                'item' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error("Sync to store failed", [
                'item_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $statusFilter = $request->get('statusFilter', '');
            $intakeFilter = $request->get('intakeFilter', '');
            $categoryFilter = $request->get('categoryFilter', '');
            $palletFilter = $request->get('palletFilter', '');
            $dateFilter = $request->get('dateFilter', '');

            $query = Inventory::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%')
                        ->orWhere('brand', 'like', '%' . $search . '%')
                        ->orWhere('serial_number', 'like', '%' . $search . '%');
                });
            }

            if ($statusFilter)
                $query->where('status', $statusFilter);
            if ($intakeFilter) {
                if (strtolower($intakeFilter) === 'denver') {
                    $query->where(function ($q) {
                        $q->where('intake_type', 'Denver')
                          ->orWhere(function ($sub) {
                              $sub->whereIn('intake_type', ['eBay', 'Ebay', 'Ebay Product'])
                                  ->where('sync_status', 1);
                          });
                    });
                } elseif (strtolower($intakeFilter) === 'ebay') {
                    $query->where(function ($q) {
                        $q->whereIn('intake_type', ['eBay', 'Ebay'])
                          ->where(function ($sub) {
                              $sub->whereNull('sync_status')
                                  ->orWhere('sync_status', '!=', 1);
                          });
                    });
                } elseif (strtolower($intakeFilter) === 'both') {
                    $query->where(function ($q) {
                        $q->where('intake_type', 'Denver')
                          ->orWhereIn('intake_type', ['eBay', 'Ebay', 'Ebay Product']);
                    });
                } else {
                    $query->where('intake_type', $intakeFilter);
                }
            }
            if ($categoryFilter)
                $query->where('category', $categoryFilter);
            if ($request->filled('subCategoryFilter'))
                $query->where('sub_category', $request->get('subCategoryFilter'));
            if ($request->filled('subSubCategoryFilter'))
                $query->where('sub_sub_category', $request->get('subSubCategoryFilter'));
            if ($palletFilter)
                $query->where('pallet_number', $palletFilter);
            if ($dateFilter)
                $query->whereDate('created_at', $dateFilter);

            $items = $query->get();

            $fileName = 'Inventory_Report_' . now()->format('Y-m-d_His') . '.csv';

            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $columns = ['Barcode', 'Item Name', 'Brand', 'Model', 'Serial', 'Category', 'Intake Type', 'Qty', 'Value ($)', 'Pallet', 'Status', 'Date Added'];

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
                        $item->intake_type,
                        $item->quantity,
                        number_format($item->estimated_value, 2),
                        $item->pallet_number ?: 'N/A',
                        $item->status,
                        $item->created_at->format('M d, Y')
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get categories in a tree structure for dropdown population
     */
    public function getCategoriesTree()
    {
        try {
            $cached = \Illuminate\Support\Facades\Cache::get('inventory_categories_tree');
            if (!is_array($cached) && !($cached instanceof \Illuminate\Support\Collection)) {
                \Illuminate\Support\Facades\Cache::forget('inventory_categories_tree');
            }

            // Cache category tree for 1 hour to avoid repeated recursive queries
            $rootCategories = \Illuminate\Support\Facades\Cache::remember('inventory_categories_tree', 3600, function () {
                return \App\Store\Models\Category::whereNull('parent_id')
                    ->with('children')
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($category) {
                        return $this->buildCategoryTree($category);
                    })
                    ->values()
                    ->all();
            });

            if ($rootCategories instanceof \Illuminate\Support\Collection) {
                $rootCategories = $rootCategories->values()->all();
            }

            return response()->json([
                'success' => true,
                'categories' => is_array($rootCategories) ? array_values($rootCategories) : [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'categories' => [],
            ], 500);
        }
    }

    /**
     * Recursively build category tree with children
     */
    private function buildCategoryTree($category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'children' => $category->children ? $category->children->sortBy('name')->map(function ($child) {
                return $this->buildCategoryTree($child);
            })->values()->toArray() : [],
        ];
    }

    /**
     * Get saved shipping rates for an inventory item
     */
    public function getShippingRates($id)
    {
        try {
            $item = Inventory::findOrFail($id);
            $rates = InventoryShippingRate::where('inventory_id', $item->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'rates' => $rates,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Direct sell item to customer (store pickup, no payment)
     */
    public function directSell(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'nullable|string|max:20',
                'payment_method' => 'nullable|in:cash,cheque,square_card,card',
                'order_status' => ['required', Rule::in(Order::statusValues())],
                'sale_type' => 'required|string'
            ]);

            $paymentMethod = $validated['payment_method'] ?? 'cash';

            return DB::transaction(function () use ($validated, $id, $paymentMethod) {
                $item = Inventory::findOrFail($id);

                // Verify sufficient quantity using the same available calculation as inventory screens
                $availableQty = max(0, (int) $item->quantity - (int) ($item->reuse_quantity ?? 0));
                if ($validated['quantity'] > $availableQty) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient quantity available'
                    ], 422);
                }

                // Create order record for store pickup
                $orderNumber = 'ORD-' . strtoupper(uniqid()) . '-' . date('Ymd');
                $subtotal = round((float) $item->price * (int) $validated['quantity'], 2);
                $tax = round($subtotal * self::TAX_RATE, 2);
                $total = $subtotal + $tax;

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => $orderNumber,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'status' => $validated['order_status'],
                    'fulfillment_type' => 'store_pickup',
                    'shipping_address' => 'Store Pickup',
                    'subtotal' => $subtotal,
                    'shipping' => 0,
                    'total' => $total,
                    'payment_method' => $paymentMethod,
                    'payment_status' => Order::PAYMENT_STATUS_PAID,
                    'notes' => 'Direct store pickup sale' . ($item->sync_status == 1 ? ' - Synced to Store' : '')
                ]);

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'product_name' => $item->name,
                    'product_type' => 'inventory',
                    'device_type' => $item->device_type ?: ('N/A'),
                    'location' => $item->put_away_location ?: ('N/A'),
                    'quantity' => $validated['quantity'],
                    'unit_price' => $item->price,
                    'total_price' => $subtotal
                ]);

                // Decrease stock quantity after sale, matching store checkout behavior
                $beforeQuantity = (int) $item->quantity;
                $item->update([
                    'quantity' => max(0, $beforeQuantity - $validated['quantity']),
                ]);

                // Synchronously trigger eBay inventory quantity synchronization after transaction commits
                DB::afterCommit(function () use ($item, $order) {
                    try {
                        app(EbayInventorySyncService::class)->syncInventoryQuantityToEbay($item->id, $order->id);
                    } catch (\Throwable $syncEx) {
                        \Log::error("Direct eBay inventory sync failed on direct sell for inventory #{$item->id} (Order #{$order->id}): " . $syncEx->getMessage());
                    }
                });

                // If item was synced to store, log this for manual store update
                if ($item->sync_status == 1) {
                    \Log::info('Synced item sold directly', [
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'barcode' => $item->barcode,
                        'quantity_sold' => $validated['quantity'],
                        'before_quantity' => $beforeQuantity,
                        'after_quantity' => $item->fresh()->quantity,
                        'order_id' => $order->id,
                        'note' => 'Item was synced to store - store inventory needs manual update'
                    ]);
                }

                // Send order confirmation email if customer email provided
                if (!empty($validated['customer_email'])) {
                    try {
                        Mail::to($validated['customer_email'])->send(new OrderConfirmation($order));
                    } catch (\Exception $e) {
                        \Log::warning('Direct sell: failed to send order confirmation email', [
                            'order_id' => $order->id,
                            'email' => $validated['customer_email'],
                            'error' => $e->getMessage()
                        ]);
                        // Do not fail the transaction if email fails; proceed with sale
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Item sold successfully' . ($item->sync_status == 1 ? '. Note: Store inventory needs to be updated manually.' : ''),
                    'order_id' => $order->id
                ]);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Direct sell failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active eBay listings for the popup modal, ordered newest added on top.
     */
    public function getEbayListings(Request $request)
    {
        set_time_limit(180);

        $seller = trim($request->get('seller') ?: (config('services.ebay.seller_username') ?: env('EBAY_SELLER_USERNAME') ?: 'systematictrade'));
        $clientId = config('services.ebay.client_id') ?: env('EBAY_CLIENT_ID', '');
        $clientSecret = config('services.ebay.client_secret') ?: env('EBAY_CLIENT_SECRET', '');
        $refreshToken = app(EbayOrderSyncService::class)->getRefreshToken();

        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'eBay API credentials not configured. Please set EBAY_CLIENT_ID and EBAY_CLIENT_SECRET in .env'
            ], 422);
        }

        [$baseUrl, $isSandbox, $oauthUrl] = $this->resolveEbayBaseUrl($clientId);

        $forceReload = $request->boolean('refresh') || $request->boolean('reload') || $request->boolean('force');
        $cacheKey = 'ebay_listings_v3_' . md5($seller . $baseUrl);
        $apiErrors = [];

        if ($forceReload) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        } else {
            $cachedData = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cachedData && !empty($cachedData['items'])) {
                return response()->json($cachedData);
            }
        }

        $diagnostics = [];
        try {
            $allEbayItems = $this->fetchRawEbayItems($clientId, $clientSecret, $refreshToken, $baseUrl, $seller, $oauthUrl, $diagnostics);

            // If API returned 0 items, construct clear, human-readable diagnostic feedback
            if (empty($allEbayItems)) {
                $diagnostics = array_values(array_unique(array_filter($diagnostics)));

                $userFriendlyReasons = [];
                foreach ($diagnostics as $d) {
                    if (str_contains($d, 'issued to another client') || str_contains($d, 'invalid_grant')) {
                        $userFriendlyReasons[] = "eBay Refresh Token Mismatch: The token in EBAY_REFRESH_TOKEN was issued to another App ID, not '{$clientId}'. Please authorize your account via /ebay/login to generate a matching token.";
                    } elseif (str_contains($d, '429') || str_contains($d, 'request limit has been reached')) {
                        $userFriendlyReasons[] = 'eBay Buy/Browse API Quota Exceeded (HTTP 429): On Production, eBay assigns a 0-quota to the public Browse API by default until Application Growth Check approval in developer.ebay.com. You must authorize your seller account via /ebay/login to use the Trading API instead.';
                    } elseif (str_contains($d, 'invalid_client')) {
                        $userFriendlyReasons[] = 'Invalid Client Credentials (HTTP 401): eBay rejected EBAY_CLIENT_ID or EBAY_CLIENT_SECRET. Please verify your App ID and Cert ID.';
                    } else {
                        $userFriendlyReasons[] = $d;
                    }
                }

                if (empty($userFriendlyReasons)) {
                    $userFriendlyReasons[] = "eBay returned 0 active listings for seller '{$seller}' on {$baseUrl}.";
                }

                $noticeText = implode("\n", $userFriendlyReasons);

                // Try to return cached data
                $cachedData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                if ($cachedData && !empty($cachedData['items'])) {
                    \Log::info('getEbayListings: API returned 0 items, serving cached data (' . count($cachedData['items']) . ' items).');
                    $cachedData['notice'] = 'Showing cached listings (API temporarily unavailable). Reasons: ' . $noticeText;
                    $cachedData['reasons'] = $userFriendlyReasons;
                    return response()->json($cachedData);
                }

                // No cache either — return error with detailed reasons
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'total_count' => 0,
                    'synced_count' => 0,
                    'unsynced_count' => 0,
                    'seller' => $seller,
                    'environment' => $isSandbox ? 'Sandbox' : 'Production',
                    'notice' => $noticeText,
                    'reasons' => $userFriendlyReasons,
                    'raw_diagnostics' => $diagnostics,
                    'api_url' => $baseUrl,
                ]);
            }

            // Fetch existing inventory items mapped by barcode (eBay itemId)
            $existingInventory = Inventory::whereIn('barcode', array_keys($allEbayItems))
                ->orWhere('intake_type', 'eBay')
                ->orWhere('intake_type', 'Denver')
                ->orWhere('barcode', 'LIKE', 'v1|%')
                ->get();

            $existingMap = [];
            foreach ($existingInventory as $inv) {
                $b = (string) $inv->barcode;
                if ($b) {
                    $existingMap[$b] = $inv;
                    if (preg_match('/(\d{9,14})/', $b, $mb)) {
                        $numId = $mb[1];
                        $existingMap[$numId] = $inv;
                        $existingMap["v1|{$numId}|0"] = $inv;
                    }
                }
            }

            $formattedItems = [];

            foreach ($allEbayItems as $itemId => $item) {
                $rawNumericId = preg_match('/(\d{9,14})/', (string) $itemId, $m) ? $m[1] : (string) $itemId;
                $existing = $existingMap[(string) $itemId] 
                    ?? ($existingMap[$rawNumericId] 
                    ?? ($existingMap["v1|{$rawNumericId}|0"] ?? null));
                $photoUrls = $this->extractEbayPhotoUrls($item);
                $mainPhoto = $photoUrls[0] ?? ($item['image']['imageUrl'] ?? null);

                $isSynced = $existing && $existing->status !== 'Sold';
                $invStatus = $existing ? $existing->status : 'Not Synced';
                $startTime = $item['start_time'] ?? now()->toIso8601String();
                $putAwayLocation = $existing ? ($existing->put_away_location ?: $this->extractPutAwayLocationFromTitle($item['title'] ?? '')) : $this->extractPutAwayLocationFromTitle($item['title'] ?? '');

                $displayQty = isset($item['quantity']) && $item['quantity'] !== null
                    ? max(0, (int) $item['quantity'])
                    : ($existing ? (int) $existing->quantity : 1);

                $formattedItems[] = [
                    'itemId' => (string) $itemId,
                    'title' => $item['title'] ?? 'Untitled eBay Product',
                    'price' => (float) ($item['price']['value'] ?? 0),
                    'quantity' => $displayQty,
                    'condition' => $item['condition'] ?? 'N/A',
                    'itemWebUrl' => $item['itemWebUrl'] ?? "https://www.ebay.com/itm/{$itemId}",
                    'image_url' => $mainPhoto,
                    'photo_urls' => $photoUrls,
                    'start_time' => $startTime,
                    'formatted_date' => \Carbon\Carbon::parse($startTime)->format('M d, Y h:i A'),
                    'is_synced' => (bool) $isSynced,
                    'inventory_id' => $existing ? $existing->id : null,
                    'inventory_status' => $invStatus,
                    'category' => $item['categories'][0]['categoryName'] ?? 'eBay',
                    'seller' => $item['seller']['username'] ?? $seller,
                    'put_away_location' => $putAwayLocation,
                    'dimensions' => $item['dimensions'] ?? null,
                ];
            }

            // Sort NEWEST added on top (by start_time descending)
            usort($formattedItems, function ($a, $b) {
                return strtotime($b['start_time']) <=> strtotime($a['start_time']);
            });

            $totalCount = count($formattedItems);
            $syncedCount = count(array_filter($formattedItems, fn($i) => $i['is_synced']));
            $unsyncedCount = $totalCount - $syncedCount;

            $responseData = [
                'success' => true,
                'items' => $formattedItems,
                'total_count' => $totalCount,
                'synced_count' => $syncedCount,
                'unsynced_count' => $unsyncedCount,
                'seller' => $seller,
                'environment' => $isSandbox ? 'Sandbox' : 'Production',
            ];

            // Cache successful results for 2 hours (can be refreshed on demand with refresh=1)
            \Illuminate\Support\Facades\Cache::put($cacheKey, $responseData, 7200);

            return response()->json($responseData);
        } catch (\Throwable $e) {
            \Log::error('getEbayListings failed', [
                'error' => $e->getMessage(),
                'api_url' => $baseUrl ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);

            // Try to return cached data on failure
            $cachedData = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cachedData && !empty($cachedData['items'])) {
                $cachedData['notice'] = 'Showing cached listings. Live fetch failed: ' . $e->getMessage();
                return response()->json($cachedData);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch eBay listings: ' . $e->getMessage() . ' (API: ' . ($baseUrl ?? 'unknown') . ')',
             ], 500);
        }
    }

    /**
     * Sync a single eBay listing item into Inventory.
     */
    public function syncSingleEbayItem(Request $request)
    {
        $itemId = $request->input('itemId');
        if (!$itemId) {
            return response()->json(['success' => false, 'message' => 'Item ID is required.'], 422);
        }

        $seller = trim($request->input('seller') ?: (config('services.ebay.seller_username') ?: env('EBAY_SELLER_USERNAME') ?: 'systematictrade'));
        $title = $request->input('title', 'Untitled eBay Product');
        $price = (float) $request->input('price', 0);
        $condition = $request->input('condition', 'N/A');
        $categoryName = $request->input('category', 'eBay');
        $itemWebUrl = $request->input('itemWebUrl', '');
        $photoPaths = (array) $request->input('photo_urls', []);
        $putAwayLocation = $request->input('put_away_location') ?: $this->extractPutAwayLocationFromTitle($title);
        $dimensions = (array) $request->input('dimensions', []);

        // Fetch detailed item aspects (Brand, Model, Description, Dimensions/Weight, All Photos)
        $fetched = $this->fetchEbaySingleItemDetails($itemId);

        if (!empty($fetched['photo_urls'])) {
            $photoPaths = array_values(array_unique(array_merge($fetched['photo_urls'], $photoPaths)));
        }

        $brand = !empty($fetched['brand']) ? $fetched['brand'] : $this->extractBrandFromTitle($title);
        $model = !empty($fetched['model']) ? $fetched['model'] : null;

        if (!$model && preg_match('/\b([A-Z0-9]{3,10}(?:-[A-Z0-9]+)?)\b/i', $title, $mModel)) {
            $candidate = $mModel[1];
            if (strtolower($candidate) !== strtolower($brand)) {
                $model = $candidate;
            }
        }

        // Combine Brand & Model
        $brandCategory = trim(($brand ? $brand . ' ' : '') . ($model ?: ''));
        if (empty($brandCategory)) {
            $brandCategory = $brand ?: $title;
        }

        $description = !empty($fetched['description']) ? $fetched['description'] : null;
        if (!empty($fetched['condition']) && $condition === 'N/A') {
            $condition = $fetched['condition'];
        }

        $pLength = isset($dimensions['length']) && (float)$dimensions['length'] > 0 ? (float)$dimensions['length'] : ($fetched['length'] ?? null);
        $pWidth = isset($dimensions['width']) && (float)$dimensions['width'] > 0 ? (float)$dimensions['width'] : ($fetched['width'] ?? null);
        $pHeight = isset($dimensions['height']) && (float)$dimensions['height'] > 0 ? (float)$dimensions['height'] : ($fetched['height'] ?? null);
        $pWeight = isset($dimensions['weight']) && (float)$dimensions['weight'] > 0 ? (float)$dimensions['weight'] : ($fetched['weight'] ?? null);

        // Fallback: extract dimensions/weight from title or description
        if (!$pLength && !$pWidth && !$pHeight && !$pWeight) {
            $fullText = $title . ' ' . strip_tags($description ?? '');
            $textDims = $this->extractDimensionsFromText($fullText);
            $pLength = $pLength ?: ($textDims['length'] ?? null);
            $pWidth = $pWidth ?: ($textDims['width'] ?? null);
            $pHeight = $pHeight ?: ($textDims['height'] ?? null);
            $pWeight = $pWeight ?: ($textDims['weight'] ?? null);
        }

        if (empty($photoPaths) && $request->input('image_url')) {
            $photoPaths = [$request->input('image_url')];
        }

        // Build Notes field containing seller description
        $cleanDesc = trim(strip_tags($description ?? ''));
        $notes = "";
        if (!empty($cleanDesc)) {
            $notes .= "Item Description:\n" . $cleanDesc . "\n\n";
        }
        $notes .= "eBay Seller: {$seller}";
        if ($itemWebUrl) {
            $notes .= "\neBay URL: {$itemWebUrl}";
        }
        $notes .= "\neBay Item ID: {$itemId}";
        $notes .= "\nImported: " . now()->format('M d, Y h:i A');

        $ebayQty = isset($fetched['quantity']) && $fetched['quantity'] !== null
            ? max(0, (int) $fetched['quantity'])
            : max(0, (int) $request->input('quantity', 1));

        try {
            $inventoryRecord = DB::transaction(function () use ($itemId, $title, $price, $condition, $categoryName, $itemWebUrl, $seller, $photoPaths, $putAwayLocation, $pLength, $pWidth, $pHeight, $pWeight, $brand, $model, $brandCategory, $notes, $ebayQty) {
                $rawNumericId = preg_match('/(\d{9,14})/', (string) $itemId, $m) ? $m[1] : (string) $itemId;
                $existing = Inventory::where('barcode', $itemId)
                    ->orWhere('barcode', $rawNumericId)
                    ->orWhere('barcode', 'v1|' . $rawNumericId . '|0')
                    ->orWhere('barcode', 'LIKE', '%' . $rawNumericId . '%')
                    ->first();

                if ($existing) {
                    $existing->update([
                        'name' => $title,
                        'brand' => $brand ?: ($existing->brand ?: $brandCategory),
                        'model' => $model ?: $existing->model,
                        'brand_category' => $brandCategory ?: $existing->brand_category,
                        'price' => $price,
                        'estimated_value' => $price,
                        'status' => $ebayQty > 0 ? 'In Stock' : ($existing->status === 'Sold' ? 'Sold' : 'Out of Stock'),
                        'condition_status' => $condition ?: $existing->condition_status,
                        'quantity' => $ebayQty,
                        'photos_paths' => !empty($photoPaths) ? $photoPaths : $existing->photos_paths,
                        'repair_photos' => !empty($photoPaths) ? $photoPaths : $existing->repair_photos,
                        'put_away_location' => $putAwayLocation ?: $existing->put_away_location,
                        'weight' => $pWeight ?: $existing->weight,
                        'notes' => $notes ?: $existing->notes,
                        'intake_type' => ((int) ($existing->sync_status ?? 0) === 1) ? 'Denver' : 'eBay',
                    ]);

                    InventoryShippingRate::updateOrCreate(
                        ['inventory_id' => $existing->id],
                        [
                            'parcel_length' => $pLength,
                            'parcel_width' => $pWidth,
                            'parcel_height' => $pHeight,
                            'parcel_weight' => $pWeight ?: $existing->weight,
                            'distance_unit' => 'in',
                            'mass_unit' => 'lb',
                        ]
                    );

                    return $existing;
                }

                $newRecord = Inventory::create([
                    'barcode' => $itemId,
                    'name' => $title,
                    'brand' => $brand ?: $brandCategory,
                    'model' => $model,
                    'brand_category' => $brandCategory,
                    'category' => $categoryName,
                    'intake_type' => 'eBay',
                    'condition_status' => $condition,
                    'quantity' => $ebayQty,
                    'estimated_value' => $price,
                    'price' => $price,
                    'photos_paths' => $photoPaths,
                    'repair_photos' => $photoPaths,
                    'notes' => $notes,
                    'status' => $ebayQty > 0 ? 'In Stock' : 'Out of Stock',
                    'device_type' => $categoryName,
                    'put_away_location' => $putAwayLocation,
                    'weight' => $pWeight,
                ]);

                InventoryShippingRate::updateOrCreate(
                    ['inventory_id' => $newRecord->id],
                    [
                        'parcel_length' => $pLength,
                        'parcel_width' => $pWidth,
                        'parcel_height' => $pHeight,
                        'parcel_weight' => $pWeight,
                        'distance_unit' => 'in',
                        'mass_unit' => 'lb',
                    ]
                );

                return $newRecord;
            });

            // Update item in eBay listings cache if cached
            try {
                $clientId = config('services.ebay.client_id') ?: env('EBAY_CLIENT_ID', '');
                [$baseUrl] = $this->resolveEbayBaseUrl($clientId);
                $cacheKey = 'ebay_listings_v3_' . md5($seller . $baseUrl);
                $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
                if ($cached && !empty($cached['items']) && is_array($cached['items'])) {
                    $rawNum = preg_match('/(\d{9,14})/', (string) $itemId, $m) ? $m[1] : (string) $itemId;
                    foreach ($cached['items'] as &$cItem) {
                        $cId = (string) ($cItem['itemId'] ?? '');
                        if ($cId === (string) $itemId || $cId === $rawNum || $cId === "v1|{$rawNum}|0") {
                            $cItem['is_synced'] = true;
                            $cItem['inventory_status'] = 'In Stock';
                            $cItem['inventory_id'] = $inventoryRecord->id;
                            $cItem['quantity'] = $inventoryRecord->quantity;
                            break;
                        }
                    }
                    unset($cItem);
                    $cached['synced_count'] = count(array_filter($cached['items'], fn($i) => !empty($i['is_synced'])));
                    $cached['unsynced_count'] = count($cached['items']) - $cached['synced_count'];
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $cached, 7200);
                }
            } catch (\Throwable $ce) {
                \Log::warning('Failed to update eBay cache after sync: ' . $ce->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => "Item '{$title}' synced to inventory successfully!",
                'item_id' => $itemId,
                'inventory_id' => $inventoryRecord->id,
                'put_away_location' => $inventoryRecord->put_away_location,
                'quantity' => $inventoryRecord->quantity,
                'status' => $inventoryRecord->status,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Sync single eBay item failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Match and update inventory quantities for all synced items to match the exact quantities
     * from the eBay listings (as shown in the Fetch from eBay modal).
     */
    public function matchSyncedEbayQuantities(Request $request)
    {
        set_time_limit(180);

        $clientId = config('services.ebay.client_id') ?: env('EBAY_CLIENT_ID', '');
        $clientSecret = config('services.ebay.client_secret') ?: env('EBAY_CLIENT_SECRET', '');
        $seller = trim($request->input('seller') ?: (config('services.ebay.seller_username') ?: env('EBAY_SELLER_USERNAME') ?: 'systematictrade'));

        // 1. Items passed directly from frontend (e.g. from the eBay listings modal state)
        $items = $request->input('items');

        // 2. If no items passed, check cache (same cache populated by getEbayListings for the modal)
        if (empty($items) || !is_array($items)) {
            [$baseUrl, $isSandbox, $oauthUrl] = $this->resolveEbayBaseUrl($clientId);
            $cacheKey = 'ebay_listings_v3_' . md5($seller . $baseUrl);
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cached && !empty($cached['items']) && is_array($cached['items'])) {
                $items = $cached['items'];
            }
        }

        // 3. If still empty, fetch listings data directly via fetchRawEbayItems
        if (empty($items) || !is_array($items)) {
            $refreshToken = app(EbayOrderSyncService::class)->getRefreshToken();
            [$baseUrl, $isSandbox, $oauthUrl] = $this->resolveEbayBaseUrl($clientId);
            try {
                $rawItems = $this->fetchRawEbayItems($clientId, $clientSecret, $refreshToken, $baseUrl, $seller, $oauthUrl);
                $items = array_values($rawItems);
            } catch (\Throwable $e) {
                \Log::warning('matchSyncedEbayQuantities fetchRawEbayItems error: ' . $e->getMessage());
                $items = [];
            }
        }

        if (empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'No active eBay listings found to match quantities with. Please ensure eBay listings are loaded.',
                'matched_count' => 0,
                'changed_count' => 0,
                'sold_count' => 0,
            ]);
        }

        // 4. Build fast lookup map for eBay quantities and details
        $ebayQtyMap = [];
        $activeEbayIds = [];

        foreach ($items as $item) {
            $rawId = (string) ($item['itemId'] ?? ($item['id'] ?? ''));
            if (!$rawId) continue;

            $qty = isset($item['quantity']) && $item['quantity'] !== null ? max(0, (int) $item['quantity']) : 1;
            $price = isset($item['price']) && (float) $item['price'] > 0 ? (float) $item['price'] : null;
            $invId = !empty($item['inventory_id']) ? (int) $item['inventory_id'] : null;

            $entry = [
                'itemId' => $rawId,
                'quantity' => $qty,
                'price' => $price,
                'inventory_id' => $invId,
            ];

            $activeEbayIds[] = $rawId;
            $ebayQtyMap[$rawId] = $entry;

            if (preg_match('/(\d{9,14})/', $rawId, $m)) {
                $numId = $m[1];
                $activeEbayIds[] = $numId;
                $activeEbayIds[] = "v1|{$numId}|0";
                $ebayQtyMap[$numId] = $entry;
                $ebayQtyMap["v1|{$numId}|0"] = $entry;
            }
        }

        // 5. Query all synced inventory items
        $syncedInventory = Inventory::where(function ($q) {
            $q->where('intake_type', 'eBay')
              ->orWhere('intake_type', 'Denver')
              ->orWhere('barcode', 'LIKE', 'v1|%');
        })->get();

        $matchedCount = 0;
        $changedCount = 0;
        $soldCount = 0;
        $updatedItems = [];
        $markUnmatchedAsSold = (bool) $request->input('mark_unmatched_sold', false);

        DB::transaction(function () use ($syncedInventory, $ebayQtyMap, $markUnmatchedAsSold, &$matchedCount, &$changedCount, &$soldCount, &$updatedItems) {
            foreach ($syncedInventory as $inv) {
                $barcode = (string) $inv->barcode;
                $numId = preg_match('/(\d{9,14})/', $barcode, $m) ? $m[1] : $barcode;

                // Match by exact barcode, numeric ID, v1 pattern, or inventory_id
                $matchedEntry = null;
                if (isset($ebayQtyMap[$barcode])) {
                    $matchedEntry = $ebayQtyMap[$barcode];
                } elseif (isset($ebayQtyMap[$numId])) {
                    $matchedEntry = $ebayQtyMap[$numId];
                } elseif (isset($ebayQtyMap["v1|{$numId}|0"])) {
                    $matchedEntry = $ebayQtyMap["v1|{$numId}|0"];
                }

                if ($matchedEntry) {
                    $matchedCount++;
                    $oldQty = (int) $inv->quantity;
                    $newQty = (int) $matchedEntry['quantity'];
                    $oldStatus = $inv->status;
                    $newStatus = $newQty > 0 ? 'In Stock' : 'Sold';

                    $updates = [];
                    if ($oldQty !== $newQty) {
                        $updates['quantity'] = $newQty;
                        $changedCount++;
                    }
                    if ($oldStatus !== $newStatus) {
                        $updates['status'] = $newStatus;
                    }
                    if (!empty($matchedEntry['price']) && (float) $inv->price != (float) $matchedEntry['price']) {
                        $updates['price'] = $matchedEntry['price'];
                        $updates['estimated_value'] = $matchedEntry['price'];
                    }

                    if (!empty($updates)) {
                        $inv->update($updates);
                        $updatedItems[] = [
                            'id' => $inv->id,
                            'name' => $inv->name,
                            'barcode' => $inv->barcode,
                            'old_qty' => $oldQty,
                            'new_qty' => $newQty,
                            'status' => $newStatus,
                        ];
                    }
                } elseif ($markUnmatchedAsSold) {
                    // Item was in eBay inventory but is NOT in active eBay listings (ended/sold on eBay)
                    if ($inv->status !== 'Sold' || (int) $inv->quantity > 0) {
                        $inv->update([
                            'quantity' => 0,
                            'status' => 'Sold',
                        ]);
                        $soldCount++;
                        $changedCount++;
                        $updatedItems[] = [
                            'id' => $inv->id,
                            'name' => $inv->name,
                            'barcode' => $inv->barcode,
                            'old_qty' => (int) $inv->quantity,
                            'new_qty' => 0,
                            'status' => 'Sold',
                        ];
                    }
                }
            }
        });

        $message = "eBay quantities matched successfully! Checked {$matchedCount} synced products in inventory. {$changedCount} product quantities updated to match eBay" . ($soldCount > 0 ? " ({$soldCount} marked sold out as no longer active on eBay)." : ".");

        return response()->json([
            'success' => true,
            'message' => $message,
            'matched_count' => $matchedCount,
            'changed_count' => $changedCount,
            'sold_count' => $soldCount,
            'updated_items' => array_slice($updatedItems, 0, 50),
        ]);
    }

    /**
     * Resolve the eBay API base URL, sandbox status, and OAuth URL.
     */
    private function resolveEbayBaseUrl(string $clientId): array
    {
        $apiUrl = config('services.ebay.api_url') ?: env('EBAY_API_URL');
        $env = config('services.ebay.mode') ?: env('EBAY_ENV');

        if (!empty($apiUrl)) {
            $baseUrl = rtrim($apiUrl, '/');
            $isSandbox = str_contains($baseUrl, 'sandbox');
        } elseif (str_contains($clientId, '-SBX-') || str_starts_with($clientId, 'SBX-')) {
            // Client ID is explicitly a Sandbox credential (-SBX-).
            // Sandbox keys cannot authenticate against production eBay API (causes 401 invalid_client).
            $isSandbox = true;
            $baseUrl = 'https://api.sandbox.ebay.com';
        } elseif (!empty($env)) {
            $isSandbox = strtolower($env) !== 'production';
            $baseUrl = $isSandbox ? 'https://api.sandbox.ebay.com' : 'https://api.ebay.com';
        } else {
            $isSandbox = false;
            $baseUrl = 'https://api.ebay.com';
        }

        $oauthUrl = config('services.ebay.oauth_url') ?: env('EBAY_OAUTH_URL') ?: ($baseUrl . '/identity/v1/oauth2/token');

        return [$baseUrl, $isSandbox, $oauthUrl];
    }

    /**
     * Helper to fetch active raw eBay items from eBay API.
     * Uses Seller OAuth Token (Trading API GetMyeBaySelling + GetSellerList) as primary source,
     * supplemented by Browse API and normalized item properties.
     */
    private function fetchRawEbayItems(string $clientId, string $clientSecret, ?string $refreshToken, string $baseUrl, string $seller, ?string $oauthUrl = null, array &$diagnostics = []): array
    {
        $allEbayItems = [];
        $isSandbox = str_contains($baseUrl, 'sandbox');
        $oauthUrl = $oauthUrl ?: (config('services.ebay.oauth_url') ?: env('EBAY_OAUTH_URL') ?: ($baseUrl . '/identity/v1/oauth2/token'));
        $tradingEndpoint = rtrim($baseUrl, '/') . '/ws/api.dll';

        $userToken = null;

        // 1. Obtain User Access Token using Refresh Token (Seller Authorized)
        if (!empty($refreshToken)) {
            try {
                $syncService = app(EbayOrderSyncService::class);
                $userToken = $syncService->getUserAccessToken();
            } catch (\Throwable $e) {
                \Log::warning('getUserAccessToken failed in fetchRawEbayItems: ' . $e->getMessage() . '. Attempting direct token refresh.');
                try {
                    // Attempt direct refresh without explicit scope (RFC 6749)
                    $userTokenResp = Http::asForm()
                        ->timeout(15)
                        ->withHeaders([
                            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                        ])
                        ->post($oauthUrl, [
                            'grant_type' => 'refresh_token',
                            'refresh_token' => $refreshToken,
                        ]);

                    if (!$userTokenResp->successful()) {
                        // Fallback with explicit scopes
                        $userTokenResp = Http::asForm()
                            ->timeout(15)
                            ->withHeaders([
                                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                            ])
                            ->post($oauthUrl, [
                                'grant_type' => 'refresh_token',
                                'refresh_token' => $refreshToken,
                                'scope' => implode(' ', [
                                    'https://api.ebay.com/oauth/api_scope',
                                    'https://api.ebay.com/oauth/api_scope/sell.inventory',
                                    'https://api.ebay.com/oauth/api_scope/sell.account',
                                    'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                                ]),
                            ]);
                    }

                    if ($userTokenResp->successful()) {
                        $userToken = $userTokenResp->json('access_token');
                    } else {
                        $errDesc = $userTokenResp->json('error_description') ?? $userTokenResp->body();
                        \Log::error('Direct token refresh failed: ' . $errDesc);
                        $diagnostics[] = 'eBay Seller Refresh Token Error: ' . $errDesc;
                    }
                } catch (\Throwable $te) {
                    \Log::error('User token refresh exception: ' . $te->getMessage());
                    $diagnostics[] = 'eBay Seller Token Refresh Exception: ' . $te->getMessage();
                }
            }
        } else {
            $diagnostics[] = 'No EBAY_REFRESH_TOKEN configured in .env (Seller Trading API & Inventory API are skipped). Authorize via /ebay/login.';
        }

        // Fast-fail if authenticated user does not match target seller (prevents 503 web server timeouts)
        if ($userToken) {
            $syncService = app(EbayOrderSyncService::class);
            $authUser = $syncService->getAuthenticatedUsername($userToken);
            if ($authUser && strtolower($authUser) !== strtolower($seller)) {
                $diagnostics[] = "Connected eBay Account Mismatch: You are authenticated as '{$authUser}', but target store listings belong to '{$seller}'. Active listings for '{$seller}' cannot be fetched under account '{$authUser}'. Please sign in with '{$seller}' via /ebay/login.";
                return [];
            }
        }

        // Helper lambda to parse a Trading API XML <Item> node into our standard array format
        $parseTradingItem = function ($item) use ($seller, &$allEbayItems) {
            $itemId = (string) ($item->ItemID ?? '');
            if (!$itemId) return;

            $title = (string) ($item->Title ?? 'Untitled eBay Item');
            $priceVal = (string) ($item->SellingStatus->CurrentPrice ?? $item->BuyItNowPrice ?? $item->StartPrice ?? '0');
            $qtyTotal = isset($item->Quantity) ? (int) $item->Quantity : 0;
            $qtySold = isset($item->SellingStatus->QuantitySold) ? (int) $item->SellingStatus->QuantitySold : 0;
            $qty = isset($item->QuantityAvailable) ? (int) $item->QuantityAvailable : max(0, $qtyTotal - $qtySold);
            if ($qty === 0 && $qtyTotal > 0 && $qtySold === 0) {
                $qty = $qtyTotal;
            }
            if ($qty <= 0 && !isset($item->SellingStatus->QuantitySold)) {
                $qty = 1;
            }

            $itemUrl = (string) ($item->ListingDetails->ViewItemURL ?? '');
            if (empty($itemUrl)) {
                $itemUrl = "https://www.ebay.com/itm/{$itemId}";
            }
            $startTime = (string) ($item->ListingDetails->StartTime ?? $item->ListingDetails->CreatedDate ?? now()->toIso8601String());
            $itemSeller = (string) ($item->Seller->UserID ?? $seller);

            $packageLength = null;
            $packageWidth = null;
            $packageHeight = null;
            $packageWeight = null;

            if (isset($item->ShippingPackageDetails)) {
                $pkg = $item->ShippingPackageDetails;
                if (isset($pkg->PackageLength) && (float)$pkg->PackageLength > 0) {
                    $packageLength = (float) $pkg->PackageLength;
                }
                if (isset($pkg->PackageWidth) && (float)$pkg->PackageWidth > 0) {
                    $packageWidth = (float) $pkg->PackageWidth;
                }
                if (isset($pkg->PackageDepth) && (float)$pkg->PackageDepth > 0) {
                    $packageHeight = (float) $pkg->PackageDepth;
                } elseif (isset($pkg->PackageHeight) && (float)$pkg->PackageHeight > 0) {
                    $packageHeight = (float) $pkg->PackageHeight;
                }
                $wMajor = isset($pkg->WeightMajor) ? (float) $pkg->WeightMajor : 0;
                $wMinor = isset($pkg->WeightMinor) ? (float) $pkg->WeightMinor : 0;
                if ($wMajor > 0 || $wMinor > 0) {
                    $packageWeight = round($wMajor + ($wMinor / 16.0), 2);
                }
            }

            if (!$packageLength && !$packageWidth && !$packageHeight && !$packageWeight) {
                $textDims = $this->extractDimensionsFromText($title);
                $packageLength = $textDims['length'];
                $packageWidth = $textDims['width'];
                $packageHeight = $textDims['height'];
                $packageWeight = $textDims['weight'];
            }

            $imgPaths = [];
            if (isset($item->PictureDetails->PictureURL)) {
                foreach ($item->PictureDetails->PictureURL as $pic) {
                    $pUrl = (string) $pic;
                    if ($pUrl) {
                        $norm = $this->normalizeEbayImageUrl($pUrl);
                        if ($norm) $imgPaths[] = $norm;
                    }
                }
            }
            if (empty($imgPaths) && isset($item->PictureDetails->GalleryURL)) {
                $gUrl = (string) $item->PictureDetails->GalleryURL;
                if ($gUrl) {
                    $norm = $this->normalizeEbayImageUrl($gUrl);
                    if ($norm) $imgPaths[] = $norm;
                }
            }

            $rawNum = preg_match('/(\d{9,14})/', $itemId, $m) ? $m[1] : $itemId;
            $foundKey = isset($allEbayItems[$itemId]) ? $itemId : (isset($allEbayItems["v1|{$rawNum}|0"]) ? "v1|{$rawNum}|0" : (isset($allEbayItems[$rawNum]) ? $rawNum : null));

            if ($foundKey) {
                $allEbayItems[$foundKey]['quantity'] = $qty;
                $allEbayItems[$foundKey]['quantity_verified'] = true;
                if ((float)$priceVal > 0) {
                    $allEbayItems[$foundKey]['price'] = ['value' => $priceVal];
                }
                if ($packageLength || $packageWidth || $packageHeight || $packageWeight) {
                    $allEbayItems[$foundKey]['dimensions'] = [
                        'length' => $packageLength ?: ($allEbayItems[$foundKey]['dimensions']['length'] ?? null),
                        'width' => $packageWidth ?: ($allEbayItems[$foundKey]['dimensions']['width'] ?? null),
                        'height' => $packageHeight ?: ($allEbayItems[$foundKey]['dimensions']['height'] ?? null),
                        'weight' => $packageWeight ?: ($allEbayItems[$foundKey]['dimensions']['weight'] ?? null),
                    ];
                }
                if (!empty($imgPaths)) {
                    if (empty($allEbayItems[$foundKey]['image']['imageUrl'])) {
                        $allEbayItems[$foundKey]['image'] = ['imageUrl' => $imgPaths[0]];
                    }
                    $existingAdd = array_map(fn($x) => is_array($x) ? ($x['imageUrl'] ?? null) : $x, $allEbayItems[$foundKey]['additionalImages'] ?? []);
                    $combinedAdd = array_values(array_unique(array_filter(array_merge($existingAdd, array_slice($imgPaths, 1)))));
                    $allEbayItems[$foundKey]['additionalImages'] = array_map(fn($u) => ['imageUrl' => $u], $combinedAdd);
                }
            } else {
                $allEbayItems[$itemId] = [
                    'itemId' => $itemId,
                    'title' => $title,
                    'price' => ['value' => $priceVal],
                    'quantity' => $qty,
                    'quantity_verified' => true,
                    'condition' => (string) ($item->ConditionDisplayName ?? 'Used'),
                    'itemWebUrl' => $itemUrl,
                    'image' => ['imageUrl' => $imgPaths[0] ?? null],
                    'additionalImages' => array_map(fn($u) => ['imageUrl' => $u], array_slice($imgPaths, 1)),
                    'categories' => isset($item->PrimaryCategory->CategoryName) ? [['categoryName' => (string) $item->PrimaryCategory->CategoryName]] : [],
                    'seller' => ['username' => $itemSeller ?: $seller],
                    'start_time' => $startTime ?: now()->toIso8601String(),
                    'dimensions' => [
                        'length' => $packageLength,
                        'width' => $packageWidth,
                        'height' => $packageHeight,
                        'weight' => $packageWeight,
                    ],
                ];
            }
        };

        // 2. Primary Source for Seller Active Listings: Trading API (GetSellerList with ReturnAll for all multiple photos)
        if ($userToken) {
            try {
                $now = now();
                $endTimeFrom = $now->copy()->subDays(1)->toIso8601String();
                $endTimeTo = $now->copy()->addDays(119)->toIso8601String();

                for ($page = 1; $page <= 10; $page++) {
                    $sellerListXml = '<?xml version="1.0" encoding="utf-8"?>'
                        . '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
                        . '  <DetailLevel>ReturnAll</DetailLevel>'
                        . '  <EndTimeFrom>' . $endTimeFrom . '</EndTimeFrom>'
                        . '  <EndTimeTo>' . $endTimeTo . '</EndTimeTo>'
                        . '  <Pagination>'
                        . '    <EntriesPerPage>200</EntriesPerPage>'
                        . '    <PageNumber>' . $page . '</PageNumber>'
                        . '  </Pagination>'
                        . '</GetSellerListRequest>';

                    $sResp = Http::timeout(25)->retry(1, 300)->withHeaders([
                        'X-EBAY-API-CALL-NAME' => 'GetSellerList',
                        'X-EBAY-API-SITEID' => '0',
                        'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                        'X-EBAY-API-IAF-TOKEN' => $userToken,
                    ])->withBody($sellerListXml, 'text/xml')->post($tradingEndpoint);

                    if ($sResp->successful()) {
                        $sXml = @simplexml_load_string($sResp->body());
                        if ($sXml && isset($sXml->ItemArray->Item)) {
                            foreach ($sXml->ItemArray->Item as $itemNode) {
                                $parseTradingItem($itemNode);
                            }

                            $totalPages = (int) ($sXml->PaginationResult->TotalNumberOfPages ?? 1);
                            if ($page >= $totalPages) {
                                break;
                            }
                        } else {
                            if ($sXml && isset($sXml->Errors)) {
                                $errMsg = (string) ($sXml->Errors->LongMessage ?? $sXml->Errors->ShortMessage ?? 'Unknown error');
                                $diagnostics[] = 'eBay Trading API GetSellerList: ' . $errMsg;
                            }
                            break;
                        }
                    } else {
                        break;
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('GetSellerList exception: ' . $e->getMessage());
                $diagnostics[] = 'eBay Trading API GetSellerList exception: ' . $e->getMessage();
            }

            // 3. Fallback Source: Trading API (GetMyeBaySelling) if GetSellerList returned 0 items
            if (empty($allEbayItems)) {
                try {
                    for ($page = 1; $page <= 10; $page++) {
                        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
                            . '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
                            . '  <ActiveList>'
                            . '    <Include>true</Include>'
                            . '    <ListingType>All</ListingType>'
                            . '    <Sort>StartTimeNewest</Sort>'
                            . '    <Pagination>'
                            . '      <EntriesPerPage>200</EntriesPerPage>'
                            . '      <PageNumber>' . $page . '</PageNumber>'
                            . '    </Pagination>'
                            . '  </ActiveList>'
                            . '  <HideVariations>false</HideVariations>'
                            . '</GetMyeBaySellingRequest>';

                        $tResp = Http::timeout(15)->retry(1, 300)->withHeaders([
                            'X-EBAY-API-CALL-NAME' => 'GetMyeBaySelling',
                            'X-EBAY-API-SITEID' => '0',
                            'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                            'X-EBAY-API-IAF-TOKEN' => $userToken,
                        ])->withBody($xmlBody, 'text/xml')->post($tradingEndpoint);

                        if ($tResp->successful()) {
                            $xml = @simplexml_load_string($tResp->body());
                            if ($xml && isset($xml->ActiveList->ItemArray->Item)) {
                                foreach ($xml->ActiveList->ItemArray->Item as $itemNode) {
                                    $parseTradingItem($itemNode);
                                }

                                $totalPages = (int) ($xml->ActiveList->PaginationResult->TotalNumberOfPages ?? 1);
                                if ($page >= $totalPages) {
                                    break;
                                }
                            } else {
                                if ($xml && isset($xml->Errors)) {
                                    $errMsg = (string) ($xml->Errors->LongMessage ?? $xml->Errors->ShortMessage ?? 'Unknown error');
                                    \Log::warning('GetMyeBaySelling error from eBay: ' . $errMsg);
                                    $diagnostics[] = 'eBay Trading API GetMyeBaySelling: ' . $errMsg;
                                } elseif ($page === 1) {
                                    $diagnostics[] = 'Trading API GetMyeBaySelling returned 0 active listings in ActiveList.';
                                }
                                break;
                            }
                        } else {
                            $tErr = 'HTTP ' . $tResp->status() . ' - ' . substr($tResp->body(), 0, 200);
                            \Log::warning('GetMyeBaySelling HTTP failed: ' . $tErr);
                            $diagnostics[] = 'eBay Trading API GetMyeBaySelling: ' . $tErr;
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::warning('GetMyeBaySelling exception: ' . $e->getMessage());
                    $diagnostics[] = 'eBay Trading API exception: ' . $e->getMessage();
                }
            }
        }

        // 4. Secondary Source: Browse API (using Client Credentials or User Token)
        // If Trading API returned 0 items, search public listings of seller
        $browseToken = $userToken;
        if (empty($allEbayItems)) {
            try {
                if (!$browseToken) {
                    $tokenResponse = Http::asForm()
                        ->timeout(12)
                        ->withHeaders([
                            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                        ])
                        ->post($oauthUrl, [
                            'grant_type' => 'client_credentials',
                            'scope' => 'https://api.ebay.com/oauth/api_scope',
                        ]);

                    if ($tokenResponse->successful()) {
                        $browseToken = $tokenResponse->json('access_token');
                    } else {
                        $bErr = $tokenResponse->json('error_description') ?? $tokenResponse->body();
                        $diagnostics[] = 'Browse API OAuth Authentication failed: ' . $bErr;
                    }
                }

                if ($browseToken) {
                    $sellersToSearch = array_unique(array_filter([$seller, 'systematictrade']));

                    // When fallback is needed, use targeted keywords to avoid eBay 429 rate-limiting
                    $searchKeywords = [$seller, ' ', 'laptop', 'monitor', 'adapter', 'cable'];

                    foreach ($sellersToSearch as $targetSeller) {
                        $batches = array_chunk($searchKeywords, 3);
                        foreach ($batches as $batch) {
                            $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($baseUrl, $browseToken, $targetSeller, $batch) {
                                $requests = [];
                                foreach ($batch as $kw) {
                                    $requests[] = $pool->withToken($browseToken)
                                        ->acceptJson()
                                        ->timeout(15)
                                        ->get($baseUrl . '/buy/browse/v1/item_summary/search', [
                                            'q' => $kw,
                                            'filter' => 'sellers:{' . $targetSeller . '}',
                                            'sort' => 'newly_listed',
                                            'limit' => 200,
                                        ]);
                                }
                                return $requests;
                            });

                            foreach ($responses as $browseResponse) {
                                if ($browseResponse instanceof \Illuminate\Http\Client\Response) {
                                    if ($browseResponse->successful()) {
                                        $data = $browseResponse->json() ?? [];
                                        $summaries = $data['itemSummaries'] ?? [];

                                        foreach ($summaries as $item) {
                                            $itemId = $item['itemId'] ?? null;
                                            if (!$itemId) continue;

                                            $rawNum = preg_match('/(\d{9,14})/', $itemId, $m) ? $m[1] : $itemId;
                                            $foundKey = isset($allEbayItems[$itemId]) ? $itemId : (isset($allEbayItems["v1|{$rawNum}|0"]) ? "v1|{$rawNum}|0" : (isset($allEbayItems[$rawNum]) ? $rawNum : null));

                                            $browseQty = null;
                                            if (!empty($item['estimatedAvailabilities'])) {
                                                foreach ($item['estimatedAvailabilities'] as $ea) {
                                                    if (isset($ea['estimatedAvailableQuantity'])) {
                                                        $browseQty = (int) $ea['estimatedAvailableQuantity'];
                                                        break;
                                                    } elseif (isset($ea['estimatedRemainingQuantity'])) {
                                                        $browseQty = (int) $ea['estimatedRemainingQuantity'];
                                                        break;
                                                    }
                                                }
                                            }

                                            $packageLength = null;
                                            $packageWidth = null;
                                            $packageHeight = null;
                                            $packageWeight = null;
                                            if (isset($item['itemDimensions'])) {
                                                $packageLength = (float) ($item['itemDimensions']['length'] ?? 0) ?: null;
                                                $packageWidth = (float) ($item['itemDimensions']['width'] ?? 0) ?: null;
                                                $packageHeight = (float) ($item['itemDimensions']['height'] ?? 0) ?: null;
                                            }

                                            if ($foundKey) {
                                                if ($browseQty !== null) {
                                                    $allEbayItems[$foundKey]['quantity'] = $browseQty;
                                                    $allEbayItems[$foundKey]['quantity_verified'] = true;
                                                }
                                                if ($packageLength || $packageWidth || $packageHeight) {
                                                    $allEbayItems[$foundKey]['dimensions'] = [
                                                        'length' => $packageLength ?: ($allEbayItems[$foundKey]['dimensions']['length'] ?? null),
                                                        'width' => $packageWidth ?: ($allEbayItems[$foundKey]['dimensions']['width'] ?? null),
                                                        'height' => $packageHeight ?: ($allEbayItems[$foundKey]['dimensions']['height'] ?? null),
                                                        'weight' => $allEbayItems[$foundKey]['dimensions']['weight'] ?? null,
                                                    ];
                                                }
                                            } else {
                                                $item['start_time'] = $item['itemCreationDate'] ?? now()->toIso8601String();
                                                $item['quantity'] = $browseQty;
                                                $item['quantity_verified'] = ($browseQty !== null);
                                                $item['dimensions'] = [
                                                    'length' => $packageLength,
                                                    'width' => $packageWidth,
                                                    'height' => $packageHeight,
                                                    'weight' => $packageWeight,
                                                ];
                                                if (empty($item['seller']['username'])) {
                                                    $item['seller'] = ['username' => $targetSeller];
                                                }
                                                $allEbayItems[$itemId] = $item;
                                            }
                                        }
                                    } elseif ($browseResponse->status() === 429) {
                                        $bErrJson = $browseResponse->json();
                                        $bMsg = $bErrJson['errors'][0]['longMessage'] ?? ($bErrJson['errors'][0]['message'] ?? 'Rate limit reached.');
                                        $diagnostics[] = "Browse API HTTP 429: {$bMsg} (eBay public Buy API quota is 0 on Production without Application Growth Check).";
                                        break 2;
                                    } elseif ($browseResponse->status() >= 400) {
                                        $bErrJson = $browseResponse->json();
                                        $bMsg = $bErrJson['errors'][0]['message'] ?? ('HTTP ' . $browseResponse->status());
                                        $diagnostics[] = "Browse API HTTP {$browseResponse->status()}: {$bMsg}";
                                    }
                                }
                            }
                        }

                        if (!empty($allEbayItems)) {
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Browse API fetch error: ' . $e->getMessage());
                $diagnostics[] = 'Browse API error: ' . $e->getMessage();
            }
        }

        // 4b. Live eBay Quantity & Details Enrichment
        // item_summary/search does not return actual available stock quantities.
        // We enrich listings with exact real-time available quantities from GET /buy/browse/v1/item/{item_id}.
        if (!empty($allEbayItems) && !empty($browseToken)) {
            $unverifiedIds = [];
            $forceReload = request()->boolean('refresh') || request()->boolean('reload') || request()->boolean('force');

            foreach ($allEbayItems as $k => $it) {
                if (empty($it['quantity_verified'])) {
                    $rawNum = preg_match('/(\d{9,14})/', (string)($it['itemId'] ?? $k), $m) ? $m[1] : (string)($it['itemId'] ?? $k);
                    $cachedQty = \Illuminate\Support\Facades\Cache::get("ebay_item_qty_{$rawNum}");
                    if ($cachedQty !== null && !$forceReload) {
                        $allEbayItems[$k]['quantity'] = max(0, (int) $cachedQty);
                        $allEbayItems[$k]['quantity_verified'] = true;
                    } else {
                        $unverifiedIds[$k] = $it['itemId'] ?? $k;
                    }
                }
            }

            if (!empty($unverifiedIds)) {
                $chunks = array_chunk(array_slice($unverifiedIds, 0, 50, true), 25, true);
                foreach ($chunks as $chunk) {
                    try {
                        $poolResponses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($baseUrl, $browseToken, $chunk) {
                            $requests = [];
                            foreach ($chunk as $originalKey => $rawId) {
                                $restId = (str_contains($rawId, '|') || str_starts_with($rawId, 'v1|'))
                                    ? $rawId
                                    : "v1|{$rawId}|0";
                                $requests[] = $pool->withToken($browseToken)
                                    ->acceptJson()
                                    ->withHeaders(['X-EBAY-C-MARKETPLACE-ID' => 'EBAY_US'])
                                    ->timeout(10)
                                    ->get("{$baseUrl}/buy/browse/v1/item/{$restId}");
                            }
                            return $requests;
                        });

                        $hitRateLimit = false;
                        foreach ($poolResponses as $resp) {
                            if ($resp instanceof \Illuminate\Http\Client\Response) {
                                if ($resp->status() === 429) {
                                    $hitRateLimit = true;
                                    break;
                                }
                                if ($resp->successful()) {
                                    $itemDetails = $resp->json() ?? [];
                                    $dItemId = $itemDetails['itemId'] ?? null;
                                    $dLegacyId = $itemDetails['legacyItemId'] ?? null;
                                    if (!$dItemId && !$dLegacyId) continue;

                                    $matchKey = null;
                                    if ($dItemId && isset($allEbayItems[$dItemId])) {
                                        $matchKey = $dItemId;
                                    } elseif ($dLegacyId && isset($allEbayItems[$dLegacyId])) {
                                        $matchKey = $dLegacyId;
                                    } elseif ($dLegacyId && isset($allEbayItems["v1|{$dLegacyId}|0"])) {
                                        $matchKey = "v1|{$dLegacyId}|0";
                                    } else {
                                        $rawNum = preg_match('/(\d{9,14})/', (string)($dItemId ?: $dLegacyId), $m) ? $m[1] : null;
                                        if ($rawNum) {
                                            $matchKey = isset($allEbayItems[$rawNum])
                                                ? $rawNum
                                                : (isset($allEbayItems["v1|{$rawNum}|0"]) ? "v1|{$rawNum}|0" : null);
                                        }
                                    }

                                    if ($matchKey) {
                                        $realQty = null;
                                        if (!empty($itemDetails['estimatedAvailabilities'])) {
                                            foreach ($itemDetails['estimatedAvailabilities'] as $ea) {
                                                if (isset($ea['estimatedAvailableQuantity'])) {
                                                    $realQty = (int) $ea['estimatedAvailableQuantity'];
                                                    break;
                                                } elseif (isset($ea['estimatedRemainingQuantity'])) {
                                                    $realQty = (int) $ea['estimatedRemainingQuantity'];
                                                    break;
                                                } elseif (isset($ea['availabilityThreshold'])) {
                                                    $realQty = (int) $ea['availabilityThreshold'];
                                                    break;
                                                } elseif (($ea['estimatedAvailabilityStatus'] ?? '') === 'OUT_OF_STOCK') {
                                                    $realQty = 0;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($realQty !== null) {
                                            $allEbayItems[$matchKey]['quantity'] = max(0, $realQty);
                                            $allEbayItems[$matchKey]['quantity_verified'] = true;

                                            $numKey = preg_match('/(\d{9,14})/', (string)($dItemId ?: $dLegacyId), $mn) ? $mn[1] : null;
                                            if ($numKey) {
                                                \Illuminate\Support\Facades\Cache::put("ebay_item_qty_{$numKey}", max(0, $realQty), 1800);
                                            }
                                        }

                                        // Enrich dimensions & weight if missing
                                        if (isset($itemDetails['packageWeightAndSize'])) {
                                            $pws = $itemDetails['packageWeightAndSize'];
                                            if (isset($pws['dimensions'])) {
                                                $allEbayItems[$matchKey]['dimensions']['length'] = $allEbayItems[$matchKey]['dimensions']['length'] ?: ((float) ($pws['dimensions']['length'] ?? 0) ?: null);
                                                $allEbayItems[$matchKey]['dimensions']['width'] = $allEbayItems[$matchKey]['dimensions']['width'] ?: ((float) ($pws['dimensions']['width'] ?? 0) ?: null);
                                                $allEbayItems[$matchKey]['dimensions']['height'] = $allEbayItems[$matchKey]['dimensions']['height'] ?: ((float) ($pws['dimensions']['height'] ?? 0) ?: null);
                                            }
                                            if (isset($pws['weight']['value'])) {
                                                $allEbayItems[$matchKey]['dimensions']['weight'] = $allEbayItems[$matchKey]['dimensions']['weight'] ?: ((float) ($pws['weight']['value'] ?? 0) ?: null);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($hitRateLimit) {
                            break;
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('Browse API quantity enrichment chunk error: ' . $e->getMessage());
                    }
                }
            }
        }

        // 5. Final fallback/normalization: ensure every item has quantity >= 0 and non-null dimensions
        foreach ($allEbayItems as $id => &$it) {
            if (!isset($it['quantity']) || $it['quantity'] === null) {
                $it['quantity'] = 1;
            }
            if (empty($it['start_time'])) {
                $it['start_time'] = now()->toIso8601String();
            }
            if (empty($it['seller']['username'])) {
                $it['seller'] = ['username' => $seller];
            }
            if (!isset($it['dimensions']) || !is_array($it['dimensions'])) {
                $textDims = $this->extractDimensionsFromText($it['title'] ?? '');
                $it['dimensions'] = [
                    'length' => $textDims['length'],
                    'width' => $textDims['width'],
                    'height' => $textDims['height'],
                    'weight' => $textDims['weight'],
                ];
            }
        }
        unset($it);

        // Sort all fetched items newest first by start_time / creation date
        uasort($allEbayItems, function ($a, $b) {
            $timeA = strtotime($a['start_time'] ?? $a['itemCreationDate'] ?? 0);
            $timeB = strtotime($b['start_time'] ?? $b['itemCreationDate'] ?? 0);
            return $timeB <=> $timeA;
        });

        return $allEbayItems;
    }

    /**
     * Extract Put Away Location from brackets in listing title.
     * Example: "Dell Zylux A525 Powered Subwoofer 2.1 (14b/b2)" => "14b/b2"
     */
    private function extractPutAwayLocationFromTitle(string $title): ?string
    {
        if (preg_match('/[\(\[](.*?)[\)\]]/', $title, $matches)) {
            $extracted = trim($matches[1]);
            if (!empty($extracted)) {
                return $extracted;
            }
        }
        return null;
    }

    /**
     * Extract dimensions (length, width, height) and weight from title or description text.
     * Example: "Dell Subwoofer 14x10x8 12 lbs" => length: 14, width: 10, height: 8, weight: 12
     */
    private function extractDimensionsFromText(string $text): array
    {
        $dims = [
            'length' => null,
            'width' => null,
            'height' => null,
            'weight' => null,
        ];

        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:"|in|inch|inches)?\s*[xX*]\s*(\d+(?:\.\d+)?)\s*(?:"|in|inch|inches)?\s*[xX*]\s*(\d+(?:\.\d+)?)/i', $text, $m)) {
            $dims['length'] = (float) $m[1];
            $dims['width'] = (float) $m[2];
            $dims['height'] = (float) $m[3];
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:"|in|inch|inches)?\s*[xX*]\s*(\d+(?:\.\d+)?)/i', $text, $m)) {
            $dims['length'] = (float) $m[1];
            $dims['width'] = (float) $m[2];
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:lbs?|pounds?|kg)\b/i', $text, $mw)) {
            $dims['weight'] = (float) $mw[1];
        }

        return $dims;
    }

    /**
     * Fetch single eBay item details (brand, model, seller description, dimensions & weight).
     * Tries REST Browse API (using Client Credentials) and Trading API GetItem.
     */
    private function fetchEbaySingleItemDetails(string $itemId): array
    {
        $details = [
            'brand' => null,
            'model' => null,
            'description' => null,
            'condition' => null,
            'length' => null,
            'width' => null,
            'height' => null,
            'weight' => null,
            'title' => null,
            'quantity' => null,
            'photo_urls' => [],
        ];

        $clientId = config('services.ebay.client_id') ?: env('EBAY_CLIENT_ID', '');
        $clientSecret = config('services.ebay.client_secret') ?: env('EBAY_CLIENT_SECRET', '');

        if (empty($clientId) || empty($clientSecret)) {
            return $details;
        }

        [$baseUrl, $isSandbox, $oauthUrl] = $this->resolveEbayBaseUrl($clientId);

        // 1. Browse API (Client Credentials Token)
        try {
            $tokenResponse = Http::asForm()
                ->timeout(10)
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                ])
                ->post($oauthUrl, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope',
                ]);

            if ($tokenResponse->successful()) {
                $token = $tokenResponse->json('access_token');
                if ($token) {
                    $restItemId = (str_contains($itemId, '|') || str_starts_with($itemId, 'v1|'))
                        ? $itemId
                        : "v1|{$itemId}|0";

                    $response = Http::withToken($token)
                        ->acceptJson()
                        ->withHeaders(['X-EBAY-C-MARKETPLACE-ID' => 'EBAY_US'])
                        ->timeout(10)
                        ->get("{$baseUrl}/buy/browse/v1/item/{$restItemId}");

                    if (!$response->successful() && !str_contains($itemId, '|')) {
                        $response = Http::withToken($token)
                            ->acceptJson()
                            ->withHeaders(['X-EBAY-C-MARKETPLACE-ID' => 'EBAY_US'])
                            ->timeout(10)
                            ->get("{$baseUrl}/buy/browse/v1/item/{$itemId}");
                    }

                    if ($response->successful()) {
                        $data = $response->json();
                        $details['title'] = $data['title'] ?? null;
                        $details['brand'] = $data['brand'] ?? null;
                        $details['description'] = $data['description'] ?? null;
                        $details['condition'] = $data['condition'] ?? $data['conditionDescription'] ?? null;

                        if (!empty($data['estimatedAvailabilities'])) {
                            foreach ($data['estimatedAvailabilities'] as $ea) {
                                if (isset($ea['estimatedAvailableQuantity'])) {
                                    $details['quantity'] = (int) $ea['estimatedAvailableQuantity'];
                                    break;
                                } elseif (isset($ea['estimatedRemainingQuantity'])) {
                                    $details['quantity'] = (int) $ea['estimatedRemainingQuantity'];
                                    break;
                                } elseif (isset($ea['availabilityThreshold'])) {
                                    $details['quantity'] = (int) $ea['availabilityThreshold'];
                                    break;
                                } elseif (($ea['estimatedAvailabilityStatus'] ?? '') === 'OUT_OF_STOCK') {
                                    $details['quantity'] = 0;
                                    break;
                                }
                            }
                        }

                        $rawNum = preg_match('/(\d{9,14})/', (string) $itemId, $m) ? $m[1] : null;
                        if ($rawNum && $details['quantity'] !== null) {
                            \Illuminate\Support\Facades\Cache::put("ebay_item_qty_{$rawNum}", $details['quantity'], 1800);
                        }

                        $aspects = $data['localizedAspects'] ?? [];
                        foreach ($aspects as $asp) {
                            $name = strtolower($asp['name'] ?? '');
                            $val = $asp['value'] ?? (isset($asp['values'][0]) ? $asp['values'][0] : null);
                            if (!$val) continue;

                            if ($name === 'brand' && !$details['brand']) {
                                $details['brand'] = $val;
                            } elseif (in_array($name, ['model', 'mpn', 'model number', 'style'])) {
                                if (!$details['model'] || $name === 'model') {
                                    $details['model'] = $val;
                                }
                            }
                        }

                        if (isset($data['packageWeightAndSize'])) {
                            $pws = $data['packageWeightAndSize'];
                            if (isset($pws['dimensions'])) {
                                $details['length'] = (float) ($pws['dimensions']['length'] ?? 0) ?: null;
                                $details['width'] = (float) ($pws['dimensions']['width'] ?? 0) ?: null;
                                $details['height'] = (float) ($pws['dimensions']['height'] ?? 0) ?: null;
                            }
                            if (isset($pws['weight'])) {
                                $details['weight'] = (float) ($pws['weight']['value'] ?? 0) ?: null;
                            }
                        }

                        if (isset($data['itemDimensions'])) {
                            $idims = $data['itemDimensions'];
                            $details['length'] = $details['length'] ?: ((float) ($idims['length'] ?? 0) ?: null);
                            $details['width'] = $details['width'] ?: ((float) ($idims['width'] ?? 0) ?: null);
                            $details['height'] = $details['height'] ?: ((float) ($idims['height'] ?? 0) ?: null);
                        }

                        // Collect Browse API photo URLs
                        $browsePhotos = [];
                        if (!empty($data['image']['imageUrl'])) {
                            $norm = $this->normalizeEbayImageUrl($data['image']['imageUrl']);
                            if ($norm) $browsePhotos[] = $norm;
                        }
                        if (!empty($data['additionalImages']) && is_array($data['additionalImages'])) {
                            foreach ($data['additionalImages'] as $ai) {
                                $u = $ai['imageUrl'] ?? null;
                                if ($u) {
                                    $norm = $this->normalizeEbayImageUrl($u);
                                    if ($norm) $browsePhotos[] = $norm;
                                }
                            }
                        }
                        if (!empty($browsePhotos)) {
                            $details['photo_urls'] = array_values(array_unique(array_filter($browsePhotos)));
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('fetchEbayBrowseItemDetails error: ' . $e->getMessage());
        }

        // 2. Trading API GetItem if refresh token is present
        $refreshToken = app(EbayOrderSyncService::class)->getRefreshToken();
        if ($refreshToken) {
            try {
                $userToken = null;
                try {
                    $syncService = app(EbayOrderSyncService::class);
                    $userToken = $syncService->getUserAccessToken();
                } catch (\Throwable $e) {
                    $userTokenResp = Http::asForm()
                        ->timeout(10)
                        ->withHeaders([
                            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                        ])
                        ->post($oauthUrl, [
                            'grant_type' => 'refresh_token',
                            'refresh_token' => $refreshToken,
                        ]);

                    if ($userTokenResp->successful()) {
                        $userToken = $userTokenResp->json('access_token');
                    }
                }

                if ($userToken) {
                    $rawNumericId = preg_match('/(\d{9,14})/', (string) $itemId, $m) ? $m[1] : (string) $itemId;
                    $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
                        . '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
                        . '  <ItemID>' . htmlspecialchars($rawNumericId) . '</ItemID>'
                        . '  <DetailLevel>ReturnAll</DetailLevel>'
                        . '  <IncludeItemSpecifics>true</IncludeItemSpecifics>'
                        . '</GetItemRequest>';

                    $tradingEndpoint = rtrim($baseUrl, '/') . '/ws/api.dll';

                    $tResp = Http::timeout(10)->withHeaders([
                        'X-EBAY-API-CALL-NAME' => 'GetItem',
                        'X-EBAY-API-SITEID' => '0',
                        'X-EBAY-API-COMPATIBILITY-LEVEL' => '1115',
                        'X-EBAY-API-IAF-TOKEN' => $userToken,
                    ])->withBody($xmlBody, 'text/xml')->post($tradingEndpoint);

                    if ($tResp->successful()) {
                        $xml = @simplexml_load_string($tResp->body());
                        if ($xml && isset($xml->Item)) {
                            $item = $xml->Item;
                            if (isset($item->QuantityAvailable)) {
                                $details['quantity'] = (int) $item->QuantityAvailable;
                            } elseif (isset($item->Quantity)) {
                                $qtyTotal = (int) $item->Quantity;
                                $qtySold = isset($item->SellingStatus->QuantitySold) ? (int) $item->SellingStatus->QuantitySold : 0;
                                $details['quantity'] = max(0, $qtyTotal - $qtySold);
                            }
                            if (isset($item->Title) && !$details['title']) {
                                $details['title'] = (string) $item->Title;
                            }
                            if (isset($item->Description) && !$details['description']) {
                                $details['description'] = (string) $item->Description;
                            }

                            if (isset($item->ItemSpecifics->NameValueList)) {
                                foreach ($item->ItemSpecifics->NameValueList as $nv) {
                                    $n = strtolower((string) $nv->Name);
                                    $v = (string) ($nv->Value[0] ?? $nv->Value);
                                    if (!$v) continue;

                                    if ($n === 'brand' && !$details['brand']) {
                                        $details['brand'] = $v;
                                    } elseif (in_array($n, ['model', 'mpn', 'model number'])) {
                                        if (!$details['model'] || $n === 'model') {
                                            $details['model'] = $v;
                                        }
                                    }
                                }
                            }

                            if (isset($item->ShippingPackageDetails)) {
                                $pkg = $item->ShippingPackageDetails;
                                if (isset($pkg->PackageLength) && (float)$pkg->PackageLength > 0) {
                                    $details['length'] = (float) $pkg->PackageLength;
                                }
                                if (isset($pkg->PackageWidth) && (float)$pkg->PackageWidth > 0) {
                                    $details['width'] = (float) $pkg->PackageWidth;
                                }
                                if (isset($pkg->PackageDepth) && (float)$pkg->PackageDepth > 0) {
                                    $details['height'] = (float) $pkg->PackageDepth;
                                } elseif (isset($pkg->PackageHeight) && (float)$pkg->PackageHeight > 0) {
                                    $details['height'] = (float) $pkg->PackageHeight;
                                }
                                $wMajor = isset($pkg->WeightMajor) ? (float) $pkg->WeightMajor : 0;
                                $wMinor = isset($pkg->WeightMinor) ? (float) $pkg->WeightMinor : 0;
                                if ($wMajor > 0 || $wMinor > 0) {
                                    $details['weight'] = round($wMajor + ($wMinor / 16.0), 2);
                                }
                            }

                            // Collect all photos from Trading API GetItem
                            if (isset($item->PictureDetails->PictureURL)) {
                                $tradingPhotos = [];
                                foreach ($item->PictureDetails->PictureURL as $pic) {
                                    $pUrl = (string) $pic;
                                    if ($pUrl) {
                                        $norm = $this->normalizeEbayImageUrl($pUrl);
                                        if ($norm) $tradingPhotos[] = $norm;
                                    }
                                }
                                if (!empty($tradingPhotos)) {
                                    $details['photo_urls'] = array_values(array_unique(array_merge($details['photo_urls'] ?? [], $tradingPhotos)));
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('fetchEbaySingleItemDetails Trading API error: ' . $e->getMessage());
            }
        }

        return $details;
    }

    /**
     * Fetch all active eBay seller listings and import them into inventory.
     * Also detects sold items and updates their status.
     */
    public function fetchFromEbay(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $limit = 10;
        set_time_limit(300); // Allow up to 5 minutes for large catalogs

        $seller = trim($request->get('seller') ?: (config('services.ebay.seller_username') ?: env('EBAY_SELLER_USERNAME') ?: 'systematictrade'));
        $clientId = config('services.ebay.client_id') ?: env('EBAY_CLIENT_ID', '');
        $clientSecret = config('services.ebay.client_secret') ?: env('EBAY_CLIENT_SECRET', '');
        $refreshToken = app(EbayOrderSyncService::class)->getRefreshToken();

        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'eBay API credentials not configured. Please set EBAY_CLIENT_ID and EBAY_CLIENT_SECRET in .env'
            ], 422);
        }

        [$baseUrl, $isSandbox, $oauthUrl] = $this->resolveEbayBaseUrl($clientId);

        $diagnostics = [];
        try {
            $allEbayItems = $this->fetchRawEbayItems($clientId, $clientSecret, $refreshToken, $baseUrl, $seller, $oauthUrl, $diagnostics);

            if (empty($allEbayItems)) {
                $diagnostics = array_values(array_unique(array_filter($diagnostics)));
                $reasonText = !empty($diagnostics) ? implode(' | ', $diagnostics) : "No active listings found on eBay for seller '{$seller}'.";

                return response()->json([
                    'success' => false,
                    'message' => 'eBay fetch returned 0 items. Reason: ' . $reasonText,
                    'reasons' => $diagnostics,
                    'total_fetched' => 0,
                    'imported' => 0,
                    'skipped' => 0,
                    'sold_updated' => 0,
                    'seller' => $seller,
                    'environment' => $isSandbox ? 'Sandbox' : 'Production',
                ], 422);
            }

            $forceResync = (bool) ($request->input('force_resync') || $request->input('overwrite'));
            $onlySynced = (bool) $request->input('only_synced', false);
            $checkOnly = (bool) $request->input('check_only', false);

        // 3. Import items into inventory (with duplicate prevention) & detect sold items inside a single DB transaction
        $imported = 0;
        $skipped = 0;
        $soldUpdated = 0;
        $activeEbayIds = [];
        $quantityChanges = [];

        DB::transaction(function () use ($allEbayItems, $seller, $forceResync, $onlySynced, $checkOnly, &$imported, &$skipped, &$soldUpdated, &$activeEbayIds, &$quantityChanges) {
            foreach ($allEbayItems as $ebayItem) {
                $ebayItemId = $ebayItem['itemId'] ?? null;
                if (!$ebayItemId) {
                    continue;
                }

                $rawNumericId = preg_match('/(\d{9,14})/', (string) $ebayItemId, $m) ? $m[1] : (string) $ebayItemId;
                $activeEbayIds[] = $ebayItemId;
                $activeEbayIds[] = $rawNumericId;
                $activeEbayIds[] = "v1|{$rawNumericId}|0";

                // Collect image URLs robustly
                $photoPaths = $this->extractEbayPhotoUrls($ebayItem);
                if (empty($photoPaths) || count($photoPaths) <= 1) {
                    $singleDetails = $this->fetchEbaySingleItemDetails($ebayItemId);
                    if (!empty($singleDetails['photo_urls'])) {
                        $photoPaths = array_values(array_unique(array_merge($singleDetails['photo_urls'], $photoPaths)));
                    }
                }

                // Check duplicate by barcode (eBay itemId)
                $title = $ebayItem['title'] ?? 'Untitled eBay Product';
                $putAwayLocation = $this->extractPutAwayLocationFromTitle($title);
                $dims = $ebayItem['dimensions'] ?? [];
                $pLength = $dims['length'] ?? null;
                $pWidth = $dims['width'] ?? null;
                $pHeight = $dims['height'] ?? null;
                $pWeight = $dims['weight'] ?? null;

                if (!$pLength && !$pWidth && !$pHeight && !$pWeight) {
                    $textDims = $this->extractDimensionsFromText($title);
                    $pLength = $textDims['length'] ?? null;
                    $pWidth = $textDims['width'] ?? null;
                    $pHeight = $textDims['height'] ?? null;
                    $pWeight = $textDims['weight'] ?? null;
                }

                $rawEbayQty = (isset($ebayItem['quantity']) && $ebayItem['quantity'] !== null) ? (int) $ebayItem['quantity'] : null;

                $rawNumericId = preg_match('/(\d{9,14})/', (string) $ebayItemId, $m) ? $m[1] : (string) $ebayItemId;
                $existing = Inventory::where('barcode', $ebayItemId)
                    ->orWhere('barcode', $rawNumericId)
                    ->orWhere('barcode', 'v1|' . $rawNumericId . '|0')
                    ->orWhere('barcode', 'LIKE', '%' . $rawNumericId . '%')
                    ->first();
                if ($existing) {
                    $oldQty = (int) $existing->quantity;
                    // If eBay provided a specific quantity, use it. Otherwise preserve existing known quantity.
                    $ebayQty = $rawEbayQty !== null ? max(0, $rawEbayQty) : $oldQty;

                    $quantityChanges[] = [
                        'barcode' => $existing->barcode,
                        'name' => $existing->name,
                        'old_qty' => $oldQty,
                        'new_qty' => $ebayQty,
                        'changed' => $oldQty !== $ebayQty,
                        'status' => $ebayQty > 0 ? 'In Stock' : 'Sold',
                    ];

                    $isSynced = (int) ($existing->sync_status ?? 0) === 1;
                    $updateData = [
                        'quantity' => $ebayQty,
                        'intake_type' => $isSynced ? 'Denver' : 'eBay',
                    ];

                    $newPrice = $ebayItem['price']['value'] ?? null;
                    if ($newPrice && $newPrice > 0 && ($forceResync || $existing->price != $newPrice)) {
                        $updateData['price'] = $newPrice;
                        $updateData['estimated_value'] = $newPrice;
                    }
                    if ($ebayQty === 0) {
                        $updateData['status'] = 'Sold';
                    } elseif ($existing->status === 'Sold' || $existing->status === 'Out of Stock') {
                        $updateData['status'] = 'In Stock';
                    }
                    if ($putAwayLocation && !$existing->put_away_location) {
                        $updateData['put_away_location'] = $putAwayLocation;
                    }
                    if ($pWeight && !$existing->weight) {
                        $updateData['weight'] = $pWeight;
                    }

                    if (!empty($photoPaths)) {
                        $existingPhotos = is_array($existing->photos_paths) ? $existing->photos_paths : [];
                        $cleanExisting = array_values(array_filter($existingPhotos, function($p) {
                            return is_string($p) && !str_starts_with($p, '/repair/http') && !str_starts_with($p, 'repair/http');
                        }));
                        $mergedPhotos = $forceResync
                            ? array_values(array_unique(array_merge($photoPaths, $cleanExisting)))
                            : array_values(array_unique(array_merge($cleanExisting, $photoPaths)));

                        if ($mergedPhotos !== $existingPhotos) {
                            $updateData['photos_paths'] = $mergedPhotos;
                            if (empty($existing->repair_photos)) {
                                $updateData['repair_photos'] = $mergedPhotos;
                            }
                        }
                    }

                    if ($forceResync) {
                        if ($title && $existing->name !== $title) {
                            $updateData['name'] = $title;
                        }
                    }

                    if (!$checkOnly && !empty($updateData)) {
                        $existing->update($updateData);
                    }

                    if (!$checkOnly) {
                        InventoryShippingRate::updateOrCreate(
                            ['inventory_id' => $existing->id],
                            [
                                'parcel_length' => $pLength,
                                'parcel_width' => $pWidth,
                                'parcel_height' => $pHeight,
                                'parcel_weight' => $pWeight ?: $existing->weight,
                                'distance_unit' => 'in',
                                'mass_unit' => 'lb',
                            ]
                        );
                    }

                    $skipped++;
                    continue;
                }

                // If only checking or syncing already synced products, skip importing new products
                if ($onlySynced) {
                    continue;
                }

                    // Map eBay item to Inventory fields
                    $price = $ebayItem['price']['value'] ?? 0;
                    $condition = $ebayItem['condition'] ?? 'N/A';
                    $categoryName = $ebayItem['categories'][0]['categoryName'] ?? 'eBay';
                    $itemWebUrl = $ebayItem['itemWebUrl'] ?? '';
                    $sellerUsername = $ebayItem['seller']['username'] ?? $seller;

                    // Extract brand from title
                    $brand = $this->extractBrandFromTitle($title);

                    // Build notes
                    $notes = "eBay Seller: {$sellerUsername}";
                    if ($itemWebUrl) {
                        $notes .= "\neBay URL: {$itemWebUrl}";
                    }
                    $notes .= "\neBay Item ID: {$ebayItemId}";
                    $notes .= "\nImported: " . now()->format('M d, Y h:i A');

                    $newInvRecord = Inventory::create([
                        'barcode' => $ebayItemId,
                        'name' => $title,
                        'brand' => $brand,
                        'category' => $categoryName,
                        'intake_type' => 'eBay',
                        'condition_status' => $condition,
                        'quantity' => $rawEbayQty !== null ? max(0, $rawEbayQty) : 1,
                        'estimated_value' => $price,
                        'price' => $price,
                        'photos_paths' => $photoPaths,
                        'repair_photos' => $photoPaths,
                        'notes' => $notes,
                        'status' => ($rawEbayQty !== null ? $rawEbayQty : 1) > 0 ? 'In Stock' : 'Out of Stock',
                        'device_type' => $categoryName,
                        'put_away_location' => $putAwayLocation,
                        'weight' => $pWeight,
                    ]);

                    InventoryShippingRate::updateOrCreate(
                        ['inventory_id' => $newInvRecord->id],
                        [
                            'parcel_length' => $pLength,
                            'parcel_width' => $pWidth,
                            'parcel_height' => $pHeight,
                            'parcel_weight' => $pWeight,
                            'distance_unit' => 'in',
                            'mass_unit' => 'lb',
                        ]
                    );

                    $imported++;
                }

                // 4. Detect sold items: eBay inventory items NOT in active listings
                if (!$checkOnly && !empty($activeEbayIds)) {
                    $ebayInventoryItems = Inventory::where(function ($q) {
                        $q->where('intake_type', 'eBay')
                          ->orWhere('intake_type', 'Denver')
                          ->orWhere('barcode', 'LIKE', 'v1|%');
                    })
                        ->where('status', '!=', 'Sold')
                        ->whereNotNull('barcode')
                        ->where('barcode', '!=', '')
                        ->get();

                    foreach ($ebayInventoryItems as $invItem) {
                        if (!in_array($invItem->barcode, $activeEbayIds)) {
                            $invItem->update([
                                'status' => 'Sold',
                                'quantity' => 0,
                            ]);
                            $soldUpdated++;
                        }
                    }
                }
            });

            $totalFetchedCount = count($allEbayItems);
            $changedCount = count(array_filter($quantityChanges, fn($c) => !empty($c['changed'])));

            $msg = $checkOnly
                ? "Checked " . count($quantityChanges) . " already synced eBay product(s)."
                : ($onlySynced
                    ? "eBay quantities matched successfully! Checked {$totalFetchedCount} eBay listings. Updated {$skipped} synced product(s) in inventory ({$changedCount} quantity changed, {$soldUpdated} marked sold out)."
                    : "eBay sync complete! Fetched {$totalFetchedCount} eBay items total. Imported: {$imported}, Updated/Existing: {$skipped}, Sold: {$soldUpdated}");

            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $imported,
                'skipped' => $skipped,
                'sold_updated' => $soldUpdated,
                'total_fetched' => $totalFetchedCount,
                'quantity_changes' => $quantityChanges,
                'changed_count' => $changedCount,
                'seller' => $seller,
                'environment' => $isSandbox ? 'Sandbox' : 'Production',
            ]);

        } catch (\Throwable $e) {
            \Log::error('eBay Fetch to Inventory failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'eBay sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract eBay photo URLs from different payload formats.
     */
    private function extractEbayPhotoUrls(array $ebayItem): array
    {
        $urls = [];

        // Main image
        if (!empty($ebayItem['image'])) {
            if (is_string($ebayItem['image'])) {
                $urls[] = $ebayItem['image'];
            } elseif (is_array($ebayItem['image'])) {
                $urls[] = $ebayItem['image']['imageUrl'] ?? $ebayItem['image']['PictureURL'] ?? null;
            }
        }

        // Additional / Thumbnail images
        foreach (['thumbnailImages', 'additionalImages', 'imageUrls', 'PictureURL', 'pictureUrl'] as $key) {
            if (!empty($ebayItem[$key])) {
                if (is_string($ebayItem[$key])) {
                    $urls[] = $ebayItem[$key];
                } elseif (is_array($ebayItem[$key])) {
                    foreach ($ebayItem[$key] as $img) {
                        if (is_string($img)) {
                            $urls[] = $img;
                        } elseif (is_array($img)) {
                            $urls[] = $img['imageUrl'] ?? $img['PictureURL'] ?? null;
                        }
                    }
                }
            }
        }

        $normalized = [];
        foreach (array_filter($urls) as $u) {
            $norm = $this->normalizeEbayImageUrl($u);
            if (!empty($norm)) {
                $normalized[] = $norm;
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Normalize eBay image URL to get higher resolution version and enforce HTTPS.
     */
    private function normalizeEbayImageUrl(?string $url): ?string
    {
        if (!$url) return null;
        $url = trim($url);
        if (str_starts_with($url, 'http://')) {
            $url = 'https://' . substr($url, 7);
        }
        $url = str_replace('/thumbs/images/', '/images/', $url);
        return preg_replace('/\/s-l\d+\.(jpg|jpeg|png|webp)/i', '/s-l1600.$1', $url);
    }

    /**
     * Extract brand name from product title using common brand patterns.
     */
    private function extractBrandFromTitle(string $title): ?string
    {
        $brands = [
            'Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer', 'Microsoft', 'Samsung',
            'Toshiba', 'Sony', 'LG', 'MSI', 'Razer', 'Alienware', 'ThinkPad',
            'EliteBook', 'Latitude', 'Inspiron', 'Pavilion', 'Surface', 'MacBook',
            'SGIN', 'Chromebook', 'Gateway', 'Panasonic', 'Fujitsu', 'Intel',
        ];

        foreach ($brands as $brand) {
            if (stripos($title, $brand) !== false) {
                return $brand;
            }
        }

        return null;
    }

    /**
     * Automatic synchronization of eBay sold orders and inventory deduction.
     */
    public function syncEbayOrders(Request $request, EbayOrderSyncService $syncService)
    {
        $authStatus = $syncService->checkAuthorizationStatus();
        if (!$authStatus['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $authStatus['message'],
                'auth_status' => $authStatus,
            ], 422);
        }

        try {
            $days = (int) $request->input('days', 30);
            $dryRun = (bool) $request->input('dry_run', false);
            $token = $syncService->getUserAccessToken();
            $orders = $syncService->fetchOrdersFromApi($token, $days);
            $summary = $syncService->processOrders($orders, $dryRun);

            return response()->json([
                'success' => true,
                'message' => "eBay order sync complete. Fetched: {$summary['total_fetched']}, Deducted: {$summary['deducted']}, Restored: {$summary['restored']}, Skipped: {$summary['already_synced']}",
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            \Log::error('syncEbayOrders error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'eBay order sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}

