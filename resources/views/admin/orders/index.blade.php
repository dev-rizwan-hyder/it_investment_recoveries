@extends('layouts.admin')

@section('content')
    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Orders</h1>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Action Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                {{-- Export Button --}}
                <a href="{{ route('admin.orders.export', request()->all()) }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                    <i class="fas fa-file-export mr-2 text-slate-400"></i> Export CSV
                </a>

                @php
                    $orderStatuses = \App\Models\Order::statusChoices();
                @endphp

                <select id="bulkStatusSelect"
                    class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-[12px] font-bold text-slate-600 outline-none min-w-[180px]">
                    <option value="">Select New Status</option>
                    @foreach ($orderStatuses as $status => $label)
                        <option value="{{ $status }}">{{ $label }}</option>
                    @endforeach
                </select>

                <button id="bulkUpdateStatusBtn" type="button" disabled
                    class="bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center border border-blue-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-50 disabled:hover:text-blue-600">
                    <i class="fas fa-edit mr-2"></i> Update Selected
                </button>

                <button id="bulkDeleteBtn" type="button" disabled
                    class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center border border-red-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-50 disabled:hover:text-red-600">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Delete Selected (<span id="selectedOrderCount">0</span>)
                </button>

                <button
                    class="bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center ml-auto shadow-lg shadow-blue-100">
                    <i class="fas fa-sync-alt mr-2"></i> Sync to Shopify
                </button>
            </div>

            {{-- Filters --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-50">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Search orders..."
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/10 outline-none">
                </div>

                <select id="statusFilter"
                    class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[180px]">
                    <option value="">All Statuses</option>
                    @foreach ($orderStatuses as $status => $label)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>

                <button onclick="window.location.href='{{ route('admin.orders.index') }}'"
                    class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200">
                    <i class="fas fa-redo-alt"></i>
                </button>
            </div>
        </div>

        <form id="bulkStatusForm" action="{{ route('admin.orders.bulk-status') }}" method="POST" class="hidden">
            @csrf
            @method('PUT')
            <input type="hidden" id="bulkStatusValue" name="status">
        </form>

        <form id="bulkDeleteForm" action="{{ route('admin.orders.bulk-delete') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto cursor-grab select-none global-drag-scroll">
                <table class="w-full text-left border-collapse table-auto">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-[11px] uppercase font-bold text-slate-600 tracking-wider">
                            <th class="px-4 py-4 text-center w-14">
                                <input id="selectAllOrders" type="checkbox"
                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    aria-label="Select all orders on this page"
                                    title="Select all orders on this page"
                                    {{ $orders->isEmpty() ? 'disabled' : '' }}>
                            </th>
                            <th class="px-6 py-4 text-center w-16">Order #</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4 text-center">Quantity</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Order Status</th>
                            <th class="px-6 py-4 text-center">Payment Status</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $order->id }}"
                                        class="order-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        aria-label="Select order {{ $order->order_number }}">
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-600">
                                    #{{ $order->order_number }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900 text-sm">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->customer_phone ?? 'N/A' }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-700">{{ $order->customer_email }}</div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-xs">
                                        {{ $order->items->sum('quantity') }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-emerald-600">${{ number_format($order->total, 2) }}</div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <select name="status"
                                            class="min-w-[140px] px-3 py-2 rounded-lg text-xs font-bold uppercase border outline-none transition-colors {{ \App\Models\Order::statusSelectClass($order->status) }}"
                                            onchange="this.form.submit()">
                                            @foreach ($orderStatuses as $status => $label)
                                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide {{ \App\Models\Order::paymentStatusBadgeClass($order->payment_status) }}">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $order->formatPlacedAtDenver('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3 items-center">
                                        <button type="button" title="Receipt"
                                            onclick="openReceiptModal('{{ route('admin.orders.receipt.preview', $order->id) }}', '{{ route('admin.orders.receipt', $order->id) }}')"
                                            class="text-emerald-600 hover:text-emerald-800 font-semibold">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" title="View"
                                            class="text-blue-600 hover:text-blue-800 font-semibold">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" title="Delete" class="text-red-600 hover:text-red-800 font-semibold"
                                                onclick="if (confirm('Are you sure you want to delete this order?')) this.closest('form').submit();">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-16 text-center text-slate-500">
                                    <i class="fas fa-inbox text-4xl mb-4 text-slate-300 block"></i>
                                    No orders found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    {{-- Receipt Preview Modal --}}
    <div id="receiptModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[92vh] overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-slate-50">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Receipt Preview</h3>
                    <p class="text-xs text-slate-500">Professional order receipt</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <button id="receiptPrintButton" type="button" onclick="printReceiptPreview()"
                        class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <a id="receiptOpenInNewTab" href="#" target="_blank" class="px-4 py-2 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-sm font-bold border border-slate-200">
                        <i class="fas fa-external-link-alt mr-2"></i> Preview
                    </a>
                    <a id="receiptPdfLink" href="#" target="_blank" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                    <button type="button" onclick="closeReceiptModal()" class="h-9 w-9 rounded-full hover:bg-slate-100 text-slate-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="relative flex-1 bg-slate-100">
                <div id="receiptLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-slate-100">
                    <div class="rounded-xl border border-slate-200 bg-white px-6 py-5 shadow-sm text-center">
                        <div class="mx-auto mb-3 h-10 w-10 rounded-full border-4 border-slate-200 border-t-emerald-600 animate-spin"></div>
                        <div class="text-sm font-black text-slate-800">Loading receipt</div>
                        <div class="text-xs text-slate-500 mt-1">Preparing preview...</div>
                    </div>
                </div>
                <iframe id="receiptIframe" src="about:blank" class="w-full h-full border-0 bg-white"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openReceiptModal(previewUrl, pdfUrl) {
            const modal = document.getElementById('receiptModal');
            const iframe = document.getElementById('receiptIframe');
            const openTab = document.getElementById('receiptOpenInNewTab');
            const pdfLink = document.getElementById('receiptPdfLink');
            const loading = document.getElementById('receiptLoading');
            const printButton = document.getElementById('receiptPrintButton');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            printButton.disabled = true;
            openTab.href = previewUrl;
            pdfLink.href = pdfUrl || previewUrl;

            iframe.dataset.loadingReceipt = '0';
            iframe.src = 'about:blank';
            requestAnimationFrame(() => {
                iframe.dataset.loadingReceipt = '1';
                iframe.src = previewUrl;
            });

            document.body.style.overflow = 'hidden';
        }

        function closeReceiptModal() {
            const modal = document.getElementById('receiptModal');
            const iframe = document.getElementById('receiptIframe');
            const loading = document.getElementById('receiptLoading');
            const printButton = document.getElementById('receiptPrintButton');

            iframe.dataset.loadingReceipt = '0';
            iframe.src = 'about:blank';
            loading.classList.add('hidden');
            printButton.disabled = true;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function printReceiptPreview() {
            const iframe = document.getElementById('receiptIframe');

            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const selectAllOrders = document.getElementById('selectAllOrders');
            const orderCheckboxes = Array.from(document.querySelectorAll('.order-checkbox'));
            const bulkStatusSelect = document.getElementById('bulkStatusSelect');
            const bulkUpdateStatusBtn = document.getElementById('bulkUpdateStatusBtn');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedOrderCount = document.getElementById('selectedOrderCount');
            const bulkStatusForm = document.getElementById('bulkStatusForm');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const bulkStatusValue = document.getElementById('bulkStatusValue');
            const receiptIframe = document.getElementById('receiptIframe');
            const receiptLoading = document.getElementById('receiptLoading');
            const receiptPrintButton = document.getElementById('receiptPrintButton');

            receiptIframe.addEventListener('load', function() {
                if (receiptIframe.dataset.loadingReceipt === '1') {
                    receiptLoading.classList.add('hidden');
                    receiptPrintButton.disabled = false;
                    receiptIframe.dataset.loadingReceipt = '0';
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeReceiptModal();
                }
            });

            function applyFilters() {
                const search = searchInput.value;
                const status = statusFilter.value;
                let url = new URL(window.location.href);

                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');

                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');

                url.searchParams.delete('page');
                window.location.href = url.href;
            }

            // Enter key press to search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applyFilters();
            });

            // Dropdown change to filter
            statusFilter.addEventListener('change', applyFilters);

            function getSelectedOrderIds() {
                return orderCheckboxes
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);
            }

            function updateBulkActionState() {
                const selectedCount = getSelectedOrderIds().length;
                const allSelected = orderCheckboxes.length > 0 && selectedCount === orderCheckboxes.length;

                selectedOrderCount.textContent = selectedCount;
                bulkDeleteBtn.disabled = selectedCount === 0;
                bulkUpdateStatusBtn.disabled = selectedCount === 0 || !bulkStatusSelect.value;

                selectAllOrders.checked = allSelected;
                selectAllOrders.indeterminate = selectedCount > 0 && !allSelected;
            }

            function appendSelectedOrderIds(form, ids) {
                form.querySelectorAll('[data-bulk-order-id]').forEach(input => input.remove());

                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    input.dataset.bulkOrderId = 'true';
                    form.appendChild(input);
                });
            }

            selectAllOrders.addEventListener('change', function() {
                orderCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllOrders.checked;
                });
                updateBulkActionState();
            });

            orderCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActionState);
            });

            bulkStatusSelect.addEventListener('change', updateBulkActionState);

            bulkUpdateStatusBtn.addEventListener('click', function() {
                const selectedIds = getSelectedOrderIds();
                const selectedStatusLabel = bulkStatusSelect.options[bulkStatusSelect.selectedIndex]?.text;

                if (selectedIds.length === 0 || !bulkStatusSelect.value) return;
                if (!confirm(`Update ${selectedIds.length} selected order(s) to "${selectedStatusLabel}"?`)) return;

                appendSelectedOrderIds(bulkStatusForm, selectedIds);
                bulkStatusValue.value = bulkStatusSelect.value;
                bulkUpdateStatusBtn.disabled = true;
                bulkStatusForm.submit();
            });

            bulkDeleteBtn.addEventListener('click', function() {
                const selectedIds = getSelectedOrderIds();

                if (selectedIds.length === 0) return;
                if (!confirm(`Delete ${selectedIds.length} selected order(s)? This action cannot be undone.`)) return;

                appendSelectedOrderIds(bulkDeleteForm, selectedIds);
                bulkDeleteBtn.disabled = true;
                bulkDeleteForm.submit();
            });

            updateBulkActionState();
        });
    </script>
@endsection
