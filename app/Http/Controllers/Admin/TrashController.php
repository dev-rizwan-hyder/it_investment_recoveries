<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TrashController extends Controller
{
    // Map section slugs to model classes
    private const SECTION_MAP = [
        'processing-intake' => \App\Models\Pallet::class,
        'data-destruction' => \App\Models\DataDestructionItem::class,
        'it-assets' => \App\Models\ItAssetsItem::class,
        'ecommerce' => \App\Models\EcommerceItem::class,
        'refurbishing' => \App\Models\RefurbishingItem::class,
        'universal-waste' => \App\Models\UniversalWasteItem::class,
        'repair' => \App\Models\RepairEntry::class,
        'inventory' => \App\Models\Inventory::class,
        'sales-invoices' => \App\Models\SalesInvoice::class,
        'orders' => \App\Models\Order::class,
        'employees' => \App\Models\Employee::class,
        'departments' => \App\Models\Department::class,
        'policies' => \App\Models\Policy::class,
        'clients' => \App\Models\Client::class,
        'expenses' => \App\Models\Expense::class,
    ];

    // Human-readable labels for sections
    private const SECTION_LABELS = [
        'processing-intake' => 'Processing Intake',
        'data-destruction' => 'Data Destruction',
        'it-assets' => 'IT Assets',
        'ecommerce' => 'Ecommerce',
        'refurbishing' => 'Refurbishing',
        'universal-waste' => 'Universal Waste',
        'repair' => 'Repair Entries',
        'inventory' => 'Inventory',
        'sales-invoices' => 'Sales Invoices',
        'orders' => 'Orders',
        'employees' => 'Employees',
        'departments' => 'Departments',
        'policies' => 'Policies',
        'clients' => 'Clients',
        'expenses' => 'Expenses',
    ];

    public function index(Request $request)
    {
        $currentSection = $request->get('section', 'all');
        $sections = self::SECTION_LABELS;
        
        // Get counts for each section
        $sectionCounts = [];
        foreach (self::SECTION_MAP as $key => $modelClass) {
            $sectionCounts[$key] = $modelClass::onlyTrashed()->count();
        }
        // Add ebay-products count
        $sectionCounts['ebay-products'] = $this->getEbayTrashCount();
        $sections['ebay-products'] = 'eBay Products';
        
        $totalTrashed = array_sum($sectionCounts);
        
        return view('admin.trash.index', compact('currentSection', 'sections', 'sectionCounts', 'totalTrashed'));
    }
    
    public function getData(Request $request)
    {
        $section = $request->get('section', 'all');
        $search = $request->get('search', '');
        $items = [];
        
        if ($section === 'ebay-products') {
            $items = $this->getEbayTrashedItems($search);
            return response()->json(['success' => true, 'data' => $items, 'section' => $section]);
        }
        
        $sectionsToQuery = ($section === 'all') ? array_keys(self::SECTION_MAP) : [$section];
        
        foreach ($sectionsToQuery as $sectionKey) {
            if (!isset(self::SECTION_MAP[$sectionKey])) continue;
            $modelClass = self::SECTION_MAP[$sectionKey];
            
            $query = $modelClass::onlyTrashed();
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    // Search common columns
                    $columns = ['name', 'barcode', 'barcode_number', 'description', 'item_description', 'customer_name', 'invoice_number', 'order_number', 'title'];
                    foreach ($columns as $col) {
                        if (\Schema::hasColumn($q->getModel()->getTable(), $col)) {
                            $q->orWhere($col, 'like', "%{$search}%");
                        }
                    }
                });
            }
            
            $records = $query->latest('deleted_at')->limit(100)->get();
            
            foreach ($records as $record) {
                $photos = $this->getItemPhotos($record, $sectionKey);
                $items[] = [
                    'id' => $record->id,
                    'section' => $sectionKey,
                    'section_label' => self::SECTION_LABELS[$sectionKey],
                    'title' => $this->getItemTitle($record, $sectionKey),
                    'subtitle' => $this->getItemSubtitle($record, $sectionKey),
                    'image_url' => $photos[0] ?? null,
                    'deleted_at' => $record->deleted_at->format('M d, Y h:i A'),
                    'deleted_at_diff' => $record->deleted_at->diffForHumans(),
                ];
            }
        }
        
        // Also include eBay products if section is 'all'
        if ($section === 'all') {
            $ebayItems = $this->getEbayTrashedItems($search);
            $items = array_merge($items, $ebayItems);
        }
        
        // Sort by deleted_at descending
        usort($items, fn($a, $b) => strtotime($b['deleted_at']) - strtotime($a['deleted_at']));
        
        return response()->json(['success' => true, 'data' => $items, 'section' => $section]);
    }

    public function restore(Request $request, $section, $id)
    {
        if ($section === 'ebay-products') {
            return $this->restoreEbayProduct($id);
        }
        
        if (!isset(self::SECTION_MAP[$section])) {
            return response()->json(['success' => false, 'message' => 'Invalid section'], 400);
        }
        
        $modelClass = self::SECTION_MAP[$section];
        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $title = $this->getItemTitle($item, $section);
        $sectionLabel = self::SECTION_LABELS[$section] ?? ucfirst($section);
        
        $item->restore();
        
        return response()->json([
            'success' => true, 
            'message' => "Item '{$title}' has been successfully restored to {$sectionLabel}!",
            'restored_title' => $title,
            'section_label' => $sectionLabel
        ]);
    }

    public function getItemDetails(Request $request, $section, $id)
    {
        if ($section === 'ebay-products') {
            $trashDir = $this->getEbayTrashPath() . DIRECTORY_SEPARATOR . basename($id);
            if (!File::isDirectory($trashDir)) {
                return response()->json(['success' => false, 'message' => 'Trashed product folder not found'], 404);
            }
            
            $metadataPath = $trashDir . DIRECTORY_SEPARATOR . 'metadata.json';
            $trashedAtPath = $trashDir . DIRECTORY_SEPARATOR . '.trashed_at';
            
            $metadata = File::isFile($metadataPath) ? (json_decode(File::get($metadataPath), true) ?: []) : [];
            $trashedAt = File::isFile($trashedAtPath) ? trim(File::get($trashedAtPath)) : null;
            
            $photos = [];
            $files = File::files($trashDir);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $photos[] = asset('ebay-product-trash/' . basename($id) . '/' . $file->getFilename());
                }
            }
            
            $title = $metadata['title'] ?? $id;
            
            $attributes = [
                'Folder Name' => $id,
                'Title' => $title,
                'Barcode' => $metadata['barcode'] ?? ($metadata['barcode_number'] ?? 'N/A'),
                'Price' => isset($metadata['price']) ? ('$' . number_format((float)$metadata['price'], 2)) : 'N/A',
                'Quantity' => $metadata['quantity'] ?? 'N/A',
                'Category' => $metadata['category'] ?? 'N/A',
                'Condition' => $metadata['condition'] ?? 'N/A',
                'Deleted Date' => $trashedAt ? date('M d, Y h:i A', strtotime($trashedAt)) : 'N/A',
            ];

            if (!empty($metadata['description'])) {
                $attributes['Description'] = $metadata['description'];
            }
            
            if (!empty($metadata['specs']) && is_array($metadata['specs'])) {
                foreach ($metadata['specs'] as $k => $v) {
                    if (is_scalar($v)) {
                        $attributes['Spec: ' . ucfirst($k)] = $v;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'section' => 'ebay-products',
                    'section_label' => 'eBay Products',
                    'title' => $title,
                    'photos' => $photos,
                    'attributes' => $attributes,
                    'deleted_at' => $trashedAt ? date('M d, Y h:i A', strtotime($trashedAt)) : 'N/A',
                ]
            ]);
        }

        if (!isset(self::SECTION_MAP[$section])) {
            return response()->json(['success' => false, 'message' => 'Invalid section'], 400);
        }

        $modelClass = self::SECTION_MAP[$section];
        $record = $modelClass::onlyTrashed()->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Item not found in trash'], 404);
        }

        $title = $this->getItemTitle($record, $section);
        $photos = $this->getItemPhotos($record, $section);

        $rawAttributes = $record->toArray();
        $attributes = [];

        $exclude = ['id', 'deleted_at', 'created_at', 'updated_at', 'photos_paths', 'repair_photos', 'picture_paths', 'photos', 'employee_picture', 'image', 'password', 'remember_token'];

        foreach ($rawAttributes as $key => $val) {
            if (in_array($key, $exclude) || is_null($val)) continue;
            
            $formattedKey = ucwords(str_replace('_', ' ', $key));
            
            if (is_array($val)) {
                $val = json_encode($val, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            } elseif (is_bool($val)) {
                $val = $val ? 'Yes' : 'No';
            }
            
            $attributes[$formattedKey] = (string)$val;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $record->id,
                'section' => $section,
                'section_label' => self::SECTION_LABELS[$section] ?? ucfirst($section),
                'title' => $title,
                'photos' => $photos,
                'attributes' => $attributes,
                'deleted_at' => $record->deleted_at ? $record->deleted_at->format('M d, Y h:i A') : 'N/A',
            ]
        ]);
    }

    public function forceDelete(Request $request, $section, $id)
    {
        if ($section === 'ebay-products') {
            return $this->forceDeleteEbayProduct($id);
        }
        
        if (!isset(self::SECTION_MAP[$section])) {
            return response()->json(['success' => false, 'message' => 'Invalid section'], 400);
        }
        
        $modelClass = self::SECTION_MAP[$section];
        $item = $modelClass::onlyTrashed()->findOrFail($id);
        
        // Clean up files before permanent delete
        $this->cleanupFilesForItem($item, $section);
        
        $item->forceDelete();
        
        return response()->json(['success' => true, 'message' => 'Item permanently deleted']);
    }

    public function bulkRestore(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.section' => 'required|string',
        ]);
        
        $restored = 0;
        foreach ($validated['items'] as $item) {
            if ($item['section'] === 'ebay-products') {
                $this->restoreEbayProduct($item['id']);
                $restored++;
                continue;
            }
            if (!isset(self::SECTION_MAP[$item['section']])) continue;
            $modelClass = self::SECTION_MAP[$item['section']];
            $record = $modelClass::onlyTrashed()->find($item['id']);
            if ($record) {
                $record->restore();
                $restored++;
            }
        }
        
        return response()->json(['success' => true, 'message' => "{$restored} item(s) restored successfully"]);
    }

    public function bulkForceDelete(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.section' => 'required|string',
        ]);
        
        $deleted = 0;
        foreach ($validated['items'] as $item) {
            if ($item['section'] === 'ebay-products') {
                $this->forceDeleteEbayProduct($item['id']);
                $deleted++;
                continue;
            }
            if (!isset(self::SECTION_MAP[$item['section']])) continue;
            $modelClass = self::SECTION_MAP[$item['section']];
            $record = $modelClass::onlyTrashed()->find($item['id']);
            if ($record) {
                $this->cleanupFilesForItem($record, $item['section']);
                $record->forceDelete();
                $deleted++;
            }
        }
        
        return response()->json(['success' => true, 'message' => "{$deleted} item(s) permanently deleted"]);
    }

    public function emptyTrash(Request $request, $section)
    {
        if ($section === 'ebay-products') {
            return $this->emptyEbayTrash();
        }
        
        if (!isset(self::SECTION_MAP[$section])) {
            return response()->json(['success' => false, 'message' => 'Invalid section'], 400);
        }
        
        $modelClass = self::SECTION_MAP[$section];
        $items = $modelClass::onlyTrashed()->get();
        $count = 0;
        foreach ($items as $item) {
            $this->cleanupFilesForItem($item, $section);
            $item->forceDelete();
            $count++;
        }
        
        return response()->json(['success' => true, 'message' => "{$count} item(s) permanently deleted from " . (self::SECTION_LABELS[$section] ?? $section)]);
    }

    // --- Helper methods ---
    
    private function getItemTitle($record, $section)
    {
        // Try common title fields
        if (!empty($record->name)) return $record->name;
        if (!empty($record->barcode_number)) return 'Pallet #' . $record->barcode_number;
        if (!empty($record->barcode)) return $record->barcode;
        if (!empty($record->invoice_number)) return 'Invoice ' . $record->invoice_number;
        if (!empty($record->order_number)) return 'Order ' . $record->order_number;
        if (!empty($record->customer_name)) return $record->customer_name;
        if (!empty($record->item_description)) return \Str::limit($record->item_description, 50);
        if (!empty($record->description)) return \Str::limit($record->description, 50);
        if (!empty($record->title)) return $record->title;
        return ucfirst(str_replace('-', ' ', $section)) . ' #' . $record->id;
    }
    
    private function getItemSubtitle($record, $section)
    {
        $parts = [];
        if (!empty($record->barcode)) $parts[] = 'Barcode: ' . $record->barcode;
        if (!empty($record->barcode_number) && $section !== 'processing-intake') $parts[] = 'Pallet: ' . $record->barcode_number;
        if (!empty($record->status)) $parts[] = 'Status: ' . $record->status;
        if (!empty($record->category)) $parts[] = $record->category;
        if (!empty($record->brand)) $parts[] = $record->brand;
        if (!empty($record->email)) $parts[] = $record->email;
        if (!empty($record->customer_email)) $parts[] = $record->customer_email;
        if (isset($record->quantity)) $parts[] = 'Qty: ' . $record->quantity;
        if (isset($record->total)) $parts[] = '$' . number_format($record->total, 2);
        if (isset($record->amount)) $parts[] = '$' . number_format($record->amount, 2);
        return implode(' • ', array_slice($parts, 0, 4));
    }
    
    private function getItemPhotos($record, string $section): array
    {
        $photos = [];

        if (!empty($record->photos_paths) && is_array($record->photos_paths)) {
            foreach ($record->photos_paths as $path) {
                if (is_string($path) && !empty($path)) {
                    if (str_starts_with($path, 'http') || str_starts_with($path, 'data:')) {
                        $photos[] = $path;
                    } else {
                        $photos[] = asset('storage/' . ltrim($path, '/'));
                    }
                }
            }
        }

        if (!empty($record->repair_photos) && is_array($record->repair_photos)) {
            foreach ($record->repair_photos as $path) {
                if (is_string($path) && !empty($path)) {
                    $photos[] = str_starts_with($path, 'http') ? $path : asset('storage/' . ltrim($path, '/'));
                }
            }
        }

        if (!empty($record->picture_paths) && is_array($record->picture_paths)) {
            foreach ($record->picture_paths as $path) {
                if (is_string($path) && !empty($path)) {
                    $photos[] = str_starts_with($path, 'http') ? $path : asset('storage/' . ltrim($path, '/'));
                }
            }
        }

        if (!empty($record->employee_picture)) {
            $photos[] = asset('employee-profile/' . $record->employee_picture);
        }

        if (!empty($record->image) && $section === 'policies') {
            $photos[] = asset('storage/' . ltrim($record->image, '/'));
        }

        if (!empty($record->photos) && $section === 'repair') {
            $rawPhotos = is_array($record->photos) ? $record->photos : (json_decode($record->photos, true) ?: []);
            foreach ($rawPhotos as $photo) {
                if (is_string($photo) && !empty($photo)) {
                    $photos[] = str_starts_with($photo, 'http') ? $photo : asset('storage/' . ltrim($photo, '/'));
                }
            }
        }

        return array_values(array_unique($photos));
    }

    private function cleanupFilesForItem($item, $section)
    {
        if (!empty($item->photos_paths) && is_array($item->photos_paths)) {
            foreach ($item->photos_paths as $path) {
                if (is_string($path) && !str_starts_with($path, 'data:')) {
                    $this->deleteFileFromStorage($path);
                }
            }
        }
        
        if (!empty($item->repair_photos) && is_array($item->repair_photos)) {
            foreach ($item->repair_photos as $path) {
                if (is_string($path)) {
                    $this->deleteFileFromStorage($path);
                }
            }
        }
        
        if (!empty($item->picture_paths) && is_array($item->picture_paths)) {
            foreach ($item->picture_paths as $path) {
                if (is_string($path)) {
                    $this->deleteFileFromStorage($path);
                }
            }
        }
        
        if (!empty($item->employee_picture)) {
            $picPath = public_path('employee-profile/' . $item->employee_picture);
            if (is_file($picPath)) @unlink($picPath);
        }
        
        $appData = $item->application_data ?? [];
        if (is_array($appData)) {
            foreach (['policy_signature_path', 'applicant_signature_path'] as $sigKey) {
                if (!empty($appData[$sigKey])) {
                    Storage::disk('public')->delete($appData[$sigKey]);
                    $publicFile = public_path('employee-signature/' . basename($appData[$sigKey]));
                    if (is_file($publicFile)) @unlink($publicFile);
                }
            }
        }
        
        if (!empty($item->image) && $section === 'policies') {
            Storage::disk('public')->delete($item->image);
        }
        
        if (!empty($item->photos) && $section === 'repair') {
            $photos = is_array($item->photos) ? $item->photos : (json_decode($item->photos, true) ?: []);
            foreach ($photos as $photo) {
                if (is_string($photo)) {
                    Storage::disk('public')->delete($photo);
                }
            }
        }
    }
    
    private function deleteFileFromStorage(string $path): void
    {
        $normalized = ltrim(trim($path), '/');
        if ($normalized === '' || str_contains($normalized, '..')) return;
        
        if (Storage::disk('public')->exists($normalized)) {
            Storage::disk('public')->delete($normalized);
            return;
        }
        
        $publicPath = public_path($normalized);
        if (is_file($publicPath)) {
            @unlink($publicPath);
        }
    }
    
    // --- eBay Products (filesystem-based) ---
    
    private function getEbayTrashPath(): string
    {
        return public_path('ebay-product-trash');
    }
    
    private function getEbayTrashCount(): int
    {
        $trashPath = $this->getEbayTrashPath();
        if (!File::isDirectory($trashPath)) return 0;
        return count(File::directories($trashPath));
    }
    
    private function getEbayTrashedItems(string $search = ''): array
    {
        $trashPath = $this->getEbayTrashPath();
        if (!File::isDirectory($trashPath)) return [];
        
        $items = [];
        foreach (File::directories($trashPath) as $dir) {
            $folder = basename($dir);
            $metadataPath = $dir . DIRECTORY_SEPARATOR . 'metadata.json';
            $trashedAtPath = $dir . DIRECTORY_SEPARATOR . '.trashed_at';
            
            $metadata = File::isFile($metadataPath) ? (json_decode(File::get($metadataPath), true) ?: []) : [];
            $trashedAt = File::isFile($trashedAtPath) ? trim(File::get($trashedAtPath)) : now()->toDateTimeString();
            
            $title = $metadata['title'] ?? $folder;
            
            if ($search && stripos($title, $search) === false && stripos($folder, $search) === false) {
                continue;
            }
            
            $imageUrl = null;
            $files = File::files($dir);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imageUrl = asset('ebay-product-trash/' . $folder . '/' . $file->getFilename());
                    break;
                }
            }
            
            $items[] = [
                'id' => $folder,
                'section' => 'ebay-products',
                'section_label' => 'eBay Products',
                'title' => $title,
                'subtitle' => 'Folder: ' . $folder,
                'image_url' => $imageUrl,
                'deleted_at' => date('M d, Y h:i A', strtotime($trashedAt)),
                'deleted_at_diff' => \Carbon\Carbon::parse($trashedAt)->diffForHumans(),
            ];
        }
        
        return $items;
    }
    
    private function restoreEbayProduct($folder)
    {
        $trashPath = $this->getEbayTrashPath() . DIRECTORY_SEPARATOR . basename($folder);
        $originalPath = public_path('ebay-product') . DIRECTORY_SEPARATOR . basename($folder);
        
        if (!File::isDirectory($trashPath)) {
            return response()->json(['success' => false, 'message' => 'Trashed folder not found'], 404);
        }
        
        $metadataPath = $trashPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $title = $folder;
        if (File::isFile($metadataPath)) {
            $meta = json_decode(File::get($metadataPath), true);
            if (!empty($meta['title'])) $title = $meta['title'];
        }

        // Remove .trashed_at file
        $trashedAtFile = $trashPath . DIRECTORY_SEPARATOR . '.trashed_at';
        if (File::isFile($trashedAtFile)) File::delete($trashedAtFile);
        
        File::moveDirectory($trashPath, $originalPath);
        
        return response()->json([
            'success' => true, 
            'message' => "eBay product '{$title}' has been successfully restored to eBay Products!",
            'restored_title' => $title,
            'section_label' => 'eBay Products'
        ]);
    }
    
    private function forceDeleteEbayProduct($folder)
    {
        $trashPath = $this->getEbayTrashPath() . DIRECTORY_SEPARATOR . basename($folder);
        
        if (!File::isDirectory($trashPath)) {
            return response()->json(['success' => false, 'message' => 'Trashed folder not found'], 404);
        }
        
        File::deleteDirectory($trashPath);
        
        return response()->json(['success' => true, 'message' => 'eBay product permanently deleted']);
    }
    
    private function emptyEbayTrash()
    {
        $trashPath = $this->getEbayTrashPath();
        if (!File::isDirectory($trashPath)) {
            return response()->json(['success' => true, 'message' => 'eBay trash is already empty']);
        }
        
        $count = count(File::directories($trashPath));
        File::deleteDirectory($trashPath);
        
        return response()->json(['success' => true, 'message' => "{$count} eBay product(s) permanently deleted"]);
    }
}
