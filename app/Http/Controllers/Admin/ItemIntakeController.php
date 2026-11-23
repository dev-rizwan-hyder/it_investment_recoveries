<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\EcommerceItem;
use App\Models\RefurbishingItem;
use App\Models\UniversalWasteItem;
use App\Models\DataDestructionItem;
use App\Models\ItAssetsItem;
use App\Models\RepairEntry;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use App\Store\Models\Category;
use App\Mail\RepairDetailsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ItemIntakeController extends Controller
{
    public function index()
    {
        $hasPallets = DB::getSchemaBuilder()->hasTable('pallets');
        $hasCategories = DB::getSchemaBuilder()->hasTable('categories');
        $hasEmployees = DB::getSchemaBuilder()->hasTable('employees');
        $hasUsers = DB::getSchemaBuilder()->hasTable('users');
        $hasClients = DB::getSchemaBuilder()->hasTable('clients');

        $pallets_list = $hasPallets ? Pallet::latest()->get() : collect();
        $categories = $hasCategories ? Category::whereNull('parent_id')->orderBy('name')->get() : collect();
        
        $employees = collect();
        if ($hasEmployees) {
            $employees = Employee::where('is_resigned', 0)->orderBy('name')->get();
        }
        if ($employees->isEmpty() && $hasUsers) {
            $employees = User::orderBy('name')->get();
        }

        $clients = $hasClients ? Client::orderBy('name')->get() : collect();
        $marketingRefs = ['No reference', 'Facebook', 'Google', 'Referral', 'Trade Show'];

        return view('admin.intake.item-intake', [
            'pallets_list' => $pallets_list,
            'categories' => $categories,
            'employees' => $employees,
            'clients' => $clients,
            'marketingRefs' => $marketingRefs,
        ]);
    }

    /**
     * Generate barcode
     */
    public function generateBarcode(Request $request)
    {
        // Accept both 'type' (prefix: EC, RF, UW) and 'intake_type' (full name)
        $type = $request->input('type');
        $intakeType = $request->input('intake_type');

        // Determine prefix
        if ($type) {
            // If type is provided (prefix), use it directly
            $prefix = strtoupper($type);
        } else {
            // If intake_type is provided (full name), convert to prefix
            $prefix = match ($intakeType) {
                'Refurbishing Intake', 'Refurbishing', 'eBay Product Intake', 'eBay Product', 'Refurbished Intake', 'Refurbished/ECommerce intake', 'Refurbished/ECommerce Intake', 'Refurb/E-commerce Intake', 'Refurb/E-Commerce Intake' => 'EB',
                'Universal Waste Intake' => 'UW',
                'Data Destruction Intake' => 'DD',
                'IT Assets Intake', 'IT Assets' => 'IT',
                'Repair Intake', 'Repair' => 'PAL',
                default => 'EB',
            };
        }

        if ($prefix === 'PAL') {
            $barcode = 'PAL-' . mt_rand(1000000000, 9999999999);
        } else {
            $barcode = $prefix . str_pad(mt_rand(1, 999999999), 10, '0', STR_PAD_LEFT);
        }

        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 40);
            $barcodeBase64 = base64_encode($barcodeImage);

            return response()->json([
                'success' => true,
                'barcode' => $barcode,
                'barcode_image' => '<img src="data:image/png;base64,' . $barcodeBase64 . '" alt="' . $barcode . '" style="max-width: 300px; height: auto;">',
                'barcodeBase64' => $barcodeBase64,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate barcode: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate a new 6-digit numeric job number (AJAX)
     */
    public function getJobNumber()
    {
        try {
            do {
                $jobNo = (string) mt_rand(100000, 999999);
            } while (Pallet::where('job_no', $jobNo)->exists());

            return response()->json([
                'success' => true,
                'job_no' => $jobNo
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new client (AJAX)
     */
    public function createClient(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:2',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'company' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            $client = Client::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'client' => $client
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Generate barcode from existing pallet
     */
    public function generateExistingBarcode(Request $request)
    {
        $barcode = $request->input('barcode') ?? $request->input('pallet_id');

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode or pallet_id is required',
            ], 400);
        }

        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128);
            $barcodeBase64 = base64_encode($barcodeImage);

            return response()->json([
                'success' => true,
                'barcode' => $barcode,
                'barcode_image' => '<img src="data:image/png;base64,' . $barcodeBase64 . '" alt="' . $barcode . '" style="max-width: 300px; height: auto;">',
                'barcodeBase64' => $barcodeBase64,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate barcode: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get all categories for dropdown
     */
    public function getCategories()
    {
        try {
            $categories = Category::whereNull('parent_id')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get sub-categories by parent category ID
     */
    public function getSubCategories(Request $request)
    {
        try {
            $parentId = $request->input('parent_id');
            
            if (!$parentId) {
                return response()->json([
                    'success' => true,
                    'categories' => [],
                ]);
            }

            if (!is_numeric($parentId)) {
                $parent = Category::where('name', $parentId)->first();
                $parentId = $parent ? $parent->id : null;
            }

            if (!$parentId) {
                return response()->json([
                    'success' => true,
                    'categories' => [],
                ]);
            }
            
            $subCategories = Category::where('parent_id', $parentId)->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'categories' => $subCategories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sub-categories: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Save repair intake entry (supports multiple items, pallet, technician section, draft status & email)
     */
    protected function saveRepairIntake(Request $request)
    {
        \Log::info('ItemIntake: Saving Repair Intake', $request->all());

        if ($request->has('repair_items') && is_string($request->repair_items)) {
            $decoded = json_decode($request->repair_items, true);
            if (is_array($decoded)) {
                $request->merge(['repair_items' => $decoded]);
            }
        }

        $validationRules = [
            'barcode' => 'required|string',
            'client_id' => 'required|integer|exists:clients,id',
            'intake_date' => 'nullable|date',
            'job_no' => 'nullable|string|max:100',
            'put_away_location' => 'required|string|max:255',
            'marketing_reference' => 'nullable|string|max:255',
            'receiving_employee' => 'nullable|string|max:255',
            'receiving_notes' => 'nullable|string',
            'technician' => 'nullable|string|max:255',
            'technician_notes' => 'nullable|string',
            'repair_status' => 'nullable|string|max:100',
            'is_draft' => 'nullable',
            'send_email' => 'nullable',
            'repair_items' => 'nullable|array',
            'repair_items.*.name' => 'nullable|string|max:255',
            'repair_items.*.brand' => 'nullable|string|max:255',
            'repair_items.*.model' => 'nullable|string|max:255',
            'repair_items.*.serial' => 'nullable|string|max:255',
            'photos.*' => 'nullable|file|max:20480',
        ];

        try {
            $validated = $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('ItemIntake Repair: Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            $storedPhotoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType() ?: 'application/octet-stream';
                    $base64Data = base64_encode(file_get_contents($photo->getRealPath()));
                    $storedPhotoPaths[] = 'data:' . $mimeType . ';base64,' . $base64Data;
                }
            }

            $client = Client::findOrFail($validated['client_id']);
            $palletBarcode = $validated['barcode'];
            $isDraft = $request->boolean('is_draft') || strcasecmp((string)$request->input('status'), 'draft') === 0;
            $intakeDate = !empty($validated['intake_date'])
                ? \Carbon\Carbon::parse($validated['intake_date'])->setTimeFrom(now())
                : now();

            $pallet = Pallet::firstOrNew(['barcode_number' => $palletBarcode]);
            $pallet->client_id = $client->id;
            if (\Illuminate\Support\Facades\Schema::hasColumn('pallets', 'date')) {
                $pallet->date = $intakeDate;
            } else {
                $pallet->created_at = $intakeDate;
            }
            $pallet->description = $pallet->description ?? 'Repair Intake Pallet';
            $pallet->put_away_location = $validated['put_away_location'];
            $pallet->marketing_reference = $validated['marketing_reference'] ?? 'No reference';
            $pallet->receiving_employee = $validated['receiving_employee'] ?? null;
            $pallet->job_no = $validated['job_no'] ?? null;
            $pallet->notes = $validated['receiving_notes'] ?? null;
            $pallet->status = $isDraft ? 'Draft' : 'Received';
            $pallet->gross_weight = $pallet->gross_weight ?? 0;
            $pallet->tare_weight = $pallet->tare_weight ?? 0;
            $itemsInput = $request->input('repair_items', []);
            if (empty($itemsInput)) {
                $itemsInput = [[
                    'name' => $request->input('item_name') ?: 'Repair Item',
                    'brand' => $request->input('brand') ?: '',
                    'model' => $request->input('model') ?: '',
                    'serial' => $request->input('serial_number') ?: '',
                ]];
            }

            $pallet->net_weight = $pallet->net_weight ?? 0;
            $pallet->estimated_count = count($itemsInput);
            if (!empty($storedPhotoPaths)) {
                $pallet->photos_paths = $storedPhotoPaths;
            }
            $pallet->save();

            $status = $isDraft ? RepairEntry::STATUS_DRAFT : ($validated['repair_status'] ?? RepairEntry::STATUS_RECEIVING);
            $technician = !empty($validated['technician']) ? trim($validated['technician']) : null;
            $receivingNotes = $validated['receiving_notes'] ?? '';
            $techNotes = $validated['technician_notes'] ?? '';
            $combinedNotes = trim(($receivingNotes ? "Receiving Notes: $receivingNotes\n" : "") . ($techNotes ? "Tech Notes: $techNotes" : ""));

            // Prepare structured item details with individual barcodes
            $structuredItems = [];
            $names = [];
            $brands = [];
            $serials = [];
            $descriptions = [];

            foreach ($itemsInput as $idx => $item) {
                $devName = trim($item['name'] ?? 'Repair Item') ?: 'Repair Item';
                $brand = trim($item['brand'] ?? '');
                $model = trim($item['model'] ?? '');
                $serial = trim($item['serial'] ?? '');
                $barcode = 'REP-' . mt_rand(1000000000, 9999999999);
                $desc = trim($devName . ($brand ? ' - ' . $brand : '') . ($model ? ' ' . $model : ''));

                $structuredItems[] = [
                    'name' => $devName,
                    'brand' => $brand,
                    'model' => $model,
                    'serial' => $serial,
                    'barcode' => $barcode,
                ];

                $names[] = $devName;
                if ($brand) $brands[] = $brand;
                if ($serial) $serials[] = $serial;
                if ($desc) $descriptions[] = $desc;
            }

            $primaryDeviceType = implode(', ', $names);
            $primaryBrand = implode(', ', array_unique($brands));
            $primarySerial = implode(', ', $serials);
            $primaryDescription = implode('; ', $descriptions);
            $primaryBarcode = $structuredItems[0]['barcode'];
            $itemsCount = count($structuredItems);

            $repair = RepairEntry::create([
                'user_id' => $client->id,
                'customer_name' => $client->name,
                'customer_email' => $client->email,
                'customer_phone' => $client->phone,
                'device_type' => $primaryDeviceType ?: 'Repair Item',
                'brand_category' => $primaryBrand ?: null,
                'item_description' => $primaryDescription ?: ($primaryDeviceType ?: 'Repair Item'),
                'serial_number' => $primarySerial ?: null,
                'technician' => $technician,
                'status' => $status,
                'repair_status' => $status,
                'pallet_info' => $palletBarcode,
                'lot_info' => trim("Job: " . ($validated['job_no'] ?? 'N/A') . " | Ref: " . ($validated['marketing_reference'] ?? 'N/A')),
                'put_away_location' => $validated['put_away_location'],
                'notes' => $combinedNotes ?: null,
                'photos' => !empty($storedPhotoPaths) ? json_encode($storedPhotoPaths) : null,
                'quantity' => $itemsCount,
                'price' => 0,
                'item_barcode' => $primaryBarcode,
                'device_specs' => json_encode($structuredItems),
            ]);

            $createdRepairs = collect([$repair]);

            $emailSent = false;
            $sendEmail = $request->boolean('send_email');
            $recipientEmail = !empty($client->email) ? $client->email : $request->input('customer_email');
            if ($sendEmail && !empty($recipientEmail)) {
                try {
                    Mail::to($recipientEmail)->send(new RepairDetailsNotification($createdRepairs, $pallet));
                    $emailSent = true;
                    Log::info('ItemIntake Repair: Sent repair details email to ' . $recipientEmail);
                } catch (\Throwable $e) {
                    Log::warning('Failed to send repair intake email', ['error' => $e->getMessage()]);
                }
            }

            $barcodeBase64 = '';
            try {
                $generator = new BarcodeGeneratorPNG();
                $barcodeBase64 = base64_encode(
                    $generator->getBarcode($palletBarcode, $generator::TYPE_CODE_128, 2, 40)
                );
            } catch (\Exception $e) {}

            $msg = $isDraft 
                ? 'Repair entry saved as draft successfully!' . ($emailSent ? ' Details email sent to ' . $recipientEmail . '.' : '')
                : $createdRepairs->count() . ' Repair Item(s) Saved Successfully!' . ($emailSent ? ' Confirmation email sent to ' . $recipientEmail . '.' : '');

            session()->flash('success', $msg);

            return response()->json([
                'success' => true,
                'message' => $msg,
                'barcode' => $palletBarcode,
                'barcodeBase64' => $barcodeBase64,
                'redirect_url' => route('admin.repair-intake.index'),
                'items' => $createdRepairs,
                'is_repair' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('ItemIntake Repair: Save failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save repair entry: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save item
     */
    public function saveItem(Request $request)
    {
        // Log incoming request data
        \Log::info('ItemIntake: Received request', [
            'intake_type' => $request->intake_type,
            'barcode' => $request->barcode,
            'category' => $request->category,
            'brand' => $request->brand,
            'model' => $request->model,
        ]);

        if ($request->input('intake_type') === 'Repair Intake' || $request->input('intake_type') === 'Repair') {
            return $this->saveRepairIntake($request);
        }

        // Determine model based on intake type
        $targetModel = match ($request->intake_type) {
            'Refurbishing Intake' => RefurbishingItem::class,
            'Universal Waste Intake' => UniversalWasteItem::class,
            'Data Destruction Intake' => DataDestructionItem::class,
            'IT Assets Intake', 'IT Assets' => ItAssetsItem::class,
            'eBay Product Intake', 'eBay Product' => EcommerceItem::class,
            default => EcommerceItem::class,
        };

        // Validation
        $validationRules = [
            'barcode' => 'required|string',
            'selected_pallet_id' => 'nullable|integer|exists:pallets,id',
            'intake_type' => 'required|string',
            'category' => 'required|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'sub_sub_category' => 'nullable|string|max:255',
            'condition' => 'required|string|max:255',
            'folder_type' => 'nullable|string|max:255',
            'estimated_value' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'receiving_employee' => 'nullable|string|max:255',
            'processing_employee' => 'nullable|string|max:255',
            'intake_employee' => 'nullable|string|max:255',
            'ecommerce_employee' => 'nullable|string|max:255',
            'refurbishing_employee' => 'nullable|string|max:255',
            'universal_waste_employee' => 'nullable|string|max:255',
            'data_destruction_employee' => 'nullable|string|max:255',
            'put_away_location' => 'nullable|string|max:255',
            'ebay_person' => 'nullable|string|max:255',
            'ebay_link' => 'nullable|string|max:2048',
            'website_link' => 'nullable|string|max:2048',
            'social_media_1' => 'nullable|string|max:2048',
            'notes' => 'nullable|string',
            'photos.*' => 'nullable|file|max:20480',
            'is_draft' => 'nullable',
            'status' => 'nullable|string|max:100',
        ];

        $validationRules['item_name'] = 'nullable|string';
        
        // Brand, model, and category validation based on intake type
        if (in_array($request->intake_type, ['eBay Product Intake', 'eBay Product'])) {
            $validationRules['brand'] = 'nullable|string';
            $validationRules['model'] = 'nullable|string';
            $validationRules['category'] = 'nullable|string|max:255';
        } else if (in_array($request->intake_type, ['Data Destruction Intake', 'IT Assets Intake', 'IT Assets'])) {
            $validationRules['brand'] = 'nullable|string';
            $validationRules['model'] = 'nullable|string';
        } else {
            $validationRules['brand'] = 'required|string';
            $validationRules['model'] = 'required|string';
        }
        
        $validationRules['serial_number'] = 'nullable|string';

        try {
            $validated = $request->validate($validationRules);
            if (in_array($request->intake_type, ['eBay Product Intake', 'eBay Product']) && empty($validated['category'])) {
                $validated['category'] = 'eBay Product';
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('ItemIntake: Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'errors' => $e->errors(),
            ], 422);
        }

        \Log::info('ItemIntake: Validation passed', $validated);

        try {
            $storedPhotoPaths = [];
            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');
                \Log::info('ItemIntake: Received ' . count($photos) . ' photos');
                
                // Determine folder based on intake type (store directly under public/)
                $folderMap = [
                    'E-commerce Intake' => 'ecommerce',
                    'Refurbishing Intake' => 'refurbishing',
                    'Universal Waste Intake' => 'universal-waste',
                    'Data Destruction Intake' => 'data_destruction_files',
                    'IT Assets Intake' => 'it_assets_files',
                ];
                $folder = $folderMap[$request->intake_type] ?? 'ecommerce';
                
                foreach ($photos as $photo) {
                    if (in_array($request->intake_type, ['Data Destruction Intake', 'IT Assets Intake', 'Universal Waste Intake', 'Universal Waste'], true)) {
                        $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType();
                        $base64Data = base64_encode(file_get_contents($photo->getRealPath()));
                        $dataUri = 'data:' . $mimeType . ';base64,' . $base64Data;
                        $storedPhotoPaths[] = $dataUri;
                        \Log::info('ItemIntake: Stored DB base64 file for ' . $request->intake_type);
                    } else {
                        // Store directly in public/{folder}
                        $destinationPath = public_path($folder);
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0777, true);
                        }
                        $filename = uniqid() . '_' . $photo->getClientOriginalName();
                        $photo->move($destinationPath, $filename);
                        $relativePath = $folder . '/' . $filename;
                        $storedPhotoPaths[] = $relativePath;
                        \Log::info('ItemIntake: Stored photo: ' . $relativePath);
                    }
                }
            } else {
                \Log::info('ItemIntake: No photos in request');
            }
            \Log::info('ItemIntake: Total photos stored: ' . count($storedPhotoPaths));

            \Log::info('ItemIntake: Creating item with data:', [
                'category' => $request->category,
                'model' => $targetModel,
            ]);

            $rawQuantity = $request->input('quantity');
            $enteredQuantity = ($rawQuantity === null || $rawQuantity === '') ? 1 : max(0, (int) $rawQuantity);
            $enteredWeight = max(0, (float) ($request->weight ?? 0));
            $selectedPalletId = $request->input('selected_pallet_id');

            DB::transaction(function () use (
                $request,
                $targetModel,
                $storedPhotoPaths,
                $enteredQuantity,
                $enteredWeight,
                $selectedPalletId
            ) {
                $selectedPalletNumber = null;

                if ($selectedPalletId) {
                    $pallet = Pallet::lockForUpdate()->find($selectedPalletId);

                    if ($pallet) {
                        $availability = $this->palletAvailability($pallet);
                        $availableQuantity = $availability['quantity'];
                        $availableWeight = $availability['weight'];
                        $selectedPalletNumber = $pallet->barcode_number;

                        if ($enteredQuantity > $availableQuantity) {
                            throw new \RuntimeException('Entered quantity exceeds available processing quantity for this pallet.');
                        }

                        if ($enteredWeight > $availableWeight + 0.0001) {
                            throw new \RuntimeException('Entered weight exceeds available processing weight for this pallet.');
                        }

                        $pallet->net_weight = max(0, $availableWeight - $enteredWeight);
                        if ($request->filled('receiving_employee')) {
                            $pallet->receiving_employee = $request->receiving_employee;
                        }
                        if ($request->filled('processing_employee')) {
                            $pallet->processing_employee = $request->processing_employee;
                        }
                        if ($request->filled('intake_employee')) {
                            $pallet->intake_employee = $request->intake_employee;
                        }
                        if ($request->filled('ecommerce_employee')) {
                            $pallet->ecommerce_employee = $request->ecommerce_employee;
                        }
                        if ($request->filled('refurbishing_employee')) {
                            $pallet->refurbishing_employee = $request->refurbishing_employee;
                        }
                        if ($request->filled('universal_waste_employee')) {
                            $pallet->universal_waste_employee = $request->universal_waste_employee;
                        }
                        if ($request->filled('data_destruction_employee')) {
                            $pallet->data_destruction_employee = $request->data_destruction_employee;
                        }
                        $pallet->save();
                    }
                }

                $intakeEmployeeVal = $request->intake_employee
                    ?? $request->ecommerce_employee
                    ?? $request->refurbishing_employee
                    ?? $request->universal_waste_employee
                    ?? $request->data_destruction_employee
                    ?? null;

                $nameVal = trim((string) ($request->item_name ?? ''));
                if ($nameVal === '') {
                    $nameVal = in_array($request->intake_type, ['Data Destruction Intake', 'Data Destruction'])
                        ? 'Data Destruction Item'
                        : (in_array($request->intake_type, ['IT Assets Intake', 'IT Assets']) ? 'IT Assets Item' : 'Unnamed Item');
                }

                $itemData = [
                    'intake_type' => $request->intake_type,
                    'barcode' => $request->barcode,
                    'name' => $nameVal,
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'serial_number' => $request->serial_number ?? null,
                    'category' => $request->category,
                    'sub_category' => $request->sub_category ?? null,
                    'sub_sub_category' => $request->sub_sub_category ?? null,
                    'category_id' => is_numeric($request->main_category) ? (int)$request->main_category : null,
                    'sub_category_id' => is_numeric($request->sub_category) ? (int)$request->sub_category : null,
                    'sub_sub_category_id' => is_numeric($request->sub_sub_category) ? (int)$request->sub_sub_category : null,
                    'condition' => $request->condition,
                    'estimated_value' => (float) ($request->estimated_value ?? 0),
                    'weight' => $enteredWeight,
                    'quantity' => $enteredQuantity,
                    'pallet_number' => $selectedPalletNumber,
                    'pallet_id' => $selectedPalletId,
                    'notes' => $request->notes ?? null,
                    'receiving_employee' => $request->receiving_employee ?? ($pallet->receiving_employee ?? null),
                    'processing_employee' => $request->processing_employee ?? ($pallet->processing_employee ?? null),
                    'intake_employee' => $intakeEmployeeVal,
                    'ecommerce_employee' => $request->ecommerce_employee ?? ($request->intake_type === 'E-commerce Intake' ? $intakeEmployeeVal : null),
                    'refurbishing_employee' => $request->refurbishing_employee ?? ($request->intake_type === 'Refurbishing Intake' ? $intakeEmployeeVal : null),
                    'universal_waste_employee' => $request->universal_waste_employee ?? ($request->intake_type === 'Universal Waste Intake' ? $intakeEmployeeVal : null),
                    'data_destruction_employee' => $request->data_destruction_employee ?? ($request->intake_type === 'Data Destruction Intake' ? $intakeEmployeeVal : null),
                    'it_assets_employee' => $request->it_assets_employee ?? (in_array($request->intake_type, ['IT Assets Intake', 'IT Assets']) ? $intakeEmployeeVal : null),
                    'photos_paths' => $storedPhotoPaths,
                    'status' => ($enteredQuantity <= 0) ? 'Completed' : 'Recieved',
                ];

                $isDraft = $request->boolean('is_draft') || strcasecmp((string) $request->input('status'), 'draft') === 0;
                $savedAsDraft = (bool) $isDraft;

                if (in_array($request->intake_type, ['Universal Waste Intake', 'Universal Waste'], true) || $targetModel === UniversalWasteItem::class) {
                    $itemData['status'] = $savedAsDraft ? 'Draft' : 'Completed';
                    $itemData['scrap_quantity'] = $enteredQuantity;
                    $itemData['scrap_weight'] = $enteredWeight;
                    $itemData['reuse_quantity'] = 0;
                    $itemData['reuse_weight'] = 0;
                } elseif ($savedAsDraft) {
                    $itemData['status'] = 'Draft';
                }

                $targetModel::create($itemData);

                if (!empty($selectedPalletNumber) && !$savedAsDraft) {
                    app(\App\Services\PalletCompletionService::class)->syncForPallet($selectedPalletNumber);
                }

                // If intake_type is eBay Product Intake, also create eBay product disk metadata so it appears in /admin/ebay-products
                if (in_array($request->intake_type, ['eBay Product Intake', 'eBay Product'])) {
                    try {
                        $folderName = trim($request->item_name ?: ($request->brand . ' ' . $request->model));
                        if (empty($folderName)) {
                            $folderName = 'eBay Item ' . $request->barcode;
                        }
                        $slug = \Illuminate\Support\Str::slug($folderName) ?: 'product';
                        $baseFolder = date('Ymd_His') . '_' . $slug;
                        $folder = $baseFolder;
                        $ebayBasePath = public_path('ebay-product');
                        \Illuminate\Support\Facades\File::ensureDirectoryExists($ebayBasePath, 0755);

                        $counter = 2;
                        while (\Illuminate\Support\Facades\File::exists($ebayBasePath . DIRECTORY_SEPARATOR . $folder)) {
                            $folder = $baseFolder . '_' . $counter;
                            $counter++;
                        }

                        $folderPath = $ebayBasePath . DIRECTORY_SEPARATOR . $folder;
                        \Illuminate\Support\Facades\File::makeDirectory($folderPath, 0755, true);

                        // Copy photos into ebay-product folder
                        $ebayImages = [];
                        foreach ($storedPhotoPaths as $pIndex => $pPath) {
                            $fullSource = public_path($pPath);
                            if (\Illuminate\Support\Facades\File::isFile($fullSource)) {
                                $ext = pathinfo($fullSource, PATHINFO_EXTENSION);
                                $destFilename = sprintf('%02d_%s.%s', $pIndex + 1, 'photo', $ext);
                                \Illuminate\Support\Facades\File::copy($fullSource, $folderPath . DIRECTORY_SEPARATOR . $destFilename);
                                $ebayImages[] = [
                                    'filename' => $destFilename,
                                    'original_name' => basename($fullSource),
                                    'size' => \Illuminate\Support\Facades\File::size($folderPath . DIRECTORY_SEPARATOR . $destFilename),
                                    'uploaded_at' => date('c'),
                                ];
                            }
                        }

                        $metadata = [
                            'folder' => $folder,
                            'folder_name' => $folderName,
                            'condition' => $request->condition ?? 'New',
                            'folder_type' => $request->folder_type ?? 'Ebay',
                            'category' => $request->category ?? '',
                            'sub_category' => $request->sub_category ?? '',
                            'sub_sub_category' => $request->sub_sub_category ?? '',
                            'location' => trim($request->put_away_location ?? $request->location ?? ''),
                            'quantity' => (int) $enteredQuantity,
                            'weight' => (float) $enteredWeight,
                            'value' => (float) ($request->estimated_value ?? 0),
                            'estimated_value' => (float) ($request->estimated_value ?? 0),
                            'serial_number' => trim($request->serial_number ?? ''),
                            'notes' => trim($request->notes ?? ''),
                            'marked' => 'remaining',
                            'user' => trim($request->ebay_person ?? $request->intake_employee ?? $request->receiving_employee ?? ''),
                            'person' => trim($request->ebay_person ?? ''),
                            'intake_employee' => trim($request->intake_employee ?? ''),
                            'ebay_link' => trim($request->ebay_link ?? ''),
                            'website_link' => trim($request->website_link ?? ''),
                            'social_media_1' => trim($request->social_media_1 ?? ''),
                            'social_media_2' => '',
                            'social_media_3' => '',
                            'images' => $ebayImages,
                            'created_at' => date('c'),
                            'updated_at' => date('c'),
                        ];

                        \Illuminate\Support\Facades\File::put(
                            $folderPath . DIRECTORY_SEPARATOR . 'metadata.json',
                            json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                        );
                    } catch (\Exception $e) {
                        \Log::error('ItemIntake: Failed to create eBay product disk metadata: ' . $e->getMessage());
                    }
                }
            });

            // Generate barcode image for success screen
            $barcodeBase64 = '';
            try {
                $generator = new BarcodeGeneratorPNG();
                $barcodeBase64 = base64_encode(
                    $generator->getBarcode($request->barcode, $generator::TYPE_CODE_128)
                );
            } catch (\Exception $e) {
                // Silent fail on barcode generation for display
            }

            $redirectUrl = match ($request->intake_type) {
                'Refurbishing Intake', 'Refurbishing' => route('admin.refurbishing-entries.index'),
                'Universal Waste Intake', 'Universal Waste' => route('admin.universal-waste-entries.index'),
                'Data Destruction Intake', 'Data Destruction' => route('admin.data-destruction-entries.index'),
                'IT Assets Intake', 'IT Assets' => route('admin.it-assets-entries.index'),
                'E-commerce Intake', 'Ecommerce Intake' => route('admin.ecommerce-entries.index'),
                'eBay Product Intake', 'eBay Product', 'Refurbished Intake', 'Refurbished/ECommerce intake', 'Refurbished/ECommerce Intake', 'Refurb/E-commerce Intake', 'Refurb/E-Commerce Intake' => route('admin.ebay-products.index'),
                default => route('admin.ebay-products.index'),
            };

            $isDraft = $request->boolean('is_draft') || strcasecmp((string) $request->input('status'), 'draft') === 0;
            $isDraftIntakeType = in_array($request->intake_type, [
                'Data Destruction Intake', 'Data Destruction',
                'IT Assets Intake', 'IT Assets',
                'Universal Waste Intake', 'Universal Waste'
            ], true);
            $savedAsDraft = ($isDraft && $isDraftIntakeType);

            $successMsg = $savedAsDraft ? 'Item saved as draft successfully!' : 'Item saved successfully!';
            session()->flash('message', $successMsg);

            return response()->json([
                'success' => true,
                'message' => $successMsg . ' ' . count($storedPhotoPaths) . ' photo(s) uploaded.',
                'status' => $savedAsDraft ? 'Draft' : null,
                'is_draft' => $savedAsDraft,
                'barcode' => $request->barcode,
                'barcodeBase64' => $barcodeBase64,
                'photosCount' => count($storedPhotoPaths),
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get pallets list
     */
    public function getPallets()
    {
        $pallets = Pallet::latest()->get(['id', 'barcode_number']);
        return response()->json([
            'success' => true,
            'pallets' => $pallets,
        ]);
    }

    /**
     * Get pallet details by ID
     */
    public function getPalletDetails($id)
    {
        $pallet = Pallet::findOrFail($id);
        $availability = $this->palletAvailability($pallet);

        $data = $pallet->toArray();
        $data['remaining_quantity'] = $availability['quantity'];
        $data['remaining_weight'] = $availability['weight'];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function palletAvailability(Pallet $pallet): array
    {
        $assigned = $this->assignedPalletTotals($pallet);
        $processingScrapQuantity = $this->processingScrapQuantity($pallet);
        $processingScrapWeight = $this->processingScrapWeight($pallet);

        return [
            'quantity' => max(0, $this->palletBaseQuantity($pallet) - $processingScrapQuantity - $assigned['quantity']),
            'weight' => max(0.0, $this->palletBaseWeight($pallet, $assigned['weight']) - $processingScrapWeight - $assigned['weight']),
        ];
    }

    private function assignedPalletTotals(Pallet $pallet): array
    {
        $palletId = $pallet->id;
        $barcode = trim((string) ($pallet->barcode_number ?? ''));

        if (!$palletId && $barcode === '') {
            return ['records' => 0, 'quantity' => 0, 'weight' => 0.0];
        }

        $totals = ['records' => 0, 'quantity' => 0, 'weight' => 0.0];

        foreach (['ecommerce_items', 'refurbishing_items', 'universal_waste_items', 'data_destruction_items', 'it_assets_items'] as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            // Query by pallet_id first (precise), fall back to pallet_number for legacy items
            $query = DB::table($table)
                ->selectRaw('COUNT(*) as records')
                ->selectRaw('COALESCE(SUM(quantity), 0) as quantity')
                ->selectRaw('COALESCE(SUM(weight), 0) as weight');

            if ($palletId && DB::getSchemaBuilder()->hasColumn($table, 'pallet_id')) {
                $query->where(function ($q) use ($palletId, $barcode) {
                    $q->where('pallet_id', $palletId);
                    // Also include legacy items (no pallet_id) that match by barcode
                    // but only if no other pallet has claimed them
                    if ($barcode !== '') {
                        $q->orWhere(function ($sq) use ($barcode) {
                            $sq->whereNull('pallet_id')
                               ->whereRaw('TRIM(pallet_number) = ?', [$barcode]);
                        });
                    }
                });
            } else {
                // No pallet_id column yet, fall back to barcode matching
                if ($barcode !== '') {
                    $query->whereRaw('TRIM(pallet_number) = ?', [$barcode]);
                } else {
                    continue;
                }
            }

            $row = $query->first();

            $totals['records'] += (int) ($row->records ?? 0);
            $totals['quantity'] += (int) ($row->quantity ?? 0);
            $totals['weight'] += (float) ($row->weight ?? 0);
        }

        return $totals;
    }

    private function palletBaseQuantity(Pallet $pallet): int
    {
        return max(0, (int) ($pallet->estimated_count ?? 0));
    }

    private function palletBaseWeight(Pallet $pallet, float $assignedWeight = 0.0): float
    {
        $grossWeight = max(0, (float) ($pallet->gross_weight ?? 0));
        $tareWeight = max(0, (float) ($pallet->tare_weight ?? 0));
        $baseWeight = max(0, $grossWeight - $tareWeight);

        if ($baseWeight > 0) {
            return $baseWeight;
        }

        return max(0, $this->remainingPalletWeight($pallet) + $this->processingScrapWeight($pallet) + max(0.0, $assignedWeight));
    }

    private function remainingPalletWeight(Pallet $pallet): float
    {
        $currentNetWeight = max(0, (float) ($pallet->net_weight ?? 0));
        $reuseWeight = $this->processingScrapWeight($pallet);
        $grossWeight = max(0, (float) ($pallet->gross_weight ?? 0));
        $tareWeight = max(0, (float) ($pallet->tare_weight ?? 0));
        $originalNetWeight = max(0, $grossWeight - $tareWeight);

        if ($reuseWeight > 0 && $originalNetWeight > 0 && abs($currentNetWeight - $originalNetWeight) < 0.01) {
            return max(0, $currentNetWeight - $reuseWeight);
        }

        return $currentNetWeight;
    }

    private function processingScrapQuantity(Pallet $pallet): int
    {
        return max(0, (int) ($pallet->reuse_quantity ?? 0));
    }

    private function processingScrapWeight(Pallet $pallet): float
    {
        return max(0.0, (float) ($pallet->reuse_weight ?? 0));
    }
}
