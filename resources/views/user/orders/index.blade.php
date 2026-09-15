@extends('user.layouts.app')

@section('title', 'My Orders')

@section('content')
        <div class="max-w-8xl mx-auto px-8 py-10">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900">My Orders</h1>
                    <p class="text-gray-600 mt-2">Track order status from your dashboard.</p>
                </div>
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl px-5 py-4 shadow-lg">
                    <p class="text-xs uppercase tracking-widest font-bold text-blue-100">Total Orders</p>
                    <p class="text-2xl font-black">{{ $orders->total() }}</p>
                </div>
            </div>

            @if($orders->isEmpty())
                <div class="bg-white rounded-2xl shadow p-12 text-center border border-gray-100">
                    <i class="fas fa-inbox text-6xl text-gray-200 mb-4 block"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Orders Yet</h3>
                    <p class="text-gray-600 mb-6">You have not placed any orders yet.</p>
                    <a href="{{ Route::has('store.index') ? route('store.index') : url('/') }}" class="inline-block bg-[#0071e3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600 transition uppercase">
                        <i class="fas fa-shopping-bag mr-2"></i> Start Shopping
                    </a>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
                    <div class="p-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="orderSearch" type="text" placeholder="Search order number..." class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none text-sm">
                            </div>
                            <div>
                                <select id="statusFilter" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none text-sm bg-white">
                                    <option value="all">All Status</option>
                                    @foreach(\App\Models\Order::statusChoices() as $status => $label)
                                        <option value="{{ $status }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select id="dateSort" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none text-sm bg-white">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-900 text-white">
                                <tr>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider">Order #</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider">Date</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider">Items</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider">Total</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider">Status</th>
                                    <th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody" class="divide-y divide-slate-100">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-blue-50/50 transition order-row" data-status="{{ strtolower($order->status) }}" data-order-number="{{ strtolower($order->order_number) }}" data-date="{{ $order->created_at->timestamp }}">
                                        <td class="px-5 py-4">
                                            <p class="font-black text-slate-900">{{ $order->order_number }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-slate-700 font-semibold">{{ $order->formatPlacedAtDenver('M d, Y') }}</td>
                                        <td class="px-5 py-4 text-slate-700 font-semibold">{{ $order->items->count() }}</td>
                                        <td class="px-5 py-4 font-black text-[#0071e3]">${{ number_format($order->total, 2) }}</td>
                                        <td class="px-5 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wide {{ \App\Models\Order::statusBadgeClass($order->status) }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('user.orders.show', $order->id) }}" class="inline-flex items-center gap-2 bg-[#0071e3] text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-600 transition text-xs uppercase">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="noFilteredResults" class="hidden">
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-500 font-semibold">
                                        No orders found for selected filters.
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
