@extends('layouts.admin')

@section('content')
    @php
        $repairStatuses = \App\Models\RepairEntry::statusChoices();
        $statusClass = \App\Models\RepairEntry::statusBadgeClass($entry->status);
        $parsedMedia = $entry->parsed_photos;
        $imageUrls = array_values(array_map(fn($m) => $m['url'], $entry->image_photos));
        $galleryJson = json_encode($imageUrls);

        $clientName = $entry->customer_name ?: ($entry->client?->name ?: ($entry->user?->name ?? 'Walk-in Customer'));
        $clientPhone = $entry->customer_phone ?: ($entry->client?->phone ?: 'N/A');
        $clientEmail = $entry->customer_email ?: ($entry->client?->email ?: 'N/A');
        $clientCompany = $entry->client?->company ?: 'N/A';
        $clientAddress = $entry->client?->address ?: 'N/A';

        $repairLabelData = [
            'barcode' => $entry->item_barcode ?: ($entry->serial_number ?: 'REP-' . $entry->id),
            'barcodeImage' => $labelBarcodeDataUri ?? null,
            'technician' => $entry->technician ?: 'N/A',
            'customerName' => $clientName,
            'customerPhone' => $clientPhone,
            'customerEmail' => $clientEmail,
            'deviceType' => $entry->device_type ?: 'Repair Item',
            'brandModel' => trim(($entry->brand_category ? $entry->brand_category . ' ' : '') . ($entry->item_description ?: '')),
            'serialNumber' => $entry->serial_number ?: 'N/A',
            'location' => $entry->put_away_location ?: 'N/A',
            'palletInfo' => $entry->pallet_info ?: 'N/A',
            'notes' => $entry->notes ?: 'N/A',
        ];
    @endphp

    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500 space-y-6">

        {{-- Top Header & Actions --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div>
                <nav class="flex items-center text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 mb-2">
                    <a href="{{ route('admin.repair-intake.index') }}" class="hover:text-blue-800 transition-colors">Repair Intake</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-500 tracking-widest">Entry Details</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <i class="fas fa-wrench text-amber-500"></i> Repair Entry #{{ $entry->id }}
                    </h1>
                    <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $statusClass }}">
                        {{ $entry->status_label }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($entry->sales_invoice)
                    <a href="{{ route('admin.sales.invoices.show', $entry->sales_invoice) }}"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-md shadow-emerald-500/20 transition-all"
                        title="View Invoice #{{ $entry->sales_invoice->invoice_number }}">
                        <i class="fas fa-check-circle mr-2"></i> Invoice Already Created
                        <span class="ml-2 px-2 py-0.5 rounded-md bg-white/20 text-[10px] font-mono">{{ $entry->sales_invoice->invoice_number }}</span>
                    </a>
                @else
                    <a href="{{ route('admin.sales.invoices.create', ['repair_id' => $entry->id]) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-md shadow-blue-500/20 transition-all">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> Create Invoice
                    </a>
                @endif
                <button type="button" onclick="printRepairViewLabel()"
                    class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-sm transition-all">
                    <i class="fas fa-tag mr-2"></i> Print Label
                </button>
                @if($entry->sales_invoice)
                    <a href="{{ route('admin.sales.invoices.print', $entry->sales_invoice) }}?autoprint=1" target="_blank" rel="noopener"
                        class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all shadow-sm"
                        title="Print Invoice #{{ $entry->sales_invoice->invoice_number }}">
                        <i class="fas fa-print mr-2 text-slate-400"></i> Print Invoice
                    </a>
                @else
                    <a href="{{ route('admin.repair.entries.invoice-print', $entry->id) }}?autoprint=1" target="_blank" rel="noopener"
                        class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all shadow-sm">
                        <i class="fas fa-print mr-2 text-slate-400"></i> Print Invoice
                    </a>
                @endif
                <a href="{{ route('admin.repair.entries.edit', $entry->id) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all shadow-md shadow-amber-500/20">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.repair-intake.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <form action="{{ route('admin.repair.entries.destroy', $entry->id) }}" method="POST" class="inline-flex"
                    onsubmit="return confirm('Are you sure you want to delete this repair entry?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 hover:bg-red-600 hover:text-white border border-red-200 text-red-600 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-700">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
            </div>
        @endif

        {{-- SECTION 1: Pallet Barcode Entry --}}
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-4">
            <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                <i class="fas fa-pallet text-blue-600"></i> Pallet Barcode Entry
            </h4>

            <div class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-black">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pallet Barcode</div>
                        <div class="text-xl font-mono font-extrabold text-slate-800">{{ $entry->pallet_info ?: 'No Pallet Assigned' }}</div>
                        @if ($entry->pallet?->put_away_location)
                            <div class="text-xs text-slate-500 mt-0.5">
                                <i class="fas fa-map-marker-alt text-amber-500 mr-1"></i> Location: {{ $entry->pallet->put_away_location }}
                            </div>
                        @endif
                    </div>
                </div>

                @if (!empty($palletBarcodeDataUri))
                    <div class="text-center p-2 bg-slate-50 border border-slate-100 rounded-xl">
                        <img src="{{ $palletBarcodeDataUri }}" alt="{{ $entry->pallet_info }}" class="h-12 mx-auto">
                        <div class="text-[10px] font-mono text-slate-600 font-bold mt-1">{{ $entry->pallet_info }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- SECTION 2: Client / Vendor Information --}}
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-5">
            <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                <i class="fas fa-user-tag text-blue-600"></i> Client / Vendor Information
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Intake Date / Entry Date --}}
                <div class="bg-white p-4 rounded-xl border border-slate-200">
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Intake Date / Entry Date</label>
                    <div class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-500"></i>
                        {{ $entry->created_at ? $entry->created_at->format('M d, Y h:i A') : 'N/A' }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Denver: {{ $entry->formatCreatedAtDenver('m/d/Y, h:i A T') }}</div>
                </div>

                {{-- Job # --}}
                <div class="bg-white p-4 rounded-xl border border-slate-200">
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Job #</label>
                    <div class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-briefcase text-blue-500"></i>
                        @if ($entry->job_no)
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-sm font-extrabold border border-blue-100">
                                #{{ $entry->job_no }}
                            </span>
                        @else
                            <span class="text-slate-400">N/A</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Client / Vendor Profile Details --}}
            <div class="bg-white p-5 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div>
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Client / Vendor</label>
                    <div class="font-bold text-slate-800 text-base mt-0.5">{{ $clientName }}</div>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Company Name</label>
                    <div class="font-medium text-slate-700 mt-0.5">{{ $clientCompany }}</div>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Phone Number</label>
                    <div class="font-medium text-slate-700 mt-0.5">{{ $clientPhone }}</div>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Email Address</label>
                    <div class="font-medium text-slate-700 mt-0.5 truncate">{{ $clientEmail }}</div>
                </div>
                @if ($clientAddress && $clientAddress !== 'N/A')
                    <div class="md:col-span-2 lg:col-span-4 border-t border-slate-100 pt-3">
                        <label class="text-[11px] font-bold uppercase text-slate-400 block">Address</label>
                        <div class="font-medium text-slate-700 mt-0.5">{{ $clientAddress }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- SECTION 3: Item Details (Repair Items Style) --}}
        <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 space-y-6">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h4 class="text-[15px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-tools text-amber-500"></i> <span>Repair Item Details</span>
                </h4>
                <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-black border border-amber-200">
                    {{ count($entry->items_list) }} {{ count($entry->items_list) > 1 ? 'Items' : 'Item' }}
                </span>
            </div>

            <div class="space-y-4">
                @foreach ($entry->items_list as $idx => $item)
                    @php
                        $itemBarcode = !empty($item['barcode']) ? $item['barcode'] : ($idx === 0 ? ($entry->item_barcode ?: 'REP-' . mt_rand(1000000000, 9999999999)) : 'REP-' . mt_rand(1000000000, 9999999999));
                        $itemSerial = !empty($item['serial']) ? $item['serial'] : ($idx === 0 ? $entry->serial_number : 'N/A');
                    @endphp
                    <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-800 text-white text-xs font-black flex items-center justify-center">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-extrabold text-sm text-slate-800">
                                    {{ $item['name'] ?: 'Repair Item' }}
                                </span>
                            </div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase">Item #{{ $idx + 1 }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Item Name --}}
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <label class="text-[11px] font-bold uppercase text-slate-400 block">Item Name</label>
                                <div class="font-bold text-slate-900 text-base mt-1">{{ $item['name'] ?: 'Repair Item' }}</div>
                            </div>

                            {{-- Brand --}}
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <label class="text-[11px] font-bold uppercase text-slate-400 block">Brand</label>
                                <div class="font-bold text-slate-800 text-base mt-1">{{ $item['brand'] ?: 'N/A' }}</div>
                            </div>

                            {{-- Model --}}
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <label class="text-[11px] font-bold uppercase text-slate-400 block">Model</label>
                                <div class="font-bold text-slate-800 text-base mt-1">{{ $item['model'] ?: 'N/A' }}</div>
                            </div>

                            {{-- Serial Number --}}
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <label class="text-[11px] font-bold uppercase text-slate-400 block">Serial Number</label>
                                <div class="font-mono font-bold text-slate-800 text-base mt-1">{{ $itemSerial ?: 'N/A' }}</div>
                            </div>
                        </div>

                        {{-- Item Barcode & Print Action --}}
                        <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Item Barcode</div>
                                    <div class="text-base font-mono font-extrabold text-blue-700">{{ $itemBarcode }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button"
                                    onclick="printSpecificRepairItemLabel('{{ addslashes($item['name'] ?? 'Repair Item') }}', '{{ addslashes($item['brand'] ?? '') }}', '{{ addslashes($item['model'] ?? '') }}', '{{ addslashes($itemSerial ?? '') }}', '{{ $itemBarcode }}')"
                                    class="bg-[#2563eb] hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-bold text-xs flex items-center shadow-md">
                                    <i class="fas fa-print mr-2"></i> Print Serial Label
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION 4: Warehouse & Receiving --}}
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-5">
            <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-blue-600"></i> Warehouse & Receiving
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Put-away Location --}}
                <div class="bg-white p-4 rounded-xl border border-slate-200">
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Put-away Location</label>
                    <div class="font-bold text-amber-700 text-sm mt-1 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-amber-500"></i> {{ $entry->put_away_location ?: 'N/A' }}
                    </div>
                </div>

                {{-- Marketing Reference --}}
                <div class="bg-white p-4 rounded-xl border border-slate-200">
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Marketing Reference</label>
                    <div class="font-bold text-slate-700 text-sm mt-1">
                        {{ $entry->marketing_ref ?: 'No reference' }}
                    </div>
                </div>

                {{-- Receiving Employee --}}
                <div class="bg-white p-4 rounded-xl border border-slate-200">
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Receiving Employee</label>
                    <div class="font-bold text-slate-800 text-sm mt-1 flex items-center gap-1.5">
                        <i class="fas fa-user-check text-emerald-500"></i> {{ $entry->receiving_employee ?: 'N/A' }}
                    </div>
                </div>
            </div>

            {{-- Receiving Notes --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200">
                <label class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Receiving Notes</label>
                <div class="text-sm text-slate-700 leading-relaxed font-medium">
                    {{ $entry->receiving_notes ?: 'No receiving notes recorded.' }}
                </div>
            </div>

            {{-- Item Photos and Files --}}
            <div class="space-y-3 pt-2 border-t border-slate-200/60">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-slate-700">Item photos and files</label>
                    <span class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-1 rounded-full">
                        {{ count($parsedMedia) }} attached
                    </span>
                </div>

                @if (!empty($parsedMedia))
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach ($parsedMedia as $index => $media)
                            @if ($media['is_image'])
                                <div class="aspect-square rounded-xl border border-slate-200 overflow-hidden bg-white shadow-sm cursor-pointer group relative"
                                    onclick="openImageGallery({{ $galleryJson }}, 'Repair Entry #{{ $entry->id }}')"
                                    title="Click to zoom image">
                                    <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                        onerror="this.src='/images/placeholder.png'">
                                    <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                        <i class="fas fa-search-plus"></i>
                                        <span>View</span>
                                    </div>
                                    <div class="absolute bottom-1 left-1 right-1 pointer-events-none">
                                        <span class="text-[9px] font-bold bg-black/60 text-white px-1.5 py-0.5 rounded truncate block text-center">
                                            {{ $media['name'] }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="aspect-square rounded-xl border border-slate-200 bg-slate-50 hover:bg-white shadow-sm p-3 flex flex-col justify-between items-center text-center cursor-pointer group transition-all hover:border-blue-400 hover:shadow-md"
                                    onclick="openDocumentFile('{{ $media['raw'] }}', '{{ $media['name'] }}')"
                                    title="Click to view/download {{ $media['name'] }}">
                                    <div class="w-full flex justify-end">
                                        <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded border {{ $media['badge_class'] }}">
                                            {{ $media['label'] }}
                                        </span>
                                    </div>
                                    <div class="my-auto flex flex-col items-center px-1">
                                        <i class="{{ $media['icon'] }} text-3xl mb-1.5 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-xs font-bold text-slate-800 line-clamp-2 break-all group-hover:text-blue-600 leading-tight">
                                            {{ $media['name'] }}
                                        </span>
                                    </div>
                                    <div class="w-full text-center">
                                        <span class="text-[10px] font-bold text-blue-600 group-hover:underline inline-flex items-center gap-1">
                                            <i class="fas fa-external-link-alt text-[9px]"></i> Open
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border-2 border-dashed border-slate-200 rounded-xl p-8 text-center text-slate-400 text-xs font-bold">
                        <i class="fas fa-folder-open text-2xl mb-2 text-slate-300 block"></i>
                        No photos or files attached to this entry.
                    </div>
                @endif
            </div>
        </div>

        {{-- SECTION 5: For Technician Use Only --}}
        <div class="bg-indigo-50/40 border border-indigo-100 rounded-2xl p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
                <h4 class="text-[14px] font-bold text-indigo-950 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-user-cog text-indigo-600"></i> For Technician use only section
                </h4>
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">Tech Section</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Tech Name --}}
                <div class="bg-white p-4 rounded-xl border border-indigo-100">
                    <label class="text-[11px] font-bold uppercase text-slate-400 block">Tech Name</label>
                    <div class="font-bold text-indigo-950 text-base mt-1 flex items-center gap-2">
                        <i class="fas fa-user-cog text-indigo-500"></i> {{ $entry->technician ?: 'N/A' }}
                    </div>
                </div>

                {{-- Repair Status & Quick Update --}}
                <div class="bg-white p-4 rounded-xl border border-indigo-100">
                    <label class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Repair Status</label>
                    <form action="{{ route('admin.repair.entries.update-status', $entry->id) }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-indigo-500/20 bg-white">
                            @foreach ($repairStatuses as $statusVal => $statusText)
                                <option value="{{ $statusVal }}" {{ $entry->status === $statusVal ? 'selected' : '' }}>
                                    {{ $statusText }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm">
                            Update
                        </button>
                    </form>
                </div>
            </div>

            {{-- Technician Notes --}}
            <div class="bg-white p-4 rounded-xl border border-indigo-100">
                <label class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Technician Notes</label>
                <div class="text-sm text-slate-800 leading-relaxed font-medium">
                    {{ $entry->technician_notes ?: 'No technician notes recorded.' }}
                </div>
            </div>
        </div>

    </div>

    @include('admin.partials.image-gallery-modal')

@endsection

@push('scripts')
<script>
    function openDocumentFile(dataUriOrUrl, fileName = 'document') {
        if (!dataUriOrUrl) return;

        if (!dataUriOrUrl.startsWith('data:')) {
            window.open(dataUriOrUrl, '_blank');
            return;
        }

        try {
            const parts = dataUriOrUrl.split(';base64,');
            const contentType = parts[0].split(':')[1] || 'application/octet-stream';
            const byteCharacters = atob(parts[1]);
            const byteArrays = [];

            const sliceSize = 512;
            for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                const slice = byteCharacters.slice(offset, offset + sliceSize);
                const byteNumbers = new Array(slice.length);
                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                byteArrays.push(byteArray);
            }

            const blob = new Blob(byteArrays, { type: contentType });
            const blobUrl = URL.createObjectURL(blob);

            if (contentType.includes('pdf')) {
                window.open(blobUrl, '_blank');
            } else {
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        } catch (e) {
            console.error('Error opening file:', e);
            const win = window.open();
            if (win) {
                win.document.write('<iframe src="' + dataUriOrUrl + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
            }
        }
    }

    function escapeRepairLabelHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function printRepairViewLabel() {
        const data = @json($repairLabelData);
        const barcode = data.barcode || 'REP-{{ $entry->id }}';
        const barcodeSrc = data.barcodeImage || `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(barcode)}&scale=2&height=10&includetext=false`;
        const printedAt = `Printed: ${new Date().toLocaleString('en-US', {
            timeZone: 'America/Denver',
            month: '2-digit',
            day: '2-digit',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            timeZoneName: 'short',
        })}`;

        const printWindow = window.open('', '_blank', 'width=420,height=320');
        if (!printWindow) {
            alert('Allow pop-ups to print the label.');
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Repair Label</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    @page { size: 3in 3in; margin: 0; }
                    body { font-family: Arial, Helvetica, sans-serif; width: 3in; height: 3in; background: white; color: #111827; }
                    .label-container { width: 3in; height: 3in; margin: 0 auto; background: white; padding: 0.08in; border: 1px solid #111; display: flex; flex-direction: column; overflow: hidden; }
                    .label-header { border-bottom: 1px solid #0071e3; padding-bottom: 0.035in; margin-bottom: 0.04in; }
                    .label-header h1 { font-size: 9px; color: #111827; font-weight: 900; letter-spacing: 0; line-height: 1; }
                    .barcode-section { text-align: center; padding: 0.035in 0.04in 0.025in; background: #f8fafc; border: 1px solid #d1d5db; }
                    .barcode-section img { max-width: 100%; height: auto; max-height: 0.32in; margin-bottom: 0.02in; }
                    .barcode-number { font-family: "Courier New", monospace; font-size: 7px; font-weight: bold; letter-spacing: 0; color: #111827; line-height: 1; }
                    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.025in 0.05in; margin-top: 0.04in; flex: 1; min-height: 0; }
                    .detail-item { min-width: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.012in; }
                    .detail-item.wide { grid-column: 1 / -1; }
                    .detail-label { display: block; font-weight: bold; color: #0071e3; text-transform: uppercase; letter-spacing: 0; font-size: 5.5px; line-height: 1.05; }
                    .detail-value { display: block; font-weight: 600; color: #111827; font-size: 7px; line-height: 1.12; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                    .footer { text-align: center; margin-top: 0.025in; padding-top: 0.018in; border-top: 1px dashed #ccc; font-size: 5.5px; color: #6b7280; line-height: 1; }
                    @media print { html, body { width: 3in; height: 3in; background: white; } }
                </style>
            </head>
            <body>
                <div class="label-container">
                    <div class="label-header"><h1>REPAIR LABEL</h1></div>
                    <div class="barcode-section">
                        <img src="${escapeRepairLabelHtml(barcodeSrc)}" alt="Barcode">
                        <div class="barcode-number">${escapeRepairLabelHtml(barcode)}</div>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item wide"><span class="detail-label">Customer</span><span class="detail-value">${escapeRepairLabelHtml(data.customerName)}</span></div>
                        <div class="detail-item"><span class="detail-label">Phone</span><span class="detail-value">${escapeRepairLabelHtml(data.customerPhone)}</span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value">${escapeRepairLabelHtml(data.customerEmail)}</span></div>
                        <div class="detail-item wide"><span class="detail-label">Item / Device</span><span class="detail-value">${escapeRepairLabelHtml(data.deviceType)}</span></div>
                        <div class="detail-item"><span class="detail-label">Brand & Model</span><span class="detail-value">${escapeRepairLabelHtml(data.brandModel)}</span></div>
                        <div class="detail-item"><span class="detail-label">Serial #</span><span class="detail-value">${escapeRepairLabelHtml(data.serialNumber)}</span></div>
                        <div class="detail-item"><span class="detail-label">Pallet #</span><span class="detail-value">${escapeRepairLabelHtml(data.palletInfo)}</span></div>
                        <div class="detail-item"><span class="detail-label">Location</span><span class="detail-value">${escapeRepairLabelHtml(data.location)}</span></div>
                        <div class="detail-item wide"><span class="detail-label">Notes</span><span class="detail-value">${escapeRepairLabelHtml(String(data.notes || '').substring(0, 56))}</span></div>
                        <div class="detail-item wide"><span class="detail-label">Technician</span><span class="detail-value">${escapeRepairLabelHtml(data.technician)}</span></div>
                    </div>
                    <div class="footer">${escapeRepairLabelHtml(printedAt)}</div>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => printWindow.print(), 300);
    }

    function printSpecificRepairItemLabel(name, brand, model, serial, barcode) {
        const baseData = @json($repairLabelData);
        const data = Object.assign({}, baseData);
        data.deviceType = name || data.deviceType;
        data.brandModel = (brand ? brand + ' ' : '') + (model || '');
        data.serialNumber = (serial && serial !== 'N/A') ? serial : data.serialNumber;
        data.barcode = barcode || data.barcode;
        data.barcodeImage = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(data.barcode)}&scale=2&height=10&includetext=false`;

        const barcodeSrc = data.barcodeImage;
        const printedAt = `Printed: ${new Date().toLocaleString('en-US', {
            timeZone: 'America/Denver',
            month: '2-digit',
            day: '2-digit',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            timeZoneName: 'short',
        })}`;

        const printWindow = window.open('', '_blank', 'width=420,height=320');
        if (!printWindow) {
            alert('Allow pop-ups to print the label.');
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Repair Label</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    @page { size: 3in 3in; margin: 0; }
                    body { font-family: Arial, Helvetica, sans-serif; width: 3in; height: 3in; background: white; color: #111827; }
                    .label-container { width: 3in; height: 3in; margin: 0 auto; background: white; padding: 0.08in; border: 1px solid #111; display: flex; flex-direction: column; overflow: hidden; }
                    .label-header { border-bottom: 1px solid #0071e3; padding-bottom: 0.035in; margin-bottom: 0.04in; }
                    .label-header h1 { font-size: 9px; color: #111827; font-weight: 900; letter-spacing: 0; line-height: 1; }
                    .barcode-section { text-align: center; padding: 0.035in 0.04in 0.025in; background: #f8fafc; border: 1px solid #d1d5db; }
                    .barcode-section img { max-width: 100%; height: auto; max-height: 0.32in; margin-bottom: 0.02in; }
                    .barcode-number { font-family: "Courier New", monospace; font-size: 7px; font-weight: bold; letter-spacing: 0; color: #111827; line-height: 1; }
                    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.025in 0.05in; margin-top: 0.04in; flex: 1; min-height: 0; }
                    .detail-item { min-width: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.012in; }
                    .detail-item.wide { grid-column: 1 / -1; }
                    .detail-label { display: block; font-weight: bold; color: #0071e3; text-transform: uppercase; letter-spacing: 0; font-size: 5.5px; line-height: 1.05; }
                    .detail-value { display: block; font-weight: 600; color: #111827; font-size: 7px; line-height: 1.12; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                    .footer { text-align: center; margin-top: 0.025in; padding-top: 0.018in; border-top: 1px dashed #ccc; font-size: 5.5px; color: #6b7280; line-height: 1; }
                    @media print { html, body { width: 3in; height: 3in; background: white; } }
                </style>
            </head>
            <body>
                <div class="label-container">
                    <div class="label-header"><h1>REPAIR LABEL</h1></div>
                    <div class="barcode-section">
                        <img src="${escapeRepairLabelHtml(barcodeSrc)}" alt="Barcode">
                        <div class="barcode-number">${escapeRepairLabelHtml(data.barcode)}</div>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item wide"><span class="detail-label">Customer</span><span class="detail-value">${escapeRepairLabelHtml(data.customerName)}</span></div>
                        <div class="detail-item"><span class="detail-label">Phone</span><span class="detail-value">${escapeRepairLabelHtml(data.customerPhone)}</span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value">${escapeRepairLabelHtml(data.customerEmail)}</span></div>
                        <div class="detail-item wide"><span class="detail-label">Item / Device</span><span class="detail-value">${escapeRepairLabelHtml(data.deviceType)}</span></div>
                        <div class="detail-item"><span class="detail-label">Brand & Model</span><span class="detail-value">${escapeRepairLabelHtml(data.brandModel)}</span></div>
                        <div class="detail-item"><span class="detail-label">Serial #</span><span class="detail-value">${escapeRepairLabelHtml(data.serialNumber)}</span></div>
                        <div class="detail-item"><span class="detail-label">Pallet #</span><span class="detail-value">${escapeRepairLabelHtml(data.palletInfo)}</span></div>
                        <div class="detail-item"><span class="detail-label">Location</span><span class="detail-value">${escapeRepairLabelHtml(data.location)}</span></div>
                        <div class="detail-item wide"><span class="detail-label">Notes</span><span class="detail-value">${escapeRepairLabelHtml(String(data.notes || '').substring(0, 56))}</span></div>
                        <div class="detail-item wide"><span class="detail-label">Technician</span><span class="detail-value">${escapeRepairLabelHtml(data.technician)}</span></div>
                    </div>
                    <div class="footer">${escapeRepairLabelHtml(printedAt)}</div>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => printWindow.print(), 300);
    }
</script>
@endpush
