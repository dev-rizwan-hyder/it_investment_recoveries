@extends('layouts.admin')

@section('content')
    @php
        $statusOptions = \App\Models\RepairEntry::statusChoices();
        $photos = $entry->photos ? json_decode($entry->photos, true) : [];
        if (!is_array($photos)) $photos = [];
        $parsedMedia = $entry->parsed_photos;

        $clientName = $entry->customer_name ?: ($entry->client?->name ?: ($entry->user?->name ?? ''));
        $clientPhone = $entry->customer_phone ?: ($entry->client?->phone ?: '');
        $clientEmail = $entry->customer_email ?: ($entry->client?->email ?: '');
        $clientCompany = $entry->client?->company ?: '';

        $currentJobNo = $entry->job_no ?? '';
        $currentMarketingRef = $entry->marketing_ref ?? 'No reference';
        $currentReceivingEmployee = $entry->receiving_employee ?? '';
        $currentReceivingNotes = $entry->receiving_notes ?? '';
        $currentTechNotes = $entry->technician_notes ?? '';

        // Extract Model from item_description if possible
        $currentModel = $entry->item_description ?? '';
        if ($entry->device_type && str_starts_with($currentModel, $entry->device_type)) {
            $currentModel = trim(substr($currentModel, strlen($entry->device_type)));
            if (str_starts_with($currentModel, '-')) $currentModel = trim(substr($currentModel, 1));
            if ($entry->brand_category && str_starts_with($currentModel, $entry->brand_category)) {
                $currentModel = trim(substr($currentModel, strlen($entry->brand_category)));
            }
        }
    @endphp

    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500 space-y-6">

        {{-- Top Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div>
                <nav class="flex items-center text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 mb-2">
                    <a href="{{ route('admin.repair-intake.index') }}" class="hover:text-blue-800 transition-colors">Repair Intake</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-500 tracking-widest">Edit Entry</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2.5">
                    <i class="fas fa-wrench text-amber-500"></i> Edit Repair Entry #{{ $entry->id }}
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.repair.entries.show', $entry->id) }}"
                    class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all shadow-sm">
                    <i class="fas fa-eye mr-2 text-blue-600"></i> View
                </a>
                <a href="{{ route('admin.repair-intake.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-black text-red-900 flex items-center gap-2 mb-1">
                    <i class="fas fa-exclamation-triangle text-red-500"></i> Please fix the errors below:
                </p>
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="editRepairEntryForm" action="{{ route('admin.repair.entries.update', $entry->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" id="form-action" value="save">

            {{-- SECTION 1: Pallet Barcode Entry --}}
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-4">
                <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                    <i class="fas fa-pallet text-blue-600"></i> Pallet Barcode Entry
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-2">
                            Select Pallet Barcode
                        </label>
                        <select id="pallet_barcode_select" class="w-full p-3 border border-slate-200 rounded-xl text-sm bg-white font-medium outline-none">
                            <option value="">-- Choose Existing Pallet --</option>
                            @foreach ($pallets_list ?? [] as $pallet)
                                <option value="{{ $pallet->barcode_number }}" {{ $entry->pallet_info === $pallet->barcode_number ? 'selected' : '' }}>
                                    {{ $pallet->barcode_number }} {{ $pallet->put_away_location ? '(' . $pallet->put_away_location . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-2">
                            Pallet Barcode Value
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="pallet_barcode" name="pallet_barcode"
                                value="{{ old('pallet_barcode', $entry->pallet_info) }}"
                                class="flex-1 border border-slate-200 rounded-xl p-3 bg-white text-sm font-mono font-bold text-slate-800 outline-none focus:border-blue-500">
                            <button type="button" id="btn-regen-pallet-barcode"
                                class="bg-blue-50 hover:bg-blue-600 hover:text-white border border-blue-200 text-blue-700 px-4 py-3 rounded-xl font-bold text-xs whitespace-nowrap transition-all shadow-sm">
                                <i class="fas fa-sync-alt"></i> New Barcode
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Client / Vendor Information --}}
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-5">
                <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                    <i class="fas fa-user-tag text-blue-600"></i> Client / Vendor Information
                </h4>

                {{-- Row 1: Intake Date & Job # side-by-side --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Intake Date --}}
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">
                            Intake Date / Entry Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="intake_date" name="intake_date"
                            value="{{ old('intake_date', $entry->created_at ? $entry->created_at->format('Y-m-d') : date('Y-m-d')) }}"
                            class="w-full border border-slate-200 rounded-xl p-3 bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-semibold text-slate-800">
                    </div>

                    {{-- Job # --}}
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">
                            <i class="fas fa-briefcase text-blue-600 mr-1"></i> Job #
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="job_no" name="job_no"
                                value="{{ old('job_no', $currentJobNo) }}"
                                class="flex-1 border border-slate-200 rounded-xl p-3 bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                placeholder="e.g., 583921">
                            <button type="button" id="btn-generate-job-no"
                                class="bg-blue-50 border border-blue-200 hover:bg-blue-600 hover:text-white text-blue-700 px-4 py-3 rounded-xl font-bold text-xs whitespace-nowrap transition-all shadow-sm">
                                <i class="fas fa-bolt"></i> Generate Job #
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Select Client / Vendor --}}
                <div>
                    <label class="block text-[13px] font-bold text-slate-700 mb-2">
                        Select Client/Vendor <span class="text-red-500">*</span>
                    </label>
                    <select id="client_id" name="client_id"
                        class="w-full border border-slate-200 rounded-xl p-3 bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium">
                        <option value="">-- Select Client --</option>
                        @foreach ($clients ?? [] as $cl)
                            <option value="{{ $cl->id }}"
                                data-name="{{ $cl->name }}"
                                data-email="{{ $cl->email }}"
                                data-phone="{{ $cl->phone }}"
                                data-company="{{ $cl->company }}"
                                {{ ($entry->user_id == $cl->id || old('client_id') == $cl->id) ? 'selected' : '' }}>
                                {{ $cl->name }} {{ $cl->company ? '(' . $cl->company . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Customer direct inputs (synced with client or edited) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Customer / Contact Name</label>
                        <input type="text" id="customer_name" name="customer_name"
                            value="{{ old('customer_name', $clientName) }}"
                            class="w-full border border-slate-200 rounded-xl p-3 bg-white text-sm font-medium outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Customer Phone</label>
                        <input type="text" id="customer_phone" name="customer_phone"
                            value="{{ old('customer_phone', $clientPhone) }}"
                            class="w-full border border-slate-200 rounded-xl p-3 bg-white text-sm font-medium outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Customer Email</label>
                        <input type="email" id="customer_email" name="customer_email"
                            value="{{ old('customer_email', $clientEmail) }}"
                            class="w-full border border-slate-200 rounded-xl p-3 bg-white text-sm font-medium outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Item Details -> Repair Items (Same style as Data Destruction Items) --}}
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h4 class="text-[15px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-tools text-amber-500"></i> <span>Repair Item Details</span>
                    </h4>
                    <span id="edit-repair-items-count-badge" class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-xs font-black">
                        {{ count($entry->items_list) }} {{ count($entry->items_list) > 1 ? 'Items' : 'Item' }}
                    </span>
                </div>

                <div id="edit-repair-items-container" class="space-y-4">
                    @foreach ($entry->items_list as $idx => $item)
                        @php
                            $iBarcode = !empty($item['barcode']) ? $item['barcode'] : ($idx === 0 ? ($entry->item_barcode ?: 'REP-' . mt_rand(1000000000, 9999999999)) : 'REP-' . mt_rand(1000000000, 9999999999));
                            $iSerial = !empty($item['serial']) ? $item['serial'] : ($idx === 0 ? $entry->serial_number : '');
                        @endphp
                        <div class="edit-repair-item-row p-5 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl relative group" data-index="{{ $idx }}">
                            @if(count($entry->items_list) > 1)
                                <button type="button" class="remove-edit-repair-item-btn absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors w-7 h-7 rounded-full flex items-center justify-center hover:bg-red-50" title="Remove Item">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-6">
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Item Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="repair_items[{{ $idx }}][name]" value="{{ $item['name'] ?: 'Repair Item' }}" placeholder="e.g. Laptop" class="repair-item-name form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Brand</label>
                                    <input type="text" name="repair_items[{{ $idx }}][brand]" value="{{ $item['brand'] ?: '' }}" placeholder="e.g. Dell" class="repair-item-brand form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Model</label>
                                    <input type="text" name="repair_items[{{ $idx }}][model]" value="{{ $item['model'] ?: '' }}" placeholder="e.g. Latitude" class="repair-item-model form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</label>
                                    <div class="flex gap-1.5">
                                        <input type="text" name="repair_items[{{ $idx }}][serial]" value="{{ $iSerial }}" placeholder="e.g. ABC123XYZ" class="repair-item-serial form-input flex-1 p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                                        <button type="button" class="btn-gen-item-serial bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap" title="Generate Random Serial Number">
                                            <i class="fas fa-magic text-[11px]"></i> Gen
                                        </button>
                                    </div>
                                    <input type="hidden" name="repair_items[{{ $idx }}][barcode]" value="{{ $iBarcode }}">
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400">Item #{{ $idx + 1 }}</span>
                                <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                    {{ $iBarcode }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Add Another Item Button --}}
                <div class="pt-2">
                    <button type="button" id="btn-add-edit-repair-item"
                        class="bg-blue-50 hover:bg-blue-100 text-[#2563eb] border border-blue-200 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 transition-all">
                        <i class="fas fa-plus"></i> Add Another Item
                    </button>
                </div>

                {{-- Primary Barcode & Print Action --}}
                <div class="border-t border-slate-100 pt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <label class="text-[12px] font-bold text-slate-700 whitespace-nowrap">Primary Barcode:</label>
                        <input type="text" id="item_barcode" name="item_barcode"
                            value="{{ old('item_barcode', $entry->item_barcode) }}"
                            class="border border-slate-200 rounded-xl p-2.5 text-sm font-mono font-bold text-blue-700 bg-white outline-none">
                        <button type="button" id="btn-regen-item-barcode"
                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-2 rounded-xl text-xs font-bold transition-all">
                            <i class="fas fa-sync-alt"></i> Regenerate
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($entry->sales_invoice)
                            <a href="{{ route('admin.sales.invoices.print', $entry->sales_invoice) }}?autoprint=1" target="_blank"
                                class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs flex items-center transition-all shadow-sm">
                                <i class="fas fa-file-invoice mr-2 text-slate-400"></i> Print Invoice
                            </a>
                        @else
                            <a href="{{ route('admin.repair.entries.invoice-print', $entry->id) }}?autoprint=1" target="_blank"
                                class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs flex items-center transition-all shadow-sm">
                                <i class="fas fa-file-invoice mr-2 text-slate-400"></i> Print Invoice
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Warehouse & Receiving --}}
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-5">
                <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-blue-600"></i> Warehouse & Receiving
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Put-away Location --}}
                    <div class="space-y-1.5">
                        <label class="text-[12px] font-bold text-[#475569]">Put-away Location <span class="text-red-500">*</span></label>
                        <input type="text" id="put_away_location" name="put_away_location"
                            value="{{ old('put_away_location', $entry->put_away_location ?: 'Warehouse A') }}"
                            placeholder="e.g., Warehouse A"
                            class="w-full p-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-blue-500 bg-white font-medium" required>
                    </div>

                    {{-- Marketing Reference --}}
                    <div class="space-y-1.5">
                        <label class="text-[12px] font-bold text-[#475569]">Marketing Reference</label>
                        <select id="marketing_reference" name="marketing_reference"
                            class="w-full p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none font-medium">
                            @foreach ($marketingRefs ?? ['No reference'] as $ref)
                                <option value="{{ $ref }}" {{ $currentMarketingRef === $ref ? 'selected' : '' }}>
                                    {{ $ref }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Receiving Employee Combo-box --}}
                    <div class="space-y-1.5 relative">
                        <label class="text-[12px] font-bold text-[#475569]">Receiving Employee</label>
                        <div class="relative">
                            <input type="text" id="receiving_employee" name="receiving_employee" autocomplete="off"
                                value="{{ old('receiving_employee', $currentReceivingEmployee) }}"
                                class="w-full p-3 pr-10 border border-slate-200 rounded-xl text-sm outline-none bg-white focus:border-blue-500 font-medium"
                                placeholder="Select or type employee...">
                            <button type="button" id="toggle-emp-dropdown"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                        </div>
                        <div id="emp-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-48 overflow-y-auto py-1">
                            @foreach ($employees ?? [] as $emp)
                                <div class="emp-opt px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer font-medium" data-val="{{ $emp->name }}">
                                    {{ $emp->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Receiving Notes --}}
                <div class="space-y-1.5">
                    <label class="text-[12px] font-bold text-[#475569]">Receiving Notes</label>
                    <textarea id="receiving_notes" name="receiving_notes" rows="3"
                        placeholder="Enter any notes about incoming repair items..."
                        class="w-full p-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-blue-500 bg-white resize-y font-medium">{{ old('receiving_notes', $currentReceivingNotes) }}</textarea>
                </div>

                {{-- Item Photos and Files --}}
                <div class="space-y-3 pt-2 border-t border-slate-200/60">
                    <label class="block text-sm font-bold text-slate-700">Item photos and files</label>

                    {{-- Existing photos with delete option --}}
                    @if (!empty($parsedMedia))
                        <div class="space-y-2">
                            <div class="text-[11px] font-bold uppercase text-slate-400">Current Attached Photos & Files:</div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3" id="existing-photos-grid">
                                @foreach ($parsedMedia as $photoIndex => $media)
                                    <div class="aspect-square rounded-xl border border-slate-200 overflow-hidden relative group bg-white shadow-sm flex flex-col items-center justify-center p-2" data-photo-card="{{ $media['raw'] }}">
                                        @if ($media['is_image'])
                                            <img src="{{ $media['url'] }}" class="w-full h-full object-cover rounded-lg" onerror="this.src='/images/placeholder.png'">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-center w-full h-full p-1 cursor-pointer" onclick="openDocumentFile('{{ $media['raw'] }}', '{{ $media['name'] }}')" title="Click to view/download {{ $media['name'] }}">
                                                <span class="text-[8px] font-black uppercase px-1 py-0.5 rounded border mb-1 {{ $media['badge_class'] }}">
                                                    {{ $media['label'] }}
                                                </span>
                                                <i class="{{ $media['icon'] }} text-2xl mb-1"></i>
                                                <span class="text-[10px] font-bold text-slate-700 truncate max-w-full block px-1">
                                                    {{ $media['name'] }}
                                                </span>
                                            </div>
                                        @endif
                                        <button type="button" onclick="markPhotoDeleted('{{ $media['raw'] }}', this)"
                                            class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs opacity-90 hover:opacity-100 shadow transition-all z-10"
                                            title="Delete file">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div id="deleted-photos-container"></div>

                    {{-- File upload for new photos --}}
                    <div class="pt-2">
                        <label class="w-full sm:w-auto cursor-pointer bg-white hover:bg-slate-50 border-2 border-dashed border-slate-200 px-6 py-3.5 rounded-xl font-bold text-xs text-slate-600 transition-all inline-flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-folder-open text-slate-400"></i>
                            <span>Attach Additional Photos & Files</span>
                            <input type="file" name="photos[]" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" onchange="previewNewPhotos(this)">
                        </label>
                        <div id="new-photos-preview" class="flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>
            </div>

            {{-- SECTION 5: For Technician Use Only --}}
            <div class="bg-indigo-50/40 border border-indigo-100 rounded-2xl p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
                    <h4 class="text-[14px] font-bold text-indigo-950 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-cog text-indigo-600"></i> For Technician use only section
                    </h4>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">Tech Only</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Tech Name --}}
                    <div class="space-y-1.5">
                        <label class="text-[12px] font-bold text-[#475569]">Tech Name <span class="text-red-500">*</span></label>
                        <input type="text" id="technician" name="technician"
                            value="{{ old('technician', $entry->technician ?: 'N/A') }}"
                            placeholder="Technician Name"
                            class="w-full p-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 bg-white font-medium" required>
                    </div>

                    {{-- Repair Status --}}
                    <div class="space-y-1.5">
                        <label class="text-[12px] font-bold text-[#475569]">Repair Status</label>
                        <select id="repair_status" name="repair_status"
                            class="w-full p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none font-bold text-slate-800 focus:border-indigo-500">
                            @if ($entry->status && !array_key_exists($entry->status, $statusOptions))
                                <option value="{{ $entry->status }}" selected>{{ $entry->status }}</option>
                            @endif
                            @foreach ($statusOptions as $val => $lbl)
                                <option value="{{ $val }}" {{ old('repair_status', $entry->status) === $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Technician Notes --}}
                <div class="space-y-1.5">
                    <label class="text-[12px] font-bold text-[#475569]">Technician Notes</label>
                    <textarea id="technician_notes" name="technician_notes" rows="3"
                        placeholder="Enter diagnosis, work performed, or parts needed..."
                        class="w-full p-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 bg-white resize-y font-medium">{{ old('technician_notes', $currentTechNotes) }}</textarea>
                </div>
            </div>

            {{-- SECTION 6: Action Buttons --}}
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row gap-4">
                <button type="button" id="btn-save-draft"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-4 rounded-xl font-bold text-[15px] flex items-center justify-center transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                    <i class="fas fa-file-alt mr-2"></i> Save as Draft
                </button>
                <button type="submit" id="btn-save-repair"
                    class="flex-1 bg-[#2563eb] hover:bg-blue-700 text-white py-4 rounded-xl font-bold text-[15px] flex items-center justify-center transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                    <i class="fas fa-save mr-2"></i> Update Repair Entry
                </button>
            </div>
        </form>

    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const palletSelect = document.getElementById('pallet_barcode_select');
        const palletInput = document.getElementById('pallet_barcode');
        const regenPalletBtn = document.getElementById('btn-regen-pallet-barcode');
        const clientSelect = document.getElementById('client_id');
        const customerName = document.getElementById('customer_name');
        const customerPhone = document.getElementById('customer_phone');
        const customerEmail = document.getElementById('customer_email');
        const genSerialBtn = document.getElementById('btn-gen-serial');
        const serialInput = document.getElementById('serial_number');
        const regenItemBarcodeBtn = document.getElementById('btn-regen-item-barcode');
        const itemBarcodeInput = document.getElementById('item_barcode');
        const genJobNoBtn = document.getElementById('btn-generate-job-no');
        const jobNoInput = document.getElementById('job_no');
        const receivingEmpInput = document.getElementById('receiving_employee');
        const toggleEmpDropdown = document.getElementById('toggle-emp-dropdown');
        const empDropdown = document.getElementById('emp-dropdown');
        const form = document.getElementById('editRepairEntryForm');
        const saveDraftBtn = document.getElementById('btn-save-draft');
        const formAction = document.getElementById('form-action');

        // Pallet select sync
        if (palletSelect && palletInput) {
            palletSelect.addEventListener('change', function() {
                if (this.value) {
                    palletInput.value = this.value;
                }
            });
        }

        // Regenerate pallet barcode
        if (regenPalletBtn && palletInput) {
            regenPalletBtn.addEventListener('click', function() {
                const randomBarcode = 'PAL-' + Math.floor(1000000000 + Math.random() * 9000000000);
                palletInput.value = randomBarcode;
            });
        }

        // Client select sync
        if (clientSelect) {
            clientSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    if (customerName) customerName.value = opt.dataset.name || '';
                    if (customerPhone) customerPhone.value = opt.dataset.phone || '';
                    if (customerEmail) customerEmail.value = opt.dataset.email || '';
                }
            });
        }

        // Generate serial
        if (genSerialBtn && serialInput) {
            genSerialBtn.addEventListener('click', function() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let result = '';
                for (let i = 0; i < 10; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                serialInput.value = result;
            });
        }

        // Regenerate item barcode
        if (regenItemBarcodeBtn && itemBarcodeInput) {
            regenItemBarcodeBtn.addEventListener('click', function() {
                const randomBarcode = 'REP-' + Math.floor(1000000000 + Math.random() * 9000000000);
                itemBarcodeInput.value = randomBarcode;
            });
        }

        // Generate Job No
        if (genJobNoBtn && jobNoInput) {
            genJobNoBtn.addEventListener('click', function() {
                jobNoInput.value = Math.floor(100000 + Math.random() * 900000);
            });
        }

        // Receiving employee dropdown toggle & selection
        if (toggleEmpDropdown && empDropdown) {
            toggleEmpDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                empDropdown.classList.toggle('hidden');
            });
        }

        document.querySelectorAll('.emp-opt').forEach(opt => {
            opt.addEventListener('click', function() {
                if (receivingEmpInput) receivingEmpInput.value = this.dataset.val;
                if (empDropdown) empDropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (empDropdown && !empDropdown.contains(e.target) && e.target !== toggleEmpDropdown) {
                empDropdown.classList.add('hidden');
            }
        });

        // Dynamic multi-item handling in Edit form
        const itemsContainer = document.getElementById('edit-repair-items-container');
        const addItemBtn = document.getElementById('btn-add-edit-repair-item');
        const countBadge = document.getElementById('edit-repair-items-count-badge');

        function updateItemsCountAndIndexes() {
            if (!itemsContainer) return;
            const rows = itemsContainer.querySelectorAll('.edit-repair-item-row');
            if (countBadge) {
                countBadge.textContent = `${rows.length} ${rows.length > 1 ? 'Items' : 'Item'}`;
            }
            rows.forEach((row, idx) => {
                row.dataset.index = idx;
                const headerNum = row.querySelector('.text-slate-400');
                if (headerNum && headerNum.textContent.startsWith('Item #')) {
                    headerNum.textContent = `Item #${idx + 1}`;
                }
                const nameInput = row.querySelector('.repair-item-name');
                const brandInput = row.querySelector('.repair-item-brand');
                const modelInput = row.querySelector('.repair-item-model');
                const serialInput = row.querySelector('.repair-item-serial');
                const barcodeHidden = row.querySelector('input[type="hidden"]');
                if (nameInput) nameInput.name = `repair_items[${idx}][name]`;
                if (brandInput) brandInput.name = `repair_items[${idx}][brand]`;
                if (modelInput) modelInput.name = `repair_items[${idx}][model]`;
                if (serialInput) serialInput.name = `repair_items[${idx}][serial]`;
                if (barcodeHidden) barcodeHidden.name = `repair_items[${idx}][barcode]`;
            });
        }

        if (addItemBtn && itemsContainer) {
            addItemBtn.addEventListener('click', function() {
                const rows = itemsContainer.querySelectorAll('.edit-repair-item-row');
                const newIdx = rows.length;
                const randomBarcode = 'REP-' + Math.floor(1000000000 + Math.random() * 9000000000);

                const newRow = document.createElement('div');
                newRow.className = 'edit-repair-item-row p-5 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl relative group animate-in fade-in duration-300';
                newRow.dataset.index = newIdx;
                newRow.innerHTML = `
                    <button type="button" class="remove-edit-repair-item-btn absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors w-7 h-7 rounded-full flex items-center justify-center hover:bg-red-50" title="Remove Item">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-6">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Item Name <span class="text-red-500">*</span></label>
                            <input type="text" name="repair_items[${newIdx}][name]" placeholder="e.g. Laptop" class="repair-item-name form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Brand</label>
                            <input type="text" name="repair_items[${newIdx}][brand]" placeholder="e.g. Dell" class="repair-item-brand form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Model</label>
                            <input type="text" name="repair_items[${newIdx}][model]" placeholder="e.g. Latitude" class="repair-item-model form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</label>
                            <div class="flex gap-1.5">
                                <input type="text" name="repair_items[${newIdx}][serial]" placeholder="e.g. ABC123XYZ" class="repair-item-serial form-input flex-1 p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white">
                                <button type="button" class="btn-gen-item-serial bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap" title="Generate Random Serial Number">
                                    <i class="fas fa-magic text-[11px]"></i> Gen
                                </button>
                            </div>
                            <input type="hidden" name="repair_items[${newIdx}][barcode]" value="${randomBarcode}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Item #${newIdx + 1}</span>
                        <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                            ${randomBarcode}
                        </span>
                    </div>
                `;
                itemsContainer.appendChild(newRow);
                updateItemsCountAndIndexes();
            });
        }

        // Delegation for Remove item and Generate serial in edit form
        if (itemsContainer) {
            itemsContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-edit-repair-item-btn');
                if (removeBtn) {
                    const row = removeBtn.closest('.edit-repair-item-row');
                    if (row) {
                        row.remove();
                        updateItemsCountAndIndexes();
                    }
                }

                const genBtn = e.target.closest('.btn-gen-item-serial');
                if (genBtn) {
                    const row = genBtn.closest('.edit-repair-item-row');
                    if (row) {
                        const serialInput = row.querySelector('.repair-item-serial');
                        if (serialInput) {
                            serialInput.value = 'REP' + Math.floor(1000000000 + Math.random() * 9000000000);
                        }
                    }
                }
            });
        }

        // Save as Draft button
        if (saveDraftBtn && form && formAction) {
            saveDraftBtn.addEventListener('click', function() {
                formAction.value = 'draft';
                const statusSelect = document.getElementById('repair_status');
                if (statusSelect) statusSelect.value = 'Draft';
                form.submit();
            });
        }
    });

    // Mark existing photo for deletion
    function markPhotoDeleted(photoUrl, btn) {
        const card = btn.closest('[data-photo-card]');
        if (card) {
            card.remove();
            const container = document.getElementById('deleted-photos-container');
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'deleted_photos[]';
            hidden.value = photoUrl;
            container.appendChild(hidden);
        }
    }

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

    // Preview newly selected photos
    function previewNewPhotos(input) {
        const previewContainer = document.getElementById('new-photos-preview');
        previewContainer.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-16 h-16 rounded-xl object-cover border border-slate-200 shadow-sm';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    let icon = 'fas fa-file-alt text-slate-500';
                    let badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                    const name = file.name.toLowerCase();
                    if (name.endsWith('.pdf')) {
                        icon = 'fas fa-file-pdf text-red-600';
                        badgeClass = 'bg-red-50 text-red-700 border-red-200';
                    } else if (name.endsWith('.doc') || name.endsWith('.docx')) {
                        icon = 'fas fa-file-word text-blue-600';
                        badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                    } else if (name.endsWith('.xls') || name.endsWith('.xlsx') || name.endsWith('.csv')) {
                        icon = 'fas fa-file-excel text-emerald-600';
                        badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    }
                    const badge = document.createElement('div');
                    badge.className = `px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 border shadow-sm ${badgeClass}`;
                    badge.innerHTML = `<i class="${icon}"></i> <span class="max-w-[150px] truncate">${file.name}</span>`;
                    previewContainer.appendChild(badge);
                }
            });
        }
    }
</script>
@endpush
