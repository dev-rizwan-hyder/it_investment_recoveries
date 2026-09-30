@extends('user.layouts.app')

@section('title', 'ESG Environmental Impact Report')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="bg-slate-100/70 min-h-screen py-8 font-['Plus_Jakarta_Sans']">
    
    {{-- Action Bar --}}
    <div class="max-w-6xl mx-auto mb-4 flex justify-end gap-3 px-4 sm:px-6 no-print">
        <button onclick="openReportModal()" class="bg-white hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl font-semibold text-xs uppercase tracking-wider shadow-sm border border-slate-200 flex items-center gap-2 transition-all active:scale-95">
            <i class="fas fa-file-contract text-emerald-600"></i> Preview Official Report Modal
        </button>
        <button onclick="openReportModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold text-xs uppercase tracking-wider shadow-md flex items-center gap-2 transition-all active:scale-95">
            <i class="fas fa-print text-white"></i> Print Official Report
        </button>
    </div>

    {{-- Main Report Container --}}
    <div class="max-w-6xl mx-auto bg-white shadow-md border border-slate-200/80 rounded-2xl overflow-hidden print-container mx-4 sm:mx-6 lg:mx-auto">
        
        @php 
            $reportMeta = $items->first(); 
        @endphp

        {{-- Header Banner Section --}}
        <div class="bg-gradient-to-r from-[#1b3d2f] via-[#244f3d] to-[#1b3d2f] p-6 sm:p-10 text-white relative overflow-hidden">
            <!-- Decorative background accents -->
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-[10px] font-semibold uppercase tracking-widest text-emerald-300">
                        <i class="fas fa-leaf text-emerald-400"></i> Environmental, Social & Governance
                    </span>
                </div>
                
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white">
                    ESG Impact Report
                </h1>
                
                <div class="mt-4 pt-3 border-t border-white/10">
                    <p class="text-[10px] font-medium text-emerald-200/80 uppercase tracking-widest">Reporting Entity</p>
                    <h2 class="text-xl sm:text-2xl font-bold text-emerald-300 uppercase tracking-wide mt-0.5">
                        {{ $client->name ?? ($reportMeta->client_display_name ?? 'Client Name') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10 space-y-8">
            {{-- Prepared For Section Card --}}
            <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 flex flex-col sm:flex-row justify-between items-start gap-6">
                <div class="space-y-1.5">
                    <span class="inline-block text-[10px] font-semibold text-emerald-800 bg-emerald-100/80 border border-emerald-200/80 px-2 py-0.5 rounded uppercase tracking-wider">
                        Prepared For
                    </span>
                    <div>
                        <p class="text-xl font-bold text-slate-800 tracking-tight">
                            {{ $client->name ?? ($reportMeta->client_display_name ?? 'N/A') }}
                        </p>
                        <p class="text-sm font-semibold text-emerald-700 italic uppercase tracking-wide mt-0.5">
                            {{ $client->company ?? ($reportMeta->company ?? 'No Company Registered') }}
                        </p>
                        <p class="text-xs text-slate-500 font-medium uppercase mt-1 max-w-md">
                            {{ $client->address ?? ($reportMeta->address ?? 'Address not available') }}
                        </p>
                    </div>
                </div>

                <div class="text-left sm:text-right space-y-1">
                    <span class="inline-block text-[10px] font-semibold text-slate-600 bg-slate-200/70 border border-slate-300/60 px-2 py-0.5 rounded uppercase tracking-wider">
                        Prepared By
                    </span>
                    <p class="text-sm font-bold text-slate-800 mt-1">
                        IT Investment Recoveries
                    </p>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-0.5">
                        {{ date('F d, Y') }}
                    </p>
                </div>
            </div>

            {{-- Impact Metrics Grid --}}
            <div>
                <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wider mb-3">Core ESG Environmental Metrics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Devices -->
                    <div class="border-l-4 border-emerald-600 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-xl border border-slate-200/80 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Devices Recycled</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight mt-1">
                            {{ number_format((float) $results['totalUnits']) }}
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium mt-1.5 uppercase flex items-center gap-1">
                            <i class="fas fa-check-circle text-emerald-600 text-xs"></i> Units Processed
                        </p>
                    </div>

                    <!-- Landfill Diversion -->
                    <div class="border-l-4 border-emerald-800 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-xl border border-slate-200/80 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Landfill Diversion</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight mt-1">
                            {{ number_format((float) $results['totalWeightLbs']) }} <span class="text-xs font-semibold text-slate-500">lbs</span>
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium mt-1.5 uppercase flex items-center gap-1">
                            <i class="fas fa-recycle text-emerald-700 text-xs"></i> {{ number_format((float) $results['totalWeightLbs'] / 2204, 2) }} Tonnes Diverted
                        </p>
                    </div>

                    <!-- CO2 Emissions Avoided -->
                    <div class="border-l-4 border-emerald-900 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-xl border border-slate-200/80 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">CO₂e Emissions Avoided</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight mt-1">
                            {{ number_format((float) $results['co2AvoidedLbs']) }} <span class="text-xs font-semibold text-slate-500">lbs</span>
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium mt-1.5 uppercase flex items-center gap-1">
                            <i class="fas fa-cloud-sun text-emerald-800 text-xs"></i> Carbon Footprint Reduction
                        </p>
                    </div>

                    <!-- Water Conserved -->
                    <div class="border-l-4 border-teal-500 bg-gradient-to-b from-white to-slate-50/50 p-5 rounded-r-xl border border-slate-200/80 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Water Conserved</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight mt-1">
                            {{ number_format((float) $results['waterSavedGallons']) }} <span class="text-xs font-semibold text-slate-500">gal</span>
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium mt-1.5 uppercase flex items-center gap-1">
                            <i class="fas fa-tint text-teal-600 text-xs"></i> Industrial Water Recovery
                        </p>
                    </div>
                </div>
            </div>

            {{-- Equivalence Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @php
                    $indicators = [
                        ['fa-tree', $results['treesEquivalent'], 'Mature Trees Planted'],
                        ['fa-car', $results['carsOffRoadDays'], 'Car Days Off Road'],
                        ['fa-bolt', $results['homesEnergyDays'], 'Home Power Days']
                    ];
                @endphp
                @foreach($indicators as $ind)
                <div class="bg-white border border-slate-200/80 p-5 text-center rounded-xl shadow-sm hover:border-emerald-300 transition-colors">
                    <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base mx-auto mb-2.5 border border-emerald-100">
                        <i class="fas {{ $ind[0] }}"></i>
                    </div>
                    <p class="text-2xl font-bold text-slate-800 tracking-tight">{{ number_format((float) $ind[1]) }}</p>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1">{{ $ind[2] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Breakdown Table --}}
            <div>
                <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wider mb-3">Detailed Breakdown by Category</h3>
                <div class="overflow-x-auto border border-slate-200/80 rounded-xl shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-[#1b3d2f] to-[#244f3d] text-white text-[10px] font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Category</th>
                                <th class="py-3.5 px-4 text-center">Units</th>
                                <th class="py-3.5 px-4 text-center">Weight (lbs)</th>
                                <th class="py-3.5 px-6 text-right">% Contribution</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse ($items->groupBy('category') as $category => $group)
                                @php
                                    $weight = (float) $group->sum('weight');
                                    $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-emerald-50/20 transition-colors">
                                    <td class="py-3.5 px-6 text-slate-900 uppercase font-semibold">{{ $category ?: 'General Peripherals' }}</td>
                                    <td class="py-3.5 px-4 text-center text-slate-700 font-semibold">{{ number_format($group->sum('quantity')) }}</td>
                                    <td class="py-3.5 px-4 text-center text-slate-700 font-semibold">{{ number_format($weight) }}</td>
                                    <td class="py-3.5 px-6 text-right text-emerald-700 font-bold text-xs">{{ number_format($percent, 1) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 px-6 text-center text-slate-400 font-medium uppercase text-xs">
                                        No completed recycled items recorded yet for this organization.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Weight Distribution Progress Bar --}}
            @if($items->isNotEmpty() && $results['totalWeightLbs'] > 0)
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-700 mb-5">Mass Distribution Across Streams</h3>
                <div class="space-y-4">
                    @foreach ($items->groupBy('category') as $category => $group)
                        @php
                            $weight = (float) $group->sum('weight');
                            $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-[11px] font-semibold uppercase mb-1.5 text-slate-600">
                                <span>{{ $category ?: 'Other Assets' }}</span>
                                <span class="text-emerald-700 font-bold">{{ number_format($percent, 1) }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 border border-slate-200/70 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-500 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 font-medium italic">{{ number_format($weight) }} lbs recovered</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="bg-slate-50/80 border-t border-slate-100 px-6 sm:px-10 py-4 flex flex-col sm:flex-row justify-between items-center text-[10px] font-semibold text-slate-400 uppercase tracking-widest gap-2">
            <span>IT Investment Recoveries • ESG Compliance Report</span>
            <span>Generated: {{ date('F d, Y') }}</span>
        </div>
    </div>
</div>

{{-- Official ESG Report Modal --}}
<div id="officialReportModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto no-print">
    <div class="relative bg-white w-full max-w-5xl max-h-[92vh] rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col my-auto">
        
        {{-- Modal Sticky Top Header --}}
        <div class="sticky top-0 z-30 bg-slate-900 text-white px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-contract text-emerald-400 text-base"></i>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-200">Official ESG Report Preview Modal</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="printFromModal()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-wider transition shadow flex items-center gap-2 active:scale-95">
                    <i class="fas fa-print"></i> Print Official Report
                </button>
                <button onclick="closeReportModal()" class="h-9 w-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm font-bold transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Modal Content Area --}}
        <div class="p-6 sm:p-8 overflow-y-auto max-h-[calc(92vh-4rem)] space-y-6 bg-slate-50/50">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-md overflow-hidden">
                
                {{-- Header Banner Section --}}
                <div class="bg-gradient-to-r from-[#1b3d2f] via-[#244f3d] to-[#1b3d2f] p-6 sm:p-10 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2.5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-[10px] font-semibold uppercase tracking-widest text-emerald-300">
                                <i class="fas fa-leaf text-emerald-400"></i> Environmental, Social & Governance
                            </span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white">ESG Impact Report</h2>
                        <div class="mt-4 pt-3 border-t border-white/10">
                            <p class="text-[10px] font-medium text-emerald-200/80 uppercase tracking-widest">Reporting Entity</p>
                            <p class="text-xl sm:text-2xl font-bold text-emerald-300 uppercase tracking-wide mt-0.5">
                                {{ $client->name ?? ($reportMeta->client_display_name ?? 'Client Name') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Prepared For Card --}}
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="space-y-1">
                            <span class="inline-block text-[10px] font-semibold text-emerald-800 bg-emerald-100/80 border border-emerald-200/80 px-2 py-0.5 rounded uppercase tracking-wider">
                                Prepared For
                            </span>
                            <div>
                                <p class="text-lg font-bold text-slate-800">
                                    {{ $client->name ?? ($reportMeta->client_display_name ?? 'N/A') }}
                                </p>
                                <p class="text-xs font-semibold text-emerald-700 italic uppercase">
                                    {{ $client->company ?? ($reportMeta->company ?? 'No Company Registered') }}
                                </p>
                                <p class="text-[11px] text-slate-500 font-medium uppercase mt-0.5">
                                    {{ $client->address ?? ($reportMeta->address ?? 'Address not available') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-left sm:text-right space-y-1">
                            <span class="inline-block text-[10px] font-semibold text-slate-600 bg-slate-200/70 border border-slate-300/60 px-2 py-0.5 rounded uppercase tracking-wider">
                                Prepared By
                            </span>
                            <p class="text-sm font-bold text-slate-800 mt-1">IT Investment Recoveries</p>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">{{ date('F d, Y') }}</p>
                        </div>
                    </div>

                    {{-- 4 Impact Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border-l-4 border-emerald-600 bg-slate-50/50 p-4 rounded-r-xl border border-slate-200/80">
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Devices Recycled</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format((float) $results['totalUnits']) }}</p>
                            <p class="text-[10px] text-slate-500 font-medium mt-1 uppercase">Units Processed</p>
                        </div>
                        <div class="border-l-4 border-emerald-800 bg-slate-50/50 p-4 rounded-r-xl border border-slate-200/80">
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Landfill Diversion</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format((float) $results['totalWeightLbs']) }} <span class="text-xs font-semibold text-slate-500">lbs</span></p>
                            <p class="text-[10px] text-slate-500 font-medium mt-1 uppercase">{{ number_format((float) $results['totalWeightLbs'] / 2204, 2) }} Tonnes Diverted</p>
                        </div>
                        <div class="border-l-4 border-emerald-900 bg-slate-50/50 p-4 rounded-r-xl border border-slate-200/80">
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">CO₂e Emissions Avoided</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format((float) $results['co2AvoidedLbs']) }} <span class="text-xs font-semibold text-slate-500">lbs</span></p>
                            <p class="text-[10px] text-slate-500 font-medium mt-1 uppercase">Carbon Footprint Reduction</p>
                        </div>
                        <div class="border-l-4 border-teal-500 bg-slate-50/50 p-4 rounded-r-xl border border-slate-200/80">
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Water Conserved</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format((float) $results['waterSavedGallons']) }} <span class="text-xs font-semibold text-slate-500">gal</span></p>
                            <p class="text-[10px] text-slate-500 font-medium mt-1 uppercase">Industrial Water Recovery</p>
                        </div>
                    </div>

                    {{-- 3 Equivalence Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($indicators as $ind)
                        <div class="bg-white border border-slate-200/80 p-4 text-center rounded-xl">
                            <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm mx-auto mb-2 border border-emerald-100">
                                <i class="fas {{ $ind[0] }}"></i>
                            </div>
                            <p class="text-xl font-bold text-slate-800">{{ number_format((float) $ind[1]) }}</p>
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">{{ $ind[2] }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Category Breakdown Table --}}
                    <div>
                        <h4 class="text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2.5">Detailed Breakdown by Category</h4>
                        <div class="overflow-x-auto border border-slate-200/80 rounded-xl">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gradient-to-r from-[#1b3d2f] to-[#244f3d] text-white text-[10px] font-semibold uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-5">Category</th>
                                        <th class="py-3 px-3 text-center">Units</th>
                                        <th class="py-3 px-3 text-center">Weight (lbs)</th>
                                        <th class="py-3 px-5 text-right">% Contribution</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                                    @forelse ($items->groupBy('category') as $category => $group)
                                        @php
                                            $weight = (float) $group->sum('weight');
                                            $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td class="py-3 px-5 text-slate-900 uppercase font-semibold">{{ $category ?: 'General Peripherals' }}</td>
                                            <td class="py-3 px-3 text-center text-slate-700 font-semibold">{{ number_format($group->sum('quantity')) }}</td>
                                            <td class="py-3 px-3 text-center text-slate-700 font-semibold">{{ number_format($weight) }}</td>
                                            <td class="py-3 px-5 text-right text-emerald-700 font-bold text-xs">{{ number_format($percent, 1) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-5 px-5 text-center text-slate-400 font-medium uppercase text-xs">
                                                No completed recycled items recorded yet for this organization.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Weight Distribution Progress Bar --}}
                    @if($items->isNotEmpty() && $results['totalWeightLbs'] > 0)
                    <div class="bg-white rounded-xl border border-slate-200/80 p-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-700 mb-4">Mass Distribution Across Streams</h4>
                        <div class="space-y-3">
                            @foreach ($items->groupBy('category') as $category => $group)
                                @php
                                    $weight = (float) $group->sum('weight');
                                    $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-[11px] font-semibold uppercase mb-1 text-slate-600">
                                        <span>{{ $category ?: 'Other Assets' }}</span>
                                        <span class="text-emerald-700 font-bold">{{ number_format($percent, 1) }}%</span>
                                    </div>
                                    <div class="w-full h-2 bg-slate-100 border border-slate-200/70 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-500 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="bg-slate-50/80 border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row justify-between items-center text-[10px] font-semibold text-slate-400 uppercase tracking-widest gap-2">
                    <span>IT Investment Recoveries • ESG Compliance Report</span>
                    <span>Generated: {{ date('F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openReportModal() {
        const modal = document.getElementById('officialReportModal');
        if (modal) {
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

    function printFromModal() {
        window.print();
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

<style>
    @media print {
        @page {
            size: letter portrait;
            margin: 0 !important; /* Zero margin suppresses browser date/time header and page URL footer */
        }

        /* Force high-contrast background & text print color rendering */
        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        html, body {
            height: 100% !important;
            overflow: visible !important;
            background-color: #ffffff !important;
            background: #ffffff !important;
            font-size: 11px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        header, sidebar, nav, #sidebar, #sidebarOverlay, .no-print, button, #toastContainer, #officialReportModal {
            display: none !important;
        }

        body * {
            visibility: hidden;
        }

        .print-container, .print-container * {
            visibility: visible;
        }

        .print-container {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0.35in 0.4in !important; /* Internal page margin without browser timestamp header */
            box-shadow: none !important;
            border: none !important;
            background: #ffffff !important;
            border-radius: 0 !important;
            box-sizing: border-box !important;
        }

        /* Header Banner solid green background print fallback */
        .print-container .bg-gradient-to-r,
        .print-container .bg-\[\#2d5a43\] {
            background-color: #1b3d2f !important;
            background: #1b3d2f !important;
            color: #ffffff !important;
            padding: 1.25rem 1.5rem !important;
        }

        .print-container .bg-gradient-to-r *,
        .print-container .bg-\[\#2d5a43\] * {
            color: #ffffff !important;
        }

        .print-container .text-emerald-300,
        .print-container .text-emerald-400 {
            color: #6ee7b7 !important;
        }

        /* Information cards solid backgrounds */
        .print-container .bg-slate-50\/70,
        .print-container .bg-slate-50 {
            background-color: #f8fafc !important;
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            padding: 0.75rem 1rem !important;
        }

        /* Impact cards left border & background */
        .print-container .border-l-4 {
            border-left-width: 4px !important;
            border-left-color: #059669 !important;
            border-top: 1px solid #cbd5e1 !important;
            border-right: 1px solid #cbd5e1 !important;
            border-bottom: 1px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            background: #f8fafc !important;
            padding: 0.75rem 1rem !important;
        }

        /* Equivalence cards */
        .print-container .bg-white.border {
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            padding: 0.75rem 1rem !important;
        }

        .print-container .bg-emerald-50 {
            background-color: #ecfdf5 !important;
            color: #059669 !important;
        }

        /* Table solid green header & borders */
        .print-container table thead {
            background-color: #1b3d2f !important;
            background: #1b3d2f !important;
        }

        .print-container table thead th {
            color: #ffffff !important;
            background-color: #1b3d2f !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 10px !important;
            border: none !important;
        }

        .print-container table tbody td {
            padding: 0.45rem 0.75rem !important;
            font-size: 11px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Stream progress bar print fill */
        .print-container .bg-gradient-to-r.from-emerald-600 {
            background-color: #059669 !important;
            background: #059669 !important;
        }

        .print-container .text-slate-900,
        .print-container .text-slate-800,
        .print-container .text-slate-700 {
            color: #0f172a !important;
        }

        .print-container .text-slate-500,
        .print-container .text-slate-400 {
            color: #475569 !important;
        }

        .print-container .text-emerald-700,
        .print-container .text-emerald-600 {
            color: #047857 !important;
        }

        /* Section layout spacing for single page portrait balance */
        .print-container .p-6,
        .print-container .p-10,
        .print-container .sm\:p-10 {
            padding: 1rem 1.25rem !important;
        }

        .print-container .space-y-8 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 0.85rem !important;
        }

        .print-container .mb-4,
        .print-container .mb-3 {
            margin-bottom: 0.5rem !important;
        }
    }
</style>
@endsection
