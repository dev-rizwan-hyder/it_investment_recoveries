@extends('layouts.admin')

@section('content')
<div class="w-full p-4 md:p-6 bg-slate-50 min-h-screen">

    {{-- ═══════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="fas fa-shipping-fast text-white text-sm"></i>
                </span>
                Shipment Intake
            </h2>
            <p class="text-slate-400 text-sm mt-1 ml-12">Record inbound pallet shipments from vendors or universal waste</p>
        </div>
        <a href="{{ route('admin.downstream-vendor.index') }}"
            class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm">
            <i class="fas fa-truck text-blue-500"></i> View Downstream Vendor
        </a>
    </div>

    {{-- Alert --}}
    <div id="formAlert" class="hidden mb-5"></div>

    {{-- ═══════════════════════════════════════════════════════════
         FORM
    ═══════════════════════════════════════════════════════════════ --}}
    <form id="shipmentIntakeForm" enctype="multipart/form-data" class="space-y-5" novalidate>
        @csrf

        {{-- ─────────────────────────────────────────────────────
             SECTION A: PALLET BARCODE ENTRY  (TOP)
        ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            {{-- Section header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <span class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0 shadow shadow-blue-200">
                    <i class="fas fa-barcode text-white text-sm"></i>
                </span>
                <div>
                    <h3 class="text-[15px] font-black text-slate-800">Pallet Barcode Entry</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Link or generate a barcode for this shipment</p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                {{-- Toggle --}}
                <label class="inline-flex items-center gap-3 cursor-pointer mb-6 group">
                    <div class="relative">
                        <input type="checkbox" id="hasExistingUwPallet" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-slate-900 transition-colors">
                        Pallet has existing universal waste entry
                    </span>
                </label>

                {{-- ══ NEW BARCODE (default) ══ --}}
                <div id="newBarcodeSection">
                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 mb-6">
                        <i class="fas fa-info-circle text-blue-400 mt-0.5 shrink-0"></i>
                        <p class="text-[13px] text-blue-700 font-medium leading-relaxed">
                            This pallet doesn't have a barcode. We'll generate one for you automatically.
                            Click <strong>Regenerate</strong> to get a new one.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        {{-- Left: controls --}}
                        <div class="space-y-4">
                            <button type="button" id="regenerateBarcodeBtn"
                                class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm transition-all">
                                <i class="fas fa-redo-alt text-blue-500"></i> Regenerate New Barcode
                            </button>

                            {{-- Active badge --}}
                            <div id="activeBarcodeSection" class="hidden">
                                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2.5 rounded-xl font-bold text-sm">
                                    <i class="fas fa-check-circle text-emerald-500"></i>
                                    <span id="activeBarcodeLabel" class="font-mono tracking-wider">—</span>
                                    <span class="text-[11px] font-semibold text-emerald-400 ml-1">active</span>
                                </div>
                            </div>
                        </div>

                        {{-- Right: barcode card --}}
                        <div id="generatedBarcodeDisplay" class="hidden">
                            <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50 flex flex-col items-center gap-3">
                                <p id="generatedBarcodeNumber" class="text-base font-black text-slate-800 tracking-wider font-mono">—</p>
                                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm w-full text-center">
                                    <img id="generatedBarcodeImage" src="" alt="barcode" class="h-14 mx-auto mb-2 block">
                                    <p id="generatedBarcodeText" class="font-mono text-xs tracking-[0.25em] text-slate-700 font-bold uppercase">—</p>
                                </div>
                                <button type="button" id="useGeneratedBarcodeBtn"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold text-sm transition-all shadow-md shadow-blue-200">
                                    <i class="fas fa-check mr-2"></i> Use This Barcode
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ EXISTING UW PALLET ══ --}}
                <div id="existingUwSection" class="hidden space-y-5">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                            Select from Universal Waste Entries <span class="text-blue-500">(Completed only)</span>
                        </label>
                        <div class="relative">
                            <select id="uwPalletSelect" name="universal_waste_item_id"
                                class="w-full px-4 py-3.5 pr-10 border border-slate-200 rounded-xl text-sm bg-white font-semibold text-slate-700 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none appearance-none cursor-pointer">
                                <option value="">-- Select Pallet --</option>
                                @foreach ($uwEntries as $entry)
                                    <option value="{{ $entry->id }}"
                                        data-pallet="{{ $entry->pallet_number }}"
                                        data-name="{{ $entry->name }}"
                                        data-brand="{{ $entry->brand }}"
                                        data-model="{{ $entry->model }}"
                                        data-category="{{ $entry->category }}"
                                        data-client-id="{{ $entry->pallet?->client_id }}"
                                        data-client-name="{{ $entry->pallet?->client?->name }}"
                                        data-client-company="{{ $entry->pallet?->client?->company }}"
                                        data-client-email="{{ $entry->pallet?->client?->email }}">
                                        {{ $entry->pallet_number }}{{ $entry->pallet?->client ? ' — ' . ($entry->pallet->client->company ?: $entry->pallet->client->name) : '' }}{{ $entry->name ? ' | ' . $entry->name : '' }}{{ $entry->model ? ' (' . $entry->model . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Selected pallet preview card --}}
                    <div id="selectedUwPreviewCard" class="hidden">
                        <div class="rounded-2xl border border-blue-100 bg-blue-50/40 overflow-hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                                {{-- Left: barcode --}}
                                <div class="flex flex-col items-center justify-center p-6 border-b md:border-b-0 md:border-r border-blue-100 bg-white">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Pallet Barcode</p>
                                    <p id="selectedUwBarcodeNumber" class="text-lg font-black text-slate-800 tracking-wider font-mono mb-3">—</p>
                                    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm w-full max-w-[220px] text-center">
                                        <img id="selectedUwBarcodeImage" src="" alt="barcode" class="h-12 mx-auto mb-1 block">
                                        <p id="selectedUwBarcodeText" class="font-mono text-[11px] tracking-[0.25em] text-slate-700 font-bold uppercase">—</p>
                                    </div>
                                </div>

                                {{-- Right: item info --}}
                                <div class="p-6 space-y-4">
                                    {{-- Client info --}}
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            <i class="fas fa-user-tie mr-1 text-blue-400"></i> Client / Vendor
                                        </p>
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                                <i class="fas fa-user text-blue-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <p id="uwPreviewClientName" class="text-sm font-black text-slate-800">—</p>
                                                <p id="uwPreviewClientCompany" class="text-[11px] text-slate-500 font-semibold">—</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-blue-100 pt-4">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            <i class="fas fa-box mr-1 text-blue-400"></i> Item Details
                                        </p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-white rounded-xl border border-slate-100 p-3 shadow-sm">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Name</p>
                                                <p id="uwPreviewName" class="text-sm font-bold text-slate-800 truncate">—</p>
                                            </div>
                                            <div class="bg-white rounded-xl border border-slate-100 p-3 shadow-sm">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Model</p>
                                                <p id="uwPreviewModel" class="text-sm font-bold text-slate-800 truncate">—</p>
                                            </div>
                                            <div class="bg-white rounded-xl border border-slate-100 p-3 shadow-sm">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Brand</p>
                                                <p id="uwPreviewBrand" class="text-sm font-bold text-slate-800 truncate">—</p>
                                            </div>
                                            <div class="bg-white rounded-xl border border-slate-100 p-3 shadow-sm">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Category</p>
                                                <p id="uwPreviewCategory" class="text-sm font-bold text-slate-800 truncate">—</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────
             SECTION B: CLIENT / VENDOR INFORMATION
        ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <span class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0 shadow shadow-blue-200">
                    <i class="fas fa-user-tie text-white text-sm"></i>
                </span>
                <div>
                    <h3 class="text-[15px] font-black text-slate-800">Client / Vendor Information</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Select who is sending this shipment</p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Intake Date --}}
                    <div>
                        <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fas fa-calendar-alt text-blue-400 mr-1"></i> Intake Date / Entry Date *
                        </label>
                        <input type="date" id="intakeDate" name="intake_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-white text-sm font-semibold text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none transition-all">
                    </div>

                    {{-- Job # --}}
                    <div>
                        <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fas fa-hashtag text-blue-400 mr-1"></i> Job #
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="jobNo" name="job_no"
                                placeholder="e.g., 583921"
                                class="flex-1 border border-slate-200 rounded-xl px-4 py-3 bg-white text-sm font-semibold text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none transition-all">
                            <button type="button" id="generateJobNoBtn"
                                class="shrink-0 bg-blue-50 hover:bg-blue-600 hover:text-white border border-blue-200 text-blue-700 px-4 py-3 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-bolt"></i> Generate Job #
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Select Client --}}
                <div class="mb-5">
                    <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">
                        Select Client/Vendor *
                    </label>
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <select id="clientSelect" name="client_id"
                                class="w-full px-4 py-3 pr-10 border border-slate-200 rounded-xl bg-white text-sm font-semibold text-slate-700 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none appearance-none transition-all">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        data-name="{{ $client->name }}"
                                        data-email="{{ $client->email }}"
                                        data-phone="{{ $client->phone }}"
                                        data-company="{{ $client->company }}"
                                        data-address="{{ $client->address }}">
                                        {{ $client->name }}{{ $client->company ? ' — ' . $client->company : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                        <button type="button" id="toggleNewClientBtn"
                            class="shrink-0 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 px-4 py-3 rounded-xl font-bold text-sm whitespace-nowrap transition-all">
                            + New Client
                        </button>
                    </div>

                    {{-- Selected client card --}}
                    <div id="selectedClientDisplay" class="hidden mt-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center shadow-md shadow-blue-200 shrink-0">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <p id="selectedClientName" class="text-sm font-black text-slate-800">—</p>
                                    <p id="selectedClientMeta" class="text-[11px] text-slate-500 font-semibold">—</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 bg-blue-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full">
                                <i class="fas fa-check text-[8px]"></i> Selected
                            </span>
                        </div>
                    </div>
                </div>

                {{-- New Client inline form --}}
                <div id="newClientFormSection" class="hidden bg-slate-50 border border-slate-200 rounded-2xl p-5 md:p-6 space-y-4">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-xs font-black text-slate-600 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-plus-circle text-blue-500"></i> Create New Client
                        </h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" id="newClientName" placeholder="Full Name *"
                            class="col-span-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 outline-none transition-all">
                        <input type="email" id="newClientEmail" placeholder="Email Address"
                            class="border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 outline-none transition-all">
                        <input type="text" id="newClientPhone" placeholder="Phone Number"
                            class="border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 outline-none transition-all">
                        <input type="text" id="newClientCompany" placeholder="Company (Optional)"
                            class="col-span-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 outline-none transition-all">
                        <textarea id="newClientAddress" placeholder="Address (Optional)" rows="2"
                            class="col-span-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 outline-none resize-none transition-all"></textarea>
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" id="createClientBtn"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-sm shadow-lg shadow-blue-200 transition-all">
                            <i class="fas fa-check-circle mr-2"></i> Create & Select Client
                        </button>
                        <button type="button" id="cancelNewClientBtn"
                            class="flex-1 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold py-3 rounded-xl text-sm transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────
             SECTION C: PALLET DETAILS
        ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <span class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0 shadow shadow-blue-200">
                    <span class="text-white text-sm font-black">1</span>
                </span>
                <div>
                    <h3 class="text-[15px] font-black text-slate-800">Item #1 — Pallet Details</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Weight, description and location details</p>
                </div>
            </div>

            <div class="p-6 md:p-8 space-y-6">

                {{-- Description & Count --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">Description</label>
                        <input type="text" id="description" name="description"
                            placeholder="e.g., Mixed Electronics Pallet"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">Estimated Item Count</label>
                        <input type="number" id="estimatedCount" name="estimated_count"
                            placeholder="e.g., 25" min="0"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none transition-all">
                    </div>
                </div>

                {{-- Weights --}}
                <div>
                    <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-3">Weight</label>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Gross Weight (lbs)</p>
                            <input type="number" id="grossWeight" name="gross_weight" step="0.1" min="0"
                                placeholder="0.00"
                                class="w-full bg-transparent border-none outline-none text-lg font-black text-slate-800 p-0"
                                oninput="shipApp.calcNet()" onchange="shipApp.calcNet()"
                                onkeydown="if(event.key==='Enter') event.preventDefault()">
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tare Weight (lbs)</p>
                            <input type="number" id="tareWeight" name="tare_weight" step="0.1" min="0"
                                placeholder="0.00"
                                class="w-full bg-transparent border-none outline-none text-lg font-black text-slate-800 p-0"
                                oninput="shipApp.calcNet()" onchange="shipApp.calcNet()"
                                onkeydown="if(event.key==='Enter') event.preventDefault()">
                        </div>
                        <div class="bg-blue-600 rounded-xl p-4 flex flex-col justify-between">
                            <p class="text-[10px] font-black text-blue-200 uppercase tracking-widest mb-2">Calculated Net</p>
                            <p id="netWeightDisplay" class="text-lg font-black text-white">0.00 lbs</p>
                            <input type="hidden" id="netWeight" name="net_weight" value="0">
                        </div>
                    </div>
                </div>

                {{-- Location & Marketing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">
                            Put-away Location *
                        </label>
                        <input type="text" id="putAwayLocation" name="put_away_location"
                            placeholder="e.g., Warehouse A"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">Marketing Reference</label>
                        <div class="relative">
                            <select id="marketingReference" name="marketing_reference"
                                class="w-full px-4 py-3 pr-10 border border-slate-200 rounded-xl bg-white text-sm font-semibold text-slate-700 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none appearance-none transition-all">
                                @foreach ($marketingRefs as $ref)
                                    <option value="{{ $ref }}">{{ $ref }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                {{-- Receiving Employee --}}
                <div>
                    <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">Receiving Employee</label>
                    <input type="text" id="receivingEmployee" name="receiving_employee"
                        list="employeesList"
                        placeholder="Enter receiving employee name..."
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none transition-all">
                    <datalist id="employeesList">
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->name }}"></option>
                        @endforeach
                    </datalist>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                        placeholder="Additional notes about the pallet..."
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 outline-none resize-none transition-all"></textarea>
                </div>

                {{-- Photo Upload --}}
                <div class="border-t border-slate-100 pt-6">
                    <label class="block text-[12px] font-black text-slate-500 uppercase tracking-widest mb-4">
                        Pallet Photos & Files
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <div id="mainPhotoPreviews" class="flex flex-wrap gap-3 contents"></div>
                        <label class="cursor-pointer bg-slate-50 hover:bg-blue-50 border-2 border-dashed border-slate-200 hover:border-blue-300 px-6 py-5 rounded-2xl font-black text-[10px] text-slate-500 hover:text-blue-600 transition-all flex flex-col items-center justify-center min-w-[140px] text-center group">
                            <i class="fas fa-cloud-upload-alt text-2xl mb-2 text-slate-300 group-hover:text-blue-400 transition-colors"></i>
                            <span class="uppercase tracking-wider">Upload Image or File</span>
                            <input type="file" id="photoInput" name="main_photos[]" multiple class="hidden" accept="*/*">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────
             SUBMIT BAR
        ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-4 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <p class="text-xs text-slate-400 font-medium flex items-center gap-2">
                <i class="fas fa-info-circle text-slate-300"></i>
                Fields marked * are required before saving.
            </p>
            <div class="flex gap-3 w-full sm:w-auto">
                <button type="button" id="resetFormBtn"
                    class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-undo mr-2"></i> Reset
                </button>
                <button type="button" id="saveShipmentBtn"
                    class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Save Shipment Intake
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
const shipmentRoutes = {
    barcode:      "{{ route('admin.shipment-intake.barcode') }}",
    jobNumber:    "{{ route('admin.shipment-intake.job-number') }}",
    clients:      "{{ route('admin.shipment-intake.clients') }}",
    createClient: "{{ route('admin.shipment-intake.create-client') }}",
    uwEntry:      "{{ route('admin.shipment-intake.uw-entry') }}",
    save:         "{{ route('admin.shipment-intake.save') }}",
};
const csrfToken = "{{ csrf_token() }}";
</script>
<script>
const shipApp = (() => {
    let activeBarcode = '';
    let selectedClientId = '';

    const $ = id => document.getElementById(id);

    // ── Alerts ──────────────────────────────────────────────────────
    function showAlert(msg, type = 'success') {
        const el = $('formAlert');
        const isOk = type === 'success';
        el.className = `mb-5 p-4 rounded-xl font-semibold flex items-center gap-3 text-sm border ${
            isOk ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'}`;
        el.innerHTML = `<i class="fas fa-${isOk ? 'check-circle' : 'exclamation-triangle'} text-base shrink-0"></i><span>${msg}</span>`;
        el.classList.remove('hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        setTimeout(() => el.classList.add('hidden'), 5000);
    }

    // ── Net weight ───────────────────────────────────────────────────
    function calcNet() {
        const g = parseFloat($('grossWeight').value) || 0;
        const t = parseFloat($('tareWeight').value) || 0;
        const n = Math.max(0, g - t);
        $('netWeightDisplay').textContent = n.toFixed(2) + ' lbs';
        $('netWeight').value = n.toFixed(2);
    }

    // ── Barcode generation ───────────────────────────────────────────
    function generateBarcode() {
        fetch(shipmentRoutes.barcode).then(r => r.json()).then(data => {
            if (!data.success) return;
            activeBarcode = data.number;
            $('generatedBarcodeNumber').textContent = data.number;
            $('generatedBarcodeImage').src = data.base64;
            $('generatedBarcodeText').textContent = data.number;
            $('generatedBarcodeDisplay').classList.remove('hidden');
        });
    }

    function activateBarcode(number) {
        activeBarcode = number;
        $('activeBarcodeValue').value = number;
        $('activePalletNumber').value = number;
        $('activeBarcodeLabel').textContent = number;
        $('activeBarcodeSection').classList.remove('hidden');
    }

    // ── Client card ──────────────────────────────────────────────────
    function updateClientCard(opt) {
        if (!opt || !opt.value) {
            $('selectedClientDisplay').classList.add('hidden');
            selectedClientId = ''; return;
        }
        selectedClientId = opt.value;
        const name = opt.dataset.name || opt.textContent.split('—')[0].trim();
        const meta = [opt.dataset.company, opt.dataset.email].filter(Boolean).join(' · ') || opt.dataset.phone || '';
        $('selectedClientName').textContent = name;
        $('selectedClientMeta').textContent = meta || 'No additional info';
        $('selectedClientDisplay').classList.remove('hidden');
    }

    // ── Photo previews ───────────────────────────────────────────────
    function addPhotoPreviews(files) {
        const container = $('mainPhotoPreviews');
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const isImg = file.type.startsWith('image/');
                const thumb = document.createElement('div');
                thumb.className = 'relative w-24 h-24 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center shadow-sm';
                thumb.innerHTML = isImg
                    ? `<img src="${e.target.result}" class="w-full h-full object-cover">`
                    : `<div class="text-center px-2"><i class="fas fa-file-alt text-2xl text-blue-300 mb-1"></i><p class="text-[9px] font-bold text-slate-500 break-all leading-tight">${file.name.substring(0, 15)}</p></div>`;
                container.appendChild(thumb);
            };
            reader.readAsDataURL(file);
        });
    }

    // ── UW pallet selected ───────────────────────────────────────────
    function onUwPalletSelected() {
        const sel = $('uwPalletSelect');
        const opt = sel.options[sel.selectedIndex];

        if (!opt.value) {
            $('selectedUwPreviewCard').classList.add('hidden');
            return;
        }

        const palletNum = opt.dataset.pallet;
        $('activePalletNumber').value = palletNum;
        $('activeBarcodeValue').value = palletNum;
        activeBarcode = palletNum;

        // Auto-fill client from option data attributes
        const clientId = opt.dataset.clientId;
        if (clientId) {
            const clientSel = $('clientSelect');
            for (const o of clientSel.options) {
                if (String(o.value) === String(clientId)) {
                    clientSel.value = clientId;
                    updateClientCard(o);
                    break;
                }
            }
        }

        // Show preview card immediately using option data
        $('uwPreviewClientName').textContent    = opt.dataset.clientName    || '—';
        $('uwPreviewClientCompany').textContent  = opt.dataset.clientCompany || (opt.dataset.clientEmail || '—');
        $('uwPreviewName').textContent           = opt.dataset.name     || '—';
        $('uwPreviewModel').textContent          = opt.dataset.model    || '—';
        $('uwPreviewBrand').textContent          = opt.dataset.brand    || '—';
        $('uwPreviewCategory').textContent       = opt.dataset.category || '—';
        $('selectedUwBarcodeNumber').textContent = palletNum;
        $('selectedUwBarcodeText').textContent   = palletNum;
        $('selectedUwPreviewCard').classList.remove('hidden');

        // Fetch barcode image + richer data via AJAX
        fetch(`${shipmentRoutes.uwEntry}?id=${opt.value}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                $('selectedUwBarcodeImage').src = data.barcode_base64;

                // Update with server values
                if (data.item.client_name)    $('uwPreviewClientName').textContent    = data.item.client_name;
                if (data.item.client_company)  $('uwPreviewClientCompany').textContent = data.item.client_company;
                if (data.item.name)            $('uwPreviewName').textContent           = data.item.name;
                if (data.item.model)           $('uwPreviewModel').textContent          = data.item.model;
                if (data.item.brand)           $('uwPreviewBrand').textContent          = data.item.brand;
                if (data.item.category)        $('uwPreviewCategory').textContent       = data.item.category;

                // Auto-fill pallet details if empty
                if (!$('description').value && data.item.name)     $('description').value     = data.item.name;
                if (!$('estimatedCount').value && data.item.quantity) $('estimatedCount').value  = data.item.quantity;
                if (!$('grossWeight').value && data.item.weight)   { $('grossWeight').value     = data.item.weight; calcNet(); }
                if (!$('notes').value && data.item.notes)          $('notes').value             = data.item.notes;
            });
    }

    // ── Save ─────────────────────────────────────────────────────────
    async function saveShipment() {
        const btn = $('saveShipmentBtn');
        if (!$('clientSelect').value)          return showAlert('Please select a Client/Vendor.', 'error');
        if (!$('putAwayLocation').value.trim()) return showAlert('Put-away Location is required.', 'error');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';

        try {
            const fd = new FormData();
            fd.append('_token',                  csrfToken);
            fd.append('intake_date',             $('intakeDate').value);
            fd.append('client_id',               $('clientSelect').value);
            fd.append('job_no',                  $('jobNo').value);
            fd.append('barcode_number',          $('activeBarcodeValue').value);
            fd.append('pallet_number',           $('activePalletNumber').value);
            fd.append('universal_waste_item_id', $('uwPalletSelect').value || '');
            fd.append('description',             $('description').value);
            fd.append('estimated_count',         $('estimatedCount').value || 0);
            fd.append('gross_weight',            $('grossWeight').value || 0);
            fd.append('tare_weight',             $('tareWeight').value || 0);
            fd.append('net_weight',              $('netWeight').value || 0);
            fd.append('put_away_location',       $('putAwayLocation').value.trim());
            fd.append('marketing_reference',     $('marketingReference').value);
            fd.append('receiving_employee',      $('receivingEmployee').value);
            fd.append('notes',                   $('notes').value);
            Array.from($('photoInput').files).forEach(f => fd.append('main_photos[]', f));

            const data = await fetch(shipmentRoutes.save, { method: 'POST', body: fd }).then(r => r.json());
            if (data.success) { window.location.href = "{{ route('admin.downstream-vendor.index') }}"; }
            else              { showAlert(data.message || 'Could not save. Please try again.', 'error'); }
        } catch (e) {
            showAlert('Network error: ' + e.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Shipment Intake';
        }
    }

    // ── Reset ────────────────────────────────────────────────────────
    function resetForm() {
        document.getElementById('shipmentIntakeForm').reset();
        activeBarcode = ''; selectedClientId = '';
        ['selectedClientDisplay','newClientFormSection','activeBarcodeSection',
         'generatedBarcodeDisplay','selectedUwPreviewCard','existingUwSection']
            .forEach(id => $( id).classList.add('hidden'));
        $('newBarcodeSection').classList.remove('hidden');
        $('netWeightDisplay').textContent = '0.00 lbs';
        $('mainPhotoPreviews').innerHTML  = '';
        $('activeBarcodeValue').value     = '';
        $('activePalletNumber').value     = '';
        generateBarcode();
    }

    // ── Init ─────────────────────────────────────────────────────────
    function init() {
        generateBarcode();

        // Toggle UW / generate
        $('hasExistingUwPallet').addEventListener('change', e => {
            if (e.target.checked) {
                $('existingUwSection').classList.remove('hidden');
                $('newBarcodeSection').classList.add('hidden');
            } else {
                $('existingUwSection').classList.add('hidden');
                $('newBarcodeSection').classList.remove('hidden');
                $('selectedUwPreviewCard').classList.add('hidden');
                $('activeBarcodeValue').value = activeBarcode;
            }
        });

        $('regenerateBarcodeBtn').addEventListener('click', generateBarcode);
        $('useGeneratedBarcodeBtn').addEventListener('click', () => activateBarcode(activeBarcode));
        $('uwPalletSelect').addEventListener('change', onUwPalletSelected);
        $('clientSelect').addEventListener('change', e => updateClientCard(e.target.options[e.target.selectedIndex]));

        $('toggleNewClientBtn').addEventListener('click', () =>
            $('newClientFormSection').classList.toggle('hidden'));
        $('cancelNewClientBtn').addEventListener('click', () =>
            $('newClientFormSection').classList.add('hidden'));

        // Create client
        $('createClientBtn').addEventListener('click', async () => {
            const name = $('newClientName').value.trim();
            if (!name) return showAlert('Client name is required.', 'error');
            const fd = new FormData();
            fd.append('_token', csrfToken);
            ['clientName','clientEmail','clientPhone','clientCompany','clientAddress'].forEach(k => {
                const el = $(k.replace('client','newClient').replace(/[A-Z]/g, m => m.toLowerCase()));
                fd.append(k, el ? el.value : '');
            });
            fd.set('clientName', name);
            fd.set('clientEmail',   $('newClientEmail').value);
            fd.set('clientPhone',   $('newClientPhone').value);
            fd.set('clientCompany', $('newClientCompany').value);
            fd.set('clientAddress', $('newClientAddress').value);
            const data = await fetch(shipmentRoutes.createClient, { method: 'POST', body: fd }).then(r => r.json());
            if (data.success) {
                const sel = $('clientSelect');
                const opt = document.createElement('option');
                opt.value = data.client.id;
                opt.textContent = data.client.name + (data.client.company ? ' — ' + data.client.company : '');
                opt.dataset.name    = data.client.name;
                opt.dataset.email   = data.client.email   || '';
                opt.dataset.company = data.client.company || '';
                sel.appendChild(opt); sel.value = data.client.id;
                updateClientCard(opt);
                $('newClientFormSection').classList.add('hidden');
                showAlert('Client created & selected!', 'success');
            } else {
                showAlert(data.message || 'Could not create client.', 'error');
            }
        });

        $('generateJobNoBtn').addEventListener('click', () =>
            fetch(shipmentRoutes.jobNumber).then(r => r.json()).then(d => { if (d.success) $('jobNo').value = d.job_no; }));

        $('photoInput').addEventListener('change', e => addPhotoPreviews(e.target.files));
        $('saveShipmentBtn').addEventListener('click', saveShipment);
        $('resetFormBtn').addEventListener('click', resetForm);
    }

    document.addEventListener('DOMContentLoaded', init);
    return { calcNet };
})();
</script>
{{-- hidden fields must be in the DOM --}}
<input type="hidden" id="activeBarcodeValue" name="barcode_number" form="shipmentIntakeForm">
<input type="hidden" id="activePalletNumber" name="pallet_number"  form="shipmentIntakeForm">
@endpush
@endsection
