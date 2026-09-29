@extends('user.layouts.app')

@section('title', 'ESG Environmental Impact Report')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Header Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <!-- Glowing background gradients -->
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-teal-600/30 to-emerald-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-cyan-500/20 to-green-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-teal-500/10 border border-teal-400/30 text-xs font-bold uppercase tracking-widest text-teal-300">
                        <i class="fa-solid fa-leaf mr-1.5 text-teal-400"></i> Corporate Sustainability & Impact
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Organization: {{ $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    ESG & Environmental Impact Report
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Comprehensive metrics detailing your carbon footprint reduction, zero-landfill e-waste diversion, and material recycling performance.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row shrink-0">
                <button onclick="window.print()" 
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur-md transition-all duration-200 hover:bg-white/20 active:scale-95">
                    <i class="fa-solid fa-print text-teal-300"></i>
                    Export / Print ESG Report
                </button>
            </div>
        </div>
    </section>

    {{-- Highlight Impact KPI Cards Grid --}}
    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1: CO2 Avoided -->
        <div class="portal-card rounded-3xl p-6 relative overflow-hidden group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Carbon Offset (CO2e)</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-teal-600">{{ $metrics['co2SavedMetricTons'] }} <span class="text-xs font-bold text-slate-500">MT</span></p>
                    <p class="mt-1 text-[11px] text-slate-500 font-semibold">{{ $metrics['co2SavedLbs'] }} lbs CO2 prevented</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                </span>
            </div>
        </div>

        <!-- Card 2: Landfill Diversion Rate -->
        <div class="portal-card rounded-3xl p-6 relative overflow-hidden group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Landfill Diversion Rate</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-emerald-600">{{ $metrics['landfillDiversionRate'] }}%</p>
                    <p class="mt-1 text-[11px] text-slate-500 font-semibold">100% EPA Zero-Landfill</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-recycle"></i>
                </span>
            </div>
        </div>

        <!-- Card 3: Trees Equivalent Saved -->
        <div class="portal-card rounded-3xl p-6 relative overflow-hidden group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Trees Equivalent Saved</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-green-600">{{ $metrics['treesSaved'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-500 font-semibold">Annual carbon sequestration</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 border border-green-100 text-green-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-tree"></i>
                </span>
            </div>
        </div>

        <!-- Card 4: Energy Saved -->
        <div class="portal-card rounded-3xl p-6 relative overflow-hidden group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Energy Conserved</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-indigo-600">{{ $metrics['kwhSaved'] }} <span class="text-xs font-bold text-slate-500">kWh</span></p>
                    <p class="mt-1 text-[11px] text-slate-500 font-semibold">Grid electricity saved</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-bolt text-lg"></i>
                </span>
            </div>
        </div>
    </section>

    {{-- Main ESG Detailed Impact Sections --}}
    <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <!-- Environmental Material Breakdown -->
        <section class="portal-card rounded-3xl overflow-hidden xl:col-span-2 p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900">E-Waste & Material Recovery Summary</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Total processed electronics converted into reusable commodity streams</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold">
                    As of {{ $metrics['reportDate'] }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 mx-auto mb-3">
                        <i class="fa-solid fa-cubes-stacked"></i>
                    </span>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Weight Recycled</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $metrics['totalRecycledLbs'] }} <span class="text-xs font-normal text-slate-500">lbs</span></p>
                    <p class="text-[11px] font-bold text-slate-500 mt-1">({{ $metrics['totalRecycledTons'] }} US Tons)</p>
                </div>

                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 mx-auto mb-3">
                        <i class="fa-solid fa-gear"></i>
                    </span>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Recovered Metals</p>
                    <p class="text-2xl font-black text-amber-700 mt-1">{{ $metrics['metalsRecoveredLbs'] }} <span class="text-xs font-normal text-slate-500">lbs</span></p>
                    <p class="text-[11px] font-bold text-slate-500 mt-1">Steel, Aluminum & Copper</p>
                </div>

                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-600 mx-auto mb-3">
                        <i class="fa-solid fa-gem"></i>
                    </span>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Precious Metals</p>
                    <p class="text-2xl font-black text-purple-700 mt-1">{{ $metrics['preciousMetalsGrams'] }} <span class="text-xs font-normal text-slate-500">grams</span></p>
                    <p class="text-[11px] font-bold text-slate-500 mt-1">Gold, Silver & Palladium</p>
                </div>
            </div>

            <!-- Activity Volumes Breakdown Table -->
            <div class="border-t border-slate-100 pt-6 space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Activity & Manifest Totals</h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    <div class="p-4 rounded-2xl bg-slate-50">
                        <p class="text-2xl font-black text-slate-900">{{ $metrics['totalIntakesCount'] }}</p>
                        <p class="text-[11px] font-bold text-slate-500 mt-0.5">Pallets Received</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50">
                        <p class="text-2xl font-black text-rose-600">{{ $metrics['totalDataDestructionCount'] }}</p>
                        <p class="text-[11px] font-bold text-slate-500 mt-0.5">Sanitized Drives</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50">
                        <p class="text-2xl font-black text-emerald-600">{{ $metrics['totalUniversalWasteCount'] }}</p>
                        <p class="text-[11px] font-bold text-slate-500 mt-0.5">Universal Waste Units</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50">
                        <p class="text-2xl font-black text-indigo-600">{{ $metrics['totalItAssetsCount'] }}</p>
                        <p class="text-[11px] font-bold text-slate-500 mt-0.5">IT Hardware Assets</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Environmental Compliance Certificate Box -->
        <aside class="space-y-6">
            <div class="portal-card rounded-3xl p-6 bg-slate-950 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-emerald-500/10 blur-2xl"></div>

                <div class="relative z-10 space-y-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <i class="fa-solid fa-award text-2xl"></i>
                    </span>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400">Official Verification</span>
                        <h3 class="text-lg font-black text-white mt-1">Scope 3 GHG Reduction</h3>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed mt-2">
                            All electronic equipment processed under your organization account strictly adheres to R2/RIOS guidelines, ISO 14001 environmental standards, and state EPA regulations.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-300">
                            <span class="text-slate-400 font-bold">Audit Standard:</span>
                            <span class="font-bold text-white">EPA WARM v15</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span class="text-slate-400 font-bold">Landfill Rate:</span>
                            <span class="font-bold text-emerald-400">0.0% (Zero Landfill)</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span class="text-slate-400 font-bold">Certificate Status:</span>
                            <span class="font-bold text-emerald-400">Active & Certified</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
