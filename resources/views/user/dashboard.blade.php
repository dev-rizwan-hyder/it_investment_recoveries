@extends('user.layouts.app')

@section('title', 'Account Overview')

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 30px rgba(15, 23, 42, 0.04);
        }
    </style>
@endpush

@section('content')
            <div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
                <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-xl sm:px-8 lg:px-10 lg:py-10">
                    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
                    <div class="absolute -bottom-32 right-1/3 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl"></div>

                    <div class="relative flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-blue-300">Account overview</p>
                            <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                                Welcome back, {{ Auth::user()->name }}
                            </h1>
                            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300 sm:text-base">
                                Track your orders and repair requests from one clear, up-to-date dashboard.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a href="{{ Route::has('store.index') ? route('store.index') : url('/') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-900 transition hover:bg-blue-50">
                                <i class="fa-solid fa-store text-blue-600"></i>
                                Shop products
                            </a>
                            <a href="{{ route('user.received-intake.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                Track Intakes
                            </a>
                        </div>
                    </div>
                </section>

                <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total orders</p>
                                <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $totalOrders }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">All purchases on your account</p>
                    </div>

                    <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Received Intakes</p>
                                <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $totalIntakes }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">Total shipments/pallets received</p>
                    </div>

                    <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Data Destruction</p>
                                <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $totalDestruction }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">Serialized media items being wiped</p>
                    </div>

                    <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">IT Assets</p>
                                <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $totalItAssets }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <i class="fa-solid fa-laptop-code"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">Processed equipment & devices</p>
                    </div>
                </section>

                <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <section class="dashboard-card overflow-hidden rounded-2xl border border-slate-200/80 bg-white xl:col-span-2">
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Recent orders</h2>
                                <p class="mt-1 text-sm text-slate-500">Your latest purchases and fulfillment status</p>
                            </div>
                            <a href="{{ route('user.orders.index') }}" class="shrink-0 text-sm font-bold text-blue-600 hover:text-blue-700">
                                View all
                            </a>
                        </div>

                        @forelse ($recentOrders as $order)
                            <a href="{{ route('user.orders.show', $order->id) }}"
                               class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 transition last:border-b-0 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="flex min-w-0 items-center gap-4">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                        <i class="fa-solid fa-box"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-extrabold text-slate-900">Order #{{ $order->order_number }}</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $order->items_count }} {{ \Illuminate\Support\Str::plural('item', $order->items_count) }}
                                            <span class="mx-1.5 text-slate-300">|</span>
                                            {{ $order->formatPlacedAtDenver('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-4 sm:justify-end">
                                    <p class="font-black text-slate-900">${{ number_format($order->total, 2) }}</p>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ \App\Models\Order::statusBadgeClass($order->status) }}">
                                        {{ $order->status_label }}
                                    </span>
                                    <i class="fa-solid fa-chevron-right text-xs text-slate-300"></i>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-14 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-600">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </span>
                                <h3 class="mt-4 font-extrabold text-slate-900">No orders yet</h3>
                                <p class="mt-1 text-sm text-slate-500">Your latest purchases will appear here.</p>
                                <a href="{{ Route::has('store.index') ? route('store.index') : url('/') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700">
                                    Browse the store
                                </a>
                            </div>
                        @endforelse
                    </section>

                    <aside class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6">
                        <h2 class="text-lg font-black text-slate-950">Quick actions</h2>
                        <p class="mt-1 text-sm text-slate-500">Go directly to the things you use most.</p>

                        <div class="mt-5 space-y-3">
                            <a href="{{ Route::has('store.index') ? route('store.index') : url('/') }}" class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                    <i class="fa-solid fa-store"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-extrabold text-slate-900">Browse products</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">Shop available inventory</span>
                                </span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                            </a>

                            <a href="{{ route('user.orders.index') }}" class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                    <i class="fa-solid fa-box-open"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-extrabold text-slate-900">Track my orders</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">Check fulfillment updates</span>
                                </span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                            </a>

                            <a href="{{ route('user.data-destruction.index') }}" class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-extrabold text-slate-900">Data Destruction</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">View serialization and wiping progress</span>
                                </span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                            </a>

                            <a href="{{ route('user.it-assets.index') }}" class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                    <i class="fa-solid fa-laptop-code"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-extrabold text-slate-900">IT Assets</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">View equipment & device inventory</span>
                                </span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                            </a>
                        </div>
 
                        <div class="mt-6 border-t border-slate-100 pt-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Signed in as</p>
                            <p class="mt-2 truncate text-sm font-extrabold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="mt-1 truncate text-sm text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                    </aside>
                </div>
 
                <section class="dashboard-card mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Recent received intakes</h2>
                            <p class="mt-1 text-sm text-slate-500">The latest shipments received and their processing status</p>
                        </div>
                        <a href="{{ route('user.received-intake.index') }}" class="shrink-0 text-sm font-bold text-blue-600 hover:text-blue-700">
                            View all
                        </a>
                    </div>
 
                    @php
                        $intakeStatusClasses = [
                            'received' => 'bg-emerald-100 text-emerald-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-slate-100 text-slate-700',
                        ];
                    @endphp
 
                    @forelse ($recentIntakes as $intake)
                        <a href="{{ route('user.received-intake.show', $intake->id) }}"
                           class="grid gap-4 border-b border-slate-100 px-5 py-5 transition last:border-b-0 hover:bg-slate-50 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-center sm:px-6">
                            <div class="flex min-w-0 items-center gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                                    <i class="fa-solid fa-box"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-extrabold text-slate-900">Pallet #{{ $intake->barcode_number }}</p>
                                    <p class="mt-1 truncate text-sm text-slate-500">
                                        {{ $intake->description ?: 'No description' }}
                                        @if ($intake->put_away_location)
                                            <span class="mx-1.5 text-slate-300">|</span>
                                            {{ $intake->put_away_location }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $intakeStatusClasses[strtolower($intake->status)] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $intake->status ?: 'Received' }}
                            </span>
                            <div class="flex items-center gap-3 text-sm text-slate-500 sm:justify-end">
                                <span>{{ $intake->created_at->format('M d, Y') }}</span>
                                <i class="fa-solid fa-chevron-right text-xs text-slate-300"></i>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-xl text-violet-600">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </span>
                            <h3 class="mt-4 font-extrabold text-slate-900">No received intakes yet</h3>
                            <p class="mt-1 text-sm text-slate-500">Your shipment history will appear here once registered by admin.</p>
                        </div>
                    @endforelse
                </section>
            </div>
@endsection
