@extends('user.layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Hero Banner Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <!-- Glowing background gradients -->
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-blue-600/30 to-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-cyan-500/20 to-teal-400/10 blur-3xl pointer-events-none"></div>

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
                    Real-time monitoring of your IT asset recoveries, certified data destruction, received inventory, and purchase orders.
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

    {{-- KPI Cards Grid --}}
    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Card 1: Orders -->
        <a href="{{ route('user.orders.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Orders</p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">{{ $totalOrders }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 text-xl shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-bag-shopping"></i>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold pt-3 border-t border-slate-100">
                <span>{{ $activeOrders }} Active fulfillment</span>
                <span class="text-blue-600 group-hover:translate-x-1 transition-transform">View all <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></span>
            </div>
        </a>

        <!-- Card 2: Received Intakes -->
        <a href="{{ route('user.received-intake.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Received Intakes</p>
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

        <!-- Card 3: Data Destruction -->
        <a href="{{ route('user.data-destruction.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Data Destruction</p>
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

        <!-- Card 4: IT Assets -->
        <a href="{{ route('user.it-assets.index') }}" class="portal-card portal-card-hover rounded-2xl p-6 block group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">IT Assets</p>
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
    </section>

    {{-- Main Activity Section (Orders & Quick Actions) --}}
    <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <!-- Recent Orders Feed -->
        <section class="portal-card rounded-3xl overflow-hidden xl:col-span-2 flex flex-col">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5 bg-slate-50/50">
                <div>
                    <h2 class="text-base font-black text-slate-900">Recent Purchase Orders</h2>
                    <p class="text-xs text-slate-500 font-medium">Track order status and fulfillment details</p>
                </div>
                <a href="{{ route('user.orders.index') }}" class="text-xs font-extrabold text-blue-600 hover:text-blue-700 hover:underline">
                    View all orders
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse ($recentOrders as $order)
                    <a href="{{ route('user.orders.show', $order->id) }}"
                       class="flex flex-col gap-4 px-6 py-4.5 transition duration-200 hover:bg-slate-50/80 sm:flex-row sm:items-center sm:justify-between group">
                        <div class="flex items-center gap-4 min-w-0">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-box text-sm"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">Order #{{ $order->order_number }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                    {{ $order->items_count }} {{ \Illuminate\Support\Str::plural('item', $order->items_count) }}
                                    <span class="mx-1.5 text-slate-300">|</span>
                                    {{ $order->formatPlacedAtDenver('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <p class="font-black text-slate-900 text-sm">${{ number_format($order->total, 2) }}</p>
                            <span class="rounded-full px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider {{ \App\Models\Order::statusBadgeClass($order->status) }}">
                                {{ $order->status_label }}
                            </span>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:translate-x-1 group-hover:text-blue-600 transition-all"></i>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-14 text-center">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 text-xl">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </span>
                        <h3 class="mt-4 font-extrabold text-slate-900 text-sm">No orders yet</h3>
                        <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Your active and past equipment orders will appear here.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Quick Actions & Profile Box -->
        <aside class="space-y-6">
            <div class="portal-card rounded-3xl p-6">
                <h2 class="text-base font-black text-slate-900">Quick Shortcuts</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Jump directly to key features</p>

                <div class="mt-4 space-y-2.5">
                    <a href="{{ route('user.received-intake.index') }}" 
                       class="group flex items-center gap-3.5 rounded-2xl border border-slate-200/80 p-3.5 transition duration-200 hover:border-blue-300 hover:bg-blue-50/40">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100/70 text-blue-600 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-xs font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">Received Intakes</span>
                            <span class="block text-[11px] text-slate-500 font-medium">Pallets & shipment progress</span>
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-300 group-hover:translate-x-1 group-hover:text-blue-600 transition-all"></i>
                    </a>

                    <a href="{{ route('user.data-destruction.index') }}" 
                       class="group flex items-center gap-3.5 rounded-2xl border border-slate-200/80 p-3.5 transition duration-200 hover:border-rose-300 hover:bg-rose-50/40">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100/70 text-rose-600 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-xs font-extrabold text-slate-900 group-hover:text-rose-600 transition-colors">Data Destruction</span>
                            <span class="block text-[11px] text-slate-500 font-medium">Sanitization compliance</span>
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-300 group-hover:translate-x-1 group-hover:text-rose-600 transition-all"></i>
                    </a>

                    <a href="{{ route('user.it-assets.index') }}" 
                       class="group flex items-center gap-3.5 rounded-2xl border border-slate-200/80 p-3.5 transition duration-200 hover:border-indigo-300 hover:bg-indigo-50/40">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100/70 text-indigo-600 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-laptop-code text-sm"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-xs font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors">IT Asset Inventory</span>
                            <span class="block text-[11px] text-slate-500 font-medium">Equipment details & specs</span>
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-300 group-hover:translate-x-1 group-hover:text-indigo-600 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Client Profile Info Box -->
            <div class="portal-card rounded-3xl p-6 bg-slate-900 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white border border-white/10">
                        <i class="fa-solid fa-user-gear"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Linked Account</p>
                        <p class="text-sm font-black truncate text-white">{{ Auth::user()->name }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 text-xs text-slate-300 border-t border-slate-800 pt-3">
                    <p class="flex justify-between">
                        <span class="text-slate-500 font-bold">Email:</span>
                        <span class="font-semibold truncate max-w-[170px] text-slate-200">{{ Auth::user()->email }}</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-slate-500 font-bold">Client Link:</span>
                        <span class="font-semibold text-emerald-400">{{ $client ? $client->name : 'Individual User' }}</span>
                    </p>
                </div>
            </div>
        </aside>
    </div>

    {{-- Recent Received Intakes Section --}}
    <section class="portal-card rounded-3xl overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5 bg-slate-50/50">
            <div>
                <h2 class="text-base font-black text-slate-900">Recent Received Intakes</h2>
                <p class="text-xs text-slate-500 font-medium">Latest pallet shipments received by our facility</p>
            </div>
            <a href="{{ route('user.received-intake.index') }}" class="text-xs font-extrabold text-blue-600 hover:text-blue-700 hover:underline">
                View all intakes
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($recentIntakes as $intake)
                <a href="{{ route('user.received-intake.show', $intake->id) }}"
                   class="grid gap-4 px-6 py-4.5 transition duration-200 hover:bg-slate-50/80 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-center group">
                    <div class="flex min-w-0 items-center gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 border border-violet-100 group-hover:bg-violet-600 group-hover:text-white transition-colors">
                            <i class="fa-solid fa-box text-sm"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-extrabold text-slate-900 text-sm group-hover:text-blue-600 transition-colors">Pallet #{{ $intake->barcode_number }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500 font-medium">
                                {{ $intake->description ?: 'No description' }}
                                @if ($intake->put_away_location)
                                    <span class="mx-1.5 text-slate-300">|</span>
                                    <span class="text-slate-600 font-bold"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $intake->put_away_location }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div>
                            @php
                                $statusStyle = match (strtolower(trim($intake->status ?: 'received'))) {
                                    'received', 'pending' => 'bg-[#357af6] text-white',
                                    'processing', 'in processing' => 'bg-[#ff6b00] text-white',
                                    'completed' => 'bg-[#10b981] text-white',
                                    default => 'bg-[#357af6] text-white',
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-white {{ $statusStyle }}">
                                {{ $intake->status ?: 'Received' }}
                            </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs font-semibold text-slate-500 sm:justify-end">
                        <span>{{ $intake->created_at ? $intake->created_at->format('M d, Y') : '' }}</span>
                        <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:translate-x-1 group-hover:text-blue-600 transition-all"></i>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 text-xl">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </span>
                    <h3 class="mt-4 font-extrabold text-slate-900 text-sm">No received intake shipments</h3>
                    <p class="mt-1 text-xs text-slate-500">Your shipment records will appear here once processed by our intake team.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

