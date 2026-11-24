@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $invoice = $invoice ?? null;
    $pageTitle = $isEdit ? 'Edit Invoice' : 'Create Invoice';
    $submitLabel = 'Save and send via email';
    $formAction = $isEdit && $invoice
        ? route('admin.sales.invoices.update', $invoice)
        : route('admin.sales.invoices.store');
    $cancelRoute = $isEdit && $invoice
        ? route('admin.sales.invoices.show', $invoice)
        : route('admin.sales.invoices');
    $selectedClientId = old('client_id', $invoice?->client_id);
    $selectedClient = $selectedClientId ? $clients->firstWhere('id', (int) $selectedClientId) : null;
    $prefilledItems = $prefilledItems ?? [];
    $formItems = old('items');

    if ($formItems === null) {
        if (!empty($prefilledItems)) {
            $formItems = $prefilledItems;
        } elseif ($invoice && $invoice->relationLoaded('items')) {
            $formItems = $invoice->items->map(fn ($item) => [
                'inventory_id' => $item->inventory_id,
                'invoice_category' => $item->invoice_category ?: $item->product_name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'taxable' => (bool) ($item->taxable ?? true),
            ])->values()->all();
        } else {
            $formItems = [];
        }
    } else {
        $formItems = array_values($formItems);
    }

    $existingPictures = $invoice?->picture_paths ?? [];
    $statusChoices = $statusChoices ?? \App\Models\SalesInvoice::statusChoices();
    $invoiceCategories = collect($invoiceCategories ?? [])->filter()->values();
@endphp

@section('content')
<div class="w-full animate-in fade-in duration-500">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-800">{{ $pageTitle }}</h1>
        </div>
        <a href="{{ route('admin.sales.invoices') }}"
            class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
            <i class="fas fa-arrow-left mr-2 text-xs"></i> All Invoices
        </a>
    </div>

    @if(isset($repair) && $repair)
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md shadow-blue-500/20">
                    <i class="fas fa-wrench"></i>
                </span>
                <div>
                    <p class="font-extrabold text-blue-950">Creating Invoice from Repair Entry #{{ $repair->id }}</p>
                    <p class="text-xs text-blue-700">Client details, device info, and notes have been automatically pre-filled. When payment status is marked Paid, this repair intake entry will automatically become Completed.</p>
                </div>
            </div>
            <a href="{{ route('admin.repair.entries.show', $repair->id) }}"
                class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-xs font-bold text-blue-700 shadow-sm transition hover:bg-blue-100 hover:text-blue-900 self-start sm:self-auto">
                <i class="fas fa-arrow-left mr-1.5 text-[10px]"></i> View Repair Entry
            </a>
        </div>
    @endif

    @if(isset($inventory) && $inventory)
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-500/20">
                    <i class="fas fa-boxes-stacked"></i>
                </span>
                <div>
                    <p class="font-extrabold text-emerald-950">Creating Invoice for Inventory Item #{{ $inventory->id }}: {{ $inventory->name }}</p>
                    <p class="text-xs text-emerald-700">Barcode: <span class="font-mono font-bold">{{ $inventory->barcode ?: 'N/A' }}</span> | Pricing, specifications, and details have been automatically pre-filled.</p>
                </div>
            </div>
            <a href="{{ route('admin.inventory.index') }}"
                class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-xs font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-100 hover:text-emerald-900 self-start sm:self-auto">
                <i class="fas fa-arrow-left mr-1.5 text-[10px]"></i> Back to Inventory
            </a>
        </div>
    @endif

    @if(isset($repairEntries) && $repairEntries->isNotEmpty() && !$isEdit)
        <div class="mb-6 rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-xs">
                        <i class="fas fa-wrench"></i>
                    </span>
                    <div>
                        <label for="select_repair_intake" class="block text-xs font-black uppercase tracking-wide text-slate-700">
                            Link to Repair Intake Entry
                        </label>
                        <p class="text-[11px] text-slate-400">Selecting a repair intake automatically fills customer info, device items, and barcode.</p>
                    </div>
                </div>
                <div class="min-w-[280px]">
                    <select id="select_repair_intake" onchange="window.location.href = this.value ? ('{{ route('admin.sales.invoices.create') }}?repair_id=' + this.value) : '{{ route('admin.sales.invoices.create') }}';"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white">
                        <option value="">-- No Repair Intake (Standalone Invoice) --</option>
                        @foreach($repairEntries as $rEntry)
                            <option value="{{ $rEntry->id }}" {{ (isset($repair) && $repair->id == $rEntry->id) || request('repair_id') == $rEntry->id ? 'selected' : '' }}>
                                Repair #{{ $rEntry->id }} - {{ $rEntry->customer_name ?: 'No customer' }} ({{ $rEntry->first_item_name }} | {{ $rEntry->status }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-black text-red-900">Please fix the invoice details below.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_380px]">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        @php
            $effectiveRepairId = $repair?->id ?? request('repair_id', $invoice?->repair_entry_id);
        @endphp
        @if($effectiveRepairId)
            <input type="hidden" name="repair_id" value="{{ $effectiveRepairId }}">
        @endif
        @php
            $effectiveInventoryId = $inventory?->id ?? request('inventory_id', request('item_id'));
        @endphp
        @if($effectiveInventoryId)
            <input type="hidden" name="inventory_id" value="{{ $effectiveInventoryId }}">
        @endif

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-800">Customer & Invoice Details</h2>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-blue-600">Service Invoice</span>
                </div>

                <input type="hidden" id="client_id" name="client_id" value="{{ $selectedClientId }}">
                <input type="hidden" id="create_customer" name="create_customer" value="{{ old('create_customer', '0') }}">

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="lg:col-span-2">
                        <label for="customer_picker_button" class="mb-2 block text-sm font-bold text-slate-700">Select Customer</label>
                        <div class="flex flex-col gap-3 md:flex-row">
                            <select id="customer_select" class="hidden" tabindex="-1" aria-hidden="true">
                                <option value="">-- Select Customer --</option>
                                @foreach($clients as $client)
                                    <option value="client-{{ $client->id }}"
                                        data-client-id="{{ $client->id }}"
                                        data-name="{{ $client->name }}"
                                        data-email="{{ $client->email }}"
                                        data-phone="{{ $client->phone }}"
                                        data-company="{{ $client->company }}"
                                        data-address="{{ $client->address }}"
                                        {{ (int) $selectedClientId === (int) $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="relative flex-1" data-customer-picker>
                                <button type="button" id="customer_picker_button"
                                    class="flex min-h-[50px] w-full items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-left text-sm font-bold text-slate-700 outline-none transition hover:border-blue-300 focus:border-blue-400 focus:bg-white">
                                    <span class="grid min-w-0 flex-1 gap-1 sm:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)] sm:items-center sm:gap-4">
                                        <span class="min-w-0">
                                            <span id="customer_picker_name" class="block truncate {{ $selectedClient || $invoice?->customer_name ? 'text-slate-800' : 'text-slate-400' }}">
                                                {{ $selectedClient?->name ?? ($invoice?->customer_name ?? 'Select customer') }}
                                            </span>
                                            <span id="customer_picker_company" class="block truncate text-[10px] font-black uppercase tracking-wide text-slate-400">
                                                {{ $selectedClient?->company ?: ($invoice?->customer_company ?: 'All customers') }}
                                            </span>
                                        </span>
                                        <span class="min-w-0 sm:text-right">
                                            <span id="customer_picker_email" class="block truncate text-xs font-bold text-slate-500">
                                                {{ $selectedClient?->email ?: ($invoice?->customer_email ?: 'Email') }}
                                            </span>
                                            <span id="customer_picker_phone" class="block truncate text-[10px] font-black uppercase tracking-wide text-slate-400">
                                                {{ $selectedClient?->phone ?: ($invoice?->customer_phone ?: 'Phone') }}
                                            </span>
                                        </span>
                                    </span>
                                    <i class="fas fa-chevron-down shrink-0 text-[10px] text-slate-300"></i>
                                </button>

                                <div id="customer_picker_menu"
                                    class="absolute left-0 right-0 top-full z-50 mt-2 hidden max-h-80 overflow-y-auto rounded-2xl border border-slate-200 bg-white py-2 shadow-xl shadow-slate-900/10">
                                    @if($clients->isNotEmpty())
                                        <div class="grid gap-4 border-b border-slate-100 px-4 pb-2 text-[10px] font-black uppercase tracking-wide text-slate-400 sm:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)]">
                                            <span>Name / Company</span>
                                            <span class="sm:text-right">Email / Phone</span>
                                        </div>
                                        @foreach($clients as $client)
                                            <button type="button" data-customer-option data-value="client-{{ $client->id }}"
                                                data-client-id="{{ $client->id }}"
                                                data-name="{{ $client->name }}"
                                                data-email="{{ $client->email }}"
                                                data-phone="{{ $client->phone }}"
                                                data-company="{{ $client->company }}"
                                                data-address="{{ $client->address }}"
                                                class="grid w-full gap-1 px-4 py-3 text-left transition hover:bg-blue-50 focus:bg-blue-50 focus:outline-none sm:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)] sm:items-center sm:gap-4">
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-black text-slate-800">{{ $client->name }}</span>
                                                    <span class="block truncate text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $client->company ?: 'No company' }}</span>
                                                </span>
                                                <span class="min-w-0 sm:text-right">
                                                    <span class="block truncate text-xs font-bold text-slate-600">{{ $client->email ?: 'No email' }}</span>
                                                    <span class="block truncate text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $client->phone ?: 'No phone' }}</span>
                                                </span>
                                            </button>
                                        @endforeach
                                    @else
                                        <div class="px-4 py-6 text-center text-xs font-black uppercase tracking-wide text-slate-400">
                                            No customers found
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="button" id="newCustomerBtn"
                                class="rounded-xl border border-slate-200 bg-slate-100 px-5 py-3 text-xs font-black uppercase tracking-wide text-slate-600 transition hover:bg-slate-200 hover:text-slate-800 md:w-auto">
                                + Add New Customer
                            </button>
                        </div>
                    </div>
                </div>

                <div id="customer_details" class="mt-5 grid grid-cols-1 gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 md:grid-cols-2">
                    <div>
                        <label for="customer_name" class="mb-2 block text-sm font-bold text-slate-700">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $invoice?->customer_name ?? $selectedClient?->name) }}" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400"
                            placeholder="Customer name">
                    </div>
                    <div>
                        <label for="customer_email" class="mb-2 block text-sm font-bold text-slate-700">Email</label>
                        <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', $invoice?->customer_email ?? $selectedClient?->email) }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400"
                            placeholder="customer@example.com">
                    </div>
                    <div>
                        <label for="customer_phone" class="mb-2 block text-sm font-bold text-slate-700">Phone</label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $invoice?->customer_phone ?? $selectedClient?->phone) }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400"
                            placeholder="(123) 456-7890">
                    </div>
                    <div>
                        <label for="customer_company" class="mb-2 block text-sm font-bold text-slate-700">Company</label>
                        <input type="text" id="customer_company" name="customer_company" value="{{ old('customer_company', $invoice?->customer_company ?? $selectedClient?->company) }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400"
                            placeholder="Company name">
                    </div>
                    <div class="md:col-span-2">
                        <label for="customer_address" class="mb-2 block text-sm font-bold text-slate-700">Address</label>
                        <textarea id="customer_address" name="customer_address" rows="2"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400"
                            placeholder="Street, city, state, ZIP">{{ old('customer_address', $invoice?->customer_address ?? $selectedClient?->address) }}</textarea>
                    </div>
                </div>
            </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div id="invoice_details" class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 md:grid-cols-3">
                <div>
                    <label for="service_date" class="mb-2 block text-sm font-bold text-slate-700">Date of Service</label>
                    <input type="date" id="service_date" name="service_date" value="{{ old('service_date', $invoice?->service_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-400">
                </div>

                <div>
                    <label for="due_date" class="mb-2 block text-sm font-bold text-slate-700">Due Date</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $invoice?->due_date?->format('Y-m-d') ?? $invoice?->service_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-400">
                </div>

                <div>
                    <label for="invoice_number" class="mb-2 block text-sm font-bold text-slate-700">Invoice ID</label>
                    <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}" readonly
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black uppercase tracking-wide text-slate-600 outline-none">
                </div>
            </div>
        </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-800">Line Items</h2>
                        <p class="text-xs text-slate-400">Add services, products, or repair items</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500">Tax ({{ number_format($taxRatePercent, 0) }}%):</span>
                        <button type="button" id="btn-enable-all-tax"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-600 hover:text-white"
                            title="Enable tax for all line items">
                            <i class="fas fa-check text-[10px]"></i> Enable All
                        </button>
                        <button type="button" id="btn-disable-all-tax"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-200"
                            title="Disable tax for all line items">
                            <i class="fas fa-ban text-[10px]"></i> Disable All
                        </button>
                    </div>
                </div>

                <div id="line-items" class="space-y-4"></div>

                <div class="mt-5 flex justify-center">
                    <button type="button" id="add-line-item"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-50 px-5 py-2.5 text-xs font-black uppercase tracking-wide text-blue-600 transition hover:bg-blue-600 hover:text-white">
                        <i class="fas fa-plus mr-2"></i> Add New
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-black text-slate-800">Message & Pictures</h2>
                <div class="space-y-4">
                    <div>
                        <label for="message" class="mb-2 block text-sm font-bold text-slate-700">Message</label>
                        <textarea id="message" name="message" rows="4"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400"
                            placeholder="Message shown on invoice">{{ old('message', $invoice?->message) }}</textarea>
                    </div>
                    <div>
                        <label for="pictures" class="mb-2 block text-sm font-bold text-slate-700">Picture & File Upload</label>
                        <p class="mb-2 text-xs text-slate-400">Upload images or documents (PDF, Word, Excel, TXT, etc.)</p>
                        <input type="file" id="pictures" name="pictures[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.rar"
                            class="w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                        <div id="picture-list" class="mt-4 hidden grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"></div>

                        @if($isEdit && count($existingPictures) > 0)
                            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($existingPictures as $path)
                                    @php
                                        $picUrl = $invoice->pictureUrl($path);
                                        $isImg = \App\Models\SalesInvoice::isImageFile($path);
                                        $fileName = \App\Models\SalesInvoice::fileName($path);
                                        $fileIcon = \App\Models\SalesInvoice::fileIconClass($path);
                                    @endphp
                                    <label class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm flex flex-col justify-between">
                                        @if($isImg)
                                            <img src="{{ $picUrl }}" alt="{{ $fileName }}" class="h-32 w-full object-cover">
                                        @else
                                            <div class="flex h-32 w-full flex-col items-center justify-center bg-slate-50 p-3 text-center">
                                                <i class="fas {{ $fileIcon }} text-3xl mb-1"></i>
                                                <span class="max-w-full truncate text-xs font-bold text-slate-700 px-2">{{ $fileName }}</span>
                                                <a href="{{ $picUrl }}" target="_blank" download="{{ $fileName }}" class="mt-1 text-[10px] font-bold text-blue-600 hover:underline">View file</a>
                                            </div>
                                        @endif
                                        <span class="flex items-center gap-2 border-t border-slate-100 p-3 text-xs font-black uppercase tracking-wide text-red-500">
                                            <input type="checkbox" name="remove_pictures[]" value="{{ $path }}" class="rounded border-slate-300 text-red-500 focus:ring-red-400">
                                            Remove
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <aside class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm xl:sticky xl:top-6 xl:self-start">
            <h2 class="mb-5 text-lg font-black text-slate-800">Invoice Summary</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between text-slate-600">
                    <span class="font-semibold">Subtotal</span>
                    <span id="invoice-subtotal" class="font-bold">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span class="font-semibold">Tax ({{ number_format($taxRatePercent, 0) }}%)</span>
                    <span id="invoice-tax" class="font-bold">$0.00</span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="font-black text-slate-800">Total</span>
                    <span id="invoice-total" class="text-2xl font-black text-emerald-600">$0.00</span>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 p-4">
                <label for="payment_method" class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Payment Method</label>
                <select id="payment_method" name="payment_method" required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 outline-none transition focus:border-blue-400">
                    @foreach($paymentMethods as $value => $label)
                        <option value="{{ $value }}" {{ old('payment_method', ($invoice?->payment_method === 'square_card' ? 'square_pos' : ($invoice?->payment_method ?? 'cash'))) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                <label for="payment_type" class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Payment Type</label>
                <select id="payment_type" name="payment_type"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 outline-none transition focus:border-blue-400">
                    <option value="in_store" {{ old('payment_type', $invoice?->payment_type ?? 'in_store') === 'in_store' ? 'selected' : '' }}>In store payment</option>
                    <option value="online_payment" {{ in_array(old('payment_type', $invoice?->payment_type), ['online_payment', 'online']) ? 'selected' : '' }}>online payment</option>
                </select>
            </div>

            <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                <label for="status" class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Status</label>
                <select id="status" name="status"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 outline-none transition focus:border-blue-400">
                    @foreach($statusChoices as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $invoice?->status ?? 'unpaid') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-xs font-semibold leading-relaxed text-slate-500">
                Tax is applied at {{ number_format($taxRatePercent, 0) }}% by default and can be enabled or disabled per item or in bulk using the Line Items header.
            </div>

            <div class="mt-6 space-y-3">
                <button type="submit" name="action" value="save_and_send_square"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                    <i class="fas fa-paper-plane text-xs"></i> Save & Send via Square
                </button>
                <button type="submit" name="action" value="save_and_send_email"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                    <i class="fas fa-envelope text-xs"></i> {{ $submitLabel }}
                </button>
                <a href="{{ $cancelRoute }}"
                    class="block w-full rounded-xl bg-slate-100 px-5 py-3 text-center text-sm font-black text-slate-700 transition hover:bg-slate-200">
                    Cancel
                </a>
            </div>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const taxRate = {{ json_encode((float) $taxRatePercent / 100) }};
        const oldItems = @json($formItems);
        const invoiceCategories = new Set(@json($invoiceCategories->values()));
        const customerSelect = document.getElementById('customer_select');
        const customerPickerButton = document.getElementById('customer_picker_button');
        const customerPickerMenu = document.getElementById('customer_picker_menu');
        const customerPickerName = document.getElementById('customer_picker_name');
        const customerPickerCompany = document.getElementById('customer_picker_company');
        const customerPickerEmail = document.getElementById('customer_picker_email');
        const customerPickerPhone = document.getElementById('customer_picker_phone');
        const customerOptionButtons = Array.from(document.querySelectorAll('[data-customer-option]'));
        const newCustomerButton = document.getElementById('newCustomerBtn');
        const clientIdInput = document.getElementById('client_id');
        const createCustomerInput = document.getElementById('create_customer');
        const customerFields = {
            name: document.getElementById('customer_name'),
            email: document.getElementById('customer_email'),
            phone: document.getElementById('customer_phone'),
            company: document.getElementById('customer_company'),
            address: document.getElementById('customer_address'),
        };
        const lineItemsContainer = document.getElementById('line-items');
        const addLineButton = document.getElementById('add-line-item');
        const pictureInput = document.getElementById('pictures');
        const pictureList = document.getElementById('picture-list');
        let lineIndex = 0;
        let selectedPictures = [];

        const money = (value) => `$${Number(value || 0).toFixed(2)}`;

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const syncPictureInput = () => {
            const dataTransfer = new DataTransfer();
            selectedPictures.forEach((picture) => {
                dataTransfer.items.add(picture.file);
            });
            pictureInput.files = dataTransfer.files;
        };

        const renderPicturePreviews = () => {
            pictureList.replaceChildren();
            pictureList.classList.toggle('hidden', selectedPictures.length === 0);
            pictureList.classList.toggle('grid', selectedPictures.length > 0);

            selectedPictures.forEach((picture) => {
                const card = document.createElement('div');
                card.className = 'overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm flex flex-col justify-between';

                if (picture.isImage && picture.previewUrl) {
                    const preview = document.createElement('img');
                    preview.src = picture.previewUrl;
                    preview.alt = picture.file.name;
                    preview.className = 'h-32 w-full object-cover';
                    card.appendChild(preview);
                } else {
                    const fileBox = document.createElement('div');
                    fileBox.className = 'flex h-32 w-full flex-col items-center justify-center bg-slate-50 p-3 text-center';

                    const ext = (picture.file.name.split('.').pop() || 'FILE').toUpperCase();
                    let iconClass = 'fa-file text-slate-400';
                    if (['PDF'].includes(ext)) iconClass = 'fa-file-pdf text-red-500';
                    else if (['DOC', 'DOCX'].includes(ext)) iconClass = 'fa-file-word text-blue-500';
                    else if (['XLS', 'XLSX', 'CSV'].includes(ext)) iconClass = 'fa-file-excel text-emerald-500';
                    else if (['ZIP', 'RAR', '7Z'].includes(ext)) iconClass = 'fa-file-archive text-amber-500';
                    else if (['TXT'].includes(ext)) iconClass = 'fa-file-alt text-slate-500';

                    fileBox.innerHTML = `
                        <i class="fas ${iconClass} text-3xl mb-1"></i>
                        <span class="max-w-full truncate text-xs font-bold text-slate-700 px-2">${escapeHtml(picture.file.name)}</span>
                        <span class="mt-1 rounded bg-slate-200 px-2 py-0.5 text-[9px] font-black uppercase text-slate-600">${escapeHtml(ext)}</span>
                    `;
                    card.appendChild(fileBox);
                }

                const footer = document.createElement('div');
                footer.className = 'flex items-center gap-2 border-t border-slate-100 p-3';

                const name = document.createElement('p');
                name.className = 'min-w-0 flex-1 truncate text-xs font-bold text-slate-600';
                name.textContent = picture.file.name;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'shrink-0 rounded-lg bg-red-50 px-3 py-2 text-[10px] font-black uppercase text-red-500 transition hover:bg-red-500 hover:text-white';
                removeButton.textContent = 'Remove';
                removeButton.addEventListener('click', () => {
                    if (picture.previewUrl) {
                        URL.revokeObjectURL(picture.previewUrl);
                    }
                    selectedPictures = selectedPictures.filter((item) => item.id !== picture.id);
                    syncPictureInput();
                    renderPicturePreviews();
                });

                footer.append(name, removeButton);
                card.append(footer);
                pictureList.appendChild(card);
            });
        };

        const closeCustomerMenu = () => {
            customerPickerMenu?.classList.add('hidden');
        };

        const setCustomerPickerSelection = (option = null) => {
            const selectedValue = option?.value || option?.dataset.value || '';
            const hasSelection = Boolean(selectedValue);

            customerPickerName.textContent = hasSelection ? (option.dataset.name || 'Selected customer') : 'Select customer';
            customerPickerCompany.textContent = hasSelection ? (option.dataset.company || 'No company') : 'All customers';
            customerPickerEmail.textContent = hasSelection ? (option.dataset.email || 'No email') : 'Email';
            customerPickerPhone.textContent = hasSelection ? (option.dataset.phone || 'No phone') : 'Phone';
            customerPickerName.classList.toggle('text-slate-400', !hasSelection);
            customerPickerName.classList.toggle('text-slate-800', hasSelection);

            customerOptionButtons.forEach((button) => {
                const isSelected = button.dataset.value === selectedValue;
                button.classList.toggle('bg-blue-50', isSelected);
                button.classList.toggle('ring-1', isSelected);
                button.classList.toggle('ring-inset', isSelected);
                button.classList.toggle('ring-blue-100', isSelected);
            });
        };

        const clearCustomerFields = () => {
            customerSelect.value = '';
            clientIdInput.value = '';
            createCustomerInput.value = '1';
            setCustomerPickerSelection();
            closeCustomerMenu();
            Object.values(customerFields).forEach((field) => {
                field.value = '';
                field.readOnly = false;
            });
            customerFields.name.focus();
        };

        const fillCustomerFields = (option) => {
            clientIdInput.value = option?.dataset.clientId || '';
            createCustomerInput.value = '0';

            customerFields.name.value = option?.dataset.name || '';
            customerFields.email.value = option?.dataset.email || '';
            customerFields.phone.value = option?.dataset.phone || '';
            customerFields.company.value = option?.dataset.company || '';
            customerFields.address.value = option?.dataset.address || '';
            Object.values(customerFields).forEach((field) => {
                field.readOnly = false;
            });
        };

        const updateTotals = () => {
            let subtotal = 0;
            let tax = 0;

            lineItemsContainer.querySelectorAll('[data-line-item]').forEach((row) => {
                const quantity = Number(row.querySelector('[data-line-quantity]').value || 0);
                const price = Number(row.querySelector('[data-line-price]').value || 0);
                const taxableInput = row.querySelector('[data-line-taxable-input]');
                const isTaxable = taxableInput ? (taxableInput.value === '1' || taxableInput.value === 'true') : true;

                const lineSubtotal = quantity * price;
                const lineTax = isTaxable ? (lineSubtotal * taxRate) : 0;
                const lineTotal = lineSubtotal + lineTax;

                subtotal += lineSubtotal;
                tax += lineTax;
                row.querySelector('[data-line-subtotal]').textContent = money(lineSubtotal);
                row.querySelector('[data-line-total]').textContent = money(lineTotal);
            });

            document.getElementById('invoice-subtotal').textContent = money(subtotal);
            document.getElementById('invoice-tax').textContent = money(tax);
            document.getElementById('invoice-total').textContent = money(subtotal + tax);
        };

        const renumberRows = () => {
            lineItemsContainer.querySelectorAll('[data-line-item]').forEach((row, index) => {
                row.querySelector('[data-line-number]').textContent = index + 1;
            });
        };

        const closeCategoryMenus = (exceptMenu = null) => {
            lineItemsContainer.querySelectorAll('[data-category-menu]').forEach((menu) => {
                if (menu !== exceptMenu) {
                    menu.classList.add('hidden');
                }
            });
        };

        document.addEventListener('click', (event) => {
            if (!event.target.closest('[data-category-picker]')) {
                closeCategoryMenus();
            }
            if (!event.target.closest('[data-customer-picker]')) {
                closeCustomerMenu();
            }
        });

        const rememberCategory = (value) => {
            const category = String(value || '').trim();

            if (!category || invoiceCategories.has(category)) {
                return;
            }

            invoiceCategories.add(category);
        };

        const categoryOptions = (inputValue = '') => {
            const search = String(inputValue || '').trim().toLowerCase();
            const categories = Array.from(invoiceCategories)
                .map((category) => String(category || '').trim())
                .filter(Boolean)
                .sort((left, right) => left.localeCompare(right));

            const matchingCategories = search
                ? categories.filter((category) => category.toLowerCase().includes(search))
                : categories;

            const hasExactMatch = categories.some((category) => category.toLowerCase() === search);
            const addOption = search && !hasExactMatch
                ? `
                    <button type="button" data-category-option data-category-value="${escapeHtml(inputValue.trim())}"
                        class="flex w-full items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-blue-50 focus:bg-blue-50 focus:outline-none">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-black text-slate-800">${escapeHtml(inputValue.trim())}</span>
                            <span class="block text-[10px] font-bold uppercase tracking-wide text-blue-500">Add new category</span>
                        </span>
                        <i class="fas fa-plus shrink-0 text-xs text-blue-500"></i>
                    </button>
                `
                : '';

            const existingOptions = matchingCategories.map((category) => `
                <button type="button" data-category-option data-category-value="${escapeHtml(category)}"
                    class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-blue-50 focus:bg-blue-50 focus:outline-none">
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-black text-slate-800">${escapeHtml(category)}</span>
                    </span>
                    <i class="fas fa-tag shrink-0 text-[10px] text-slate-300"></i>
                </button>
            `).join('');

            if (!addOption && !existingOptions) {
                return `
                    <div class="px-4 py-5 text-center text-xs font-black uppercase tracking-wide text-slate-300">
                        No invoice categories yet
                    </div>
                `;
            }

            return addOption + existingOptions;
        };

        const renderCategoryMenu = (menu, input) => {
            menu.innerHTML = categoryOptions(input.value);

            menu.querySelectorAll('[data-category-option]').forEach((button) => {
                button.addEventListener('click', () => {
                    input.value = button.dataset.categoryValue || '';
                    rememberCategory(input.value);
                    menu.classList.add('hidden');
                });
            });
        };

        const addLineItem = (item = {}) => {
            const index = lineIndex;
            lineIndex += 1;

            const isTaxable = item.taxable !== undefined
                ? Boolean(item.taxable && item.taxable !== '0' && item.taxable !== 0 && item.taxable !== 'false')
                : true;

            const row = document.createElement('div');
            row.className = 'rounded-2xl border border-slate-100 bg-slate-50 p-4';
            row.dataset.lineItem = 'true';
            row.innerHTML = `
                <input type="hidden" name="items[${index}][inventory_id]" value="${item.inventory_id || ''}">
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-black text-slate-700">Item <span data-line-number></span></p>
                    <button type="button" data-remove-line class="rounded-lg bg-white px-3 py-2 text-xs font-black text-red-400 transition hover:bg-red-50 hover:text-red-600">
                        <i class="fas fa-trash mr-1"></i> Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <div class="relative lg:col-span-4" data-category-picker>
                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Add Or Select Category</label>
                        <div class="relative">
                            <input type="text" name="items[${index}][invoice_category]" data-line-category required autocomplete="off"
                                class="h-[46px] w-full rounded-xl border border-slate-200 bg-white px-3 pr-10 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-400"
                                placeholder="Select or type category">
                            <button type="button" data-category-trigger
                                class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-300 transition hover:bg-slate-100 hover:text-slate-500">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </button>
                        </div>
                        <div data-category-menu class="absolute left-0 right-0 top-full z-40 mt-2 hidden max-h-72 overflow-y-auto rounded-2xl border border-slate-200 bg-white py-2 shadow-xl shadow-slate-900/10"></div>
                    </div>
                    <div class="lg:col-span-8">
                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Description</label>
                        <input type="text" name="items[${index}][description]" data-line-description required
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-400"
                            placeholder="Service or item description">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Qty</label>
                        <input type="number" name="items[${index}][quantity]" data-line-quantity min="1" value="1" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm font-black text-slate-700 outline-none focus:border-blue-400">
                    </div>
                    <div class="lg:col-span-3">
                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Price</label>
                        <input type="number" name="items[${index}][unit_price]" data-line-price min="0" step="0.01" value="0.00" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm font-black text-slate-700 outline-none focus:border-blue-400">
                    </div>
                    <div class="lg:col-span-2">
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-xs font-black uppercase tracking-wide text-slate-400">Tax</label>
                            <span data-tax-badge class="text-[10px] font-black uppercase tracking-wide ${isTaxable ? 'text-emerald-600' : 'text-slate-400'}">
                                ${isTaxable ? 'Tax (8%)' : 'No Tax'}
                            </span>
                        </div>
                        <div data-tax-box class="flex h-[46px] cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white px-3 transition hover:border-blue-400 select-none"
                            title="Click to enable or disable tax for this item">
                            <input type="hidden" name="items[${index}][taxable]" data-line-taxable-input value="${isTaxable ? '1' : '0'}">
                            <span data-tax-rate-text class="text-sm font-black ${isTaxable ? 'text-slate-800' : 'text-slate-400'}">
                                ${isTaxable ? '8%' : '0%'}
                            </span>
                            <button type="button" data-tax-toggle
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${isTaxable ? 'bg-blue-600' : 'bg-slate-300'}"
                                role="switch" aria-checked="${isTaxable ? 'true' : 'false'}" title="Enable or disable tax for this item">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${isTaxable ? 'translate-x-5' : 'translate-x-0'}"></span>
                            </button>
                        </div>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Subtotal</label>
                        <div data-line-subtotal class="flex h-[46px] items-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-black text-slate-700">$0.00</div>
                    </div>
                    <div class="lg:col-span-3">
                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-400">Total</label>
                        <div data-line-total class="flex h-[46px] items-center rounded-xl border border-emerald-100 bg-emerald-50 px-3 text-sm font-black text-emerald-700">$0.00</div>
                    </div>
                </div>
            `;

            lineItemsContainer.appendChild(row);

            const categoryInput = row.querySelector('[data-line-category]');
            const categoryTrigger = row.querySelector('[data-category-trigger]');
            const categoryMenu = row.querySelector('[data-category-menu]');
            const descriptionInput = row.querySelector('[data-line-description]');
            const quantityInput = row.querySelector('[data-line-quantity]');
            const priceInput = row.querySelector('[data-line-price]');
            const taxBox = row.querySelector('[data-tax-box]');
            const taxToggle = row.querySelector('[data-tax-toggle]');
            const taxableInput = row.querySelector('[data-line-taxable-input]');
            const taxRateText = row.querySelector('[data-tax-rate-text]');
            const taxBadge = row.querySelector('[data-tax-badge]');
            const knob = taxToggle.querySelector('span');

            const setTaxState = (taxable) => {
                taxableInput.value = taxable ? '1' : '0';
                taxToggle.setAttribute('aria-checked', taxable ? 'true' : 'false');
                if (taxable) {
                    taxToggle.classList.remove('bg-slate-300');
                    taxToggle.classList.add('bg-blue-600');
                    knob.classList.remove('translate-x-0');
                    knob.classList.add('translate-x-5');
                    taxRateText.textContent = '8%';
                    taxRateText.classList.remove('text-slate-400');
                    taxRateText.classList.add('text-slate-800');
                    taxBadge.textContent = 'Tax (8%)';
                    taxBadge.classList.remove('text-slate-400');
                    taxBadge.classList.add('text-emerald-600');
                } else {
                    taxToggle.classList.remove('bg-blue-600');
                    taxToggle.classList.add('bg-slate-300');
                    knob.classList.remove('translate-x-5');
                    knob.classList.add('translate-x-0');
                    taxRateText.textContent = '0%';
                    taxRateText.classList.remove('text-slate-800');
                    taxRateText.classList.add('text-slate-400');
                    taxBadge.textContent = 'No Tax';
                    taxBadge.classList.remove('text-emerald-600');
                    taxBadge.classList.add('text-slate-400');
                }
                updateTotals();
            };

            row.setTaxState = setTaxState;

            taxBox.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const current = taxableInput.value === '1';
                setTaxState(!current);
            });

            categoryTrigger.addEventListener('click', (event) => {
                event.stopPropagation();
                const willOpen = categoryMenu.classList.contains('hidden');
                closeCategoryMenus(categoryMenu);
                renderCategoryMenu(categoryMenu, categoryInput);
                categoryMenu.classList.toggle('hidden', !willOpen);
            });

            categoryInput.addEventListener('focus', () => {
                closeCategoryMenus(categoryMenu);
                renderCategoryMenu(categoryMenu, categoryInput);
                categoryMenu.classList.remove('hidden');
            });

            categoryInput.addEventListener('input', () => {
                renderCategoryMenu(categoryMenu, categoryInput);
                categoryMenu.classList.remove('hidden');
            });

            categoryInput.addEventListener('change', () => {
                rememberCategory(categoryInput.value);
                renderCategoryMenu(categoryMenu, categoryInput);
            });

            [descriptionInput, quantityInput, priceInput].forEach((field) => {
                field.addEventListener('input', updateTotals);
                field.addEventListener('change', updateTotals);
            });

            row.querySelector('[data-remove-line]').addEventListener('click', () => {
                if (lineItemsContainer.querySelectorAll('[data-line-item]').length === 1) {
                    return;
                }
                row.remove();
                renumberRows();
                updateTotals();
            });

            if (item.invoice_category) {
                categoryInput.value = item.invoice_category;
                rememberCategory(item.invoice_category);
            }
            if (item.description) {
                descriptionInput.value = item.description;
            }
            if (item.quantity) {
                quantityInput.value = item.quantity;
            }
            if (item.unit_price) {
                priceInput.value = Number(item.unit_price).toFixed(2);
            }
            renumberRows();
            updateTotals();
        };

        const btnEnableAllTax = document.getElementById('btn-enable-all-tax');
        const btnDisableAllTax = document.getElementById('btn-disable-all-tax');

        if (btnEnableAllTax) {
            btnEnableAllTax.addEventListener('click', () => {
                lineItemsContainer.querySelectorAll('[data-line-item]').forEach((row) => {
                    if (typeof row.setTaxState === 'function') {
                        row.setTaxState(true);
                    }
                });
            });
        }

        if (btnDisableAllTax) {
            btnDisableAllTax.addEventListener('click', () => {
                lineItemsContainer.querySelectorAll('[data-line-item]').forEach((row) => {
                    if (typeof row.setTaxState === 'function') {
                        row.setTaxState(false);
                    }
                });
            });
        };

        customerSelect.addEventListener('change', () => {
            const option = customerSelect.selectedOptions[0];
            if (!option || !option.value) {
                clientIdInput.value = '';
                createCustomerInput.value = '0';
                setCustomerPickerSelection();
                return;
            }

            fillCustomerFields(option);
            setCustomerPickerSelection(option);
        });

        customerPickerButton.addEventListener('click', (event) => {
            event.stopPropagation();
            customerPickerMenu.classList.toggle('hidden');
        });

        customerOptionButtons.forEach((button) => {
            button.addEventListener('click', () => {
                customerSelect.value = button.dataset.value;
                fillCustomerFields(button);
                setCustomerPickerSelection(button);
                closeCustomerMenu();
            });
        });

        newCustomerButton.addEventListener('click', () => {
            clearCustomerFields();
        });

        Object.values(customerFields).forEach((field) => {
            field.addEventListener('input', () => {
                if (!clientIdInput.value) {
                    createCustomerInput.value = '1';
                }
            });
        });

        addLineButton.addEventListener('click', () => addLineItem());

        pictureInput.addEventListener('change', () => {
            Array.from(pictureInput.files || []).forEach((file) => {
                const isImage = file.type.startsWith('image/');
                selectedPictures.push({
                    id: `${file.name}-${file.size}-${file.lastModified}-${Date.now()}-${Math.random()}`,
                    file,
                    isImage,
                    previewUrl: isImage ? URL.createObjectURL(file) : null,
                });
            });
            syncPictureInput();
            renderPicturePreviews();
        });

        if (oldItems.length > 0) {
            oldItems.forEach((item) => addLineItem(item));
        } else {
            addLineItem();
        }

        if (customerFields.name.value) {
            createCustomerInput.value = clientIdInput.value ? '0' : createCustomerInput.value;
        }

        const serviceDateInput = document.getElementById('service_date');
        const dueDateInput = document.getElementById('due_date');
        let dueDateTouched = false;
        if (dueDateInput) {
            dueDateInput.addEventListener('input', () => {
                dueDateTouched = true;
            });
        }
        if (serviceDateInput && dueDateInput) {
            serviceDateInput.addEventListener('change', () => {
                if (!dueDateTouched && !{{ $isEdit ? 'true' : 'false' }}) {
                    dueDateInput.value = serviceDateInput.value;
                }
            });
        }

        setCustomerPickerSelection(customerSelect.selectedOptions[0]);
    });
</script>
@endpush
