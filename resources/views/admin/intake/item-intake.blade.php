@extends('layouts.admin')

@section('content')
<div>
    <div class="w-full animate-in fade-in duration-500 p-2 md:p-0">
        {{-- Success Message --}}
        <div id="success-alert" class="hidden mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold flex items-center text-sm">
            <i class="fas fa-check-circle mr-2"></i> <span id="success-message"></span>
        </div>

        {{-- Error Message --}}
        <div id="error-alert" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-bold flex items-center text-sm">
            <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i> <span id="error-message"></span>
        </div>

        {{-- Stepper Navigation (2 Steps) --}}
        <div class="bg-white rounded-xl shadow-sm border border-[#f1f5f9] p-4 md:p-8 mb-6">
            @php $steps = ['Type', 'Details']; @endphp

            <div class="relative flex items-center justify-between w-full max-w-2xl mx-auto px-4 md:px-12">
                {{-- Progress Line --}}
                <div class="absolute top-4 md:top-5 left-12 right-12 h-[2px] bg-[#e2e8f0] -z-0">
                    <div id="progress-line" class="h-full bg-[#10b981] transition-all duration-500" style="width: 0%;"></div>
                </div>

                @foreach ($steps as $index => $label)
                    @php $currentLoopStep = $index + 1; @endphp
                    <div class="relative z-10 flex flex-col items-center step-indicator" data-step="{{ $currentLoopStep }}">
                        <div class="step-circle w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-[12px] md:text-[14px] transition-all duration-300 bg-[#f8fafc] border border-[#e2e8f0] text-[#94a3b8]">
                            <span class="step-number">{{ $currentLoopStep }}</span>
                            <i class="fas fa-check text-[10px] md:text-[12px] hidden"></i>
                        </div>
                        <span class="step-label text-[9px] md:text-[11px] font-bold uppercase tracking-tighter md:tracking-widest mt-2 md:mt-3 whitespace-nowrap text-[#cbd5e1]">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- STEP 1: Select Intake Type --}}
        <div id="step-1" class="step-container bg-white rounded-xl shadow-sm border border-[#f1f5f9] p-6 md:p-12 mb-6">
            <h3 class="text-[16px] md:text-[18px] font-bold text-[#1e293b] mb-1">Select Intake Type</h3>
            <p class="text-[12px] md:text-[13px] text-[#94a3b8] mb-6 md:mb-10 font-medium">Please select the type of intake to help organize your items.</p>
            
            <div class="space-y-4 md:space-y-6">
                {{-- Row 1: 3 Intake Types --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 text-center">
                    <div class="intake-type-card border border-[#f1f5f9] rounded-2xl p-6 md:p-8 cursor-pointer transition-all hover:border-[#2563eb] shadow-sm" data-type="eBay Product Intake">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-[#f8fafc] rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-tools text-[#2563eb] text-xl md:text-2xl"></i>
                        </div>
                        <h4 class="text-[14px] md:text-[16px] font-bold text-[#1e293b] mb-1">Refurb/E-commerce Intake</h4>
                        <p class="text-[11px] md:text-[12px] text-[#94a3b8] font-medium leading-relaxed">Items intended for refurbishing & store.</p>
                    </div>

                    <div class="intake-type-card border border-[#f1f5f9] rounded-2xl p-6 md:p-8 cursor-pointer transition-all hover:border-[#ef4444] shadow-sm" data-type="Data Destruction Intake">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-[#f8fafc] rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-eraser text-[#ef4444] text-xl md:text-2xl"></i>
                        </div>
                        <h4 class="text-[14px] md:text-[16px] font-bold text-[#1e293b] mb-1">Data Destruction Intake</h4>
                        <p class="text-[11px] md:text-[12px] text-[#94a3b8] font-medium leading-relaxed">Items for data wiping & shredding.</p>
                    </div>

                    <div class="intake-type-card border border-[#f1f5f9] rounded-2xl p-6 md:p-8 cursor-pointer transition-all hover:border-[#8b5cf6] shadow-sm" data-type="IT Assets Intake">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-[#f8fafc] rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-desktop text-[#8b5cf6] text-xl md:text-2xl"></i>
                        </div>
                        <h4 class="text-[14px] md:text-[16px] font-bold text-[#1e293b] mb-1">IT Assets Intake</h4>
                        <p class="text-[11px] md:text-[12px] text-[#94a3b8] font-medium leading-relaxed">Items for IT asset management.</p>
                    </div>
                </div>

                {{-- Row 2: 2 Intake Types Below Centered (Repair Intake before Universal Waste Intake) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 text-center max-w-2xl mx-auto">
                    <div class="intake-type-card border border-[#f1f5f9] rounded-2xl p-6 md:p-8 cursor-pointer transition-all hover:border-[#f59e0b] shadow-sm" data-type="Repair Intake">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-[#f8fafc] rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-wrench text-[#f59e0b] text-xl md:text-2xl"></i>
                        </div>
                        <h4 class="text-[14px] md:text-[16px] font-bold text-[#1e293b] mb-1">Repair Intake</h4>
                        <p class="text-[11px] md:text-[12px] text-[#94a3b8] font-medium leading-relaxed">Items for diagnostics, repair & servicing.</p>
                    </div>

                    <div class="intake-type-card border border-[#f1f5f9] rounded-2xl p-6 md:p-8 cursor-pointer transition-all hover:border-[#10b981] shadow-sm" data-type="Universal Waste Intake">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-[#f8fafc] rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-recycle text-[#10b981] text-xl md:text-2xl"></i>
                        </div>
                        <h4 class="text-[14px] md:text-[16px] font-bold text-[#1e293b] mb-1">Universal Waste Intake</h4>
                        <p class="text-[11px] md:text-[12px] text-[#94a3b8] font-medium leading-relaxed">Items for recycling/processing.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 2: Barcode & Item Details --}}
        <div id="step-2" class="step-container hidden bg-white rounded-xl shadow-sm border border-[#f1f5f9] p-6 md:p-12 mb-6">
            {{-- Active Type Header --}}
            <div class="bg-[#f0f7ff] border border-[#dbeafe] rounded-xl p-4 mb-6 text-[13px] text-[#2563eb] flex items-center justify-between">
                <div>
                    <span class="font-bold">Active Type:</span> <span id="active-type-display" class="font-bold underline"></span>
                </div>
            </div>

            {{-- STANDARD INTAKE FLOW --}}
            <div id="standard-intake-flow">
                {{-- Barcode Entry Section --}}
                <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 mb-8 space-y-4">
                <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2 mb-2">
                    <i class="fas fa-barcode text-blue-600"></i> Barcode Entry
                </h4>

                {{-- Existing Barcode Checkbox --}}
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="existing-barcode-checkbox" class="w-5 h-5 text-blue-600 rounded border-gray-300 cursor-pointer">
                    <label for="existing-barcode-checkbox" class="ml-2 text-[14px] text-[#475569] font-medium cursor-pointer">Existing barcode (Use Pallet)</label>
                </div>

                {{-- Generate New Barcode Mode --}}
                <div id="generate-mode" class="space-y-4">
                    <button id="generate-barcode-btn" class="w-full bg-[#2563eb] text-white py-3.5 rounded-xl font-bold text-[14px] shadow-md hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-qrcode"></i> Generate New Barcode
                    </button>

                    <div id="barcode-display" class="hidden max-w-xs mx-auto border border-slate-200 rounded-2xl py-4 px-4 flex flex-col items-center bg-white shadow-sm">
                        <div class="p-2 text-center w-full overflow-hidden">
                            <div id="barcode-image" style="text-align: center;"></div>
                            <p id="barcode-text" class="font-mono text-[14px] text-slate-800 font-bold uppercase mt-2"></p>
                        </div>
                    </div>
                </div>

                {{-- Pallet Assignment Mode --}}
                <div id="pallet-mode" class="hidden bg-white border border-[#e2e8f0] rounded-2xl p-6 space-y-4">
                    {{-- Mode Selector (Camera/USB) --}}
                    <div class="flex p-1 bg-slate-200/50 rounded-2xl mb-6 max-w-xs mx-auto">
                        <button type="button" onclick="window.itemIntakeManager.scanMode='camera'; window.itemIntakeManager.stopScanner();" 
                            class="mode-camera flex-1 py-2 rounded-xl transition-all font-bold text-[10px] uppercase tracking-widest border-none bg-white shadow-sm text-blue-600">
                            <i class="fas fa-camera mr-1"></i> Camera
                        </button>
                        <button type="button" onclick="window.itemIntakeManager.scanMode='usb'; window.itemIntakeManager.stopScanner();" 
                            class="mode-usb flex-1 py-2 rounded-xl transition-all font-bold text-[10px] uppercase tracking-widest border-none text-slate-500">
                            <i class="fas fa-usb mr-1"></i> USB Scanner
                        </button>
                    </div>

                    {{-- Scanner UI --}}
                    <div class="mb-6">
                        {{-- Camera Mode --}}
                        <div id="camera-mode" class="text-center space-y-4">
                            <button onclick="window.itemIntakeManager.toggleScanner()" type="button"
                                id="scanner-toggle-btn"
                                class="w-full sm:w-auto px-8 py-3 rounded-full font-bold text-[11px] uppercase tracking-widest shadow-lg mx-auto bg-blue-600 text-white">
                                <i class="fas fa-camera"></i>
                                <span>Scan Barcode</span>
                            </button>
                            <div id="interactive" class="hidden relative max-w-[280px] mx-auto overflow-hidden rounded-2xl border-4 border-white shadow-xl bg-black" style="aspect-ratio: 1;">
                                <div class="viewport"></div>
                                <div class="absolute inset-x-0 top-0 h-1 bg-blue-500 animate-scan"></div>
                            </div>
                        </div>

                        {{-- USB Mode --}}
                        <div id="usb-mode" class="hidden animate-in fade-in">
                            <div class="bg-blue-50 border-2 border-dashed border-blue-200 rounded-2xl p-6 text-center">
                                <i class="fas fa-keyboard text-2xl text-blue-500 mb-2"></i>
                                <p class="text-[12px] font-bold text-blue-800 uppercase">USB Scanner Active</p>
                                <input type="text" id="usb-input" 
                                    @keydown.enter="if(this.value) { window.itemIntakeManager.processUsbScan(this.value); this.value=''; }"
                                    class="absolute opacity-0 pointer-events-none" autofocus>
                            </div>
                        </div>
                    </div>

                    {{-- Pallet Select --}}
                    <div class="space-y-4">
                        <label class="text-[13px] font-bold text-[#475569] block">Select Pallet as Barcode</label>
                        <select id="pallet-select" class="w-full p-4 border rounded-xl text-[14px] bg-white font-bold">
                            <option value="">-- Select or Scan Pallet --</option>
                            @php
                                $barcodeCounts = [];
                                foreach ($pallets_list as $pallet) {
                                    $barcode = $pallet->barcode_number;
                                    if (!isset($barcodeCounts[$barcode])) {
                                        $barcodeCounts[$barcode] = 0;
                                    }
                                    $barcodeCounts[$barcode]++;
                                }
                                $currentCount = [];
                            @endphp
                            @foreach ($pallets_list as $pallet)
                                @php
                                    $barcode = $pallet->barcode_number;
                                    if (!isset($currentCount[$barcode])) {
                                        $currentCount[$barcode] = 1;
                                    } else {
                                        $currentCount[$barcode]++;
                                    }
                                    $displayText = $barcodeCounts[$barcode] > 1 
                                        ? $barcode . ' (#' . $currentCount[$barcode] . ')' 
                                        : $barcode;
                                @endphp
                                <option value="{{ $pallet->id }}" data-barcode="{{ $barcode }}">{{ $displayText }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="pallet-barcode-display" class="hidden max-w-xs mx-auto border border-slate-100 rounded-2xl py-4 px-4 flex flex-col items-center bg-slate-50/40">
                        <div class="bg-white p-4 border border-slate-200 rounded-xl text-center shadow-sm w-full overflow-hidden">
                            <div id="pallet-barcode-image" style="text-align: center;"></div>
                            <p id="pallet-barcode-text" class="font-mono text-[14px] text-slate-800 font-bold uppercase mt-2"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Item Details Header & Fields --}}
            <h3 class="text-[18px] font-bold text-[#1e293b] mb-6 pt-4 border-t border-slate-100">Item Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 md:gap-y-6">
                <div id="standard-item-fields" class="contents">
                    {{-- 1. Folder Type * --}}
                    <div class="space-y-1 md:space-y-2 hidden col-span-1 md:col-span-2 lg:col-span-3" id="ebay-folder-type-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Folder Type *</label>
                        <select name="folder_type" id="ebay-folder-type-select" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-slate-50 outline-none focus:border-[#2563eb] focus:bg-white transition-all">
                            <option value="Ebay">Ebay</option>
                            <option value="Denver">Denver</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>

                    {{-- 2. Categories, Sub Categories, Sub Sub Categories (data from DB) --}}
                    <div id="category-selection-wrapper" class="col-span-1 md:col-span-2 lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1 md:space-y-2">
                            <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Category *</label>
                            <select name="main_category" id="main-category-select" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-slate-50 outline-none focus:border-[#2563eb] focus:bg-white transition-all">
                                <option value="">Select Main Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1 md:space-y-2">
                            <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Sub Category</label>
                            <select name="sub_category" id="sub-category-select" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-slate-50 outline-none focus:border-[#2563eb] focus:bg-white transition-all" disabled>
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="space-y-1 md:space-y-2">
                            <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Sub Sub Category</label>
                            <select name="sub_sub_category" id="sub-sub-category-select" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-slate-50 outline-none focus:border-[#2563eb] focus:bg-white transition-all" disabled>
                                <option value="">Select Sub Sub Category</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="category" id="category-final" value="">

                    {{-- 3. Description * --}}
                    <div class="space-y-1 md:space-y-2 col-span-1 md:col-span-1 lg:col-span-2">
                        <label id="item-name-label" class="text-[12px] md:text-[13px] font-bold text-[#475569]">Description *</label>
                        <input type="text" name="item_name" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all" placeholder="Product description / title">
                    </div>

                    {{-- Brand * & Model * (Hidden for Refurb, shown for other intake types) --}}
                    <div class="space-y-1 md:space-y-2" id="brand-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Brand *</label>
                        <input type="text" name="brand" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>
                    <div class="space-y-1 md:space-y-2" id="model-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Model *</label>
                        <input type="text" name="model" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>

                    {{-- 4. Serial Number (with option to print serial number) --}}
                    <div class="space-y-1 md:space-y-2 col-span-1 md:col-span-1 lg:col-span-1" id="serial-number-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Serial Number</label>
                        
                        {{-- Default Single Serial Input with Camera Scan, Auto Generate, and Print --}}
                        <div id="single-serial-container" class="space-y-2">
                            <div class="flex flex-wrap sm:flex-nowrap gap-1.5 sm:gap-2">
                                <input type="text" name="serial_number" id="serial-number-field" class="form-input flex-1 min-w-[120px] p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all" placeholder="Serial Number">
                                <button type="button" id="ebay-scan-serial-btn" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 sm:px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap" title="Scan Serial Number from Camera">
                                    <i class="fas fa-camera text-blue-600"></i> Scan
                                </button>
                                <button type="button" id="ebay-generate-serial-btn" class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-2.5 sm:px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap" title="Generate Random Serial Number">
                                    <i class="fas fa-magic"></i> Gen
                                </button>
                                <button type="button" id="ebay-print-serial-btn" class="bg-slate-800 hover:bg-black text-white px-2.5 sm:px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap shadow-sm hover:shadow active:scale-95" title="Print Serial Number Label">
                                    <i class="fas fa-print text-amber-400"></i> Print
                                </button>
                            </div>
                        </div>

                        {{-- Data Destruction Multiple Serial Input --}}
                        <div id="multiple-serial-container" class="hidden space-y-3">
                            <div class="flex gap-2">
                                <input type="text" id="serial-number-input" placeholder="Type or scan a serial number..." class="form-input flex-1 p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] bg-slate-50">
                                <button type="button" id="add-serial-btn" class="bg-[#2563eb] text-white px-4 rounded-xl font-bold text-[13px] hover:bg-blue-700 transition-all flex items-center">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" id="bulk-serial-toggle" class="bg-slate-100 text-slate-600 px-4 rounded-xl font-bold text-[13px] hover:bg-slate-200 transition-all flex items-center" title="Bulk Import / Paste">
                                    <i class="fas fa-paste"></i>
                                </button>
                            </div>

                            {{-- Bulk Textarea --}}
                            <div id="bulk-serial-container" class="hidden">
                                <textarea id="bulk-serial-input" rows="4" placeholder="Enter multiple serial numbers, one per line or separated by commas..." class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none resize-none bg-slate-50"></textarea>
                                <div class="flex justify-end gap-2 mt-2">
                                    <button type="button" id="cancel-bulk-serial-btn" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-slate-200">Cancel</button>
                                    <button type="button" id="apply-bulk-serial-btn" class="bg-[#2563eb] text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700">Apply</button>
                                </div>
                            </div>

                            {{-- Badges / Tags List --}}
                            <div id="serial-tags-container" class="flex flex-wrap gap-2 p-3 bg-[#f8fafc] border border-dashed border-slate-200 rounded-xl min-h-[50px]">
                                <span class="text-[12px] text-slate-400 italic" id="no-serials-placeholder">No serial numbers added yet.</span>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Location --}}
                    <div class="space-y-1 md:space-y-2 col-span-1" id="put-away-location-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Location</label>
                        <input type="text" name="put_away_location" placeholder="e.g. Warehouse A, Shelf 2" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>

                    {{-- 6. Condition * --}}
                    <div class="space-y-1 md:space-y-2 col-span-1">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Condition *</label>
                        <select name="condition" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-slate-50 outline-none focus:border-[#2563eb] focus:bg-white transition-all">
                            <option value="">Select condition</option>
                            <option value="New">New</option>
                            <option value="Used">Used</option>
                            <option value="Refurbished">Refurbished</option>
                            <option value="Open Box">Open Box</option>
                            <option value="Poor">Poor</option>
                            <option value="Damaged">Damaged</option>
                        </select>
                    </div>

                    {{-- 7. Value ($) --}}
                    <div class="space-y-1 md:space-y-2 col-span-1">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Value ($)</label>
                        <input type="number" name="estimated_value" step="0.01" placeholder="0.00" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>

                    {{-- 8. Weight (lbs) --}}
                    <div class="space-y-1 md:space-y-2 col-span-1">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Weight (lbs)</label>
                        <input type="number" name="weight" step="0.01" placeholder="0.00" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                        <p id="weight-remaining-text" class="text-[11px] text-[#64748b] font-medium">Remaining Weight: -- lbs</p>
                    </div>

                    {{-- 9. Quantity --}}
                    <div class="space-y-1 md:space-y-2 col-span-1">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Quantity</label>
                        <input type="number" name="quantity" placeholder="1" class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                        <p id="quantity-remaining-text" class="text-[11px] text-[#64748b] font-medium">Remaining Quantity: --</p>
                    </div>

                    {{-- 10. eBay Link (Shown for Refurb) --}}
                    <div class="space-y-1 md:space-y-2 col-span-1 hidden" id="ebay-link-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">eBay Link</label>
                        <input type="url" name="ebay_link" placeholder="https://www.ebay.com/itm/..." class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>

                    {{-- 11. Website Link (Shown for Refurb) --}}
                    <div class="space-y-1 md:space-y-2 col-span-1 hidden" id="website-link-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Website Link</label>
                        <input type="url" name="website_link" placeholder="https://denverdiscountcomputers.com/..." class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>

                    {{-- 12. Social Media Link (Shown for Refurb) --}}
                    <div class="space-y-1 md:space-y-2 col-span-1 md:col-span-1 lg:col-span-2 hidden" id="social-media-link-wrapper">
                        <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Social Media Link</label>
                        <input type="url" name="social_media_1" placeholder="https://facebook.com/..." class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] focus:bg-white bg-slate-50 transition-all">
                    </div>
                </div>

                <!-- Data Destruction Items Dynamic Section -->
                <div id="data-destruction-items-section" class="hidden col-span-1 md:col-span-2 lg:col-span-3 space-y-6 mb-6">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <h4 class="text-[15px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i id="dd-items-header-icon" class="fas fa-list text-red-500"></i> <span id="dd-items-header-text">Data Destruction Items</span>
                        </h4>
                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-black" id="dd-items-count-badge">1 Item</span>
                    </div>
                    
                    <div id="data-destruction-items-list" class="space-y-4"></div>
                    
                    <button type="button" id="add-dd-item-btn" class="w-full md:w-auto bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-sm">
                        <i class="fas fa-plus text-blue-600"></i> Add Another Item
                    </button>
                </div>
            </div>

            <div id="selected-pallet-summary" class="mt-3 hidden rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-[12px] text-blue-900 font-semibold">
                Selected Pallet Remaining: <span id="selected-pallet-remaining-summary">--</span>
            </div>

            {{-- 13. Notes --}}
            <div class="mt-4 md:mt-6 space-y-2">
                <label class="text-[13px] font-bold text-[#475569]">Notes</label>
                <textarea name="notes" rows="3" placeholder="Enter notes or additional details..." class="form-input w-full p-4 border border-[#e2e8f0] rounded-xl text-[14px] outline-none resize-none bg-slate-50 focus:border-[#2563eb] focus:bg-white transition-all"></textarea>
            </div>

            {{-- 14. Intake Employee & 15. Person --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
                {{-- Intake Employee --}}
                <div class="space-y-1 md:space-y-2 relative" id="intake-employee-wrapper">
                    <label id="intake-employee-label" class="text-[12px] md:text-[13px] font-bold text-[#475569]">Intake Employee</label>
                    <div class="relative">
                        <input type="text" name="intake_employee" id="intake-employee-select" autocomplete="off" class="form-input w-full p-3 pr-10 border border-[#e2e8f0] rounded-xl text-[14px] outline-none bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all" placeholder="Enter or select intake employee name...">
                        <button type="button" id="intake-employee-toggle-btn" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="intake-employee-chevron"></i>
                        </button>
                    </div>
                    <div id="intake-employee-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto py-1 divide-y divide-slate-50">
                        <div id="intake-employee-options-container" class="py-1">
                            @if (isset($employees))
                                @foreach ($employees as $emp)
                                    <div class="intake-emp-option px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer font-medium transition-colors flex items-center justify-between" data-value="{{ $emp->name }}">
                                        <span>{{ $emp->name }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Person (shown for Refurb/E-commerce intake) --}}
                <div class="space-y-1 md:space-y-2 hidden" id="ebay-person-field-wrapper">
                    <label class="text-[12px] md:text-[13px] font-bold text-[#475569]">Person</label>
                    <select name="ebay_person" id="ebay-person-input" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-slate-50 outline-none focus:border-[#2563eb] focus:bg-white transition-all">
                        <option value="">Select Person</option>
                        <option value="Abid">Abid</option>
                        <option value="Junaid">Junaid</option>
                    </select>
                </div>
            </div>

            {{-- Photo Upload --}}
            <div class="mt-6" id="photo-section">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-bold text-slate-700">Pallet / Item Photos & Files</label>
                    <span id="photo-count-badge" class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">0 attached</span>
                </div>

                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <div id="photo-previews" class="flex flex-wrap gap-3"></div>

                    {{-- Camera Capture Button (shown when Refurb/E-commerce Intake is selected) --}}
                    <div id="camera-capture-btn-wrapper" class="hidden w-full sm:w-auto">
                        <button type="button" id="open-camera-capture-btn" class="w-full sm:w-auto cursor-pointer bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-xl font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 group">
                            <i class="fas fa-camera text-base group-hover:scale-110 transition-transform"></i>
                            <span>Capture via Camera</span>
                        </button>
                    </div>

                    {{-- Regular File Select Button --}}
                    <div class="w-full sm:w-auto">
                        <label class="w-full sm:w-auto cursor-pointer bg-slate-50 hover:bg-slate-100 border-2 border-dashed border-slate-200 px-6 py-4 rounded-xl font-bold text-xs text-slate-500 transition-all flex items-center justify-center group">
                            <i class="fas fa-folder-open mr-2 text-lg text-slate-400 group-hover:text-blue-500"></i>
                            <span>Select Photos & Files</span>
                            <input type="file" id="photo-input" name="photos" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                        </label>
                    </div>
                </div>

                {{-- Upload Progress --}}
                <div id="upload-progress" class="hidden w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-blue-600 h-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>

            {{-- Save Action Section --}}
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-3" id="save-item-container">
                <button id="save-draft-item-btn" type="button" class="flex-1 no-print bg-amber-500 text-white py-4 rounded-xl font-bold text-[15px] flex items-center justify-center transition-all hover:bg-amber-600 shadow-lg shadow-amber-500/20 active:scale-95">
                    <i class="fas fa-file-alt mr-2"></i> Save as Draft
                </button>
                <button id="save-item-btn" type="button" class="w-full flex-1 no-print bg-[#2563eb] text-white py-4 rounded-xl font-bold text-[15px] flex items-center justify-center transition-all hover:bg-blue-700 shadow-lg shadow-blue-500/20 active:scale-95">
                    <i class="fas fa-save mr-2"></i> Save Item
                </button>
            </div>
            </div> {{-- End standard-intake-flow --}}

            {{-- REPAIR INTAKE FLOW --}}
            <div id="repair-intake-flow" class="hidden space-y-8">
                {{-- SECTION 1: Pallet Barcode Entry --}}
                <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-4">
                    <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                        <i class="fas fa-pallet text-blue-600"></i> Pallet Barcode Entry
                    </h4>

                    {{-- Existing Barcode Checkbox --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="repair-has-existing-barcode" class="w-5 h-5 text-blue-600 rounded border-gray-300 cursor-pointer">
                        <label for="repair-has-existing-barcode" class="ml-2 text-[14px] text-[#475569] font-medium cursor-pointer">
                            Pallet has existing barcode
                        </label>
                    </div>

                    {{-- New Barcode Section --}}
                    <div id="repair-new-barcode-section" class="space-y-4">
                        <div class="bg-blue-50/60 border border-blue-100 rounded-xl px-5 py-3 text-blue-700 text-[13px]">
                            This pallet doesn't have a barcode. We'll generate one for you.
                        </div>

                        <button type="button" id="repair-regenerate-barcode-btn"
                            class="w-full bg-[#2563eb] text-white py-3.5 rounded-xl font-bold text-[14px] shadow-md hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-sync-alt"></i> Regenerate New Barcode
                        </button>

                        <div id="repair-generated-barcode-display" class="max-w-xs mx-auto border border-slate-200 rounded-2xl py-6 px-4 flex flex-col items-center bg-white shadow-sm">
                            <p id="repair-generated-barcode-number" class="text-xl font-bold text-slate-800 mb-3">PAL-7536994674</p>
                            <div class="bg-white p-3 border border-slate-100 rounded-xl mb-4 text-center shadow-xs w-full">
                                <div id="repair-generated-barcode-image" class="h-16 md:h-20 max-w-full mx-auto mb-2 flex items-center justify-center"></div>
                                <p id="repair-generated-barcode-text" class="font-mono text-[13px] md:text-[14px] tracking-[0.25em] text-slate-800 font-bold uppercase">PAL-7536994674</p>
                            </div>
                            <button type="button" id="repair-use-generated-barcode-btn"
                                class="w-full bg-[#2563eb] text-white py-2.5 rounded-xl font-bold text-sm hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-check"></i> Use This Barcode
                            </button>
                        </div>
                    </div>

                    {{-- Existing Barcode Mode --}}
                    <div id="repair-existing-barcode-section" class="hidden space-y-3 bg-white border border-[#e2e8f0] rounded-xl p-5">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            Select Pallet from List
                        </label>
                        <select id="repair-existing-pallet-select" class="w-full p-3 border border-slate-200 rounded-xl text-[14px] bg-white font-medium outline-none">
                            <option value="">-- Select or Scan Pallet --</option>
                            @foreach ($pallets_list as $pallet)
                                <option value="{{ $pallet->barcode_number }}">{{ $pallet->barcode_number }} {{ $pallet->put_away_location ? '(' . $pallet->put_away_location . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Active Pallet Barcode Hidden Field & Badge --}}
                    <input type="hidden" id="repair-active-pallet-barcode" value="">
                    <div id="repair-active-barcode-badge" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800 font-bold">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                            <span>Selected Pallet Barcode: <span id="repair-active-barcode-text" class="font-mono text-emerald-900"></span></span>
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
                            <input type="date" id="repair-intake-date" name="repair_intake_date"
                                value="{{ date('Y-m-d') }}"
                                class="w-full border-slate-200 rounded-xl p-3 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-semibold text-slate-800">
                        </div>

                        {{-- Job # --}}
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">
                                <i class="fas fa-briefcase text-blue-600 mr-1"></i> Job #
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="repair-job-no" name="repair_job_no"
                                    class="flex-1 border-slate-200 rounded-xl p-3 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                    placeholder="e.g., 583921">
                                <button type="button" id="repair-generate-job-no-btn"
                                    class="w-auto bg-blue-50 border border-blue-200 hover:bg-blue-600 hover:text-white text-blue-700 px-4 py-3 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center justify-center gap-2 shadow-sm">
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
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select id="repair-client-select" class="flex-1 border-slate-200 rounded-xl p-3 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                        data-email="{{ $client->email }}" data-phone="{{ $client->phone }}"
                                        data-company="{{ $client->company }}" data-address="{{ $client->address }}">
                                        {{ $client->name }} {{ $client->company ? '(' . $client->company . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="repair-toggle-new-client-btn"
                                class="w-full sm:w-auto bg-slate-100 border border-slate-200 hover:bg-slate-200 px-5 py-3 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center justify-center gap-1.5 shadow-sm text-slate-700">
                                <i class="fas fa-plus text-blue-600"></i> New Client
                            </button>
                        </div>

                        {{-- Selected Client Info Display --}}
                        <div id="repair-selected-client-display" class="hidden mt-3 p-3 bg-blue-50/60 border border-blue-100 rounded-xl flex items-center justify-between text-xs text-blue-900">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-user-circle text-blue-600 text-base"></i>
                                <div>
                                    <span id="repair-selected-client-name" class="font-bold"></span>
                                    <span id="repair-selected-client-meta" class="text-slate-500 ml-2"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Inline New Client Form --}}
                        <div id="repair-new-client-form-section" class="hidden mt-4 bg-white border border-blue-200 rounded-xl p-4 md:p-6 space-y-4 shadow-sm">
                            <h5 class="text-xs font-black text-blue-600 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-user-plus"></i> Create New Client
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <input type="text" id="repair-new-client-name" placeholder="Full Name *"
                                        class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <input type="email" id="repair-new-client-email" placeholder="Email Address"
                                        class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <input type="text" id="repair-new-client-phone" placeholder="Phone Number"
                                        class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <input type="text" id="repair-new-client-company" placeholder="Company Name (Optional)"
                                        class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <textarea id="repair-new-client-address" rows="2" placeholder="Street Address"
                                        class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-blue-500 outline-none"></textarea>
                                </div>
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="button" id="repair-create-new-client-btn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition-all flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-check"></i> Save & Select
                                </button>
                                <button type="button" id="repair-cancel-new-client-btn"
                                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl text-xs transition-all">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: Item Details -> Repair Items (Same style as Data Destruction) --}}
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 space-y-6">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <h4 class="text-[15px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-tools text-amber-500"></i> <span>Repair Items</span>
                        </h4>
                        <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-xs font-black" id="repair-items-count-badge">1 Item</span>
                    </div>

                    <div id="repair-items-list" class="space-y-4">
                        {{-- Populated dynamically by JS --}}
                    </div>

                    <button type="button" id="add-repair-item-btn" class="w-full md:w-auto bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-sm">
                        <i class="fas fa-plus text-amber-600"></i> Add Another Item
                    </button>
                </div>

                {{-- SECTION 4: Warehouse & Receiving Details --}}
                <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-5">
                    <h4 class="text-[15px] font-bold text-[#1e293b] flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-600"></i> Warehouse & Receiving
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Put-away Location --}}
                        <div class="space-y-1.5">
                            <label class="text-[12px] font-bold text-[#475569]">Put-away Location <span class="text-red-500">*</span></label>
                            <input type="text" id="repair-put-away-location" placeholder="e.g., Warehouse A"
                                class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] bg-white">
                        </div>

                        {{-- Marketing Reference --}}
                        <div class="space-y-1.5">
                            <label class="text-[12px] font-bold text-[#475569]">Marketing Reference</label>
                            <select id="repair-marketing-ref" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-white outline-none">
                                @foreach ($marketingRefs as $ref)
                                    <option value="{{ $ref }}">{{ $ref }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Receiving Employee --}}
                        <div class="space-y-1.5 relative" id="repair-receiving-employee-wrapper">
                            <label class="text-[12px] font-bold text-[#475569]">Receiving Employee</label>
                            <div class="relative">
                                <input type="text" id="repair-receiving-employee" autocomplete="off"
                                    class="form-input w-full p-3 pr-10 border border-[#e2e8f0] rounded-xl text-[14px] outline-none bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
                                    placeholder="Select or type employee...">
                                <button type="button" id="repair-receiving-employee-toggle-btn"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1">
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="repair-receiving-employee-chevron"></i>
                                </button>
                            </div>
                            <div id="repair-receiving-employee-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto py-1 divide-y divide-slate-50">
                                <div id="repair-receiving-employee-options-container" class="py-1">
                                    @if (isset($employees))
                                        @foreach ($employees as $emp)
                                            <div class="repair-receiving-emp-option px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer font-medium transition-colors flex items-center justify-between" data-value="{{ $emp->name }}">
                                                <span>{{ $emp->name }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Receiving Notes --}}
                    <div class="space-y-1.5">
                        <label class="text-[12px] font-bold text-[#475569]">Receiving Notes</label>
                        <textarea id="repair-receiving-notes" rows="3" placeholder="Enter any notes about the incoming repair items or condition..."
                            class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-[#2563eb] bg-white resize-y"></textarea>
                    </div>

                    {{-- Item Photos and Files --}}
                    <div class="space-y-3 pt-2 border-t border-slate-200/60">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-bold text-slate-700">Item photos and files</label>
                            <span id="repair-photo-count-badge" class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">0 attached</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div id="repair-photo-previews" class="flex flex-wrap gap-3"></div>

                            {{-- File Select Button --}}
                            <div class="w-full sm:w-auto">
                                <label class="w-full sm:w-auto cursor-pointer bg-white hover:bg-slate-50 border-2 border-dashed border-slate-200 px-6 py-3.5 rounded-xl font-bold text-xs text-slate-600 transition-all flex items-center justify-center gap-2 group shadow-sm">
                                    <i class="fas fa-folder-open text-slate-400 group-hover:text-blue-500"></i>
                                    <span>Select Photos & Files</span>
                                    <input type="file" id="repair-photo-input" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 5: For Technician Use Only --}}
                <div class="bg-indigo-50/40 border border-indigo-100 rounded-2xl p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
                        <h4 class="text-[14px] font-bold text-indigo-950 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-user-cog text-indigo-600"></i> For Technician use only section
                        </h4>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-500 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">Tech Only</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tech Name --}}
                        <div class="space-y-1.5">
                            <label class="text-[12px] font-bold text-[#475569]">Tech Name</label>
                            <input type="text" id="repair-tech-name" placeholder="Technician Name"
                                class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-indigo-500 bg-white">
                        </div>

                        {{-- Repair Status --}}
                        <div class="space-y-1.5">
                            <label class="text-[12px] font-bold text-[#475569]">Repair Status</label>
                            <select id="repair-status-select" class="form-select w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] bg-white outline-none font-semibold text-slate-800">
                                <option value="Receiving" selected>Receiving</option>
                                <option value="Processing">Processing</option>
                                <option value="Ready for Pickup">Ready for Pickup</option>
                                <option value="Ready for Shipment">Ready for Shipment</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    {{-- Technician Notes --}}
                    <div class="space-y-1.5">
                        <label class="text-[12px] font-bold text-[#475569]">Technician Notes</label>
                        <textarea id="repair-tech-notes" rows="3" placeholder="Enter diagnosis, work performed, or parts needed..."
                            class="form-input w-full p-3 border border-[#e2e8f0] rounded-xl text-[14px] outline-none focus:border-indigo-500 bg-white resize-y"></textarea>
                    </div>
                </div>

                {{-- SECTION 6: Email Customer Checkbox & Action Buttons --}}
                <div class="pt-4 border-t border-slate-200 space-y-5">
                    {{-- Checkbox: Send repair email to customer --}}
                    <div class="flex items-center bg-blue-50/50 border border-blue-100 p-4 rounded-xl">
                        <input type="checkbox" id="repair-send-email-checkbox" checked class="w-5 h-5 text-blue-600 rounded border-gray-300 cursor-pointer">
                        <label for="repair-send-email-checkbox" class="ml-3 text-[14px] text-slate-700 font-semibold cursor-pointer flex items-center gap-2">
                            <i class="fas fa-envelope text-blue-600"></i> Also send repair email to customer
                        </label>
                    </div>

                    {{-- Save as Draft & Save Repair Entry Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button id="repair-save-draft-btn" type="button"
                            class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-4 rounded-xl font-bold text-[15px] flex items-center justify-center transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                            <i class="fas fa-file-alt mr-2"></i> Save as Draft
                        </button>
                        <button id="repair-save-btn" type="button"
                            class="flex-1 bg-[#2563eb] hover:bg-blue-700 text-white py-4 rounded-xl font-bold text-[15px] flex items-center justify-center transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                            <i class="fas fa-save mr-2"></i> Save Repair Entry
                        </button>
                    </div>
                </div>
            </div>

            {{-- Success Screen directly inside Step 2 --}}
            <div id="success-screen" class="hidden mt-12 pt-12 border-t-2 border-dashed border-slate-100 flex flex-col items-center no-print">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-6 ring-8 ring-emerald-50">
                    <i class="fas fa-check text-2xl"></i>
                </div>

                <h2 class="text-2xl font-black text-slate-800 mb-2">Item Added Successfully!</h2>
                <p class="text-slate-400 mb-10 text-sm">The item has been recorded in the system.</p>

                <div class="bg-white border-2 border-slate-50 rounded-[2.5rem] p-10 mb-10 shadow-xl max-w-sm w-full text-center">
                    <div class="space-y-4">
                        <div id="success-barcode-image"></div>
                        <p id="success-barcode-text" class="font-mono text-xl tracking-[0.3em] text-slate-900 font-bold uppercase mt-4"></p>
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-center">Item Barcode: <span id="success-barcode-display"></span></p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 w-full max-w-md mx-auto">
                    <button onclick="window.itemIntakeManager?.printLabel()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-4 rounded-2xl font-bold flex items-center justify-center border border-slate-200">
                        <i class="fas fa-print mr-2"></i> Print Label
                    </button>
                    <button id="add-another-btn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-200">
                        Add Another Item
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div id="footer-actions" class="flex flex-col sm:flex-row justify-between items-center gap-4 py-4">
            <button id="back-btn" class="w-full sm:w-auto bg-[#f1f5f9] text-[#64748b] px-8 py-4 rounded-xl font-bold text-[13px] flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>

            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <button id="reset-btn" class="flex-1 sm:flex-none bg-red-50 text-red-500 px-6 py-4 rounded-xl font-bold text-[13px] flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden Print Label --}}
    <div id="printable-item-label" class="hidden-on-screen">
        <div style="border: 2px solid #000; padding: 20px; width: 380px; margin: auto; font-family: sans-serif; color: #000; background: #fff;">
            <div style="text-align: center; margin-bottom: 10px;">
                <div id="print-barcode-image"></div>
                <p id="print-barcode-text" style="font-family: monospace; font-size: 22px; font-weight: bold; margin: 5px 0; letter-spacing: 2px;"></p>
            </div>

            <hr style="border: 0; border-top: 1px solid #000; margin: 10px 0;">

            <div style="text-align: left; font-size: 14px; line-height: 1.6;">
                <div><strong>Item:</strong> <span id="print-item-name"></span></div>
                <div><strong>Brand/Model:</strong> <span id="print-brand-model"></span></div>
                <div><strong>Serial:</strong> <span id="print-serial"></span></div>
                <div><strong>Category:</strong> <span id="print-category"></span></div>

                <div style="margin-top: 10px;">
                    <div><strong>Barcode:</strong> <span id="print-barcode-final"></span></div>
                    <div><strong>Date:</strong> <span id="print-date"></span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Continuous Multi-Image Camera Capture Modal Dialog --}}
    <div id="intake-camera-modal" class="hidden fixed inset-0 z-[99999] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-5 no-print" style="overscroll-behavior: contain;">
        {{-- Modal Card --}}
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col my-auto animate-scaleIn">
            
            {{-- 1. Modal Header --}}
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <i class="fas fa-camera text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 leading-tight">Product Camera</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Capture multiple photos without closing camera</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Flip Camera --}}
                    <button type="button" id="camera-switch-btn" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5" title="Switch between front and back camera">
                        <i class="fas fa-sync-alt text-[11px]"></i>
                        <span class="hidden sm:inline">Flip Camera</span>
                    </button>
                    {{-- Close X --}}
                    <button type="button" id="camera-close-x-btn" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all text-sm font-bold" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- 2. Camera Viewfinder (Half Screen Size, ~260px - 280px) & Shutter Capture Bar --}}
            <div class="p-4 sm:p-5 bg-slate-900 flex flex-col items-center">
                {{-- Video Viewfinder Container --}}
                <div class="relative w-full h-64 sm:h-72 rounded-2xl overflow-hidden bg-black flex items-center justify-center border border-slate-800 shadow-inner">
                    <video id="intake-camera-video" autoplay playsinline webkit-playsinline muted class="w-full h-full object-cover"></video>
                    <canvas id="intake-camera-canvas" class="hidden"></canvas>
                    <div id="intake-camera-flash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-100 z-20"></div>

                    {{-- Centering Grid --}}
                    <div class="absolute inset-0 pointer-events-none grid grid-cols-3 grid-rows-3 z-10 opacity-20">
                        <div class="border-r border-b border-white"></div>
                        <div class="border-r border-b border-white"></div>
                        <div class="border-b border-white"></div>
                        <div class="border-r border-b border-white"></div>
                        <div class="border-r border-b border-white"></div>
                        <div class="border-b border-white"></div>
                        <div class="border-r border-white"></div>
                        <div class="border-r border-white"></div>
                        <div></div>
                    </div>

                    {{-- Live Indicator --}}
                    <div class="absolute top-2.5 left-2.5 z-20">
                        <span class="bg-black/60 backdrop-blur-md text-emerald-400 border border-emerald-500/30 text-[10px] font-extrabold px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Live Camera</span>
                        </span>
                    </div>

                    {{-- Error State --}}
                    <div id="intake-camera-error" class="hidden absolute inset-0 z-30 bg-slate-900/95 flex flex-col items-center justify-center p-4 text-center text-white">
                        <i class="fas fa-exclamation-triangle text-amber-400 text-2xl mb-2"></i>
                        <p id="intake-camera-error-msg" class="text-xs text-slate-300 max-w-xs mb-3">Camera access denied or unavailable.</p>
                        <label class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                            <i class="fas fa-camera"></i>
                            <span>Use Device File / Camera</span>
                            <input type="file" id="intake-camera-fallback-input" accept="image/*" capture="environment" multiple class="hidden">
                        </label>
                    </div>
                </div>

                {{-- Capture Shutter Button directly with camera --}}
                <div class="mt-3.5 flex items-center justify-center gap-3 w-full">
                    <button type="button" id="intake-camera-shutter-btn" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black px-8 py-3 rounded-2xl shadow-xl shadow-red-900/30 text-sm flex items-center gap-2.5 active:scale-95 transition-all cursor-pointer">
                        <i class="fas fa-camera text-base"></i>
                        <span>Capture Photo</span>
                        <span class="hidden sm:inline text-[11px] opacity-80 font-normal">(Spacebar)</span>
                    </button>
                </div>
            </div>

            {{-- 3. Multiple Captured Images Section (Below Camera) --}}
            <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-images text-blue-600"></i>
                        <span>Captured Images (<span id="intake-camera-carousel-count" class="text-blue-600 font-black">0</span>)</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Tap ✕ to remove any photo</span>
                </div>

                {{-- Captured Images Thumbnails Row --}}
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-2.5 min-h-[96px] max-h-40 overflow-y-auto">
                    <div id="intake-camera-thumbs-container" class="flex flex-wrap items-center gap-2.5">
                        {{-- Empty state --}}
                        <div id="camera-thumbs-empty" class="w-full text-center py-4 text-xs text-slate-400 italic flex items-center justify-center gap-2">
                            <i class="fas fa-camera text-slate-300"></i>
                            <span>No photos captured yet. Click "Capture Photo" above.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Done Footer Actions (Below Captured Images) --}}
            <div class="px-5 py-3.5 border-t border-slate-100 bg-white flex items-center justify-between gap-3">
                <button type="button" id="camera-cancel-btn" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all">
                    Cancel
                </button>

                <button type="button" id="intake-camera-done-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-7 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 text-sm flex items-center gap-2 active:scale-95 transition-all">
                    <i class="fas fa-check"></i>
                    <span>Done (<span id="camera-done-btn-count">0</span> Photos)</span>
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            0% { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-scaleIn {
            animation: scaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 9999px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 9999px;
        }
        .hidden-on-screen {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .hidden-on-screen {
                display: block !important;
            }

            #printable-item-label,
            #printable-item-label * {
                visibility: visible;
            }

            #printable-item-label {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        #interactive video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        canvas.drawingBuffer {
            display: none;
        }

        @keyframes scan {
            0% {
                top: 0%;
            }

            100% {
                top: 100%;
            }
        }

        .animate-scan {
            position: absolute;
            animation: scan 2s linear infinite;
        }
    </style>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const INTAKE_API = "{{ route('admin.items-lot.generate-barcode') }}";
        const CATEGORIES_API = "{{ route('admin.items-lot.categories') }}";
        const SUB_CATEGORIES_API = "{{ route('admin.items-lot.sub-categories') }}";
        const SAVE_API = "{{ route('admin.items-lot.save') }}";
        const BARCODE_GEN_API = "{{ route('admin.items-lot.generate-existing-barcode') }}";
        const PALLET_DETAILS_API = "{{ route('admin.items-lot.pallet-details', ['id' => '__ID__']) }}";
        const JOB_NO_API = "{{ route('admin.items-lot.get-job-number') }}";
        const CREATE_CLIENT_API = "{{ route('admin.items-lot.create-client') }}";
    </script>
    <script src="{{ asset('js/admin/intake/item-intake.js') }}?v={{ filemtime(public_path('js/admin/intake/item-intake.js')) }}"></script>
    <script src="{{ asset('js/admin/intake/item-intake-refurb.js') }}?v={{ filemtime(public_path('js/admin/intake/item-intake-refurb.js')) }}"></script>
    <script src="{{ asset('js/admin/intake/item-intake-data-destruction.js') }}?v={{ filemtime(public_path('js/admin/intake/item-intake-data-destruction.js')) }}"></script>
    <script src="{{ asset('js/admin/intake/item-intake-it-assets.js') }}?v={{ filemtime(public_path('js/admin/intake/item-intake-it-assets.js')) }}"></script>
    <script src="{{ asset('js/admin/intake/item-intake-repair.js') }}?v={{ filemtime(public_path('js/admin/intake/item-intake-repair.js')) }}"></script>
    <script src="{{ asset('js/admin/intake/item-intake-universal-waste.js') }}?v={{ filemtime(public_path('js/admin/intake/item-intake-universal-waste.js')) }}"></script>
    <script>
        $(function () {
            const $pallet = $('#pallet-select');
            if ($pallet.length) {
                if ($pallet.hasClass('select2-hidden-accessible')) {
                    $pallet.select2('destroy');
                }
                $pallet.select2({
                    width: '100%',
                    placeholder: '-- Select or Scan Pallet --',
                    allowClear: true
                });
                
                $pallet.on('change', function() {
                    if (window.itemIntakeManager) {
                        const selectedId = $(this).val();
                        if (selectedId) {
                            window.itemIntakeManager.selectedPalletId = selectedId;
                            window.itemIntakeManager.populatePalletDetails(selectedId);
                        } else {
                            window.itemIntakeManager.selectedPalletId = null;
                            window.itemIntakeManager.selectedPalletData = {};
                            window.itemIntakeManager.palletRemaining = { quantity: 0, weight: 0 };
                            window.itemIntakeManager.clearPalletLimits();
                            window.itemIntakeManager.updateRemainingDisplay();
                        }
                    }
                });
            }
        });
    </script>
@endpush
@endsection
