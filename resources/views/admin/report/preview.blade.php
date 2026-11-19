@extends('layouts.admin')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<div class="bg-[#eef2f6] min-h-screen pt-8 pb-16 font-['Plus_Jakarta_Sans',sans-serif]">
    
    {{-- Action Bar --}}
    <div class="max-w-7xl mx-auto mb-6 flex flex-wrap justify-end gap-3 no-print">
        @php $clientEmail = $items->first()->email ?? null; @endphp
        <form action="{{ route('admin.report.send-email') }}" method="POST" onsubmit="return confirm('Send this report to {{ $clientEmail ?: 'the client' }}?')">
            @csrf
            <input type="hidden" name="client_email" value="{{ $clientEmail }}">
            <button type="submit" @disabled(! $clientEmail) class="bg-white hover:bg-slate-50 text-slate-700 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-sm border border-slate-200 flex items-center gap-3 transition-all {{ $clientEmail ? '' : 'opacity-50 cursor-not-allowed' }}">
                <i class="fas fa-envelope text-emerald-600"></i> Send Email to Client
            </button>
        </form>
        <button onclick="window.print()" class="bg-white hover:bg-slate-50 text-slate-700 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-sm border border-slate-200 flex items-center gap-3 transition-all">
            <i class="fas fa-print text-emerald-600"></i> Print Official Report
        </button>
    </div>

    @if (session('success'))
        <div class="max-w-7xl mx-auto mb-6 no-print">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl font-bold text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto mb-6 no-print">
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl font-bold text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main Report Container (7xl Width as requested) --}}
    <div class="max-w-7xl mx-auto bg-white shadow-2xl border border-slate-200/80 rounded-3xl overflow-hidden print-container">
        
        @php 
            $reportMeta = $items->first(); 
            $clientDisplayName = $reportMeta->client_display_name ?? 'Client';
            $companyName = $reportMeta->company ?? 'No Company Registered';
            $address = $reportMeta->address ?? '';
            $totalPalletsCount = $items->count();
        @endphp

        {{-- Dark Green Top Header Bar --}}
        <div class="bg-[#1b4332] p-10 md:p-12 text-white relative">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                <div>
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#24523e] text-[#4ade80] text-[10px] font-extrabold uppercase tracking-widest mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#4ade80]"></span>
                        ENVIRONMENTAL, SOCIAL & GOVERNANCE
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4">ESG Impact Report</h1>
                    
                    <div>
                        <p class="text-[10px] font-black text-emerald-200/60 uppercase tracking-widest mb-1">REPORTING ENTITY</p>
                        <h2 class="text-3xl font-black text-[#4ade80] uppercase tracking-wide">
                            {{ $clientDisplayName }}
                        </h2>
                    </div>
                </div>

                <div class="text-left md:text-right">
                    <p class="text-[10px] font-black text-emerald-200/60 uppercase tracking-widest mb-1">SCOPE & SELECTION</p>
                    <p class="text-sm font-extrabold text-white">All {{ $totalPalletsCount }} Pallets Included</p>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 space-y-10">
            
            {{-- Prepared Cards --}}
            <div class="bg-slate-50/70 border border-slate-200/70 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-800 text-[10px] font-black uppercase tracking-wider mb-3">
                        PREPARED FOR
                    </span>
                    <h3 class="text-2xl font-black text-slate-900">{{ $clientDisplayName }}</h3>
                    @if($companyName)
                        <p class="text-xs font-bold text-slate-500 uppercase mt-0.5">{{ $companyName }}</p>
                    @endif
                    @if($address)
                        <p class="text-xs font-semibold text-slate-400 uppercase mt-1">{{ $address }}</p>
                    @endif
                </div>

                <div class="text-left md:text-right">
                    <span class="inline-block px-3 py-1 rounded-full bg-slate-200/70 text-slate-700 text-[10px] font-black uppercase tracking-wider mb-3">
                        PREPARED BY
                    </span>
                    <h4 class="text-base font-black text-slate-800">IT Investment Recoveries</h4>
                    <p class="text-xs font-extrabold text-slate-400 uppercase mt-1 tracking-wider">
                        {{ strtoupper(date('F d, Y')) }}
                    </p>
                </div>
            </div>

            {{-- 1. CORE ESG ENVIRONMENTAL METRICS --}}
            <div>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    1. CORE ESG ENVIRONMENTAL METRICS
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    {{-- Metric 1 --}}
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">TOTAL DEVICES RECYCLED</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tight my-1">
                            {{ number_format((float) $results['totalUnits']) }}
                        </p>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 mt-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            UNITS PROCESSED
                        </p>
                    </div>

                    {{-- Metric 2 --}}
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">LANDFILL DIVERSION</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tight my-1">
                            {{ number_format((float) $results['totalWeightLbs'], 1) }} <span class="text-xs font-bold text-slate-500">lbs</span>
                        </p>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 mt-3">
                            <i class="fas fa-recycle text-emerald-600 text-xs"></i>
                            {{ number_format((float) $results['totalWeightLbs'] / 2204, 2) }} TONNES DIVERTED
                        </p>
                    </div>

                    {{-- Metric 3 --}}
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">CO₂e EMISSIONS AVOIDED</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tight my-1">
                            {{ number_format((float) $results['co2AvoidedLbs'], 1) }} <span class="text-xs font-bold text-slate-500">lbs</span>
                        </p>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 mt-3">
                            <i class="fas fa-leaf text-emerald-600 text-xs"></i>
                            CARBON FOOTPRINT REDUCTION
                        </p>
                    </div>

                    {{-- Metric 4 (Highlighted with Border) --}}
                    <div class="bg-white border-2 border-[#1b4332] rounded-2xl p-6 shadow-sm relative">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">WATER CONSERVED</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tight my-1">
                            {{ number_format((float) $results['waterSavedGallons']) }} <span class="text-xs font-bold text-slate-500">gal</span>
                        </p>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 mt-3">
                            <i class="fas fa-tint text-emerald-600 text-xs"></i>
                            INDUSTRIAL WATER RECOVERY
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2. ENVIRONMENTAL EQUIVALENCY IMPACT --}}
            <div>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4">
                    2. ENVIRONMENTAL EQUIVALENCY IMPACT
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $indicators = [
                            ['fa-tree', $results['treesEquivalent'], 'MATURE TREES PLANTED EQUIVALENT'],
                            ['fa-car', $results['carsOffRoadDays'], 'CAR DAYS OFF THE ROAD'],
                            ['fa-bolt', $results['homesEnergyDays'], 'HOME POWER DAYS CONSERVED']
                        ];
                    @endphp
                    @foreach($indicators as $ind)
                    <div class="bg-slate-50/70 border border-slate-200/80 p-8 text-center rounded-2xl">
                        <div class="w-12 h-12 rounded-full bg-emerald-100/80 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                            <i class="fas {{ $ind[0] }} text-xl"></i>
                        </div>
                        <p class="text-4xl font-black text-slate-900 tracking-tight">{{ number_format((float) $ind[1]) }}</p>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mt-2">{{ $ind[2] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. DETAILED ITEM BREAKDOWN BY CATEGORY --}}
            <div>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4">
                    3. DETAILED ITEM BREAKDOWN BY CATEGORY
                </h3>
                <div class="overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-[#1b4332] text-white text-[10px] uppercase font-black tracking-widest">
                            <tr>
                                <th class="py-4 px-6">CATEGORY STREAM</th>
                                <th class="py-4 px-6 text-center">DEVICES / UNITS</th>
                                <th class="py-4 px-6 text-center">NET WEIGHT (LBS)</th>
                                <th class="py-4 px-6 text-right">% CONTRIBUTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                            @foreach ($items->groupBy('category') as $category => $group)
                                @php
                                    $weight = (float) $group->sum('weight');
                                    $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6 font-extrabold text-slate-900 uppercase">{{ $category ?: 'GENERAL PERIPHERALS' }}</td>
                                    <td class="py-4 px-6 text-center font-bold">{{ number_format($group->sum('quantity')) }}</td>
                                    <td class="py-4 px-6 text-center font-bold">{{ number_format($weight, 1) }}</td>
                                    <td class="py-4 px-6 text-right text-emerald-600 font-black">{{ number_format($percent, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. MASS DISTRIBUTION ACROSS WASTE STREAMS --}}
            <div>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4">
                    4. MASS DISTRIBUTION ACROSS WASTE STREAMS
                </h3>
                <div class="bg-slate-50/70 border border-slate-200/80 p-6 md:p-8 rounded-2xl">
                    @foreach ($items->groupBy('category') as $category => $group)
                        @php
                            $weight = (float) $group->sum('weight');
                            $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                        @endphp
                        <div class="mb-4 last:mb-0">
                            <div class="flex justify-between text-xs font-extrabold uppercase mb-2 text-slate-700">
                                <span>{{ strtoupper($category ?: 'GENERAL PERIPHERALS') }}</span>
                                <span class="text-emerald-600 font-black">{{ number_format($percent, 1) }}%</span>
                            </div>
                            <div class="w-full h-3 bg-slate-200/80 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5 font-bold italic">{{ number_format($weight, 1) }} lbs recovered</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Compliance Certification Statement Box --}}
            <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 flex items-start gap-4">
                <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    <strong class="font-bold text-slate-800">IT Investment Recoveries</strong> certifies that the materials referenced in this ESG report have been recycled and recovered in accordance with established IT Asset Management & E-Waste recycling procedures and in compliance with applicable <strong class="font-bold text-slate-800">NIST SP 800-88 Rev. 2</strong> Guidelines for Media Sanitization / Physical Destruction, as well as all applicable local, city, state, and federal environmental regulations.
                </p>
            </div>

            {{-- Authorized Signature Block --}}
            <div class="pt-4 border-t border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">AUTHORIZED COMPLIANCE OFFICER</p>
                <div class="inline-block border-b border-slate-300 pb-1 pr-12">
                    <span style="font-family: 'Dancing Script', 'Brush Script MT', cursive;" class="text-3xl text-slate-800 font-bold tracking-wide">
                        Salvaster Daniel
                    </span>
                </div>
            </div>
        </div>

        {{-- Footer Bar --}}
        <div class="bg-slate-50 border-t border-slate-200/80 px-8 py-4 flex flex-col sm:flex-row justify-between items-center text-[10px] font-black text-slate-400 uppercase tracking-widest gap-2">
            <span>IT INVESTMENT RECOVERIES • ESG ENVIRONMENTAL REPORT</span>
            <span>GENERATED: {{ strtoupper(date('F d, Y')) }}</span>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        
        .print-container, .print-container * {
            visibility: visible;
        }

        .print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }

        .no-print, button {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background-color: white !important;
        }

        html, body {
            height: auto !important;
            overflow: visible !important;
        }
    }
</style>
@endsection