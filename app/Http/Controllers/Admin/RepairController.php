<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RepairDetailsNotification;
use App\Mail\RepairStatusUpdated;
use App\Models\RepairEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Picqer\Barcode\BarcodeGeneratorPNG;

class RepairController extends Controller
{
    /**
     * Display a listing of the repair entries.
     */
    public function index(Request $request)
    {
        $query = RepairEntry::query()
            ->with(['user', 'client', 'pallet']);

        if ($request->filled('status') && $request->status !== 'All Statuses') {
            $query->where('repair_entries.status', $request->status);
        }

        if ($request->filled('pallet') && $request->pallet !== 'All Pallets') {
            $query->where('repair_entries.pallet_info', $request->pallet);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('repair_entries.device_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.customer_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.customer_phone', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.customer_email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.serial_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.technician', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.item_description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.item_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.pallet_info', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.lot_info', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.brand_category', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.put_away_location', 'like', '%' . $searchTerm . '%')
                    ->orWhere('repair_entries.id', 'like', '%' . $searchTerm . '%');
            });
        }

        $entries = $query->latest('repair_entries.created_at')->paginate(15)->appends($request->all());
        $totalCount = RepairEntry::count();
        $pallets_list = \App\Models\Pallet::latest()->get();

        $stats = [
            ['label' => 'Draft', 'status' => RepairEntry::STATUS_DRAFT, 'value' => RepairEntry::where('status', RepairEntry::STATUS_DRAFT)->count(), 'color' => 'text-slate-500', 'bg' => 'bg-slate-500'],
            ['label' => 'Receiving', 'status' => RepairEntry::STATUS_RECEIVING, 'value' => RepairEntry::where('status', RepairEntry::STATUS_RECEIVING)->count(), 'color' => 'text-sky-600', 'bg' => 'bg-sky-500'],
            ['label' => 'Processing', 'status' => RepairEntry::STATUS_PROCESSING, 'value' => RepairEntry::where('status', RepairEntry::STATUS_PROCESSING)->count(), 'color' => 'text-amber-600', 'bg' => 'bg-amber-500'],
            ['label' => 'Ready for Pickup', 'status' => RepairEntry::STATUS_READY_FOR_PICKUP, 'value' => RepairEntry::where('status', RepairEntry::STATUS_READY_FOR_PICKUP)->count(), 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-600'],
            ['label' => 'Ready for Shipment', 'status' => RepairEntry::STATUS_READY_FOR_SHIPMENT, 'value' => RepairEntry::where('status', RepairEntry::STATUS_READY_FOR_SHIPMENT)->count(), 'color' => 'text-blue-500', 'bg' => 'bg-blue-500'],
            ['label' => 'Shipped', 'status' => RepairEntry::STATUS_SHIPPED, 'value' => RepairEntry::where('status', RepairEntry::STATUS_SHIPPED)->count(), 'color' => 'text-purple-500', 'bg' => 'bg-purple-500'],
            ['label' => 'Completed', 'status' => RepairEntry::STATUS_COMPLETED, 'value' => RepairEntry::where('status', RepairEntry::STATUS_COMPLETED)->count(), 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-600'],
        ];

        return view('admin.repairs.index', [
            'entries' => $entries,
            'totalCount' => $totalCount,
            'currentStatus' => $request->status ?? 'All Statuses',
            'currentPallet' => $request->pallet ?? '',
            'pallets_list' => $pallets_list,
            'stats' => $stats,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = RepairEntry::query();

        // Filters applied on export too
        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(fn($q) => $q->where('device_type', 'like', "%$searchTerm%")->orWhere('serial_number', 'like', "%$searchTerm%"));
        }

        $entries = $query->get();
        $fileName = 'Repair_Inventory_Report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Customer', 'Email', 'Phone', 'Technician', 'Device Type', 'Category', 'Quantity', 'Price', 'Payment Method', 'Payment Status', 'Serial Number', 'Status', 'Received At'];

        $callback = function () use ($entries, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($entries as $entry) {
                fputcsv($file, [
                    $entry->id,
                    $entry->customer_name,
                    $entry->customer_email,
                    $entry->customer_phone,
                    $entry->technician,
                    $entry->device_type,
                    $entry->category ?? 'N/A',
                    $entry->quantity,
                    $entry->price,
                    $entry->payment_method_label,
                    $entry->payment_status_label,
                    $entry->serial_number,
                    $entry->status,
                    $entry->created_at->format('M d, Y H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store a newly created repair entry.
     */
    public function store(Request $request)
    {
        if ($request->has('items')) {
            $validated = $request->validate([
                'user_id' => 'nullable|integer|exists:users,id',
                'technician' => 'required|string|max:255',
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'items' => 'required|array|min:1',
                'items.*.device_type' => 'required|string|max:255',
                'items.*.category' => 'nullable|string|max:255',
                'items.*.sub_category' => 'nullable|string|max:255',
                'items.*.sub_sub_category' => 'nullable|string|max:255',
                'items.*.category_id' => 'nullable|integer|exists:categories,id',
                'items.*.sub_category_id' => 'nullable|integer|exists:categories,id',
                'items.*.sub_sub_category_id' => 'nullable|integer|exists:categories,id',
                'items.*.brand_category' => 'nullable|string|max:255',
                'items.*.item_description' => 'nullable|string|max:255',
                'items.*.item_barcode' => 'nullable|string|max:100',
                'items.*.price' => 'nullable|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.status' => ['required', Rule::in(RepairEntry::statusValues())],
                'items.*.operating_system' => 'nullable|string|max:255',
                'items.*.put_away_location' => 'nullable|string|max:255',
                'items.*.device_specs' => 'nullable|string',
                'items.*.notes' => 'nullable|string',
                'items.*.photos' => 'nullable|array',
                'items.*.photos.*' => 'nullable|file|max:20480',
            ]);

            $sharedData = [
                'user_id' => $validated['user_id'] ?? null,
                'technician' => $validated['technician'],
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
            ];

            $createdRepairs = DB::transaction(function () use ($validated, $sharedData, $request) {
                $createdRepairs = collect();

                foreach ($validated['items'] as $index => $itemData) {
                    unset($itemData['photos']);

                    $data = array_merge($sharedData, $itemData);
                    $data['price'] = $data['price'] ?? 0;
                    $data['item_description'] = $data['item_description'] ?? $data['device_type'];

                    $uploadedPhotos = $this->saveRepairEntryPhotos($request->file("items.$index.photos", []));
                    if (!empty($uploadedPhotos)) {
                        $data['photos'] = json_encode($uploadedPhotos);
                    }

                    $createdRepairs->push(RepairEntry::create($data));
                }

                return $createdRepairs;
            });

            $this->sendRepairDetailsEmail($createdRepairs);

            $createdCount = $createdRepairs->count();
            $message = $createdCount > 1
                ? $createdCount . ' Repair Entries Added Successfully!'
                : 'New Repair Entry Added Successfully!';

            $targetRoute = \Illuminate\Support\Facades\Route::has('admin.repair-intake.index') ? 'admin.repair-intake.index' : 'admin.sales.invoices';
            return redirect()->route($targetRoute)->with('success', $message);
        }

        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'technician' => 'required|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'device_type' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'sub_sub_category' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:categories,id',
            'sub_sub_category_id' => 'nullable|integer|exists:categories,id',
            'brand_category' => 'nullable|string|max:255',
            'item_description' => 'nullable|string|max:255',
            'item_barcode' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status' => ['required', Rule::in(RepairEntry::statusValues())],
            'operating_system' => 'nullable|string|max:255',
            'put_away_location' => 'nullable|string|max:255',
            'device_specs' => 'nullable|string',
            'notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|max:20480',
        ]);

        $data = $validated;
        $data['price'] = $data['price'] ?? 0;
        $data['item_description'] = $data['item_description'] ?? $data['device_type'];
        $uploadedPhotos = $this->saveRepairEntryPhotos($request->file('photos', []));

        if (!empty($uploadedPhotos)) {
            $data['photos'] = json_encode($uploadedPhotos);
        }

        $repair = RepairEntry::create($data);
        $this->sendRepairDetailsEmail($repair);

        $targetRoute = \Illuminate\Support\Facades\Route::has('admin.repair-intake.index') ? 'admin.repair-intake.index' : 'admin.sales.invoices';
        return redirect()->route($targetRoute)->with('success', 'New Repair Entry Added Successfully!');
    }

    /**
     * Generate a new barcode for repair entries (AJAX).
     */
    public function generateBarcode()
    {
        try {
            do {
                $number = 'REP-' . mt_rand(1000000000, 9999999999);
            } while (RepairEntry::where('item_barcode', $number)->exists());

            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($number, $generator::TYPE_CODE_128, 2, 30);
            $base64 = base64_encode($barcodeData);

            return response()->json([
                'success' => true,
                'number' => $number,
                'barcode' => $number,
                'base64' => 'data:image/png;base64,' . $base64,
                'barcodeBase64' => $base64,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to generate barcode: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update repair entry status from the table.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(RepairEntry::statusValues())],
        ]);

        $repair = RepairEntry::with(['client', 'user'])->findOrFail($id);
        $previousStatus = $repair->status;
        $repair->update([
            'status' => $validated['status'],
            'repair_status' => $validated['status'],
        ]);
        $emailResult = $this->sendRepairStatusUpdateEmail($repair->fresh(['client', 'user', 'pallet']), $previousStatus);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $repair->status,
                'email_sent' => $emailResult['sent'],
                'email_message' => $emailResult['message'],
                'message' => $emailResult['sent']
                    ? 'Status updated and email sent to customer.'
                    : ($emailResult['message'] ?: 'Status updated successfully.'),
            ]);
        }

        $redirect = redirect()->back()->with('success', 'Repair status updated successfully!' . ($emailResult['sent'] ? ' ' . $emailResult['message'] : ''));

        if (!$emailResult['sent'] && $emailResult['message']) {
            $redirect->with('warning', $emailResult['message']);
        }

        return $redirect;
    }

    /**
     * Update repair entry quantity from the table.
     */
    public function updateQuantity(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $repair = RepairEntry::findOrFail($id);
        $repair->update(['quantity' => $validated['quantity']]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'quantity' => $repair->quantity,
                'message' => 'Quantity updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Repair quantity updated successfully!');
    }

    /**
     * Display the specified repair entry.
     */
    public function show($id)
    {
        $entry = RepairEntry::with(['user', 'client', 'pallet'])->findOrFail($id);
        $labelBarcodeDataUri = $this->barcodeDataUri($entry->item_barcode);
        $palletBarcodeDataUri = $entry->pallet_info ? $this->barcodeDataUri($entry->pallet_info) : null;

        return view('admin.repairs.view', compact('entry', 'labelBarcodeDataUri', 'palletBarcodeDataUri'));
    }

    /**
     * Print a professional repair invoice from the invoice listing.
     */
    public function printInvoice($id)
    {
        $entry = RepairEntry::with('user')->findOrFail($id);

        if ($entry->sales_invoice) {
            $url = route('admin.sales.invoices.print', $entry->sales_invoice);
            if (request()->has('autoprint')) {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'autoprint=' . request('autoprint');
            }
            return redirect($url);
        }

        return response()
            ->view('admin.repairs.invoice-print', compact('entry'))
            ->header('Cache-Control', 'private, max-age=60');
    }

    /**
     * Show the form for editing the specified repair entry.
     */
    public function edit($id)
    {
        $entry = RepairEntry::with(['user', 'client', 'pallet'])->findOrFail($id);
        $clients = \App\Models\Client::latest()->get();
        $employees = \App\Models\Employee::where('is_resigned', 0)->orderBy('name')->get();
        $marketingRefs = [
            'No reference',
            'Referral: Tech Store A',
            'Referral: Tech Store B',
            'Online - Google Search',
            'Online - Social Media',
            'Word of Mouth',
            'Walk-in / Local Resident',
            'Enterprise Client Referral',
        ];
        $pallets_list = \App\Models\Pallet::latest()->get();
        $labelBarcodeDataUri = $this->barcodeDataUri($entry->item_barcode);
        $palletBarcodeDataUri = $entry->pallet_info ? $this->barcodeDataUri($entry->pallet_info) : null;

        return view('admin.repairs.edit', compact(
            'entry',
            'clients',
            'employees',
            'marketingRefs',
            'pallets_list',
            'labelBarcodeDataUri',
            'palletBarcodeDataUri'
        ));
    }

    /**
     * Update the specified repair entry.
     */
    public function update(Request $request, $id)
    {
        $repair = RepairEntry::findOrFail($id);
        $previousStatus = $repair->status;

        // Map intake-style field names to repair_entries fields if provided
        if ($request->filled('item_name')) {
            $request->merge(['device_type' => $request->item_name]);
        }
        if ($request->filled('brand')) {
            $request->merge(['brand_category' => $request->brand]);
        }
        if ($request->filled('model')) {
            $deviceName = $request->input('item_name', $repair->device_type);
            $brandName = $request->input('brand', $repair->brand_category);
            $modelName = $request->input('model');
            $desc = trim($deviceName . ($brandName ? ' - ' . $brandName : '') . ($modelName ? ' ' . $modelName : ''));
            $request->merge(['item_description' => $desc]);
        }
        if ($request->filled('pallet_barcode')) {
            $request->merge(['pallet_info' => $request->pallet_barcode]);
        }
        if ($request->has('repair_items')) {
            $itemsInput = $request->input('repair_items');
            if (is_string($itemsInput)) {
                $itemsInput = json_decode($itemsInput, true) ?: [];
            }
            if (is_array($itemsInput) && !empty($itemsInput)) {
                $structuredItems = [];
                $names = [];
                $brands = [];
                $serials = [];
                $descriptions = [];
                foreach ($itemsInput as $it) {
                    $dName = trim($it['name'] ?? '') ?: 'Repair Item';
                    $bName = trim($it['brand'] ?? '');
                    $mName = trim($it['model'] ?? '');
                    $sName = trim($it['serial'] ?? '');
                    $bCode = !empty($it['barcode']) ? $it['barcode'] : ('REP-' . mt_rand(1000000000, 9999999999));
                    $desc = trim($dName . ($bName ? ' - ' . $bName : '') . ($mName ? ' ' . $mName : ''));

                    $structuredItems[] = [
                        'name' => $dName,
                        'brand' => $bName,
                        'model' => $mName,
                        'serial' => $sName,
                        'barcode' => $bCode,
                    ];
                    $names[] = $dName;
                    if ($bName) $brands[] = $bName;
                    if ($sName) $serials[] = $sName;
                    if ($desc) $descriptions[] = $desc;
                }

                $request->merge([
                    'device_type' => implode(', ', $names),
                    'brand_category' => implode(', ', array_unique($brands)),
                    'serial_number' => implode(', ', $serials),
                    'item_description' => implode('; ', $descriptions),
                    'quantity' => count($structuredItems),
                    'device_specs' => json_encode($structuredItems),
                ]);
            }
        }

        if ($request->input('action') === 'draft' || $request->boolean('is_draft')) {
            $request->merge(['status' => RepairEntry::STATUS_DRAFT]);
        }

        // Handle Client selection
        if ($request->filled('client_id')) {
            $client = \App\Models\Client::find($request->client_id);
            if ($client) {
                $request->merge([
                    'user_id' => $client->id,
                    'customer_name' => $client->name,
                    'customer_email' => $client->email,
                    'customer_phone' => $client->phone,
                ]);
            }
        }

        // Handle Job # and Marketing Ref
        if ($request->has('job_no') || $request->has('marketing_reference')) {
            $jobNo = $request->input('job_no', $repair->job_no ?? 'N/A');
            $marketingRef = $request->input('marketing_reference', $repair->marketing_ref ?? 'No reference');
            $request->merge([
                'lot_info' => trim("Job: {$jobNo} | Ref: {$marketingRef}"),
            ]);
        }

        // Handle Receiving Notes & Technician Notes
        if ($request->has('receiving_notes') || $request->has('technician_notes')) {
            $rNotes = $request->input('receiving_notes', '');
            $tNotes = $request->input('technician_notes', '');
            $mergedNotes = trim(($rNotes ? "Receiving Notes: $rNotes\n" : "") . ($tNotes ? "Tech Notes: $tNotes" : ""));
            $request->merge(['notes' => $mergedNotes ?: null]);
        }

        // Defaults for legacy fields
        if (!$request->has('technician') || empty($request->technician)) {
            $request->merge(['technician' => $request->input('receiving_employee', $repair->technician ?? 'N/A')]);
        }
        if (!$request->has('device_type') || empty($request->device_type)) {
            $request->merge(['device_type' => $repair->device_type ?? 'Repair Item']);
        }
        if (!$request->has('quantity') || empty($request->quantity)) {
            $request->merge(['quantity' => $repair->quantity ?? 1]);
        }
        if (!$request->has('price')) {
            $request->merge(['price' => $repair->price ?? 0]);
        }
        if (!$request->has('status') || empty($request->status)) {
            $request->merge(['status' => $repair->status ?? RepairEntry::STATUS_REPAIR_SERVICES]);
        }
        if (!$request->has('payment_status') || empty($request->payment_status)) {
            $request->merge(['payment_status' => $repair->payment_status ?? RepairEntry::PAYMENT_STATUS_PENDING]);
        }

        $request->validate([
            'user_id' => 'nullable',
            'technician' => 'required|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'device_type' => 'required|string|max:255',
            'brand_category' => 'nullable|string|max:255',
            'item_description' => 'nullable|string|max:255',
            'item_barcode' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:100',
            'pallet_info' => 'nullable|string|max:100',
            'lot_info' => 'nullable|string|max:255',
            'put_away_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $data = $request->except(['photos', 'deleted_photos', 'action', 'is_draft', 'intake_date', 'job_no', 'marketing_reference', 'item_name', 'brand', 'model', 'receiving_employee', 'receiving_notes', 'technician_notes', 'repair_status']);
        $data['price'] = $data['price'] ?? 0;
        $data['repair_status'] = $request->status;

        if ($request->filled('intake_date')) {
            try {
                $data['created_at'] = \Carbon\Carbon::parse($request->intake_date)->setTimeFrom(now());
            } catch (\Exception $e) {}
        }

        // Photos handling: remove deleted photos, append new ones
        $existingPhotos = json_decode($repair->photos, true) ?? [];
        if ($request->has('deleted_photos')) {
            $deleted = (array) $request->input('deleted_photos', []);
            $existingPhotos = array_values(array_filter($existingPhotos, fn($p) => !in_array($p, $deleted)));
        }

        if ($request->hasFile('photos')) {
            $storedPhotoPaths = [];
            foreach ($request->file('photos') as $photo) {
                if ($photo && $photo->isValid()) {
                    $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType() ?: 'application/octet-stream';
                    $base64Data = base64_encode(file_get_contents($photo->getRealPath()));
                    $storedPhotoPaths[] = 'data:' . $mimeType . ';base64,' . $base64Data;
                }
            }
            $existingPhotos = array_merge($existingPhotos, $storedPhotoPaths);
        }

        $data['photos'] = !empty($existingPhotos) ? json_encode($existingPhotos) : null;

        $repair->update($data);

        // Also update the associated pallet if it exists
        if ($repair->pallet_info) {
            $pallet = \App\Models\Pallet::firstOrNew(['barcode_number' => $repair->pallet_info]);
            if ($request->filled('client_id')) $pallet->client_id = $request->client_id;
            if ($request->filled('put_away_location')) $pallet->put_away_location = $request->put_away_location;
            if ($request->filled('marketing_reference')) $pallet->marketing_reference = $request->marketing_reference;
            if ($request->filled('receiving_employee')) $pallet->receiving_employee = $request->receiving_employee;
            if ($request->filled('job_no')) $pallet->job_no = $request->job_no;
            $pallet->photos_paths = !empty($existingPhotos) ? $existingPhotos : null;
            $pallet->save();
        }

        $updatedRepair = $repair->fresh();
        $this->sendRepairDetailsEmail($updatedRepair);
        $emailResult = $this->sendRepairStatusUpdateEmail($updatedRepair, $previousStatus);

        $redirect = redirect()
            ->route('admin.repair.entries.show', $repair->id)
            ->with('success', 'Repair Entry Updated Successfully!');

        if (!$emailResult['sent'] && $emailResult['message']) {
            $redirect->with('warning', $emailResult['message']);
        }

        return $redirect;
    }

    /**
     * Update repair payment method from the view screen.
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method' => ['required', Rule::in(RepairEntry::paymentMethodValues())],
        ]);

        $repair = RepairEntry::findOrFail($id);
        $repair->update([
            'payment_method' => $request->payment_method,
            'payment_status' => RepairEntry::PAYMENT_STATUS_PAID,
        ]);

        return redirect()->back()
            ->with('success', 'Repair payment method updated successfully!');
    }

    /**
     * Update repair payment status from the view screen.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => ['required', Rule::in(RepairEntry::paymentStatusValues())],
        ]);

        $repair = RepairEntry::findOrFail($id);
        $repair->update(['payment_status' => $request->payment_status]);

        return redirect()->back()
            ->with('success', 'Repair payment status updated successfully!');
    }

    /**
     * Remove the specified repair entry and its media.
     */
    public function destroy($id)
    {
        $repair = RepairEntry::findOrFail($id);

        $repair->delete();

        return redirect()->back()->with('success', 'Repair Entry Deleted Successfully!');
    }

    private function saveRepairEntryPhotos(array $photos): array
    {
        if (empty($photos)) {
            return [];
        }

        $uploadedPhotos = [];

        foreach ($photos as $photo) {
            if (!$photo || !$photo->isValid()) {
                continue;
            }

            $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType() ?: 'application/octet-stream';
            $base64Data = base64_encode(file_get_contents($photo->getRealPath()));
            $uploadedPhotos[] = 'data:' . $mimeType . ';base64,' . $base64Data;
        }

        return $uploadedPhotos;
    }


    private function barcodeDataUri(?string $barcode): ?string
    {
        if (empty($barcode)) {
            return null;
        }

        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 30);

            return 'data:image/png;base64,' . base64_encode($barcodeData);
        } catch (\Throwable $e) {
            Log::warning('Failed to generate repair label barcode image', [
                'barcode' => $barcode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function sendRepairDetailsEmail($repairs): void
    {
        $repairEntries = $repairs instanceof RepairEntry
            ? collect([$repairs])
            : collect($repairs)->filter(fn($repair) => $repair instanceof RepairEntry)->values();

        if ($repairEntries->isEmpty()) {
            return;
        }

        $email = $repairEntries->first()->customer_email;

        if (empty($email)) {
            return;
        }

        try {
            Mail::to($email)->send(new RepairDetailsNotification($repairEntries));
        } catch (\Throwable $e) {
            Log::warning('Failed to send repair details email', [
                'repair_ids' => $repairEntries->pluck('id')->all(),
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendRepairStatusUpdateEmail(RepairEntry $repair, ?string $previousStatus = null): array
    {
        if ($previousStatus === $repair->status) {
            return [
                'sent' => false,
                'message' => null,
            ];
        }

        $repair->loadMissing(['client', 'user', 'pallet.client']);
        $customerEmail = trim((string) (
            $repair->customer_email
            ?: ($repair->client?->email
                ?: ($repair->user?->email
                    ?: ($repair->pallet?->client?->email ?? '')))
        ));

        if (empty($customerEmail) && $repair->user_id) {
            $client = \App\Models\Client::find($repair->user_id);
            if ($client && !empty($client->email)) {
                $customerEmail = trim((string) $client->email);
            }
        }

        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Repair status update email skipped: no valid customer email found', [
                'repair_id' => $repair->id,
                'raw_email' => $customerEmail,
                'previous_status' => $previousStatus,
                'status' => $repair->status,
            ]);

            return [
                'sent' => false,
                'message' => 'Status email not sent: no valid customer email address found (' . ($customerEmail ?: 'empty') . ').',
            ];
        }

        if (empty($repair->customer_email) && !empty($customerEmail)) {
            try {
                $repair->update(['customer_email' => $customerEmail]);
            } catch (\Throwable $ignore) {}
        }

        try {
            Mail::to($customerEmail)->send(new RepairStatusUpdated($repair, $previousStatus));

            Log::info('Repair status update email sent to customer successfully', [
                'repair_id' => $repair->id,
                'recipient' => $customerEmail,
                'previous_status' => $previousStatus,
                'status' => $repair->status,
            ]);

            return [
                'sent' => true,
                'message' => 'Status email sent to ' . $customerEmail . '.',
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to send repair status update email', [
                'repair_id' => $repair->id,
                'email' => $customerEmail,
                'previous_status' => $previousStatus,
                'status' => $repair->status,
                'error' => $e->getMessage(),
            ]);

            return [
                'sent' => false,
                'message' => 'Status email could not be sent: ' . $e->getMessage(),
            ];
        }
    }
}
