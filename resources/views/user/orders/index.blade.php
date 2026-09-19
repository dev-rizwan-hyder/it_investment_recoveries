@extends('user.layouts.app')

@section('title', 'My Orders')

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 10px 30px rgba(15, 23, 42, 0.04);
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    {{-- Header & Stat Cards --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Order Management</span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-0.5">My Orders</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Track order status, delivery progress, and invoices.</p>
            </div>
        </div>

        {{-- Metrics Summary Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Orders</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-box-archive text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">{{ $orders->total() }}</p>
            </div>

            @php
                $pendingCount = $orders->getCollection()->filter(fn($o) => in_array(strtolower($o->status), ['pending', 'processing', 'awaiting_payment']))->count();
                $completedCount = $orders->getCollection()->filter(fn($o) => in_array(strtolower($o->status), ['completed', 'delivered', 'shipped']))->count();
                $totalSum = $orders->getCollection()->sum('total');
            @endphp

            <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Processing</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-truck-fast text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-blue-600 mt-2">{{ $pendingCount }}</p>
            </div>

            <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Fulfilled</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-2">{{ $completedCount }}</p>
            </div>

            <div class="dashboard-card rounded-2xl border border-slate-200/80 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Spent</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                        <i class="fa-solid fa-receipt text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">${{ number_format($totalSum, 2) }}</p>
            </div>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-12 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-50 text-amber-500 mb-4">
                <i class="fa-solid fa-inbox text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">No Orders Found</h3>
            <p class="text-slate-500 font-medium max-w-md mx-auto text-sm">You have not placed any hardware or service orders yet.</p>
        </div>
    @else
        {{-- Table Container --}}
        <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white overflow-hidden">
            {{-- Filter Control Toolbar --}}
            <div class="border-b border-slate-100 bg-slate-50/50 p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Live Search --}}
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input id="orderSearch" type="text" placeholder="Search by order number..." 
                               class="w-full pl-11 pr-4 py-2.5 rounded-2xl border border-slate-200 bg-white font-medium text-slate-800 placeholder-slate-400 text-sm focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 outline-none transition">
                    </div>
                    
                    {{-- Status Filter --}}
                    <div>
                        <select id="statusFilter" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white font-semibold text-slate-700 text-sm focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 outline-none transition">
                            <option value="all">All Statuses</option>
                            @foreach(\App\Models\Order::statusChoices() as $status => $label)
                                <option value="{{ $status }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sorting --}}
                    <div>
                        <select id="dateSort" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white font-semibold text-slate-700 text-sm focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 outline-none transition">
                            <option value="newest">Sort: Newest First</option>
                            <option value="oldest">Sort: Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-900 text-white">
                        <tr class="text-[11px] font-extrabold uppercase tracking-wider">
                            <th class="text-left px-6 py-4">Order Number</th>
                            <th class="text-left px-6 py-4">Date Placed</th>
                            <th class="text-left px-6 py-4">Fulfillment</th>
                            <th class="text-left px-6 py-4">Items Count</th>
                            <th class="text-left px-6 py-4">Total Amount</th>
                            <th class="text-left px-6 py-4">Status</th>
                            <th class="text-right px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody" class="divide-y divide-slate-100 bg-white">
                        @foreach($orders as $order)
                            <tr class="hover:bg-amber-50/30 transition duration-150 order-row" 
                                data-status="{{ strtolower($order->status) }}" 
                                data-order-number="{{ strtolower($order->order_number) }}" 
                                data-date="{{ $order->created_at->timestamp }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-slate-900 text-sm">#{{ $order->order_number }}</span>
                                        <button onclick="copyToClipboard('{{ $order->order_number }}', 'Order number copied!')" class="text-slate-300 hover:text-amber-600 transition" title="Copy Order Number">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ $order->formatPlacedAtDenver('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-700 uppercase">
                                        <i class="fa-solid {{ ($order->fulfillment_type ?? 'shipping') === 'pickup' ? 'fa-store text-amber-500' : 'fa-truck text-blue-500' }} text-xs"></i>
                                        {{ $order->fulfillment_type ?? 'shipping' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-extrabold text-slate-800">
                                    {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-black text-emerald-600 text-sm">${{ number_format($order->total, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wide border shadow-2xs {{ \App\Models\Order::statusBadgeClass($order->status) }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('user.orders.show', $order->id) }}" 
                                       class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-amber-600 text-white px-3.5 py-2 rounded-xl text-xs font-extrabold transition uppercase tracking-wider shadow-xs">
                                        <span>Details</span>
                                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noFilteredResults" class="hidden">
                            <td colspan="7" class="px-6 py-12 text-center bg-slate-50/50">
                                <i class="fa-solid fa-magnifying-glass text-slate-300 text-2xl mb-2"></i>
                                <p class="text-slate-500 font-semibold text-sm">No orders match your search or filter criteria.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
const orderSearch = document.getElementById('orderSearch');
const statusFilter = document.getElementById('statusFilter');
const dateSort = document.getElementById('dateSort');
const tableBody = document.getElementById('ordersTableBody');
const noFilteredResults = document.getElementById('noFilteredResults');

function applyOrderFilters() {
    const query = (orderSearch.value || '').toLowerCase().trim();
    const status = statusFilter.value;
    const rows = Array.from(document.querySelectorAll('.order-row'));

    rows.forEach((row) => {
        const orderNo = row.dataset.orderNumber || '';
        const rowStatus = row.dataset.status || '';
        const matchesSearch = !query || orderNo.includes(query);
        const matchesStatus = status === 'all' || rowStatus === status;
        row.classList.toggle('hidden', !(matchesSearch && matchesStatus));
    });

    const visibleRows = rows.filter((row) => !row.classList.contains('hidden'));
    noFilteredResults?.classList.toggle('hidden', visibleRows.length > 0);
}

function applyDateSort() {
    const rows = Array.from(document.querySelectorAll('.order-row'));
    const sortType = dateSort.value;
    rows.sort((a, b) => {
        const aDate = Number(a.dataset.date || 0);
        const bDate = Number(b.dataset.date || 0);
        return sortType === 'oldest' ? aDate - bDate : bDate - aDate;
    });
    rows.forEach((row) => tableBody.appendChild(row));
}

orderSearch?.addEventListener('input', applyOrderFilters);
statusFilter?.addEventListener('change', applyOrderFilters);
dateSort?.addEventListener('change', () => {
    applyDateSort();
    applyOrderFilters();
});

applyDateSort();
applyOrderFilters();
</script>
@endpush
