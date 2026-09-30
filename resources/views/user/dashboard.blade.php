@extends('user.layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Hero Banner Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <!-- Glowing background gradients -->
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-blue-600/30 to-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-emerald-500/20 to-teal-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-xs font-bold uppercase tracking-widest text-blue-300">
                        <i class="fa-solid fa-chart-pie mr-1.5 text-blue-400"></i> Account Dashboard
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> {{ $client->company ?: $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    Welcome back, {{ Auth::user()->name }}
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Real-time monitoring of your IT asset recoveries, certified data destruction, universal waste recycling, and ESG compliance.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row shrink-0">
                <a href="{{ route('user.received-intake.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur-md transition-all duration-200 hover:bg-white/20 active:scale-95">
                    <i class="fa-solid fa-boxes-stacked text-blue-300"></i>
                    Track Intakes
                </a>
            </div>
        </div>
    </section>

    {{-- KPI Cards Grid Matching Sidebar Tabs --}}
    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <!-- Card 1: Recieving / Processing -->
        <a href="{{ route('user.received-intake.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Recieving / Processing</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">{{ $totalIntakes }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold pt-3 border-t border-slate-100">
                <span>Shipments & Pallets</span>
                <span class="text-blue-600 group-hover:translate-x-1 transition-transform">Track <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></span>
            </div>
        </a>

        <!-- Card 2: IT Asset management -->
        <a href="{{ route('user.it-assets.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">IT Asset management</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $totalItAssets }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-laptop-code"></i>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold pt-3 border-t border-slate-100">
                <span>Processed Hardware</span>
                <span class="text-indigo-600 group-hover:translate-x-1 transition-transform">Inventory <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></span>
            </div>
        </a>

        <!-- Card 3: Data destruction / Certificate -->
        <a href="{{ route('user.data-destruction.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Data destruction / Certificate</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900 group-hover:text-rose-600 transition-colors">{{ $totalDestruction }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold pt-3 border-t border-slate-100">
                <span>Wiped & Serialized</span>
                <span class="text-rose-600 group-hover:translate-x-1 transition-transform">Compliance <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></span>
            </div>
        </a>

        <!-- Card 4: Universal Waste / Certificate -->
        <a href="{{ route('user.universal-waste.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Universal Waste / Certificate</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">{{ $totalUniversalWaste }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-recycle"></i>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold pt-3 border-t border-slate-100">
                <span>Recycled & Certified</span>
                <span class="text-emerald-600 group-hover:translate-x-1 transition-transform">Certificates <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></span>
            </div>
        </a>

        <!-- Card 5: ESG Report -->
        <a href="{{ route('user.esg-report.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">ESG Report</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-teal-600 group-hover:text-teal-700 transition-colors">{{ $co2SavedTons }} <span class="text-xs font-bold text-slate-500">MT</span></p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-leaf"></i>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold pt-3 border-t border-slate-100">
                <span>Carbon Offset (CO2e)</span>
                <span class="text-teal-600 group-hover:translate-x-1 transition-transform">View Report <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></span>
            </div>
        </a>
    </section>
</div>
@endsection
