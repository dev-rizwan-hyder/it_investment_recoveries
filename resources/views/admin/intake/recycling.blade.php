@extends('layouts.admin')

@section('content')
    <div class="w-full p-4 md:p-6 bg-slate-50 min-h-screen">

        {{-- Top Header with Toggle Buttons --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Recycling Intake</h2>

            <div class="flex bg-slate-200 p-1 rounded-xl shadow-inner">
                <button type="button" data-mode="full"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all duration-200 mode-btn bg-white shadow-sm text-blue-600 active">
                    Full Intake
                </button>
                <button type="button" data-mode="quick"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all duration-200 mode-btn text-slate-500 hover:text-slate-700">
                    Quick Intake
                </button>
            </div>
        </div>

        {{-- Hidden data for marketing refs --}}
        <div id="marketingRefsData" style="display: none;">
            @foreach ($marketingRefs as $ref)
                <span data-ref="{{ $ref }}">{{ $ref }}</span>
            @endforeach
        </div>

        {{-- Hidden data for employees --}}
        <div id="employeesData" style="display: none;">
            @foreach ($employees as $emp)
                <span data-name="{{ $emp->name }}" data-id="{{ $emp->employee_id ?? '' }}">{{ $emp->name }}</span>
            @endforeach
        </div>

        {{-- Mode Alert --}}
        <div id="modeAlert"
            class="bg-blue-50 border border-blue-100 text-blue-600 px-6 py-4 rounded-xl mb-6 text-[13px] leading-relaxed animate-in fade-in slide-in-from-top-2">
            <span class="font-bold">Quick Intake Mode:</span> Enter customer info and description to generate a receipt
            quickly. You can complete weight and item details later by editing the pallet.
        </div>

        {{-- Dynamic Progress Bar --}}
        <div id="progressBar" class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-8 mb-6 h-auto">
            <div class="flex items-center justify-between max-w-5xl mx-auto relative px-2">
                <div class="absolute top-5 left-0 w-full h-[2px] bg-slate-100 -z-0"></div>
                <div id="progressLine" class="absolute top-5 left-0 h-[2px] bg-green-500 transition-all duration-500 -z-0"
                    style="width: 0%"></div>

                <div id="quickModeProgress" class="w-full flex justify-between items-center" style="display: flex;">
                    <div class="step-btn relative z-10 flex flex-col items-center">
                        <div
                            class="step-indicator w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-blue-600 text-white shadow-lg shadow-blue-200 scale-110">
                            1</div>
                        <span
                            class="block text-[10px] md:text-[11px] font-bold uppercase tracking-widest mt-3 text-slate-800 text-center">
                            Customer Info
                        </span>
                    </div>
                    <div class="step-btn relative z-10 flex flex-col items-center">
                        <div
                            class="step-indicator w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-slate-50 text-slate-400 border border-slate-200">
                            2</div>
                        <span
                            class="block text-[10px] md:text-[11px] font-bold uppercase tracking-widest mt-3 text-slate-800 text-center">
                            Description
                        </span>
                    </div>
                    <div class="step-btn relative z-10 flex flex-col items-center">
                        <div
                            class="step-indicator w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-slate-50 text-slate-400 border border-slate-200">
                            3</div>
                        <span
                            class="block text-[10px] md:text-[11px] font-bold uppercase tracking-widest mt-3 text-slate-800 text-center">
                            Revieew & Save
                        </span>
                    </div>
                </div>

                <div id="fullModeProgress" class="w-full flex justify-between items-center" style="display: none;">
                    <div class="step-btn relative z-10 flex flex-col items-center">
                        <div
                            class="step-indicator w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-blue-600 text-white shadow-lg shadow-blue-200 scale-110">
                            1</div>
                        <span
                            class="block text-[10px] md:text-[11px] font-bold uppercase tracking-widest mt-3 text-slate-800 text-center">
                            Barcode & Client
                        </span>
                    </div>
                    <div class="step-btn relative z-10 flex flex-col items-center">
                        <div
                            class="step-indicator w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-slate-50 text-slate-400 border border-slate-200">
                            2</div>
                        <span
                            class="block text-[10px] md:text-[11px] font-bold uppercase tracking-widest mt-3 text-slate-800 text-center">
                            Vendor & Details
                        </span>
                    </div>
                    <div class="step-btn relative z-10 flex flex-col items-center">
                        <div
                            class="step-indicator w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-slate-50 text-slate-400 border border-slate-200">
                            3</div>
                        <span
                            class="block text-[10px] md:text-[11px] font-bold uppercase tracking-widest mt-3 text-slate-800 text-center">
                            Review & Save
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        <form id="recyclingForm"
            class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 md:p-8 mb-6 min-h-[400px]">
            @csrf

            {{-- FULL MODE STEP 1: Pallet Barcode Entry & Client Selection --}}
            <div class="step-pane step-full-1" style="display: none;">
                {{-- SECTION 1: Client / Vendor Selection in Step 1 --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 mb-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-user-tie text-blue-600 mr-2"></i>
                        Client / Vendor Information
                    </h3>

                    <div class="space-y-4">
                        <div class="mb-2">
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">
                                <i class="fas fa-calendar-alt text-blue-600 mr-1"></i> Intake Date / Entry Date *
                            </label>
                            <input type="date" id="intakeDateFullModeStep1" name="intakeDateStep1"
                                value="{{ date('Y-m-d') }}"
                                class="w-full sm:w-64 max-w-xs border-slate-200 rounded-lg p-3 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-semibold text-slate-800">
                        </div>

                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Select Client/Vendor *</label>
                        <div class="flex flex-col md:flex-row gap-3">
                            <select id="selectedVendorFullModeStep1" name="selectedVendorStep1"
                                class="flex-1 border-slate-200 rounded-lg p-3 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                        data-email="{{ $client->email }}" data-phone="{{ $client->phone }}"
                                        data-company="{{ $client->company }}" data-address="{{ $client->address }}">
                                        {{ $client->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="toggleNewClientFormBtnStep1"
                                class="w-full md:w-auto bg-slate-100 border border-slate-200 px-4 py-3 rounded-lg font-bold text-xs whitespace-nowrap hover:bg-slate-200 transition-all">
                                + New Client
                            </button>
                        </div>

                        <!-- Selected Client Display Step 1 -->
                        <div id="selectedClientDisplayStep1" style="display: none;"
                            class="mt-2 p-4 bg-blue-50/50 border border-blue-100 rounded-lg animate-in fade-in">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-sm border border-blue-50">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <div>
                                        <p id="selectedClientNameStep1" class="text-sm font-bold text-slate-800">---</p>
                                        <p id="selectedClientMetaStep1"
                                            class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">---
                                        </p>
                                    </div>
                                </div>
                                <button type="button"
                                    class="openEditClientModalBtn bg-white border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 transition-all flex items-center gap-1.5 shadow-sm shrink-0">
                                    <i class="fas fa-edit text-blue-600"></i> Edit Client
                                </button>
                            </div>
                        </div>

                        <!-- New Client Form Step 1 -->
                        <div id="newClientFormSectionStep1" style="display: none;"
                            class="bg-blue-50/30 border border-blue-100 rounded-xl p-4 md:p-6 space-y-4 animate-in slide-in-from-top duration-300">
                            <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-2">Create New Client</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <input type="text" id="newClientNameInputStep1" name="newClientNameStep1"
                                    placeholder="Name *"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="email" id="newClientEmailInputStep1" name="newClientEmailStep1"
                                        placeholder="Email"
                                        class="border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                                    <input type="text" id="newClientPhoneInputStep1" name="newClientPhoneStep1"
                                        placeholder="Phone"
                                        class="border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                                </div>
                                <input type="text" id="newClientCompanyInputStep1" name="newClientCompanyStep1"
                                    placeholder="Company (Optional)"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                                <textarea id="newClientAddressInputStep1" name="newClientAddressStep1" placeholder="Address"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border h-20 focus:ring-4 focus:ring-blue-500/10 outline-none"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" id="createNewClientBtnStep1"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg text-sm shadow-lg shadow-blue-200 transition-all">
                                    <i class="fas fa-check-circle mr-2"></i> Create & Select Client
                                </button>
                                <button type="button" id="cancelNewClientBtnStep1"
                                    class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg text-sm transition-all">
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Job # Section Step 1 (After client select, before generate barcode) -->
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">
                                <i class="fas fa-briefcase text-blue-600 mr-1"></i> Job #
                            </label>
                            <div class="flex flex-col sm:flex-row gap-3 max-w-md">
                                <div class="relative flex-1">
                                    <input type="text" id="jobNoStep1" name="jobNoStep1"
                                        class="w-full border-slate-200 rounded-lg p-3 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                        placeholder="e.g., 583921">
                                </div>
                                <button type="button" id="generateJobNoBtnStep1"
                                    class="w-full sm:w-auto bg-blue-50 border border-blue-200 hover:bg-blue-600 hover:text-white text-blue-700 px-4 py-3 rounded-lg font-bold text-xs whitespace-nowrap transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <i class="fas fa-bolt"></i> Generate Job #
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: Pallet Barcode Entry --}}
                <h3 class="text-lg font-bold text-slate-800 mb-6">Pallet Barcode Entry</h3>
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="hasExistingBarcode"
                        class="w-5 h-5 text-blue-600 border-slate-300 rounded cursor-pointer">
                    <label for="hasExistingBarcode" class="ml-3 text-sm font-medium text-slate-600 cursor-pointer">Pallet
                        has existing barcode</label>
                </div>

                <div id="newBarcodeSection">
                    <div class="bg-blue-50/50 border border-blue-100 rounded-lg px-5 py-3 mb-6 text-blue-600 text-[13px]">
                        This pallet doesn't have a barcode. We'll generate one for you.
                    </div>

                    <button type="button" id="generateBarcodeBtn"
                        class="w-full bg-blue-600 text-white py-4 rounded-lg font-bold text-sm mb-8 flex justify-center items-center">
                        <i class="fas fa-qrcode mr-2 text-lg"></i> Generate New Pallet Barcode
                    </button>

                    <div id="generatedBarcodeDisplay" style="display: none;">
                        <div
                            class="max-w-xs mx-auto border border-slate-100 rounded-2xl py-6 px-4 flex flex-col items-center bg-slate-50/40">
                            <p id="generatedBarcodeNumber" class="text-xl font-bold text-slate-800 mb-4">---</p>
                            <div class="bg-white p-4 border border-slate-200 rounded-xl mb-6 text-center shadow-sm w-full">
                                <img id="generatedBarcodeImage" src="" class="h-16 md:h-20 max-w-full mx-auto mb-2"
                                    alt="barcode">
                                <p id="generatedBarcodeText"
                                    class="font-mono text-[13px] md:text-[15px] tracking-[0.3em] text-slate-800 font-bold uppercase">
                                    ---</p>
                            </div>
                            <button type="button" id="useGeneratedBarcodeBtn"
                                class="mt-2 w-full bg-blue-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition-all">
                                <i class="fas fa-check mr-2"></i> Use This Barcode
                            </button>
                        </div>
                    </div>
                </div>

                <div id="existingBarcodeSection" style="display: none;">
                    {{-- Pallet Selection Dropdown --}}
                    <div class="mb-5">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">
                            Select Pallet from List
                        </label>
                        <select id="existingBarcodeSelect"
                            class="w-full p-4 border border-slate-200 rounded-xl text-[14px] bg-white font-bold focus:ring-4 focus:ring-blue-500/5 outline-none appearance-none cursor-pointer">
                            <option value="">-- Select or Scan Pallet --</option>
                            @foreach ($pallets_list as $pallet)
                                <option value="{{ $pallet->barcode_number }}">{{ $pallet->barcode_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Barcode Image Display for Selected/Scanned Pallet --}}
                    <div id="selectedBarcodeDisplay" style="display: none;" class="mb-6 animate-in fade-in slide-in-from-top-2 duration-300">
                        <div
                            class="max-w-xs mx-auto border border-slate-100 rounded-2xl py-6 px-4 flex flex-col items-center bg-slate-50/40">
                            <p id="selectedBarcodeNumber" class="text-xl font-bold text-slate-800 mb-4">---</p>
                            <div class="bg-white p-4 border border-slate-200 rounded-xl mb-6 text-center shadow-sm w-full">
                                <img id="selectedBarcodeImage" src="" class="h-16 md:h-20 max-w-full mx-auto mb-2"
                                    alt="barcode">
                                <p id="selectedBarcodeText"
                                    class="font-mono text-[13px] md:text-[15px] tracking-[0.3em] text-slate-800 font-bold uppercase">
                                    ---</p>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="relative flex items-center mb-5">
                        <div class="flex-grow border-t border-slate-100"></div>
                        <span class="flex-shrink mx-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Or
                            Entry Manually</span>
                        <div class="flex-grow border-t border-slate-100"></div>
                    </div>

                    {{-- Manual Entry with Scan Button --}}
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <input type="text" id="manualBarcodeEntry" name="manualBarcodeEntry"
                                class="w-full border-slate-200 rounded-xl p-4 border focus:ring-4 focus:ring-blue-500/5 outline-none font-bold text-lg"
                                placeholder="Pallet Barcode">
                        </div>
                        <button type="button" id="openScannerBtn"
                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-4 rounded-xl font-bold flex items-center gap-2 transition-all">
                            <i class="fas fa-expand"></i> Scan
                        </button>
                    </div>
                </div>
            </div>

            {{-- QUICK INTAKE VIEWS --}}
            {{-- STEP 1: Customer Info (Quick) --}}
            <div class="step-pane step-1" style="display: none;">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Customer Information</h3>
                <div class="space-y-6 animate-in fade-in duration-300">
                    <div
                        class="bg-slate-50 border border-slate-100 rounded-2xl p-6 md:p-10 text-center relative overflow-hidden">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Pallet Barcode</p>
                        <h4 id="barcodeNumberDisplay" class="text-xl font-black text-slate-800 mb-6 font-mono">
                            Generating...</h4>
                        <div class="bg-white p-6 inline-block rounded-2xl shadow-sm border border-slate-200">
                            <img id="barcodeImageDisplay" src="" class="h-20 md:h-24 mx-auto" alt="barcode">
                            <p id="barcodeNumberText"
                                class="font-mono text-sm mt-4 tracking-[0.3em] font-bold text-slate-900"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">
                                <i class="fas fa-calendar-alt text-blue-600 mr-1"></i> Intake Date / Entry Date *
                            </label>
                            <input type="date" id="intakeDateQuick" name="intakeDateQuick"
                                value="{{ date('Y-m-d') }}"
                                class="w-full sm:w-64 max-w-xs border-slate-200 rounded-xl p-4 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">Select Client/Vendor *</label>
                            <div class="flex gap-3">
                                <select id="selectedVendor" name="selectedVendor"
                                    class="flex-1 border-slate-200 rounded-xl p-4 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium">
                                    <option value="">-- Select Client --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                            data-email="{{ $client->email }}" data-phone="{{ $client->phone }}"
                                            data-company="{{ $client->company }}" data-address="{{ $client->address }}">
                                            {{ $client->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="newClientBtn"
                                    class="bg-slate-100 border-slate-200 border px-6 rounded-xl text-xs font-bold hover:bg-slate-200 transition-colors">+
                                    New Client</button>
                            </div>

                            <!-- Selected Client Display Quick Mode -->
                            <div id="selectedClientDisplay" style="display: none;"
                                class="mt-3 p-4 bg-blue-50/50 border border-blue-100 rounded-xl animate-in fade-in">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-sm border border-blue-50">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <p id="selectedClientName" class="text-sm font-bold text-slate-800">---</p>
                                            <p id="selectedClientMeta"
                                                class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">---
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="openEditClientModalBtn bg-white border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 transition-all flex items-center gap-1.5 shadow-sm shrink-0">
                                        <i class="fas fa-edit text-blue-600"></i> Edit Client
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">
                                <i class="fas fa-briefcase text-blue-600 mr-1"></i> Job #
                            </label>
                            <div class="flex flex-col sm:flex-row gap-3 max-w-md">
                                <input type="text" id="jobNoQuick" name="jobNoQuick"
                                    class="flex-1 border-slate-200 rounded-xl p-3.5 border bg-white text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                    placeholder="e.g., 583921">
                                <button type="button" id="generateJobNoBtnQuick"
                                    class="w-full sm:w-auto bg-blue-50 border border-blue-200 hover:bg-blue-600 hover:text-white text-blue-700 px-5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <i class="fas fa-bolt"></i> Generate Job #
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 italic font-medium">Click "Generate Job #" to automatically create a numeric job number.</p>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">Put-away location *</label>
                            <input type="text" id="putAwayLocation" name="putAwayLocation"
                                class="w-full border-slate-200 rounded-xl p-4 border text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                placeholder="e.g., Warehouse A, Loading Dock 3">
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Description/Details (Quick & Full) --}}
            <div class="step-pane step-2" style="display: none;">
                <!-- QUICK MODE CONTENT -->
                <div id="quickModeStep2" class="quick-mode-only">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Drop-off Description</h3>
                    <div class="space-y-6 animate-in slide-in-from-right-4 duration-300">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">Description of Items Dropped Off</label>
                            <textarea id="description" name="description"
                                class="w-full border-slate-200 rounded-xl p-4 border text-sm h-40 focus:ring-4 focus:ring-blue-500/5 outline-none"
                                placeholder="e.g., Mixed electronics, laptops, monitors, cables..."></textarea>
                            <p class="text-[11px] text-slate-400 mt-2 italic font-medium">Describe what the customer
                                dropped off. You can add weight and item count later.</p>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">Receiving Employee</label>
                            <input type="text" id="receivingEmployee" name="receivingEmployee" list="employeesList"
                                class="w-full border-slate-200 rounded-xl p-4 border text-sm focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                placeholder="Enter receiving employee name...">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">Additional Notes
                                (Optional)</label>
                            <textarea id="notes" name="notes"
                                class="w-full border-slate-200 rounded-xl p-4 border text-sm h-28 focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                placeholder="Any additional information..."></textarea>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">Pallet Photos & Files (Optional)</label>
                            <div class="flex flex-wrap gap-3 mb-2">
                                <div id="quickPhotoPreviews" class="flex flex-wrap gap-3 contents"></div>
                                <label
                                    class="cursor-pointer bg-slate-50 hover:bg-blue-50 border-2 border-dashed border-slate-200 px-6 py-4 rounded-2xl font-black text-[10px] text-slate-500 transition-all flex flex-col items-center justify-center min-w-[140px] text-center">
                                    <i class="fas fa-paperclip text-xl mb-1 text-blue-500"></i>
                                    <span>UPLOAD IMAGE OR FILE</span>
                                    <input type="file" id="quickPhotoInput" name="main_photos" multiple class="hidden"
                                        accept="*/*">
                                </label>
                            </div>
                        </div>
                        <div
                            class="bg-yellow-50/80 border border-yellow-100 p-4 rounded-xl text-yellow-800 text-xs font-bold flex items-center gap-3">
                            <i class="fas fa-info-circle text-lg"></i>
                            Note: Weight, item count, and other details can be added later by editing this pallet.
                        </div>
                    </div>
                </div>

                <!-- FULL MODE CONTENT -->
                <!-- FULL MODE STEP 2: Complete Multi-Section Form -->
                <div id="fullModeStep2" class="full-mode-only space-y-8 animate-in fade-in duration-500 pb-20"
                    style="display: none;">



                    <!-- SECTION 2: Main Pallet Details -->
                    <div class="bg-white rounded-xl border-l-4 border-blue-500 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                            <span
                                class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 text-sm font-black">1</span>
                            Item #1 - Pallet Details
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                                <input type="text" id="descriptionFull" name="description"
                                    placeholder="e.g., Mixed Electronics Pallet"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/5 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Estimated Item Count</label>
                                <input type="number" id="estimatedCountFull" name="estimatedCount"
                                    placeholder="e.g., 25"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/5 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase mb-2 italic text-[10px]">Gross
                                    Weight (lbs)</label>
                                <input type="number" id="grossWeightFull" name="grossWeight" step="0.1"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border font-bold"
                                    oninput="app.calculateNetFull()"
                                    onchange="app.calculateNetFull()"
                                    onkeydown="if(event.key==='Enter') event.preventDefault()">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase mb-2 italic text-[10px]">Tare
                                    Weight (lbs)</label>
                                <input type="number" id="tareWeightFull" name="tareWeight" step="0.1"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border font-bold"
                                    oninput="app.calculateNetFull()"
                                    onchange="app.calculateNetFull()"
                                    onkeydown="if(event.key==='Enter') event.preventDefault()">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase mb-2 italic text-[10px]">Calculated
                                    Net</label>
                                <div id="netWeightDisplayFull"
                                    class="w-full bg-blue-50 border-blue-100 rounded-lg p-3 text-sm border font-black text-blue-700">
                                    0.00 lbs</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Put-away Location *</label>
                                <input type="text" id="putAwayLocationFull" name="putAwayLocation"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/5 outline-none"
                                    placeholder="e.g., Warehouse A">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Marketing Reference</label>
                                <select id="marketingReferenceFull" name="marketingReference"
                                    class="w-full border-slate-200 rounded-lg p-3 text-sm border bg-white focus:ring-4 focus:ring-blue-500/5 outline-none">
                                    @foreach ($marketingRefs as $ref)
                                        <option value="{{ $ref }}">{{ $ref }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Receiving Employee</label>
                            <input type="text" id="receivingEmployeeFull" name="receivingEmployee" list="employeesList"
                                class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                                placeholder="Enter receiving employee name...">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Notes</label>
                            <textarea id="notesFull" name="notes"
                                class="w-full border-slate-200 rounded-lg p-3 text-sm border h-24 focus:ring-4 focus:ring-blue-500/5 outline-none"
                                placeholder="Additional notes about the pallet..."></textarea>
                        </div>

                        <!-- Photo Upload with Progress Bar (jQuery) -->
                        <div class="mt-6 border-t pt-6 border-slate-50">
                            <label class="block text-sm font-black text-slate-700 mb-4 uppercase tracking-wider">Pallet
                                Photos & Files</label>

                            <div class="flex flex-wrap gap-3 mb-4">
                                <div id="mainPhotoPreviews" class="flex flex-wrap gap-3 contents"></div>
                                <label
                                    class="cursor-pointer bg-slate-50 hover:bg-blue-50 border-2 border-dashed border-slate-200 px-6 py-4 rounded-2xl font-black text-[10px] text-slate-500 transition-all flex flex-col items-center justify-center min-w-[140px] text-center">
                                    <i class="fas fa-paperclip text-xl mb-1 text-blue-500"></i>
                                    <span>UPLOAD IMAGE OR FILE</span>
                                    <input type="file" id="mainPhotoInput" name="main_photos" multiple class="hidden"
                                        accept="*/*">
                                </label>
                            </div>

                            <div id="mainPhotoProgress"
                                class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden hidden">
                                <div class="bg-blue-600 h-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Additional Items Loop -->
                    <div id="additionalItemsContainer" class="space-y-6"></div>

                    <!-- Add More Button -->
                    <div class="flex justify-center mt-10">
                        <button type="button" id="addAnotherItemBtn"
                            class="group flex items-center bg-white text-blue-600 border-2 border-blue-600 px-10 py-4 rounded-2xl font-black text-sm hover:bg-blue-600 hover:text-white transition-all shadow-xl shadow-blue-100">
                            <i class="fas fa-plus-circle mr-3 group-hover:rotate-90 transition-transform"></i>
                            ADD ANOTHER ITEM / TO THIS CUSTOMER
                        </button>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Review & Save (Full Itemized Layout) --}}
            <div class="step-pane step-3" style="display: none;">
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 mb-6 animate-in fade-in duration-500">

                    <!-- HEADER: Shipment Overview -->
                    <div class="bg-slate-800 rounded-2xl p-6 mb-8 text-white shadow-lg">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <span class="text-blue-400 text-[10px] font-black uppercase tracking-widest">Shipment
                                    Overview</span>
                                <h3 class="text-2xl font-black tracking-tight mt-1">
                                    <i class="fas fa-barcode mr-2"></i> <span id="headerBarcode">---</span>
                                </h3>
                            </div>
                            <div class="md:text-right border-l md:border-l-0 md:border-r border-slate-600 px-4">
                                <p class="text-slate-400 text-xs font-bold uppercase">Client / Vendor</p>
                                <p class="text-lg font-bold text-blue-100" id="headerClientName">Unassigned</p>
                                <p class="text-xs text-slate-400" id="headerClientCompany">N/A</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-slate-700 px-4 py-2 rounded-xl text-center">
                                    <p class="text-slate-400 text-[10px] font-black uppercase">Intake Date</p>
                                    <p class="text-sm font-black text-blue-200" id="headerIntakeDate">{{ date('M d, Y') }}</p>
                                </div>
                                <div class="bg-slate-700 px-4 py-2 rounded-xl text-center">
                                    <p class="text-slate-400 text-[10px] font-black uppercase">Total Units</p>
                                    <p class="text-xl font-black text-white text-center" id="totalUnitsCount">1</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ITEMS SECTION -->
                    <div class="space-y-6">
                        <!-- MAIN ITEM (#01) -->
                        <div
                            class="group relative bg-white rounded-2xl border-2 border-slate-100 p-6 hover:border-blue-200 transition-all shadow-sm">
                            <span
                                class="absolute -top-3 left-6 bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase">Item
                                #1</span>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mt-2">
                                <div class="md:col-span-5">
                                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Description & Notes</p>
                                    <p id="reviewMainDescription" class="text-slate-800 font-bold leading-snug">---</p>
                                    <p id="reviewMainReceivingEmployee" class="text-xs font-semibold text-blue-600 mt-1" style="display: none;">
                                        <i class="fas fa-user-check mr-1"></i> Receiver: <span id="reviewMainReceivingEmployeeVal">---</span>
                                    </p>
                                    <p id="reviewMainJobNo" class="text-xs font-semibold text-slate-600 mt-1" style="display: none;">
                                        <i class="fas fa-briefcase mr-1"></i> Job #: <span id="reviewMainJobNoVal">---</span>
                                    </p>
                                    <p id="reviewMainNotes"
                                        class="text-xs text-slate-500 italic mt-2 bg-slate-50 p-2 rounded border border-dashed border-slate-200"
                                        style="display: none;">---</p>
                                </div>
                                <div class="md:col-span-4 grid grid-cols-3 gap-2 text-center items-center">
                                    <div class="bg-slate-50 p-2 rounded-lg">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Gross</span>
                                        <span id="reviewMainGross" class="text-sm font-bold text-slate-700">---</span>
                                    </div>
                                    <div class="bg-slate-50 p-2 rounded-lg">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Tare</span>
                                        <span id="reviewMainTare" class="text-sm font-bold text-slate-700">---</span>
                                    </div>
                                    <div class="bg-green-50 p-2 rounded-lg border border-green-100">
                                        <span class="block text-[9px] font-black text-green-600 uppercase">Net</span>
                                        <span id="reviewMainNet" class="text-sm font-black text-green-700">--- lbs</span>
                                    </div>
                                </div>
                                <div
                                    class="md:col-span-3 flex flex-col justify-center text-right border-l border-slate-50 pl-4">
                                    <p class="text-xs font-bold text-blue-600 italic mb-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i><span id="reviewMainLocation">---</span>
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">
                                        <i class="fas fa-paperclip mr-1"></i> <span id="reviewMainPhotos">0</span> Photos / Files
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ADDITIONAL ITEMS LOOP -->
                        <div id="reviewAdditionalItemsContainer"></div>
                    </div>

                    <!-- TOTAL WEIGHT & ACTIONS -->
                    <div class="mt-10 pt-8 border-t border-slate-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            <div
                                class="bg-blue-50 rounded-2xl p-6 border border-blue-100 flex items-center justify-between">
                                <div>
                                    <p class="text-blue-500 text-[10px] font-black uppercase tracking-widest">Grand Total
                                        Weight</p>
                                    <p class="text-3xl font-black text-blue-700"><span id="totalWeightDisplay">0.00</span>
                                        <span class="text-sm font-normal">lbs</span>
                                    </p>
                                </div>
                                <div
                                    class="bg-blue-200/50 w-12 h-12 rounded-full flex items-center justify-center text-blue-600">
                                    <i class="fas fa-weight-hanging text-xl"></i>
                                </div>
                            </div>
                            <div
                                class="bg-slate-50 rounded-2xl p-6 border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" id="sendEmailFull" name="sendEmail"
                                        class="w-6 h-6 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                                    <label for="sendEmailFull"
                                        class="ml-4 text-sm font-bold text-slate-700 cursor-pointer">
                                        Send Receipt to <span id="sendEmailAddress">Client</span>
                                    </label>
                                </div>
                                <i class="fas fa-envelope-open-text text-slate-300 text-2xl"></i>
                            </div>
                        </div>

                        <div id="errorMessages" style="display: none;"
                            class="bg-red-50 text-red-600 p-4 rounded-xl mb-4 text-xs space-y-1"></div>

                        <button type="submit" id="saveBtnFull"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg flex items-center justify-center transition-all disabled:bg-slate-200">
                            <i class="fas fa-save mr-3"></i>
                            <span>SAVE PALLET</span>
                        </button>

                        <button type="button" id="saveDraftBtnFull"
                            class="w-full mt-3 bg-white hover:bg-slate-50 text-slate-700 font-black py-4 rounded-xl border-2 border-slate-200 shadow-sm flex items-center justify-center transition-all disabled:bg-slate-200">
                            <i class="fas fa-file-alt mr-3 text-slate-400"></i>
                            <span>SAVE AS DRAFT</span>
                        </button>

                        <!-- SUCCESS SECTION -->
                        <div id="successViewFull" style="display: none;"
                            class="mt-12 pt-12 border-t-2 border-dashed border-slate-100 flex flex-col items-center no-print">
                            <div
                                class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-check text-2xl"></i>
                            </div>
                            <h2 id="successTitleFull" class="text-2xl font-black text-slate-800 mb-2">Item Added Successfully!</h2>
                            <p id="successMessageFull" class="text-slate-400 mb-10">The pallet information has been securely saved.</p>

                            <div
                                class="bg-white border-2 border-slate-100 rounded-[2.5rem] p-10 mb-10 shadow-xl max-w-sm w-full text-center">
                                <img id="successBarcodeImgFull" src="" class="h-20 mx-auto" alt="barcode">
                                <p id="successBarcodeFull"
                                    class="font-mono text-xl tracking-[0.3em] text-slate-900 font-bold uppercase mt-4">---
                                </p>
                                <p class="text-[10px] text-slate-300 uppercase mt-2">Item Barcode: <span
                                        id="successBarcodeTextFull">---</span></p>
                            </div>

                            <div class="flex flex-wrap gap-4 w-full max-w-2xl mx-auto justify-center">
                                <button type="button" onclick="app.printLabel()"
                                    class="flex-1 bg-slate-100 py-4 px-6 rounded-2xl font-bold border hover:bg-slate-200 transition-all no-print">
                                    <i class="fas fa-tag mr-2"></i> Print Label
                                </button>
                                <button type="button" onclick="app.printReceiptFull()"
                                    class="flex-1 bg-orange-100 text-orange-700 py-4 px-6 rounded-2xl font-bold border border-orange-200 hover:bg-orange-200 transition-all no-print">
                                    <i class="fas fa-file-invoice mr-2"></i> Print Receipt
                                </button>
                                <button type="button" id="addAnotherFull"
                                    class="w-full md:flex-1 bg-blue-600 text-white py-4 px-6 rounded-2xl font-black hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all no-print">
                                    Add Another Item
                                </button>
                            </div>
                        </div>

                        <!-- PRINT LABEL TEMPLATE -->
                        <div id="printLabelTemplate" class="hidden-on-screen"></div>

                        <!-- PRINT RECEIPT TEMPLATE -->
                        <div id="printReceiptTemplate" class="hidden-on-screen"></div>
                    </div>
                </div>
            </div>

            {{-- Barcode Scanner Modal --}}
            <div id="scannerModal" style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-slate-800">Barcode Scanner</h3>
                        <button type="button" id="closeScannerBtn"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>

                    <!-- Scanner Mode Selection -->
                    <div id="scannerModeSelection" class="space-y-4 mb-6">
                        <p class="text-slate-600 font-semibold">Choose scanning method:</p>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" id="cameraScanMode"
                                class="relative p-6 border-2 border-slate-200 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition-all text-center group">
                                <i class="fas fa-video text-3xl text-slate-800 mb-3 group-hover:text-blue-600 transition-colors"></i>
                                <p class="font-bold text-slate-800 text-lg">Camera</p>
                                <p class="text-sm text-slate-500 mt-1">Use your webcam</p>
                            </button>
                            <button type="button" id="usbScanMode"
                                class="relative p-6 border-2 border-slate-200 rounded-2xl hover:border-green-500 hover:bg-green-50 transition-all text-center group">
                                <i class="fas fa-barcode text-3xl text-slate-800 mb-3 group-hover:text-green-600 transition-colors"></i>
                                <p class="font-bold text-slate-800 text-lg">USB Scanner</p>
                                <p class="text-sm text-slate-500 mt-1">Hardware device</p>
                            </button>
                        </div>
                    </div>

                    <!-- Camera Scanning Interface -->
                    <div id="cameraScannerInterface" style="display: none;" class="space-y-4">
                        <!-- Camera Video Stream -->
                        <div id="scannerContainer" class="relative bg-black rounded-2xl overflow-hidden" style="min-height: 400px;">
                            <video id="scannerVideo" class="w-full h-full object-cover" style="display: none;"></video>
                            <canvas id="scannerCanvas" style="display: none;"></canvas>
                            <div id="scannerLoading" class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <div class="inline-block">
                                        <i class="fas fa-spinner fa-spin text-white text-4xl mb-4"></i>
                                    </div>
                                    <p class="text-white font-bold">Initializing camera...</p>
                                    <p class="text-slate-300 text-sm mt-2">Make sure you've granted camera permission</p>
                                </div>
                            </div>
                        </div>

                        <!-- Scanning Instructions -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-blue-700 text-sm flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5 text-lg flex-shrink-0"></i>
                            <div>
                                <p class="font-bold mb-1">Point your camera at the barcode</p>
                                <p class="text-blue-600">The barcode will be automatically detected and entered when scanned.</p>
                            </div>
                        </div>

                        <!-- Scanned Result Display -->
                        <div id="scanResult" style="display: none;" class="bg-green-50 border border-green-100 rounded-xl p-4 text-green-700">
                            <p class="font-bold mb-2">Barcode Detected:</p>
                            <p id="scannedBarcodeValue" class="font-mono text-lg font-bold text-green-900 mb-4">---</p>
                            <button type="button" id="useScannerResult"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition-all">
                                <i class="fas fa-check mr-2"></i> Use This Barcode
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button type="button" id="scanAgainBtn"
                                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg transition-all">
                                <i class="fas fa-redo mr-2"></i> Scan Again
                            </button>
                            <button type="button" id="backToModeBtn"
                                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-lg transition-all">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                        </div>
                    </div>

                    <!-- USB Scanner Input Interface -->
                    <div id="usbScannerInterface" style="display: none;" class="space-y-4">
                        <!-- Instructions -->
                        <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-green-700 flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5 text-lg flex-shrink-0"></i>
                            <div>
                                <p class="font-bold mb-1">Place focus in the input field below</p>
                                <p class="text-green-600">Scan the barcode with your USB scanner device</p>
                            </div>
                        </div>

                        <!-- USB Scanner Input -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-3">Scanned Barcode</label>
                            <input type="text" id="usbScannerInput" placeholder="Scan barcode here..."
                                class="w-full border-2 border-slate-300 rounded-xl p-4 text-lg font-bold focus:border-green-500 focus:ring-4 focus:ring-green-500/10 outline-none transition-all"
                                autofocus>
                            <p class="text-xs text-slate-500 mt-2">The input field is automatically focused. Just scan your barcode.</p>
                        </div>

                        <!-- Scanned Result Display (USB) -->
                        <div id="usbScanResult" style="display: none;" class="bg-green-50 border border-green-100 rounded-xl p-4 text-green-700">
                            <p class="font-bold mb-2">Barcode Scanned:</p>
                            <p id="scannedUsbBarcodeValue" class="font-mono text-lg font-bold text-green-900 mb-4">---</p>
                            <button type="button" id="useUsbScannerResult"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition-all">
                                <i class="fas fa-check mr-2"></i> Use This Barcode
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button type="button" id="clearUsbInputBtn"
                                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg transition-all">
                                <i class="fas fa-trash mr-2"></i> Clear
                            </button>
                            <button type="button" id="backToModeBtn2"
                                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-lg transition-all">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New Client Form Modal --}}
            <div id="newClientForm" style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6">
                    <h3 class="text-xl font-bold text-slate-800 mb-6">Create New Client</h3>
                    <div class="space-y-4">
                        <input type="text" id="clientName" name="clientName" placeholder="Name *"
                            class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="email" id="clientEmail" name="clientEmail" placeholder="Email"
                                class="border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <input type="text" id="clientPhone" name="clientPhone" placeholder="Phone"
                                class="border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                        </div>
                        <input type="text" id="clientCompany" name="clientCompany" placeholder="Company (Optional)"
                            class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                        <div class="flex gap-3">
                            <button type="button" id="createClientBtn"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg text-sm shadow-lg shadow-blue-200 transition-all">
                                <i class="fas fa-check-circle mr-2"></i> Create & Select Client
                            </button>
                            <button type="button" id="closeClientForm"
                                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg text-sm transition-all">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Client Modal --}}
            <div id="editClientModal" style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-slate-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-user-edit text-blue-600"></i> Edit Client Details
                        </h3>
                        <button type="button" id="closeEditClientModalBtn"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <input type="hidden" id="editClientId">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name *</label>
                            <input type="text" id="editClientName" placeholder="Name *"
                                class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                                <input type="email" id="editClientEmail" placeholder="Email"
                                    class="border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone</label>
                                <input type="text" id="editClientPhone" placeholder="Phone"
                                    class="border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none w-full">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Company</label>
                            <input type="text" id="editClientCompany" placeholder="Company (Optional)"
                                class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Address</label>
                            <textarea id="editClientAddress" placeholder="Address" rows="2"
                                class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/10 outline-none"></textarea>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" id="saveEditClientBtn"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Update Client
                            </button>
                            <button type="button" id="cancelEditClientBtn"
                                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg text-sm transition-all">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Navigation Bar --}}
        <div id="navBar"
            class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-100 mb-10 shadow-sm">
            <button type="button" id="prevBtn"
                class="w-full md:w-auto px-10 py-4 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all"
                style="display: none;">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <button type="button" id="resetBtn"
                    class="flex-1 md:flex-none bg-white border border-slate-200 px-8 py-4 rounded-xl font-bold text-sm text-slate-400 hover:text-red-500 hover:border-red-100 transition-all">
                    Reset
                </button>
                <button type="button" id="nextBtn"
                    class="flex-[2] md:flex-none bg-blue-600 text-white px-12 py-4 rounded-xl font-black text-sm shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
        </div>

    </div>

    <datalist id="employeesList">
        @if(isset($employees) && count($employees) > 0)
            @foreach($employees as $emp)
                <option value="{{ $emp->name }}"></option>
            @endforeach
        @endif
    </datalist>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/intake/recyclingintake.css') }}">
    <style>
        .hidden-on-screen {
            display: none;
        }

        .no-print {
            /* These buttons won't print */
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .hidden-on-screen {
                display: block !important;
                visibility: visible;
            }

            #printLabelTemplate,
            #printLabelTemplate * {
                visibility: visible;
            }

            #printLabelTemplate {
                position: absolute;
                left: 0;
                top: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script>
        const API_ROUTES = {
            generateBarcode: "{{ route('admin.recycling.intake.barcode') }}",
            getBarcodeImage: "{{ route('admin.recycling.intake.barcode-image') }}",
            generateJobNumber: "{{ route('admin.recycling.intake.job-number') }}",
            createClient: "{{ route('admin.recycling.intake.create-client') }}",
            saveQuick: "{{ route('admin.recycling.intake.save-quick') }}",
            saveFull: "{{ route('admin.recycling.intake.save-full') }}",
        };
    </script>
    <script src="{{ asset('js/admin/intake/recyclingintake.js') }}?v={{ filemtime(public_path('js/admin/intake/recyclingintake.js')) }}"></script>
@endpush
