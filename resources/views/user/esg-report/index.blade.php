@extends('user.layouts.app')

@section('title', 'ESG Environmental Impact Report Generator')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
<style>
    .font-sans-custom {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="bg-slate-100/70 min-h-screen py-6 sm:py-8 font-sans-custom">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if($noClientLinked)
            {{-- No Client Linked Warning Card --}}
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm space-y-4 max-w-2xl mx-auto my-12">
                <div class="h-16 w-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl mx-auto border border-amber-200">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">No Organization Account Linked</h2>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed font-medium">
                    Your account email (<span class="font-bold text-slate-900">{{ $userEmail }}</span>) is not yet attached to a registered client profile. ESG report generation requires linked intake pallets.
                </p>
                <div class="pt-2">
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white font-extrabold text-xs shadow-sm hover:bg-slate-800 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Return to Dashboard</span>
                    </a>
                </div>
            </div>
        @else

            {{-- Top Wizard Progress Header --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">ESG Sustainability Portal</span>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-0.5">ESG Environmental Impact Report</h1>
                    </div>
                </div>

                {{-- Step Progress Indicator --}}
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1.5 self-start sm:self-auto">
                    <button id="step1-tab-btn" onclick="switchStep(1)" type="button" 
                            class="px-4 py-1.5 rounded-lg text-xs font-black transition flex items-center gap-2 bg-emerald-600 text-white shadow-xs">
                        <span class="h-5 w-5 rounded-full bg-white/20 text-white text-[11px] flex items-center justify-center font-extrabold">1</span>
                        <span>Pallet Selection</span>
                    </button>
                    <button id="step2-tab-btn" onclick="switchStep(2)" type="button" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 text-slate-500 hover:text-slate-900">
                        <span class="h-5 w-5 rounded-full bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center font-extrabold">2</span>
                        <span>Generated Report</span>
                    </button>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- STEP 1: PALLET SELECTION & DATE FILTERING SECTION          --}}
            {{-- ======================================================== --}}
            <div id="step1-container" class="space-y-6">
                
                {{-- Step 1 Main Selection Box --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="border-b border-slate-100 pb-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900">Step 1: Select Pallets & Date Filter</h2>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    Select multiple pallet numbers from your receiving/intake history or filter entries by date range to calculate custom ESG metrics.
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllPallets()" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-extrabold transition">
                                    <i class="fa-solid fa-check-double text-emerald-600 mr-1"></i> Select All Pallets
                                </button>
                                <button type="button" onclick="clearPalletSelection()" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold transition">
                                    <i class="fa-solid fa-xmark text-slate-400 mr-1"></i> Clear Selection
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Multi-Select Dropdown & Date Search Controls Grid --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 bg-slate-50/60 p-5 rounded-2xl border border-slate-200/70">
                        
                        {{-- Multi-select Pallets Dropdown Box (7 cols) --}}
                        <div class="lg:col-span-7 space-y-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                                <i class="fa-solid fa-boxes-stacked text-emerald-600 mr-1.5"></i> Multiple Pallets Selector Dropdown
                            </label>
                            
                            <div class="relative" id="dropdownContainer">
                                {{-- Search Input Field --}}
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" 
                                           id="palletSearchInput" 
                                           placeholder="Search Pallet # (e.g. PAL-6296028911 or Oct 06)..." 
                                           class="w-full pl-9 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition shadow-2xs">
                                    <button type="button" id="clearDropdownSearchBtn" onclick="clearDropdownSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>

                                {{-- Multi-select Checklist Dropdown Box --}}
                                <div id="palletDropdownMenu" class="mt-2 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-56 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                    @forelse($pallets as $p)
                                        <label class="pallet-option-item flex items-center justify-between p-2.5 rounded-xl hover:bg-emerald-50/60 transition cursor-pointer border border-transparent hover:border-emerald-100"
                                               data-barcode="{{ strtolower($p['barcode']) }}"
                                               data-date="{{ $p['date'] }}"
                                               data-formatted-date="{{ strtolower($p['formatted_date']) }}">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" 
                                                       value="{{ $p['barcode'] }}" 
                                                       class="pallet-checkbox h-4 w-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 transition cursor-pointer"
                                                       onchange="onPalletCheckboxChange(this)">
                                                <div>
                                                    <span class="text-xs font-black text-slate-900 block">#{{ $p['barcode'] }}</span>
                                                    <span class="text-[10px] font-semibold text-slate-400">Date: {{ $p['formatted_date'] }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200 block">
                                                    {{ $p['total_units'] }} units • {{ number_format($p['total_weight'], 1) }} lbs
                                                </span>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="text-center py-6 text-slate-400 text-xs font-semibold">
                                            No pallet entries found for your account.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Search by Date / Date Range (5 cols) --}}
                        <div class="lg:col-span-5 space-y-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                                <i class="fa-solid fa-calendar-days text-emerald-600 mr-1.5"></i> Search & Filter Pallets by Date
                            </label>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Start Date</span>
                                    <input type="date" id="filterStartDate" onchange="applyDateFilter()"
                                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition shadow-2xs">
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">End Date</span>
                                    <input type="date" id="filterEndDate" onchange="applyDateFilter()"
                                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1 gap-2">
                                <div class="flex gap-1 flex-wrap">
                                    <button type="button" onclick="setDatePreset('today')" class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition">Today</button>
                                    <button type="button" onclick="setDatePreset('7days')" class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition">Last 7 Days</button>
                                    <button type="button" onclick="setDatePreset('month')" class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition">This Month</button>
                                </div>
                                <button type="button" onclick="resetDateFilter()" class="text-[10px] font-bold text-rose-600 hover:underline">
                                    Reset Dates
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Selected Pallet Badges Tag Pills --}}
                    <div id="selectedBadgesContainer" class="p-4 rounded-2xl bg-emerald-50/40 border border-emerald-100/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase text-emerald-900 tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check text-emerald-600"></i> Selected Pallet Entries (<span id="selectedCountBadge">0</span>)
                            </span>
                            <span class="text-[11px] font-bold text-emerald-700" id="selectedMetricsSummary">0 Units • 0.00 lbs Total Weight</span>
                        </div>
                        <div id="selectedBadgesPills" class="flex flex-wrap gap-2 pt-1">
                            <span class="text-xs text-slate-400 font-semibold italic">No pallets selected yet. Check individual boxes or click "Select All Pallets".</span>
                        </div>
                    </div>

                    {{-- Pallets Listing Table --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Receiving & Intake Pallets Listing</h3>
                            <span class="text-xs text-slate-400 font-semibold" id="tableMatchingCount">{{ count($pallets) }} pallets listed</span>
                        </div>

                        <div class="overflow-x-auto border border-slate-200 rounded-2xl shadow-2xs">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-slate-900 text-white font-black text-[10px] uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4 w-10 text-center">
                                            <input type="checkbox" id="masterTableCheckbox" onchange="toggleMasterTableCheckbox(this)" class="h-4 w-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                        </th>
                                        <th class="py-3 px-4">Pallet Barcode #</th>
                                        <th class="py-3 px-4">Intake Date</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-center">Total Devices / Items</th>
                                        <th class="py-3 px-4 text-center">Net Weight (lbs)</th>
                                        <th class="py-3 px-4">Location Tag</th>
                                    </tr>
                                </thead>
                                <tbody id="palletsTableBody" class="divide-y divide-slate-200 font-bold text-slate-700 bg-white">
                                    @forelse($pallets as $p)
                                        <tr class="pallet-table-row hover:bg-emerald-50/30 transition duration-150"
                                            data-barcode="{{ strtolower($p['barcode']) }}"
                                            data-date="{{ $p['date'] }}">
                                            <td class="py-3 px-4 text-center">
                                                <input type="checkbox" 
                                                       value="{{ $p['barcode'] }}" 
                                                       class="pallet-checkbox h-4 w-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                                       onchange="onPalletCheckboxChange(this)">
                                            </td>
                                            <td class="py-3 px-4 text-slate-900 font-black">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <i class="fa-solid fa-barcode text-slate-400"></i>
                                                    #{{ $p['barcode'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-slate-700 font-semibold">{{ $p['formatted_date'] }}</td>
                                            <td class="py-3 px-4">
                                                @php
                                                    $rawSt = strtolower(trim($p['status']));
                                                    if (str_contains($rawSt, 'complet')) {
                                                        $stClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                                    } elseif (str_contains($rawSt, 'progr')) {
                                                        $stClass = 'bg-amber-100 text-amber-800 border-amber-200';
                                                    } else {
                                                        $stClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                                    }
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase border {{ $stClass }}">
                                                    {{ $p['status'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-center font-black text-slate-900">{{ number_format($p['total_units']) }} units</td>
                                            <td class="py-3 px-4 text-center font-black text-emerald-700">{{ number_format($p['total_weight'], 1) }} lbs</td>
                                            <td class="py-3 px-4 text-slate-500 font-semibold">
                                                <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> {{ $p['location'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-slate-400 font-semibold text-xs">
                                                No pallet records available for your account.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Action Footer Bar --}}
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">
                                Ready to generate? Selected entries will be compiled into a certified ESG Impact Report in Step 2.
                            </p>
                        </div>
                        <button type="button" onclick="generateESGReport()" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs uppercase tracking-wider shadow-md transition transform active:scale-95 cursor-pointer">
                            <span>Generate ESG Impact Report</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- STEP 2: GENERATED ESG IMPACT REPORT SECTION              --}}
            {{-- ======================================================== --}}
            <div id="step2-container" class="hidden space-y-6">
                
                {{-- Action Bar Top --}}
                <div class="flex flex-wrap items-center justify-between gap-3 no-print">
                    <button type="button" onclick="switchStep(1)" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-extrabold text-xs shadow-2xs transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Modify Pallet Selection</span>
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openReportModal()" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs uppercase tracking-wider shadow-sm transition">
                            <i class="fa-solid fa-print"></i>
                            <span>Print Official Report</span>
                        </button>
                    </div>
                </div>

                {{-- Report Card Container --}}
                <div id="esgReportCard" class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden font-sans-custom">
                    
                    {{-- Header Banner Section --}}
                    <div class="bg-gradient-to-r from-[#1b3d2f] via-[#244f3d] to-[#1b3d2f] p-6 sm:p-10 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-[10px] font-extrabold uppercase tracking-widest text-emerald-300">
                                    <i class="fa-solid fa-leaf text-emerald-400"></i> Environmental, Social & Governance
                                </span>
                            </div>
                            
                            <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                ESG Impact Report
                            </h2>
                            
                            <div class="mt-4 pt-3 border-t border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <p class="text-[10px] font-extrabold text-emerald-200/80 uppercase tracking-widest">Reporting Entity</p>
                                    <h3 class="text-xl sm:text-2xl font-black text-emerald-300 uppercase tracking-wide mt-0.5">
                                        {{ $client->name ?? 'Client Name' }}
                                    </h3>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] font-extrabold text-emerald-200/80 uppercase tracking-widest block">Scope & Selection</span>
                                    <span class="text-xs font-extrabold text-white" id="esgSelectionTagSummary">All Pallets Included</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-10 space-y-8">
                        {{-- Prepared For Section Card --}}
                        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 flex flex-col sm:flex-row justify-between items-start gap-6">
                            <div class="space-y-1.5">
                                <span class="inline-block text-[10px] font-extrabold text-emerald-800 bg-emerald-100/80 border border-emerald-200/80 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                    Prepared For
                                </span>
                                <div>
                                    <p class="text-xl font-black text-slate-900 tracking-tight">
                                        {{ $client->name ?? 'Client Name' }}
                                    </p>
                                    <p class="text-xs font-black text-emerald-700 italic uppercase tracking-wide mt-0.5">
                                        {{ $client->company ?? 'No Company Registered' }}
                                    </p>
                                    <p class="text-xs text-slate-500 font-semibold uppercase mt-1 max-w-md">
                                        {{ $client->address ?? 'Address not available' }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-left sm:text-right space-y-1">
                                <span class="inline-block text-[10px] font-extrabold text-slate-600 bg-slate-200/70 border border-slate-300/60 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                    Prepared By
                                </span>
                                <p class="text-sm font-black text-slate-900 mt-1">
                                    IT Investment Recoveries
                                </p>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-0.5">
                                    {{ date('F d, Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Core Impact Metrics Grid --}}
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 border-b border-slate-200 pb-2">1. Core ESG Environmental Metrics</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                {{-- Total Devices --}}
                                <div class="border-l-4 border-emerald-600 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-2xl border border-slate-200/80 shadow-2xs">
                                    <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Total Devices Recycled</p>
                                    <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1" id="metricTotalUnits">0</p>
                                    <p class="text-[10px] text-slate-500 font-bold mt-1.5 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Units Processed
                                    </p>
                                </div>

                                {{-- Landfill Diversion --}}
                                <div class="border-l-4 border-emerald-800 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-2xl border border-slate-200/80 shadow-2xs">
                                    <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Landfill Diversion</p>
                                    <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                                        <span id="metricTotalWeight">0.00</span> <span class="text-xs font-extrabold text-slate-500">lbs</span>
                                    </p>
                                    <p class="text-[10px] text-slate-500 font-bold mt-1.5 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-recycle text-emerald-700 text-xs"></i> <span id="metricTotalTonnes">0.00</span> Tonnes Diverted
                                    </p>
                                </div>

                                {{-- CO2 Avoided --}}
                                <div class="border-l-4 border-emerald-900 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-2xl border border-slate-200/80 shadow-2xs">
                                    <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">CO₂e Emissions Avoided</p>
                                    <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                                        <span id="metricCo2Avoided">0.00</span> <span class="text-xs font-extrabold text-slate-500">lbs</span>
                                    </p>
                                    <p class="text-[10px] text-slate-500 font-bold mt-1.5 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-cloud-sun text-emerald-800 text-xs"></i> Carbon Footprint Reduction
                                    </p>
                                </div>

                                {{-- Water Conserved --}}
                                <div class="border-l-4 border-teal-500 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-2xl border border-slate-200/80 shadow-2xs">
                                    <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Water Conserved</p>
                                    <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                                        <span id="metricWaterSaved">0</span> <span class="text-xs font-extrabold text-slate-500">gal</span>
                                    </p>
                                    <p class="text-[10px] text-slate-500 font-bold mt-1.5 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-droplet text-teal-600 text-xs"></i> Industrial Water Recovery
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Equivalence Cards Section --}}
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 border-b border-slate-200 pb-2">2. Environmental Equivalency Impact</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white border border-slate-200/80 p-5 text-center rounded-2xl shadow-2xs hover:border-emerald-300 transition">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base mx-auto mb-2 border border-emerald-100">
                                        <i class="fa-solid fa-tree"></i>
                                    </div>
                                    <p class="text-2xl font-black text-slate-900 tracking-tight" id="metricTrees">0</p>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider mt-1">Mature Trees Planted Equivalent</p>
                                </div>

                                <div class="bg-white border border-slate-200/80 p-5 text-center rounded-2xl shadow-2xs hover:border-emerald-300 transition">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base mx-auto mb-2 border border-emerald-100">
                                        <i class="fa-solid fa-car"></i>
                                    </div>
                                    <p class="text-2xl font-black text-slate-900 tracking-tight" id="metricCars">0</p>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider mt-1">Car Days Off the Road</p>
                                </div>

                                <div class="bg-white border border-slate-200/80 p-5 text-center rounded-2xl shadow-2xs hover:border-emerald-300 transition">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base mx-auto mb-2 border border-emerald-100">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                    <p class="text-2xl font-black text-slate-900 tracking-tight" id="metricHomes">0</p>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider mt-1">Home Power Days Conserved</p>
                                </div>
                            </div>
                        </div>

                        {{-- Detailed Category Breakdown Table --}}
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 border-b border-slate-200 pb-2">3. Detailed Item Breakdown by Category</h3>
                            <div class="overflow-x-auto border border-slate-200 rounded-2xl shadow-2xs">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gradient-to-r from-[#1b3d2f] to-[#244f3d] text-white text-[10px] font-black uppercase tracking-wider">
                                        <tr>
                                            <th class="py-3.5 px-6">Category Stream</th>
                                            <th class="py-3.5 px-4 text-center">Devices / Units</th>
                                            <th class="py-3.5 px-4 text-center">Net Weight (lbs)</th>
                                            <th class="py-3.5 px-6 text-right">% Contribution</th>
                                        </tr>
                                    </thead>
                                    <tbody id="esgCategoryTableBody" class="divide-y divide-slate-100 text-xs font-bold text-slate-700 bg-white">
                                        {{-- Populated via JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Stream Progress Bar Visual --}}
                        <div class="bg-slate-50/80 rounded-2xl border border-slate-200/80 p-6 space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">4. Mass Distribution Across Waste Streams</h3>
                            <div id="esgStreamProgressBars" class="space-y-3">
                                {{-- Populated via JS --}}
                            </div>
                        </div>

                        {{-- Environmental Compliance Certification Statement --}}
                        <div class="pt-4 border-t border-slate-200">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-emerald-100 text-emerald-700 font-bold">
                                            <i class="fa-solid fa-certificate"></i>
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-700 font-medium leading-relaxed">
                                        <span class="font-extrabold text-slate-900">IT Investment Recoveries</span> certifies that the materials referenced in this ESG report have been recycled and recovered in accordance with established IT Asset Management & E-Waste recycling procedures and in compliance with applicable <span class="font-extrabold text-slate-900">NIST SP 800-88 Rev. 2</span> Guidelines for Media Sanitization / Physical Destruction, as well as all applicable local, city, state, and federal environmental regulations.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Authorized Signature --}}
                        <div class="pt-4 border-t border-slate-200">
                            <div class="flex-1 max-w-[260px]">
                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Authorized Compliance Officer</p>
                                <p class="text-2xl text-slate-800 mt-1 mb-0.5" style="font-family: 'Dancing Script', cursive;">Salwaster Daniel</p>
                                <div class="border-b-2 border-slate-400"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Bar --}}
                    <div class="bg-slate-50 border-t border-slate-200 px-6 sm:px-10 py-4 flex flex-col sm:flex-row justify-between items-center text-[10px] font-black text-slate-400 uppercase tracking-widest gap-2">
                        <span>IT Investment Recoveries • ESG Environmental Report</span>
                        <span>Generated: {{ date('F d, Y') }}</span>
                    </div>
                </div>
            </div>

        @endif
    </div>
</div>

{{-- Official Print Preview Modal --}}
<div id="officialReportModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 sm:p-6 overflow-y-auto no-print">
    <div class="relative bg-white w-full max-w-5xl max-h-[92vh] rounded-3xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col my-auto">
        {{-- Modal Top Toolbar --}}
        <div class="flex items-center justify-between px-6 py-4 bg-slate-900 text-white border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <div>
                    <h3 class="text-base font-black">Official ESG Report Print Preview</h3>
                    <p class="text-xs text-slate-400 font-medium">Verified Environmental Compliance Document</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="triggerReportPrint()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>Print / Save PDF</span>
                </button>
                <button type="button" onclick="closeReportModal()" 
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-bold transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Modal Content Body --}}
        <div class="p-6 sm:p-8 overflow-y-auto bg-slate-100/70 flex-1" id="modalReportBody">
            {{-- Content injected from #esgReportCard --}}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Server-provided datasets
    const ALL_PALLETS = @json($pallets ?? []);
    const ALL_ITEMS = @json($items ?? []);
    const EMISSION_FACTORS = @json($emissionFactors ?? []);

    // State management
    let selectedPalletBarcodes = new Set();

    document.addEventListener('DOMContentLoaded', function() {
        // Default: Select all pallets initially
        if (ALL_PALLETS && ALL_PALLETS.length > 0) {
            ALL_PALLETS.forEach(p => selectedPalletBarcodes.add(p.barcode));
        }
        syncCheckboxStates();
        updateSelectedSummary();

        // Search in dropdown
        const searchInput = document.getElementById('palletSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = (this.value || '').toLowerCase().trim();
                const items = document.querySelectorAll('.pallet-option-item');
                const clearBtn = document.getElementById('clearDropdownSearchBtn');

                if (clearBtn) clearBtn.classList.toggle('hidden', query === '');

                items.forEach(item => {
                    const bc = item.dataset.barcode || '';
                    const dt = item.dataset.date || '';
                    const fmtDt = item.dataset.formattedDate || '';
                    if (!query || bc.includes(query) || dt.includes(query) || fmtDt.includes(query)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        }
    });

    function switchStep(stepNum) {
        const step1Container = document.getElementById('step1-container');
        const step2Container = document.getElementById('step2-container');
        const btn1 = document.getElementById('step1-tab-btn');
        const btn2 = document.getElementById('step2-tab-btn');

        if (stepNum === 1) {
            step1Container.classList.remove('hidden');
            step2Container.classList.add('hidden');

            btn1.className = 'px-4 py-1.5 rounded-lg text-xs font-black transition flex items-center gap-2 bg-emerald-600 text-white shadow-xs';
            btn1.querySelector('span').className = 'h-5 w-5 rounded-full bg-white/20 text-white text-[11px] flex items-center justify-center font-extrabold';
            
            btn2.className = 'px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 text-slate-500 hover:text-slate-900';
            btn2.querySelector('span').className = 'h-5 w-5 rounded-full bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center font-extrabold';
        } else {
            if (selectedPalletBarcodes.size === 0) {
                alert('Please select at least one pallet entry in Step 1 to generate the ESG report.');
                return;
            }
            renderESGReport();

            step1Container.classList.add('hidden');
            step2Container.classList.remove('hidden');

            btn2.className = 'px-4 py-1.5 rounded-lg text-xs font-black transition flex items-center gap-2 bg-emerald-600 text-white shadow-xs';
            btn2.querySelector('span').className = 'h-5 w-5 rounded-full bg-white/20 text-white text-[11px] flex items-center justify-center font-extrabold';
            
            btn1.className = 'px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 text-slate-500 hover:text-slate-900';
            btn1.querySelector('span').className = 'h-5 w-5 rounded-full bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center font-extrabold';

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function onPalletCheckboxChange(input) {
        const val = input.value;
        if (input.checked) {
            selectedPalletBarcodes.add(val);
        } else {
            selectedPalletBarcodes.delete(val);
        }
        syncCheckboxStates();
        updateSelectedSummary();
    }

    function selectAllPallets() {
        // Select all visible/filtered or all available pallets
        ALL_PALLETS.forEach(p => selectedPalletBarcodes.add(p.barcode));
        syncCheckboxStates();
        updateSelectedSummary();
    }

    function clearPalletSelection() {
        selectedPalletBarcodes.clear();
        syncCheckboxStates();
        updateSelectedSummary();
    }

    function syncCheckboxStates() {
        const checkboxes = document.querySelectorAll('.pallet-checkbox');
        let checkedCount = 0;
        let totalCount = checkboxes.length;

        checkboxes.forEach(cb => {
            if (selectedPalletBarcodes.has(cb.value)) {
                cb.checked = true;
                checkedCount++;
            } else {
                cb.checked = false;
            }
        });

        const masterCb = document.getElementById('masterTableCheckbox');
        if (masterCb) {
            masterCb.checked = totalCount > 0 && checkedCount === totalCount;
            masterCb.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }
    }

    function toggleMasterTableCheckbox(masterInput) {
        const visibleRows = document.querySelectorAll('#palletsTableBody tr:not(.hidden)');
        visibleRows.forEach(row => {
            const cb = row.querySelector('.pallet-checkbox');
            if (cb) {
                if (masterInput.checked) {
                    selectedPalletBarcodes.add(cb.value);
                } else {
                    selectedPalletBarcodes.delete(cb.value);
                }
            }
        });
        syncCheckboxStates();
        updateSelectedSummary();
    }

    function clearDropdownSearch() {
        const searchInput = document.getElementById('palletSearchInput');
        if (searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    }

    function applyDateFilter() {
        const startDate = document.getElementById('filterStartDate')?.value;
        const endDate = document.getElementById('filterEndDate')?.value;

        let visibleCount = 0;

        const dropdownItems = document.querySelectorAll('.pallet-option-item');
        dropdownItems.forEach(item => {
            const pDate = item.dataset.date || '';
            let show = true;
            if (startDate && pDate < startDate) show = false;
            if (endDate && pDate > endDate) show = false;

            if (show) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });

        const tableRows = document.querySelectorAll('.pallet-table-row');
        tableRows.forEach(row => {
            const pDate = row.dataset.date || '';
            let show = true;
            if (startDate && pDate < startDate) show = false;
            if (endDate && pDate > endDate) show = false;

            if (show) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        const tableMatchingCount = document.getElementById('tableMatchingCount');
        if (tableMatchingCount) {
            tableMatchingCount.textContent = visibleCount + ' pallets matching date filter';
        }
    }

    function resetDateFilter() {
        const startDate = document.getElementById('filterStartDate');
        const endDate = document.getElementById('filterEndDate');
        if (startDate) startDate.value = '';
        if (endDate) endDate.value = '';
        applyDateFilter();
    }

    function setDatePreset(type) {
        const today = new Date();
        const endDateStr = today.toISOString().split('T')[0];
        let startDateStr = '';

        if (type === 'today') {
            startDateStr = endDateStr;
        } else if (type === '7days') {
            const d = new Date();
            d.setDate(d.getDate() - 7);
            startDateStr = d.toISOString().split('T')[0];
        } else if (type === 'month') {
            const d = new Date(today.getFullYear(), today.getMonth(), 1);
            startDateStr = d.toISOString().split('T')[0];
        }

        document.getElementById('filterStartDate').value = startDateStr;
        document.getElementById('filterEndDate').value = endDateStr;
        applyDateFilter();
    }

    function updateSelectedSummary() {
        const countBadge = document.getElementById('selectedCountBadge');
        const metricsSummary = document.getElementById('selectedMetricsSummary');
        const pillsContainer = document.getElementById('selectedBadgesPills');

        const selectedPallets = ALL_PALLETS.filter(p => selectedPalletBarcodes.has(p.barcode));

        const totalUnits = selectedPallets.reduce((acc, p) => acc + (p.total_units || 0), 0);
        const totalWeight = selectedPallets.reduce((acc, p) => acc + (p.total_weight || 0), 0);

        if (countBadge) countBadge.textContent = selectedPalletBarcodes.size;
        if (metricsSummary) metricsSummary.textContent = `${totalUnits.toLocaleString()} Units • ${totalWeight.toFixed(1)} lbs Total Weight`;

        if (pillsContainer) {
            if (selectedPalletBarcodes.size === 0) {
                pillsContainer.innerHTML = '<span class="text-xs text-slate-400 font-semibold italic">No pallets selected yet. Check individual boxes or click "Select All Pallets".</span>';
            } else {
                let html = '';
                selectedPallets.forEach(p => {
                    html += `
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-200 text-xs font-black">
                            #${p.barcode}
                            <button type="button" onclick="removePalletSelection('${p.barcode}')" class="text-emerald-700 hover:text-rose-600 ml-1">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </span>
                    `;
                });
                pillsContainer.innerHTML = html;
            }
        }
    }

    function removePalletSelection(barcode) {
        selectedPalletBarcodes.delete(barcode);
        syncCheckboxStates();
        updateSelectedSummary();
    }

    function generateESGReport() {
        switchStep(2);
    }

    function renderESGReport() {
        const selectedBarcodesArr = Array.from(selectedPalletBarcodes);
        const selectedPallets = ALL_PALLETS.filter(p => selectedPalletBarcodes.has(p.barcode));

        // Filter items matching selected pallets
        let selectedItems = ALL_ITEMS.filter(item => selectedPalletBarcodes.has(item.pallet_number));

        // Fallback: If some selected pallets don't have detailed item breakdown records, construct synthetic aggregate item entries
        selectedPallets.forEach(pallet => {
            const hasItems = selectedItems.some(i => i.pallet_number === pallet.barcode);
            if (!hasItems) {
                selectedItems.push({
                    pallet_number: pallet.barcode,
                    category: 'General Peripherals',
                    quantity: pallet.total_units > 0 ? pallet.total_units : 1,
                    weight: pallet.total_weight > 0 ? pallet.total_weight : 0.0,
                });
            }
        });

        const totalUnits = selectedItems.reduce((acc, i) => acc + (parseInt(i.quantity) || 1), 0);
        const totalWeightLbs = selectedItems.reduce((acc, i) => acc + (parseFloat(i.weight) || 0.0), 0);
        const totalTonnes = totalWeightLbs / 2204.62;

        // Calculate CO2 avoided
        let co2AvoidedLbs = 0;
        selectedItems.forEach(item => {
            const factor = getEmissionFactor(item.category);
            co2AvoidedLbs += ((parseFloat(item.weight) || 0) * factor);
        });

        const trees = Math.round(co2AvoidedLbs / 48);
        const cars = Math.round(co2AvoidedLbs / 24.6);
        const homes = Math.round(co2AvoidedLbs / 30);
        const water = Math.round(totalWeightLbs * 0.5);

        // Update DOM
        document.getElementById('metricTotalUnits').textContent = totalUnits.toLocaleString();
        document.getElementById('metricTotalWeight').textContent = totalWeightLbs.toLocaleString(undefined, { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        document.getElementById('metricTotalTonnes').textContent = totalTonnes.toFixed(2);
        document.getElementById('metricCo2Avoided').textContent = co2AvoidedLbs.toLocaleString(undefined, { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        document.getElementById('metricWaterSaved').textContent = water.toLocaleString();

        document.getElementById('metricTrees').textContent = trees.toLocaleString();
        document.getElementById('metricCars').textContent = cars.toLocaleString();
        document.getElementById('metricHomes').textContent = homes.toLocaleString();

        // Tag summary in header
        const esgSelectionTagSummary = document.getElementById('esgSelectionTagSummary');
        if (esgSelectionTagSummary) {
            if (selectedPallets.length === ALL_PALLETS.length) {
                esgSelectionTagSummary.textContent = `All ${selectedPallets.length} Pallets Included`;
            } else {
                esgSelectionTagSummary.textContent = `${selectedPallets.length} Selected Pallet(s)`;
            }
        }

        // Render Category Table
        renderCategoryTable(selectedItems, totalWeightLbs);

        // Render Stream Progress Bars
        renderStreamProgressBars(selectedItems, totalWeightLbs);
    }

    function getEmissionFactor(cat) {
        if (!cat) return EMISSION_FACTORS['default'] || 2.5;
        const key = cat.toLowerCase();
        if (key.includes('laptop') || key.includes('notebook')) return EMISSION_FACTORS['laptop'] || 4.5;
        if (key.includes('desktop') || key.includes('pc') || key.includes('computer')) return EMISSION_FACTORS['desktop'] || 5.2;
        if (key.includes('monitor') || key.includes('display') || key.includes('screen')) return EMISSION_FACTORS['monitor'] || 3.8;
        if (key.includes('server')) return EMISSION_FACTORS['server'] || 8.0;
        return EMISSION_FACTORS[key] || EMISSION_FACTORS['default'] || 2.5;
    }

    function renderCategoryTable(items, totalWeight) {
        const tbody = document.getElementById('esgCategoryTableBody');
        if (!tbody) return;

        // Group by category
        const groups = {};
        items.forEach(item => {
            const cat = item.category || 'General Peripherals';
            if (!groups[cat]) {
                groups[cat] = { qty: 0, weight: 0 };
            }
            groups[cat].qty += (parseInt(item.quantity) || 1);
            groups[cat].weight += (parseFloat(item.weight) || 0);
        });

        const categories = Object.keys(groups);
        if (categories.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-6 px-6 text-center text-slate-400 font-medium uppercase text-xs">
                        No item entries recorded for the selected pallets.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        categories.forEach(cat => {
            const g = groups[cat];
            const pct = totalWeight > 0 ? (g.weight / totalWeight) * 100 : 0;
            html += `
                <tr class="hover:bg-emerald-50/20 transition">
                    <td class="py-3.5 px-6 text-slate-900 uppercase font-black">${cat}</td>
                    <td class="py-3.5 px-4 text-center text-slate-800 font-extrabold">${g.qty.toLocaleString()}</td>
                    <td class="py-3.5 px-4 text-center text-slate-800 font-extrabold">${g.weight.toLocaleString(undefined, { minimumFractionDigits: 1, maximumFractionDigits: 1 })}</td>
                    <td class="py-3.5 px-6 text-right text-emerald-700 font-black text-xs">${pct.toFixed(1)}%</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function renderStreamProgressBars(items, totalWeight) {
        const container = document.getElementById('esgStreamProgressBars');
        if (!container) return;

        const groups = {};
        items.forEach(item => {
            const cat = item.category || 'General Peripherals';
            if (!groups[cat]) {
                groups[cat] = { weight: 0 };
            }
            groups[cat].weight += (parseFloat(item.weight) || 0);
        });

        const categories = Object.keys(groups);
        if (categories.length === 0) {
            container.innerHTML = '<p class="text-xs text-slate-400">No mass distribution data available.</p>';
            return;
        }

        let html = '';
        categories.forEach(cat => {
            const g = groups[cat];
            const pct = totalWeight > 0 ? (g.weight / totalWeight) * 100 : 0;
            html += `
                <div>
                    <div class="flex justify-between text-[11px] font-black uppercase mb-1.5 text-slate-700">
                        <span>${cat}</span>
                        <span class="text-emerald-700 font-black">${pct.toFixed(1)}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-200/70 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-500 rounded-full transition-all duration-500" style="width: ${pct.toFixed(1)}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 font-semibold italic">${g.weight.toFixed(1)} lbs recovered</p>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function openReportModal() {
        const card = document.getElementById('esgReportCard');
        const modalBody = document.getElementById('modalReportBody');
        const modal = document.getElementById('officialReportModal');

        if (card && modalBody && modal) {
            modalBody.innerHTML = card.outerHTML;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeReportModal() {
        const modal = document.getElementById('officialReportModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function triggerReportPrint() {
        const reportElement = document.getElementById('esgReportCard');
        if (!reportElement) {
            window.print();
            return;
        }

        const reportHtml = reportElement.outerHTML;
        const printWin = window.open('', '_blank', 'width=1000,height=850');
        if (!printWin) {
            window.print();
            return;
        }

        let docContent = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        docContent += '<title>ESG Environmental Impact Report</title>';
        docContent += '<script src="https://cdn.tailwindcss.com"><\/script>';
        docContent += '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
        docContent += '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">';
        docContent += '<style>';
        docContent += '@page { size: letter portrait; margin: 12mm 14mm; }';
        docContent += '*, *::before, *::after { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; box-sizing: border-box !important; }';
        docContent += 'html, body { background: #ffffff !important; margin: 0 !important; padding: 0 !important; font-family: "Plus Jakarta Sans", system-ui, -apple-system, sans-serif !important; color: #0f172a !important; width: 100% !important; }';
        docContent += '#esgReportCard { border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }';
        docContent += 'tr, td, th { page-break-inside: avoid !important; break-inside: avoid !important; }';
        docContent += '.no-print { display: none !important; }';
        docContent += '@media print { body { padding: 0 !important; } }';
        docContent += '</style>';
        docContent += '</head><body class="bg-white"><div class="w-full">';
        docContent += reportHtml;
        docContent += '</div><script>setTimeout(function() { window.print(); }, 400);<\/script></body></html>';

        printWin.document.write(docContent);
        printWin.document.close();
    }

    document.getElementById('officialReportModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'officialReportModal') {
            closeReportModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeReportModal();
        }
    });
</script>
@endpush
@endsection
