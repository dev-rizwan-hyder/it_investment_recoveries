@extends('user.layouts.app')

@section('title', 'Paid / Unpaid Invoices')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-6 sm:py-8 font-sans">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                            Billing & Payments
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">Paid / Unpaid Invoices</h1>
                    <p class="text-xs text-slate-500 mt-0.5">View your invoices, check due dates, print receipts, and pay outstanding balances via Square.</p>
                </div>
            </div>
        </div>

        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm font-bold text-blue-700 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if (session('warning'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-bold text-amber-800 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Summary Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Invoices --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">Total Invoices</span>
                    <span class="h-9 w-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-slate-900">{{ $stats['total_count'] ?? 0 }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Billed ${{ number_format($stats['total_amount'] ?? 0, 2) }}</p>
                </div>
            </div>

            {{-- Paid Invoices --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-600">Paid Invoices</span>
                    <span class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-circle-check"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-emerald-600">{{ $stats['paid_count'] ?? 0 }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Paid ${{ number_format($stats['paid_amount'] ?? 0, 2) }}</p>
                </div>
            </div>

            {{-- Unpaid Invoices --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-amber-600">Unpaid Invoices</span>
                    <span class="h-9 w-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-clock"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-amber-600">{{ $stats['unpaid_count'] ?? 0 }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Outstanding ${{ number_format($stats['unpaid_amount'] ?? 0, 2) }}</p>
                </div>
            </div>

            {{-- Square Payment Gateway Status --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">Payment Gateway</span>
                    <span class="h-9 w-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-bold">
                        <i class="fab fa-square"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <p class="text-sm font-black text-slate-900">Square Checkout</p>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mt-1">Accepts Card, Apple Pay & Google Pay</p>
                </div>
            </div>
        </div>

        {{-- Filters & Search Section --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form id="userInvoiceFilters" class="flex flex-wrap items-center gap-3">
                {{-- Date filter --}}
                <label class="relative min-w-[170px] flex-1 sm:flex-none">
                    <span class="sr-only">Date</span>
                    <i class="fas fa-calendar-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input id="filter-date" type="date"
                        class="w-full h-11 rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-3 text-xs font-bold text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                </label>

                {{-- Status filter --}}
                <label class="relative min-w-[150px] flex-1 sm:flex-none">
                    <span class="sr-only">Status</span>
                    <select id="filter-status"
                        class="w-full h-11 appearance-none rounded-xl border border-slate-200 bg-slate-50/50 pl-3.5 pr-8 text-xs font-bold text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                        <option value="">All Statuses</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none"></i>
                </label>

                {{-- Payment Method filter --}}
                <label class="relative min-w-[180px] flex-1 sm:flex-none">
                    <span class="sr-only">Payment Method</span>
                    <select id="filter-method"
                        class="w-full h-11 appearance-none rounded-xl border border-slate-200 bg-slate-50/50 pl-3.5 pr-8 text-xs font-bold text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                        <option value="">All Payment Methods</option>
                        <option value="square_card">Square Card</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none"></i>
                </label>

                {{-- Search text --}}
                <div class="relative flex-1 min-w-[220px]">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input id="filter-search" type="text" placeholder="Search by Invoice #, description, category..."
                        class="w-full h-11 rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-3 text-xs font-bold text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400">
                </div>

                {{-- Reset Button --}}
                <button type="button" id="filter-reset"
                    class="h-11 px-4 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    <i class="fas fa-rotate-left mr-1.5 text-[11px]"></i> Reset
                </button>
            </form>
        </div>

        {{-- Invoices Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] uppercase font-black text-slate-400 tracking-[0.15em]">
                            <th class="px-5 py-4">Invoice #</th>
                            <th class="px-5 py-4">Category</th>
                            <th class="px-5 py-4">Date of Service</th>
                            <th class="px-5 py-4">Due Date</th>
                            <th class="px-5 py-4">Method</th>
                            <th class="px-5 py-4 text-right">Total</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoiceRows as $invoice)
                            @php
                                $searchable = strtolower(implode(' ', [
                                    $invoice['num'],
                                    $invoice['client'],
                                    $invoice['email'] ?? '',
                                    $invoice['company'] ?? '',
                                    $invoice['address'] ?? '',
                                    $invoice['category_label'] ?? '',
                                    $invoice['status'],
                                    $invoice['val'],
                                    implode(' ', $invoice['categories'] ?? []),
                                ]));
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors group"
                                data-invoice-row
                                data-invoice-date="{{ $invoice['service_date_value'] }}"
                                data-payment-method="{{ $invoice['payment_method'] }}"
                                data-invoice-status="{{ strtolower($invoice['status']) }}"
                                data-invoice-search="{{ $searchable }}">

                                {{-- Invoice # --}}
                                <td class="px-5 py-4 align-middle">
                                    <a href="{{ $invoice['view_url'] }}" class="text-sm font-black text-slate-900 hover:text-blue-600 transition flex items-center gap-1.5">
                                        <span>{{ $invoice['num'] }}</span>
                                    </a>
                                </td>

                                {{-- Category --}}
                                <td class="px-5 py-4 align-middle">
                                    <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700 border border-blue-200/60">
                                        {{ $invoice['category_label'] }}
                                    </span>
                                </td>

                                {{-- Date of Service --}}
                                <td class="px-5 py-4 align-middle text-xs font-bold text-slate-700">
                                    {{ $invoice['service_date'] ?: 'N/A' }}
                                </td>

                                {{-- Due Date --}}
                                <td class="px-5 py-4 align-middle text-xs font-bold text-slate-700">
                                    {{ $invoice['due_date'] ?: ($invoice['service_date'] ?: 'N/A') }}
                                </td>

                                {{-- Payment Method --}}
                                <td class="px-5 py-4 align-middle">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-slate-600">
                                        {{ $invoice['payment_method_label'] }}
                                    </span>
                                </td>

                                {{-- Total --}}
                                <td class="px-5 py-4 text-right align-middle text-sm font-black text-slate-900">
                                    {{ $invoice['val'] }}
                                </td>

                                {{-- Status (Strictly Paid or Unpaid) --}}
                                <td class="px-5 py-4 text-center align-middle">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $invoice['status_class'] }}">
                                        {{ $invoice['status'] }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 text-right align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Pay via Square Option for Unpaid Invoices --}}
                                        @if(!$invoice['is_paid'])
                                            <form action="{{ $invoice['pay_url'] }}" method="POST" class="inline-flex">
                                                @csrf
                                                <button type="submit" title="Pay via Square"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs transition active:scale-95">
                                                    <i class="fab fa-square"></i>
                                                    <span>Pay via Square</span>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- View Details --}}
                                        <a href="{{ $invoice['view_url'] }}" title="View Invoice Details"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-600 transition text-xs shadow-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Print Receipt (Modal Preview) --}}
                                        <button type="button" title="Print Receipt"
                                            onclick="openUserInvoiceReceiptModal('{{ $invoice['print_url'] }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 transition text-xs shadow-xs">
                                            <i class="fas fa-print"></i>
                                        </button>

                                        {{-- Sync Square Status if linked --}}
                                        @if($invoice['has_square'])
                                            <form action="{{ $invoice['sync_url'] }}" method="POST" class="inline-flex">
                                                @csrf
                                                <button type="submit" title="Sync Square Payment Status"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 transition text-xs shadow-xs">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state-row">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-3">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">No invoices found</p>
                                    <p class="text-xs text-slate-400 mt-1">There are no invoices linked to your account at this time.</p>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="no-filter-match-row" class="hidden">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <p class="text-sm font-bold text-slate-600">No invoices match the chosen filters.</p>
                                <p class="text-xs text-slate-400 mt-1">Try clearing or adjusting your search keywords or filter criteria.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Receipt Print/Preview Modal --}}
<div id="userInvoiceReceiptModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 p-4 backdrop-blur-xs flex items-center justify-center">
    <div class="relative flex h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-receipt text-slate-600"></i>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Invoice Receipt Preview</h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="modalPrintBtn" onclick="printUserInvoiceReceiptPreview()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-black text-white hover:bg-slate-800 transition">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <button type="button" onclick="closeUserInvoiceReceiptModal()"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
        <div class="relative flex-1 bg-slate-100">
            <div id="modalReceiptLoading" class="absolute inset-0 flex items-center justify-center bg-white/80">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Loading receipt preview...</span>
                </div>
            </div>
            <iframe id="userInvoiceReceiptIframe" class="h-full w-full border-0" src="about:blank"></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openUserInvoiceReceiptModal(url) {
        const modal = document.getElementById('userInvoiceReceiptModal');
        const iframe = document.getElementById('userInvoiceReceiptIframe');
        const loading = document.getElementById('modalReceiptLoading');
        const printBtn = document.getElementById('modalPrintBtn');

        if (!modal || !iframe) return;

        loading.classList.remove('hidden');
        if (printBtn) printBtn.disabled = true;
        iframe.dataset.loadingReceipt = '1';
        iframe.src = url;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeUserInvoiceReceiptModal() {
        const modal = document.getElementById('userInvoiceReceiptModal');
        const iframe = document.getElementById('userInvoiceReceiptIframe');

        if (!modal) return;
        if (iframe) iframe.src = 'about:blank';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function printUserInvoiceReceiptPreview() {
        const iframe = document.getElementById('userInvoiceReceiptIframe');
        if (iframe?.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const filterDate = document.getElementById('filter-date');
        const filterStatus = document.getElementById('filter-status');
        const filterMethod = document.getElementById('filter-method');
        const filterSearch = document.getElementById('filter-search');
        const resetBtn = document.getElementById('filter-reset');
        const rows = Array.from(document.querySelectorAll('[data-invoice-row]'));
        const noMatchRow = document.getElementById('no-filter-match-row');
        const receiptIframe = document.getElementById('userInvoiceReceiptIframe');
        const receiptLoading = document.getElementById('modalReceiptLoading');
        const receiptPrintBtn = document.getElementById('modalPrintBtn');

        const applyFilters = () => {
            const dateVal = filterDate?.value || '';
            const statusVal = (filterStatus?.value || '').trim().toLowerCase();
            const methodVal = (filterMethod?.value || '').trim().toLowerCase();
            const searchVal = (filterSearch?.value || '').trim().toLowerCase();
            const searchTerms = searchVal.split(/\s+/).filter(Boolean);

            let visibleCount = 0;

            rows.forEach((row) => {
                const rowDate = row.dataset.invoiceDate || '';
                const rowStatus = row.dataset.invoiceStatus || '';
                const rowMethod = row.dataset.paymentMethod || '';
                const rowSearch = row.dataset.invoiceSearch || '';

                const matchesDate = !dateVal || rowDate === dateVal;
                const matchesStatus = !statusVal || rowStatus === statusVal;
                const matchesMethod = !methodVal || rowMethod === methodVal;
                const matchesSearch = searchTerms.length === 0 || searchTerms.every(t => rowSearch.includes(t));

                const isVisible = matchesDate && matchesStatus && matchesMethod && matchesSearch;
                row.classList.toggle('hidden', !isVisible);

                if (isVisible) {
                    visibleCount++;
                }
            });

            if (noMatchRow) {
                noMatchRow.classList.toggle('hidden', rows.length === 0 || visibleCount > 0);
            }
        };

        [filterDate, filterStatus, filterMethod, filterSearch].forEach(el => {
            el?.addEventListener('input', applyFilters);
            el?.addEventListener('change', applyFilters);
        });

        resetBtn?.addEventListener('click', () => {
            if (filterDate) filterDate.value = '';
            if (filterStatus) filterStatus.value = '';
            if (filterMethod) filterMethod.value = '';
            if (filterSearch) filterSearch.value = '';
            applyFilters();
        });

        receiptIframe?.addEventListener('load', () => {
            if (receiptIframe.dataset.loadingReceipt === '1') {
                receiptLoading?.classList.add('hidden');
                if (receiptPrintBtn) receiptPrintBtn.disabled = false;
                receiptIframe.dataset.loadingReceipt = '0';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeUserInvoiceReceiptModal();
            }
        });
    });
</script>
@endpush
@endsection
