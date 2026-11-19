<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RepairEntry;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Mail\SalesInvoiceNotification;
use App\Services\SquareInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SalesInvoiceController extends Controller
{
    private const TAX_RATE = 0.08;
    private const TAX_RATE_PERCENT = 8.00;
    private const REPAIR_INVOICE_CATEGORY = 'Repair Services';

    public function index()
    {
        $invoices = SalesInvoice::with('client', 'items')
            ->latest()
            ->get();

        $invoiceRows = $invoices->map(function (SalesInvoice $invoice) {
            $categories = $invoice->items
                ->pluck('invoice_category')
                ->filter()
                ->unique()
                ->values();

            $picturePaths = collect($invoice->picture_paths ?? [])->filter()->values();
            $pictureUrls = $picturePaths->map(fn($p) => $invoice->pictureUrl($p))->values()->all();
            $firstPicture = $pictureUrls[0] ?? null;
            $firstIsImage = $firstPicture ? SalesInvoice::isImageFile($picturePaths[0]) : false;
            $firstIcon = $firstPicture ? SalesInvoice::fileIconClass($picturePaths[0]) : null;
            $firstFileName = $firstPicture ? SalesInvoice::fileName($picturePaths[0]) : null;

            return [
                'id' => $invoice->id,
                'row_key' => 'invoice-' . $invoice->id,
                'num' => $invoice->invoice_number,
                'pictures' => $pictureUrls,
                'first_picture' => $firstPicture,
                'first_is_image' => $firstIsImage,
                'first_icon' => $firstIcon,
                'first_file_name' => $firstFileName,
                'picture_count' => count($pictureUrls),
                'client' => $invoice->customer_name,
                'email' => $invoice->customer_email,
                'phone' => $invoice->customer_phone,
                'company' => $invoice->customer_company,
                'address' => $invoice->customer_company
                    ?: ($invoice->customer_address ?: ($invoice->customer_email ?: 'No customer destination saved')),
                'customer_address' => $invoice->customer_address,
                'val' => '$' . number_format((float) $invoice->total, 2),
                'status' => $invoice->status_label,
                'status_class' => $invoice->status_badge_class,
                'has_square' => $invoice->hasSquareInvoice(),
                'square_status' => $invoice->square_status,
                'square_badge_class' => $invoice->square_status_badge_class,
                'send_square_url' => route('admin.sales.invoices.send-square', $invoice),
                'remind_square_url' => route('admin.sales.invoices.remind-square', $invoice),
                'square_public_url' => $invoice->square_public_url,
                'square_reminded_at' => $invoice->square_reminded_at?->format('m/d/Y h:i A'),
                'square_reminder_count' => (int) $invoice->square_reminder_count,
                'is_paid' => $invoice->isSquarePaid() || $invoice->status === SalesInvoice::STATUS_PAID,
                'default_reminder_message' => SalesInvoice::defaultReminderMessage($invoice),
                'date' => $invoice->service_date?->format('m/d/Y') ?? $invoice->created_at?->format('m/d/Y'),
                'date_value' => $invoice->service_date?->format('Y-m-d') ?? $invoice->created_at?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('m/d/Y'),
                'due_date_value' => $invoice->due_date?->format('Y-m-d'),
                'payment_method' => $invoice->payment_method,
                'payment_method_label' => $invoice->payment_method_label,
                'payment_type' => $invoice->payment_type,
                'payment_type_label' => $invoice->payment_type_label,
                'type' => 'service',
                'type_label' => 'Service Invoice',
                'categories' => $categories->all(),
                'category_label' => $categories->isNotEmpty() ? $categories->implode(', ') : 'Service Invoice',
                'view_url' => route('admin.sales.invoices.show', $invoice),
                'edit_url' => route('admin.sales.invoices.edit', $invoice),
                'print_url' => route('admin.sales.invoices.print', $invoice) . '?embedded=1',
                'delete_url' => route('admin.sales.invoices.destroy', $invoice),
                'delete_confirm' => 'Delete this invoice?',
            ];
        });

        $repairRows = RepairEntry::with('user')
            ->latest()
            ->get()
            ->map(function (RepairEntry $repair) {
                $quantity = (int) ($repair->quantity ?: 1);
                $unitPrice = (float) ($repair->price ?? 0);
                $total = $quantity * $unitPrice;
                $customerName = $repair->customer_name
                    ?: ($repair->user?->name
                        ?: ($repair->customer_email
                            ?: ($repair->customer_phone ?: 'No customer')));

                $repairPhotos = [];
                if ($repair->photos) {
                    $decoded = is_string($repair->photos) ? json_decode($repair->photos, true) : $repair->photos;
                    if (is_array($decoded)) {
                        $repairPhotos = array_values(array_filter(array_map(function($p) {
                            if (empty($p)) return null;
                            if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://') || str_starts_with($p, 'data:')) {
                                return $p;
                            }
                            return asset($p);
                        }, $decoded)));
                    }
                }
                $firstRepairPhoto = $repairPhotos[0] ?? null;

                return [
                    'id' => $repair->id,
                    'row_key' => 'repair-' . $repair->id,
                    'num' => $repair->item_barcode ?: 'REP-' . $repair->id,
                    'pictures' => $repairPhotos,
                    'first_picture' => $firstRepairPhoto,
                    'first_is_image' => !empty($firstRepairPhoto),
                    'first_icon' => 'fa-file-image text-blue-500',
                    'first_file_name' => 'Repair Photo',
                    'picture_count' => count($repairPhotos),
                    'client' => $customerName,
                    'email' => $repair->customer_email,
                    'phone' => $repair->customer_phone,
                    'company' => null,
                    'address' => $repair->put_away_location ?: ($repair->device_type ?: 'Repair service'),
                    'customer_address' => $repair->put_away_location,
                    'val' => '$' . number_format($total, 2),
                    'status' => (strtolower((string) ($repair->payment_status ?? '')) === RepairEntry::PAYMENT_STATUS_PAID) ? 'Paid' : 'Unpaid',
                    'status_class' => (strtolower((string) ($repair->payment_status ?? '')) === RepairEntry::PAYMENT_STATUS_PAID) ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-700',
                    'date' => $repair->created_at?->format('m/d/Y'),
                    'date_value' => $repair->created_at?->format('Y-m-d'),
                    'payment_method' => $repair->payment_method,
                    'payment_method_label' => $repair->payment_method_label,
                    'payment_type' => 'in_store',
                    'payment_type_label' => 'In store payment',
                    'type' => 'repair',
                    'type_label' => self::REPAIR_INVOICE_CATEGORY,
                    'categories' => [self::REPAIR_INVOICE_CATEGORY],
                    'category_label' => self::REPAIR_INVOICE_CATEGORY,
                    'view_url' => route('admin.repair.entries.show', $repair->id),
                    'edit_url' => route('admin.repair.entries.edit', $repair->id),
                    'print_url' => route('admin.repair.entries.invoice-print', $repair->id) . '?embedded=1',
                    'delete_url' => route('admin.repair.entries.destroy', $repair->id),
                    'delete_confirm' => 'Delete this repair entry?',
                ];
            });

        $invoiceRows = $invoiceRows
            ->merge($repairRows)
            ->sortByDesc('date_value')
            ->values();

        $invoiceCategories = $this->invoiceCategoryOptions();
        $invoices = null;

        return view('admin.sales.invoices', compact('invoices', 'invoiceRows', 'invoiceCategories'));
    }

    public function create(Request $request)
    {
        $repairId = $request->query('repair_id');
        $repair = null;
        $prefilledInvoice = null;
        $prefilledItems = [];

        if ($repairId) {
            $repair = RepairEntry::with(['client', 'user'])->find($repairId);
            if ($repair) {
                // Find matching client
                $clientId = $repair->user_id;
                if ($clientId && !Client::where('id', $clientId)->exists()) {
                    $clientId = null;
                }
                if (!$clientId && $repair->customer_email) {
                    $clientId = Client::where('email', $repair->customer_email)->value('id');
                }

                $customerName = $repair->customer_name ?: ($repair->client?->name ?: ($repair->user?->name ?? ''));
                $customerEmail = $repair->customer_email ?: ($repair->client?->email ?? '');
                $customerPhone = $repair->customer_phone ?: ($repair->client?->phone ?? '');
                $customerCompany = $repair->client?->company ?? '';
                $customerAddress = $repair->client?->address ?: ($repair->put_away_location ?? '');

                // Parse photos if any
                $photos = [];
                if (!empty($repair->photos)) {
                    $decoded = is_string($repair->photos) ? json_decode($repair->photos, true) : $repair->photos;
                    if (is_array($decoded)) {
                        $photos = array_values(array_filter($decoded));
                    }
                }

                // Build initial line items from repair entry device_specs or fields
                if (!empty($repair->device_specs)) {
                    $specs = json_decode($repair->device_specs, true);
                    if (is_array($specs)) {
                        foreach ($specs as $spec) {
                            $specTitle = trim(($spec['name'] ?? '') . ' ' . ($spec['brand'] ?? '') . ' ' . ($spec['model'] ?? ''));
                            if (!empty($spec['serial'])) {
                                $specTitle .= ' (SN: ' . $spec['serial'] . ')';
                            }
                            if ($specTitle) {
                                $prefilledItems[] = [
                                    'invoice_category' => self::REPAIR_INVOICE_CATEGORY,
                                    'description' => $specTitle,
                                    'quantity' => 1,
                                    'unit_price' => (float) ($repair->price ?? 0),
                                    'taxable' => true,
                                ];
                            }
                        }
                    }
                }

                if (empty($prefilledItems)) {
                    $descParts = array_filter([
                        $repair->device_type,
                        $repair->brand_category,
                        $repair->item_description,
                        $repair->serial_number ? "SN: {$repair->serial_number}" : null,
                        $repair->item_barcode ? "Barcode: {$repair->item_barcode}" : null,
                    ]);
                    $desc = !empty($descParts) ? implode(' - ', $descParts) : 'Repair Service';

                    $prefilledItems[] = [
                        'invoice_category' => self::REPAIR_INVOICE_CATEGORY,
                        'description' => $desc,
                        'quantity' => (int) ($repair->quantity ?: 1),
                        'unit_price' => (float) ($repair->price ?? 0),
                        'taxable' => true,
                    ];
                }

                $message = "Repair Entry #{$repair->id}";
                if ($repair->item_barcode) {
                    $message .= " | Barcode: {$repair->item_barcode}";
                }
                if ($repair->notes) {
                    $message .= "\n" . $repair->notes;
                }

                $paymentMethod = $repair->payment_method;
                if (!in_array($paymentMethod, array_keys(SalesInvoice::PAYMENT_METHOD_CHOICES))) {
                    $paymentMethod = SalesInvoice::PAYMENT_METHOD_CASH;
                }

                $prefilledInvoice = new SalesInvoice([
                    'client_id' => $clientId,
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'customer_company' => $customerCompany,
                    'customer_address' => $customerAddress,
                    'service_date' => $repair->created_at ? $repair->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'due_date' => now()->format('Y-m-d'),
                    'payment_method' => $paymentMethod,
                    'message' => $message,
                    'status' => strtolower((string) ($repair->payment_status ?? '')) === RepairEntry::PAYMENT_STATUS_PAID
                        ? SalesInvoice::STATUS_PAID
                        : SalesInvoice::STATUS_UNPAID,
                    'picture_paths' => $photos,
                ]);
            }
        }

        return view('admin.sales.invoices-create', [
            'clients' => Client::orderBy('name')->get(),
            'invoice' => $prefilledInvoice,
            'prefilledItems' => $prefilledItems,
            'invoiceNumber' => SalesInvoice::generateInvoiceNumber(),
            'paymentMethods' => SalesInvoice::PAYMENT_METHOD_CHOICES,
            'invoiceCategories' => $this->invoiceCategoryOptions(),
            'taxRatePercent' => self::TAX_RATE_PERCENT,
            'statusChoices' => SalesInvoice::statusChoices(),
            'isEdit' => false,
            'repair' => $repair,
        ]);
    }

    public function store(Request $request, SquareInvoiceService $squareService)
    {
        $validated = $this->validateInvoice($request);
        $picturePaths = $this->storeUploadedPictures($request);

        $invoiceNumber = (!empty($validated['invoice_number'])) ? $validated['invoice_number'] : SalesInvoice::generateInvoiceNumber();
        if (SalesInvoice::where('invoice_number', $invoiceNumber)->exists()) {
            $invoiceNumber = SalesInvoice::generateInvoiceNumber();
        }

        $invoice = DB::transaction(function () use ($validated, $request, $picturePaths, $invoiceNumber) {
            $client = $this->resolveClient($validated, $request);
            $totals = $this->buildInvoiceItems($validated['items']);
            $repairId = $request->input('repair_id');
            if (!$repairId && !empty($validated['message']) && preg_match('/Repair Entry #(\d+)/', $validated['message'], $m)) {
                $repairId = (int) $m[1];
            }

            $invoice = SalesInvoice::create([
                'repair_entry_id' => $repairId ?: null,
                'client_id' => $client?->id,
                'created_by' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'invoice_title' => $invoiceNumber,
                'service_date' => $validated['service_date'],
                'due_date' => $validated['due_date'] ?? $validated['service_date'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_company' => $validated['customer_company'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'message' => $validated['message'] ?? null,
                'picture_paths' => $picturePaths,
                'payment_method' => $validated['payment_method'],
                'subtotal' => $totals['subtotal'],
                'tax_rate' => $totals['tax_rate'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'status' => ($validated['status'] ?? null) === SalesInvoice::STATUS_PAID
                    ? SalesInvoice::STATUS_PAID
                    : SalesInvoice::STATUS_UNPAID,
                'paid_at' => ($validated['status'] ?? null) === SalesInvoice::STATUS_PAID
                    ? now()
                    : null,
            ]);

            $invoice->items()->createMany($totals['items']);

            return $invoice;
        });

        $shouldSendSquare = $request->input('action') === 'save_and_send_square';

        if ($shouldSendSquare) {
            $result = $squareService->createAndSendInvoice($invoice);
            if ($result['success']) {
                return redirect()
                    ->route('admin.sales.invoices.show', $invoice)
                    ->with('success', "Invoice {$invoice->invoice_number} created and sent via Square successfully.");
            }

            return redirect()
                ->route('admin.sales.invoices.show', $invoice)
                ->with('warning', "Invoice {$invoice->invoice_number} created locally, but Square Invoice sending failed: {$result['message']}");
        }

        $emailResult = $this->sendCustomerInvoiceEmail($invoice);

        if ($emailResult['sent']) {
            return redirect()
                ->route('admin.sales.invoices.show', $invoice)
                ->with('success', "Invoice {$invoice->invoice_number} created and sent to {$invoice->customer_email} successfully.");
        }

        if (!empty($invoice->customer_email)) {
            return redirect()
                ->route('admin.sales.invoices.show', $invoice)
                ->with('warning', "Invoice {$invoice->invoice_number} created, but email could not be sent: {$emailResult['message']}");
        }

        return redirect()
            ->route('admin.sales.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} created successfully.");
    }

    public function show(SalesInvoice $invoice)
    {
        $invoice->load(['client', 'creator', 'items']);

        return view('admin.sales.invoices-show', compact('invoice'));
    }

    public function edit(SalesInvoice $invoice)
    {
        $invoice->load('items');

        return view('admin.sales.invoices-create', [
            'clients' => Client::orderBy('name')->get(),
            'invoice' => $invoice,
            'invoiceNumber' => $invoice->invoice_number,
            'paymentMethods' => SalesInvoice::PAYMENT_METHOD_CHOICES,
            'statusChoices' => SalesInvoice::statusChoices(),
            'invoiceCategories' => $this->invoiceCategoryOptions(),
            'taxRatePercent' => self::TAX_RATE_PERCENT,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, SalesInvoice $invoice, SquareInvoiceService $squareService)
    {
        $validated = $this->validateInvoice($request, $invoice);
        $newPicturePaths = $this->storeUploadedPictures($request);
        $existingPicturePaths = collect($invoice->picture_paths ?? [])->filter()->values();
        $removedPicturePaths = collect($validated['remove_pictures'] ?? [])
            ->filter(fn ($path) => $existingPicturePaths->contains($path))
            ->values();

        $picturePaths = $existingPicturePaths
            ->reject(fn ($path) => $removedPicturePaths->contains($path))
            ->merge($newPicturePaths)
            ->values()
            ->all();

        DB::transaction(function () use ($validated, $request, $invoice, $picturePaths) {
            $client = $this->resolveClient($validated, $request);
            $totals = $this->buildInvoiceItems($validated['items']);
            $repairId = $request->input('repair_id', $invoice->repair_entry_id);

            $invoice->update([
                'repair_entry_id' => $repairId ?: $invoice->repair_entry_id,
                'client_id' => $client?->id,
                'invoice_number' => $validated['invoice_number'] ?: $invoice->invoice_number,
                'invoice_title' => $validated['invoice_number'] ?: $invoice->invoice_number,
                'service_date' => $validated['service_date'],
                'due_date' => $validated['due_date'] ?? $validated['service_date'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_company' => $validated['customer_company'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'message' => $validated['message'] ?? null,
                'picture_paths' => $picturePaths,
                'payment_method' => $validated['payment_method'],
                'subtotal' => $totals['subtotal'],
                'tax_rate' => $totals['tax_rate'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'status' => ($validated['status'] ?? $invoice->status) === SalesInvoice::STATUS_PAID
                    ? SalesInvoice::STATUS_PAID
                    : SalesInvoice::STATUS_UNPAID,
                'paid_at' => ($validated['status'] ?? $invoice->status) === SalesInvoice::STATUS_PAID
                    ? ($invoice->paid_at ?? now())
                    : null,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($totals['items']);
        });

        if ($removedPicturePaths->isNotEmpty()) {
            $this->deletePictureFiles($removedPicturePaths->all());
        }

        $shouldSendSquare = $request->input('action') === 'save_and_send_square';
        if ($shouldSendSquare) {
            $result = $squareService->createAndSendInvoice($invoice->fresh(['items', 'client']));
            if ($result['success']) {
                return redirect()
                    ->route('admin.sales.invoices.show', $invoice)
                    ->with('success', "Invoice {$invoice->invoice_number} updated and sent via Square successfully.");
            }

            return redirect()
                ->route('admin.sales.invoices.show', $invoice)
                ->with('warning', "Invoice {$invoice->invoice_number} updated locally, but Square Invoice sending failed: {$result['message']}");
        }

        $emailResult = $this->sendCustomerInvoiceEmail($invoice->fresh(['items', 'client']));

        if ($emailResult['sent']) {
            return redirect()
                ->route('admin.sales.invoices.show', $invoice)
                ->with('success', "Invoice {$invoice->invoice_number} updated and sent to {$invoice->customer_email} successfully.");
        }

        if (!empty($invoice->customer_email)) {
            return redirect()
                ->route('admin.sales.invoices.show', $invoice)
                ->with('warning', "Invoice {$invoice->invoice_number} updated, but email could not be sent: {$emailResult['message']}");
        }

        return redirect()
            ->route('admin.sales.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} updated successfully.");
    }

    public function sendEmail(SalesInvoice $invoice)
    {
        $emailResult = $this->sendCustomerInvoiceEmail($invoice);

        if ($emailResult['sent']) {
            return redirect()
                ->back()
                ->with('success', "Invoice {$invoice->invoice_number} sent to {$invoice->customer_email} successfully.");
        }

        return redirect()
            ->back()
            ->with('error', $emailResult['message']);
    }

    private function sendCustomerInvoiceEmail(SalesInvoice $invoice): array
    {
        $customerEmail = trim((string) $invoice->customer_email);
        if (empty($customerEmail)) {
            return [
                'sent' => false,
                'message' => 'No customer email address was provided.',
            ];
        }

        try {
            $invoice->loadMissing(['items', 'client', 'creator']);
            Mail::to($customerEmail)->send(new SalesInvoiceNotification($invoice));

            Log::info('Sales invoice sent via email to customer successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'recipient' => $customerEmail,
            ]);

            return [
                'sent' => true,
                'message' => "Invoice sent to {$customerEmail} successfully.",
            ];
        } catch (\Throwable $e) {
            Log::error('Sales invoice customer email failed', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'recipient' => $customerEmail,
                'error' => $e->getMessage(),
            ]);

            return [
                'sent' => false,
                'message' => "Failed to send email to {$customerEmail}: " . $e->getMessage(),
            ];
        }
    }

    public function sendSquare(SalesInvoice $invoice, SquareInvoiceService $squareService)
    {
        $invoice->load(['items', 'client']);
        $result = $squareService->createAndSendInvoice($invoice);

        if ($result['success']) {
            return redirect()
                ->back()
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    public function syncSquare(SalesInvoice $invoice, SquareInvoiceService $squareService)
    {
        $result = $squareService->syncInvoiceStatus($invoice);

        if ($result['success']) {
            return redirect()
                ->back()
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    public function cancelSquare(SalesInvoice $invoice, SquareInvoiceService $squareService)
    {
        $result = $squareService->cancelInvoice($invoice);

        if ($result['success']) {
            return redirect()
                ->back()
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    public function remindSquare(Request $request, SalesInvoice $invoice, SquareInvoiceService $squareService)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
        ]);

        $recipientEmail = !empty($validated['recipient_email'])
            ? trim($validated['recipient_email'])
            : trim((string) $invoice->customer_email);

        if (empty($recipientEmail)) {
            return redirect()
                ->back()
                ->with('error', 'A customer email address is required to send the payment reminder.');
        }

        $invoice->load(['client', 'items']);
        $result = $squareService->sendPaymentReminder($invoice, $validated['message'], $recipientEmail);

        if ($result['success']) {
            return redirect()
                ->back()
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    public function printInvoice(SalesInvoice $invoice)
    {
        $invoice->load(['client', 'creator', 'items']);

        return response()
            ->view('admin.sales.invoices-print', compact('invoice'))
            ->header('Cache-Control', 'private, max-age=60');
    }

    public function destroy(SalesInvoice $invoice)
    {
        $invoiceNumber = $invoice->invoice_number;
        $picturePaths = $invoice->picture_paths ?? [];

        $invoice->delete();

        return redirect()
            ->route('admin.sales.invoices')
            ->with('success', "Invoice {$invoiceNumber} deleted successfully.");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|string',
        ]);

        $invoiceIds = [];
        $repairIds = [];

        foreach ($validated['ids'] as $rawId) {
            if (str_starts_with($rawId, 'repair-')) {
                $repairIds[] = (int) substr($rawId, 7);
            } elseif (str_starts_with($rawId, 'invoice-')) {
                $invoiceIds[] = (int) substr($rawId, 8);
            } elseif (is_numeric($rawId)) {
                $invoiceIds[] = (int) $rawId;
            }
        }

        $deletedInvoices = 0;
        $deletedRepairs = 0;

        DB::transaction(function () use ($invoiceIds, $repairIds, &$deletedInvoices, &$deletedRepairs) {
            if (!empty($invoiceIds)) {
                $deletedInvoices = SalesInvoice::whereIn('id', $invoiceIds)->delete();
            }
            if (!empty($repairIds)) {
                $deletedRepairs = RepairEntry::whereIn('id', $repairIds)->delete();
            }
        });

        $totalDeleted = $deletedInvoices + $deletedRepairs;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$totalDeleted} invoice(s) deleted successfully.",
                'deleted_count' => $totalDeleted,
            ]);
        }

        return redirect()
            ->route('admin.sales.invoices')
            ->with('success', "{$totalDeleted} invoice(s) deleted successfully.");
    }

    private function validateInvoice(Request $request, ?SalesInvoice $invoice = null): array
    {
        $invoiceNumberRule = Rule::unique('sales_invoices', 'invoice_number');
        if ($invoice) {
            $invoiceNumberRule->ignore($invoice->id);
        }

        $rules = [
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'create_customer' => ['nullable', 'boolean'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_company' => ['nullable', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'service_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(SalesInvoice::statusValues())],
            'invoice_number' => ['nullable', 'string', 'max:80', $invoiceNumberRule],
            'message' => ['nullable', 'string'],
            'payment_method' => ['required', Rule::in(SalesInvoice::paymentMethodValues())],
            'pictures' => ['nullable', 'array'],
            'pictures.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp,bmp,svg,pdf,doc,docx,xls,xlsx,csv,txt,zip,rar', 'max:20480'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.invoice_category' => ['required', 'string', 'max:255'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.taxable' => ['nullable'],
        ];

        if ($invoice) {
            $rules['status'] = ['required', Rule::in(SalesInvoice::statusValues())];
            $rules['remove_pictures'] = ['nullable', 'array'];
            $rules['remove_pictures.*'] = ['nullable', 'string'];
        }

        return $request->validate($rules);
    }

    private function resolveClient(array $validated, Request $request): ?Client
    {
        if (!empty($validated['client_id'])) {
            return Client::find($validated['client_id']);
        }

        if ($request->boolean('create_customer')) {
            return Client::create([
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'] ?? null,
                'phone' => $validated['customer_phone'] ?? null,
                'company' => $validated['customer_company'] ?? null,
                'address' => $validated['customer_address'] ?? null,
            ]);
        }

        return null;
    }

    private function storeUploadedPictures(Request $request): array
    {
        $pictureDataUris = [];

        if (!$request->hasFile('pictures')) {
            return $pictureDataUris;
        }

        foreach ($request->file('pictures') as $picture) {
            if ($picture && $picture->isValid()) {
                $pictureDataUris[] = $this->convertFileToBase64($picture);
            }
        }

        return $pictureDataUris;
    }

    /**
     * Convert an uploaded invoice picture or document into an optimized Base64 data URI string.
     */
    private function convertFileToBase64(\Illuminate\Http\UploadedFile $file): string
    {
        $realPath = $file->getRealPath();
        $mimeType = $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream';
        $originalName = $file->getClientOriginalName() ?: ('file_' . time());

        // For bitmap images, optimize & resize if dimensions exceed 1600px to keep DB storage light
        try {
            if (function_exists('imagecreatefromstring') && function_exists('imagejpeg') && str_starts_with($mimeType, 'image/') && !str_contains($mimeType, 'svg')) {
                $rawContent = file_get_contents($realPath);
                $img = @imagecreatefromstring($rawContent);

                if ($img !== false) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $maxDimension = 1600;

                    if ($width > $maxDimension || $height > $maxDimension) {
                        if ($width >= $height) {
                            $newWidth = $maxDimension;
                            $newHeight = (int) round(($height / $width) * $maxDimension);
                        } else {
                            $newHeight = $maxDimension;
                            $newWidth = (int) round(($width / $height) * $maxDimension);
                        }

                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($img);
                        $img = $resized;
                    }

                    ob_start();
                    if ($mimeType === 'image/png') {
                        imagepng($img, null, 7);
                    } else {
                        imagejpeg($img, null, 80);
                    }
                    $compressedData = ob_get_clean();
                    imagedestroy($img);

                    if (!empty($compressedData)) {
                        return 'data:' . $mimeType . ';name=' . rawurlencode($originalName) . ';base64,' . base64_encode($compressedData);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Invoice image optimization failed, falling back to raw file: ' . $e->getMessage());
        }

        $base64Data = base64_encode(file_get_contents($realPath));
        return 'data:' . $mimeType . ';name=' . rawurlencode($originalName) . ';base64,' . $base64Data;
    }

    private function deletePictureFiles(iterable $paths): void
    {
        foreach ($paths as $path) {
            $path = (string) $path;
            if ($path === '' || str_starts_with($path, 'data:')) {
                continue;
            }

            $path = ltrim($path, '/');
            if (str_starts_with($path, 'sales/') || str_starts_with($path, 'storage/')) {
                File::delete(public_path($path));
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }

    private function buildInvoiceItems(array $items): array
    {
        $invoiceItems = [];
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($items as $itemData) {
            $invoiceCategory = trim((string) ($itemData['invoice_category'] ?? ''));
            $description = trim((string) ($itemData['description'] ?? ''));
            $quantity = (int) $itemData['quantity'];
            $unitPrice = round((float) $itemData['unit_price'], 2);
            $lineSubtotal = round($quantity * $unitPrice, 2);

            $isTaxable = filter_var($itemData['taxable'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $lineTax = $isTaxable ? round($lineSubtotal * self::TAX_RATE, 2) : 0.00;
            $lineTaxRate = $isTaxable ? self::TAX_RATE_PERCENT : 0.00;
            $lineTotal = round($lineSubtotal + $lineTax, 2);

            $subtotal += $lineSubtotal;
            $taxAmount += $lineTax;

            $invoiceItems[] = [
                'inventory_id' => null,
                'invoice_category' => $invoiceCategory,
                'product_name' => $invoiceCategory ?: $description,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $lineSubtotal,
                'taxable' => $isTaxable,
                'tax_rate' => $lineTaxRate,
                'tax_amount' => $lineTax,
                'total' => $lineTotal,
            ];
        }

        $subtotal = round($subtotal, 2);
        $taxAmount = round($taxAmount, 2);

        return [
            'items' => $invoiceItems,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'tax_rate' => $taxAmount > 0 ? self::TAX_RATE_PERCENT : 0.00,
            'total' => round($subtotal + $taxAmount, 2),
        ];
    }

    private function invoiceCategoryOptions()
    {
        $categories = collect([self::REPAIR_INVOICE_CATEGORY]);

        if (!Schema::hasTable('sales_invoice_items') || !Schema::hasColumn('sales_invoice_items', 'invoice_category')) {
            return $categories;
        }

        return $categories
            ->merge(SalesInvoiceItem::query()
            ->whereNotNull('invoice_category')
            ->where('invoice_category', '!=', '')
            ->distinct()
            ->orderBy('invoice_category')
                ->pluck('invoice_category'))
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }
}
