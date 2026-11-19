@extends('layouts.admin')

@section('content')
@php
    $invoiceCategories = collect($invoiceCategories ?? [])->filter()->values();
@endphp
<div class="w-full animate-in fade-in duration-500">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#1e293b] tracking-tight">All Invoices</h1>
        </div>
        <div class="flex items-center gap-3">
            <button id="bulkDeleteInvoicesBtn" type="button" disabled
                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-5 py-3 rounded-xl font-bold text-[14px] flex items-center border border-red-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-50 disabled:hover:text-red-600 shadow-sm">
                <i class="fas fa-trash-alt mr-2"></i> Delete Selected (<span id="selectedInvoiceCount">0</span>)
            </button>
            <a href="{{ route('admin.sales.invoices.create') }}" class="bg-[#2563eb] hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold text-[15px] flex items-center shadow-lg shadow-blue-500/20 transition-all">
                <i class="fas fa-plus mr-2 text-sm"></i> Create Invoice
            </a>
        </div>
    </div>

    <form id="bulkDeleteForm" action="{{ route('admin.sales.invoices.bulk-delete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-bold text-amber-800">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
            <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-[#f1f5f9] mb-8">
        <form id="invoiceFilters" class="flex flex-wrap items-center gap-3">
            <label class="relative min-w-[180px] flex-1 sm:flex-none">
                <span class="sr-only">Invoice date</span>
                <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
                <input id="invoice-date-filter" type="date"
                    class="w-full h-12 rounded-2xl border border-slate-100 bg-[#f8fafc] pl-11 pr-4 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
            </label>

            <label class="relative min-w-[210px] flex-1 sm:flex-none">
                <span class="sr-only">Payment method</span>
                <select id="invoice-method-filter"
                    class="w-full h-12 appearance-none rounded-2xl border border-slate-100 bg-[#f8fafc] pl-4 pr-10 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                    <option value="">All Payment Methods</option>
                    <option value="cash">Cash</option>
                    <option value="square_card">Square Card</option>
                    <option value="cheque">Cheque</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
            </label>

            <label class="relative min-w-[150px] flex-1 sm:flex-none">
                <span class="sr-only">Invoice status</span>
                <select id="invoice-status-filter"
                    class="w-full h-12 appearance-none rounded-2xl border border-slate-100 bg-[#f8fafc] pl-4 pr-10 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                    <option value="">All Statuses</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
            </label>

            <label class="relative min-w-[180px] flex-1 sm:flex-none">
                <span class="sr-only">Payment type</span>
                <select id="invoice-payment-filter"
                    class="w-full h-12 appearance-none rounded-2xl border border-slate-100 bg-[#f8fafc] pl-4 pr-10 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                    <option value="">All Payment</option>
                    <option value="in_store">In store purchased</option>
                    <option value="online">Online Purchased</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
            </label>


            <label class="relative min-w-[210px] flex-1 sm:flex-none">
                <span class="sr-only">Invoice category</span>
                <select id="invoice-category-filter"
                    class="w-full h-12 appearance-none rounded-2xl border border-slate-100 bg-[#f8fafc] pl-4 pr-10 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                    <option value="">All Categories</option>
                    @foreach ($invoiceCategories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
            </label>

            <div class="relative min-w-[260px] flex-[2_1_320px]">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                <input id="invoice-search-filter" type="search" placeholder="Filter by invoice, customer, email..."
                    class="w-full h-12 rounded-2xl border border-slate-100 bg-white pl-12 pr-5 text-sm font-semibold text-[#475569] placeholder-[#94a3b8] outline-none transition focus:border-blue-200 focus:ring-4 focus:ring-blue-500/10">
            </div>

            <button id="invoice-clear-filters" type="button"
                class="h-12 rounded-2xl bg-slate-100 px-5 text-sm font-black text-slate-500 transition hover:bg-slate-200 hover:text-slate-700">
                Clear
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-[#f1f5f9] overflow-hidden">
        <div class="overflow-x-auto cursor-grab select-none global-drag-scroll">
            <table class="min-w-[1300px] w-full table-fixed text-left border-collapse">
                <colgroup>
                    <col class="w-[50px]">
                    <col class="w-[145px]">
                    <col class="w-[85px]">
                    <col class="w-[170px]">
                    <col class="w-[210px]">
                    <col class="w-[135px]">
                    <col class="w-[160px]">
                    <col class="w-[220px]">
                    <col class="w-[145px]">
                    <col class="w-[145px]">
                    <col class="w-[125px]">
                    <col class="w-[115px]">
                    <col class="w-[105px]">
                    <col class="w-[220px]">
                </colgroup>
                <thead>
                    <tr class="bg-[#f8fafc] border-b border-[#f1f5f9] text-[11px] uppercase font-black text-[#94a3b8] tracking-[0.18em]">
                        <th class="px-4 py-5 text-center">
                            <input id="selectAllInvoices" type="checkbox"
                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                aria-label="Select all invoices on this page"
                                title="Select all invoices">
                        </th>
                        <th class="px-5 py-5">Invoice #</th>
                        <th class="px-4 py-5 text-center">Media</th>
                        <th class="px-5 py-5">Customer</th>
                        <th class="px-5 py-5">Email</th>
                        <th class="px-5 py-5">Phone</th>
                        <th class="px-5 py-5">Company</th>
                        <th class="px-5 py-5">Address</th>
                        <th class="px-5 py-5">Method</th>
                        <th class="px-5 py-5">Payment</th>
                        <th class="px-5 py-5 text-right">Total</th>
                        <th class="px-5 py-5 text-center">Status</th>
                        <th class="px-5 py-5 text-center">Date</th>
                        <th class="px-5 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                 <tbody class="divide-y divide-[#f8fafc]">
                    @php $invoiceRows = $invoiceRows ?? collect(); @endphp

                    @foreach($invoiceRows as $invoice)
                    @php
                        $invoiceSearch = strtolower(implode(' ', [
                            $invoice['num'],
                            $invoice['client'],
                            $invoice['email'],
                            $invoice['phone'],
                            $invoice['company'],
                            $invoice['address'],
                            $invoice['customer_address'],
                            $invoice['val'],
                            $invoice['status'],
                            $invoice['payment_method_label'],
                            $invoice['payment_type_label'],
                            $invoice['type_label'],
                            $invoice['category_label'],
                            implode(' ', $invoice['categories'] ?? []),
                        ]));
                        $invoiceCategoriesValue = strtolower(implode('|', $invoice['categories'] ?? []));
                    @endphp
                    <tr class="hover:bg-[#f8fafc]/80 transition-colors group"
                        data-invoice-row
                        data-row-key="{{ $invoice['row_key'] ?? $invoice['id'] }}"
                        data-invoice-date="{{ $invoice['date_value'] }}"
                        data-payment-method="{{ $invoice['payment_method'] }}"
                        data-payment-type="{{ $invoice['payment_type'] }}"
                        data-invoice-type="{{ $invoice['type'] }}"
                        data-invoice-categories="{{ $invoiceCategoriesValue }}"
                        data-invoice-status="{{ strtolower($invoice['status']) }}"
                        data-invoice-search="{{ $invoiceSearch }}">
                        <td class="px-4 py-5 text-center align-middle">
                            <input type="checkbox" value="{{ $invoice['row_key'] ?? $invoice['id'] }}"
                                class="invoice-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                aria-label="Select invoice {{ $invoice['num'] }}">
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <p class="truncate text-[14px] font-black uppercase tracking-tight text-slate-700">{{ $invoice['num'] }}</p>
                            <p class="mt-1 truncate text-[10px] font-black uppercase tracking-wide text-blue-500">{{ $invoice['category_label'] ?? $invoice['type_label'] }}</p>
                        </td>
                        <td class="px-4 py-4 text-center align-middle">
                            @if (!empty($invoice['pictures']))
                                @php
                                    $galleryJson = json_encode($invoice['pictures']);
                                @endphp
                                @if ($invoice['first_is_image'])
                                    <div class="relative inline-block cursor-pointer group/thumb"
                                        onclick="openImageGallery({{ $galleryJson }}, 'Invoice #{{ $invoice['num'] }}')"
                                        title="Click to view media">
                                        <img src="{{ $invoice['first_picture'] }}"
                                            class="h-10 w-10 rounded-xl object-cover border border-slate-200 mx-auto transition-transform group-hover/thumb:scale-105 group-hover/thumb:ring-2 group-hover/thumb:ring-blue-500 shadow-sm"
                                            onerror="this.src='/images/placeholder.png'">
                                        @if ($invoice['picture_count'] > 1)
                                            <span class="absolute -top-1.5 -right-1.5 bg-blue-600 text-white text-[9px] font-black rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center shadow">
                                                +{{ $invoice['picture_count'] - 1 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="relative inline-flex flex-col items-center justify-center cursor-pointer group/thumb h-10 w-10 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 mx-auto transition-transform group-hover/thumb:scale-105 group-hover/thumb:ring-2 group-hover/thumb:ring-blue-500 shadow-sm"
                                        onclick="openImageGallery({{ $galleryJson }}, 'Invoice #{{ $invoice['num'] }}')"
                                        title="{{ $invoice['first_file_name'] ?: 'View attachment' }}">
                                        <i class="fas {{ $invoice['first_icon'] ?: 'fa-file text-slate-500' }} text-sm"></i>
                                        @if ($invoice['picture_count'] > 1)
                                            <span class="absolute -top-1.5 -right-1.5 bg-blue-600 text-white text-[9px] font-black rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center shadow">
                                                +{{ $invoice['picture_count'] - 1 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <span class="text-xs font-semibold text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <p class="truncate text-sm font-black text-[#1e293b]">{{ $invoice['client'] ?: 'No customer' }}</p>
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <p class="truncate text-sm font-semibold text-slate-500">{{ $invoice['email'] ?: 'No email' }}</p>
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <p class="truncate text-sm font-semibold text-slate-500">{{ $invoice['phone'] ?: 'No phone' }}</p>
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <p class="truncate text-sm font-semibold text-slate-600">{{ $invoice['company'] ?: 'No company' }}</p>
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <p class="truncate text-sm font-semibold text-slate-500">{{ $invoice['customer_address'] ?: 'No address' }}</p>
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <span class="inline-flex max-w-full rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-slate-500">
                                <span class="truncate">{{ $invoice['payment_method_label'] }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 align-middle">
                            <span class="inline-flex max-w-full rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-indigo-600">
                                <span class="truncate">{{ $invoice['payment_type_label'] }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 text-right align-middle text-[15px] font-black tracking-tight text-emerald-600">{{ $invoice['val'] }}</td>
                        <td class="px-5 py-5 text-center align-middle">
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $invoice['status_class'] ?? ($invoice['status'] == 'Paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600') }} shadow-sm">
                                    {{ $invoice['status'] }}
                                </span>
                                @if(!empty($invoice['square_status']))
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wide {{ $invoice['square_badge_class'] ?? 'bg-slate-100 text-slate-600' }}">
                                        <i class="fas fa-credit-card text-[8px]"></i> {{ strtoupper($invoice['square_status']) }}
                                    </span>
                                @endif
                                @if(!empty($invoice['square_reminder_count']) && $invoice['square_reminder_count'] > 0)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-600" title="Last reminded: {{ $invoice['square_reminded_at'] ?? 'Recently' }}">
                                        <i class="fas fa-bell text-[8px]"></i> Reminded ({{ $invoice['square_reminder_count'] }})
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-5 text-center align-middle">
                            <p class="text-[14px] font-black text-slate-700">{{ $invoice['date'] }}</p>
                            @if(!empty($invoice['due_date']))
                                <p class="text-[11px] font-bold text-slate-400">Due: {{ $invoice['due_date'] }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-5 text-right align-middle">
                            <div class="flex flex-row items-center justify-end gap-2">
                                @if(($invoice['type'] ?? '') === 'service' && empty($invoice['is_paid']))
                                    @php
                                        $remindMsgB64 = base64_encode($invoice['default_reminder_message'] ?? '');
                                    @endphp
                                    <button type="button" title="Send Square Payment Reminder"
                                        data-square-remind-btn
                                        onclick="triggerSquareRemind(this)"
                                        data-url="{{ $invoice['remind_square_url'] }}"
                                        data-invoice-number="{{ $invoice['num'] }}"
                                        data-customer-name="{{ $invoice['client'] }}"
                                        data-customer-email="{{ $invoice['email'] }}"
                                        data-amount="{{ $invoice['val'] }}"
                                        data-due-date="{{ $invoice['due_date'] ?? 'Upon receipt' }}"
                                        data-has-square="{{ !empty($invoice['has_square']) ? '1' : '0' }}"
                                        data-square-url="{{ $invoice['square_public_url'] ?? '' }}"
                                        data-reminder-count="{{ (int) ($invoice['square_reminder_count'] ?? 0) }}"
                                        data-last-reminded="{{ $invoice['square_reminded_at'] ?? '' }}"
                                        data-message-b64="{{ $remindMsgB64 }}"
                                        class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition-all hover:bg-amber-600 hover:text-white active:scale-90">
                                        <i class="fas fa-bell text-xs"></i>
                                        @if(!empty($invoice['square_reminder_count']) && $invoice['square_reminder_count'] > 0)
                                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[8px] font-black text-white">
                                                {{ $invoice['square_reminder_count'] }}
                                            </span>
                                        @endif
                                    </button>
                                @endif
                                @if(!empty($invoice['send_square_url']) && empty($invoice['has_square']) && ($invoice['type'] ?? '') === 'service')
                                    <form action="{{ $invoice['send_square_url'] }}" method="POST" class="inline-flex">
                                        @csrf
                                        <button type="submit" title="Send via Square"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-all hover:bg-emerald-600 hover:text-white active:scale-90">
                                            <i class="fas fa-paper-plane text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ $invoice['view_url'] }}" title="View"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-400 transition-all hover:bg-slate-900 hover:text-white active:scale-90">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ $invoice['edit_url'] }}" title="Edit"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition-all hover:bg-slate-200 active:scale-90">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if (!empty($invoice['print_url']))
                                    <button type="button" title="Print"
                                        onclick="openInvoiceReceiptModal('{{ $invoice['print_url'] }}')"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition-all hover:bg-amber-600 hover:text-white active:scale-90">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                @endif
                                @if (!empty($invoice['delete_url']))
                                    <form action="{{ $invoice['delete_url'] }}" method="POST" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" title="Delete"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-400 transition-all hover:bg-red-500 hover:text-white active:scale-90"
                                            onclick="if (confirm('{{ $invoice['delete_confirm'] ?? 'Delete this invoice?' }}')) this.closest('form').submit();">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr id="invoice-empty-state" class="{{ count($invoiceRows) > 0 ? 'hidden' : '' }}">
                        <td colspan="14" class="px-10 py-12 text-center">
                            <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-300">No invoices match these filters</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @if(isset($invoices) && $invoices->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <div id="invoiceReceiptModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeInvoiceReceiptModal()"></div>
        <div class="relative flex h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Invoice Receipt</h3>
                    <p class="text-xs font-semibold text-slate-500">Professional invoice preview</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <button id="invoiceReceiptPrintButton" type="button" onclick="printInvoiceReceiptPreview()" disabled
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <button type="button" onclick="closeInvoiceReceiptModal()"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="relative flex-1 bg-slate-100">
                <div id="invoiceReceiptLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-slate-100">
                    <div class="rounded-xl border border-slate-200 bg-white px-6 py-5 text-center shadow-sm">
                        <div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-amber-600"></div>
                        <div class="text-sm font-black text-slate-800">Loading invoice receipt</div>
                        <div class="mt-1 text-xs text-slate-500">Preparing preview...</div>
                    </div>
                </div>
                <iframe id="invoiceReceiptIframe" src="about:blank" class="h-full w-full border-0 bg-white"></iframe>
            </div>
        </div>
    </div>

    @include('admin.partials.image-gallery-modal')
    @include('admin.sales.partials.square-remind-modal')
</div>
@endsection

@push('scripts')
<script>
    function openInvoiceReceiptModal(previewUrl) {
        const modal = document.getElementById('invoiceReceiptModal');
        const iframe = document.getElementById('invoiceReceiptIframe');
        const loading = document.getElementById('invoiceReceiptLoading');
        const printButton = document.getElementById('invoiceReceiptPrintButton');

        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        printButton.disabled = true;
        iframe.dataset.loadingReceipt = '0';
        iframe.src = 'about:blank';

        requestAnimationFrame(() => {
            iframe.dataset.loadingReceipt = '1';
            iframe.src = previewUrl;
        });

        document.body.style.overflow = 'hidden';
    }

    function closeInvoiceReceiptModal() {
        const modal = document.getElementById('invoiceReceiptModal');
        const iframe = document.getElementById('invoiceReceiptIframe');
        const loading = document.getElementById('invoiceReceiptLoading');
        const printButton = document.getElementById('invoiceReceiptPrintButton');

        iframe.dataset.loadingReceipt = '0';
        iframe.src = 'about:blank';
        loading.classList.add('hidden');
        printButton.disabled = true;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function printInvoiceReceiptPreview() {
        const iframe = document.getElementById('invoiceReceiptIframe');

        if (iframe?.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const filterForm = document.getElementById('invoiceFilters');
        const filters = {
            date: document.getElementById('invoice-date-filter'),
            method: document.getElementById('invoice-method-filter'),
            status: document.getElementById('invoice-status-filter'),
            payment: document.getElementById('invoice-payment-filter'),
            category: document.getElementById('invoice-category-filter'),
            search: document.getElementById('invoice-search-filter'),
        };
        const rows = Array.from(document.querySelectorAll('[data-invoice-row]'));
        const emptyState = document.getElementById('invoice-empty-state');
        const clearButton = document.getElementById('invoice-clear-filters');
        const receiptIframe = document.getElementById('invoiceReceiptIframe');
        const receiptLoading = document.getElementById('invoiceReceiptLoading');
        const receiptPrintButton = document.getElementById('invoiceReceiptPrintButton');

        const applyInvoiceFilters = () => {
            const selectedDate = filters.date?.value || '';
            const selectedMethod = filters.method?.value || '';
            const selectedStatus = (filters.status?.value || '').trim().toLowerCase();
            const selectedPayment = filters.payment?.value || '';
            const selectedCategory = (filters.category?.value || '').trim().toLowerCase();
            const searchTerm = (filters.search?.value || '').trim().toLowerCase();
            const searchParts = searchTerm.split(/\s+/).filter(Boolean);
            let visibleRows = 0;

            rows.forEach((row) => {
                const matchesDate = !selectedDate || row.dataset.invoiceDate === selectedDate;
                const matchesMethod = !selectedMethod || row.dataset.paymentMethod === selectedMethod;
                const matchesStatus = !selectedStatus || row.dataset.invoiceStatus === selectedStatus;
                const matchesPayment = !selectedPayment || row.dataset.paymentType === selectedPayment;
                const rowCategories = (row.dataset.invoiceCategories || '').split('|').filter(Boolean);
                const matchesCategory = !selectedCategory || rowCategories.includes(selectedCategory);
                const searchableText = row.dataset.invoiceSearch || '';
                const matchesSearch = searchParts.length === 0 || searchParts.every((term) => searchableText.includes(term));
                const isVisible = matchesDate && matchesMethod && matchesStatus && matchesPayment && matchesCategory && matchesSearch;

                row.classList.toggle('hidden', !isVisible);
                if (isVisible) {
                    visibleRows += 1;
                }
            });

            emptyState?.classList.toggle('hidden', visibleRows > 0);
            updateBulkActionState();
        };

        const selectAllInvoices = document.getElementById('selectAllInvoices');
        const bulkDeleteInvoicesBtn = document.getElementById('bulkDeleteInvoicesBtn');
        const selectedInvoiceCount = document.getElementById('selectedInvoiceCount');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        function getVisibleInvoiceCheckboxes() {
            return rows
                .filter(row => !row.classList.contains('hidden'))
                .map(row => row.querySelector('.invoice-checkbox'))
                .filter(Boolean);
        }

        function getSelectedInvoiceIds() {
            const checkboxes = Array.from(document.querySelectorAll('.invoice-checkbox:checked'));
            return checkboxes.map(cb => cb.value);
        }

        function updateBulkActionState() {
            const selectedIds = getSelectedInvoiceIds();
            const visibleCheckboxes = getVisibleInvoiceCheckboxes();
            const visibleCount = visibleCheckboxes.length;
            const visibleCheckedCount = visibleCheckboxes.filter(cb => cb.checked).length;

            if (selectedInvoiceCount) {
                selectedInvoiceCount.textContent = selectedIds.length;
            }
            if (bulkDeleteInvoicesBtn) {
                bulkDeleteInvoicesBtn.disabled = selectedIds.length === 0;
            }

            if (selectAllInvoices) {
                const allSelected = visibleCount > 0 && visibleCheckedCount === visibleCount;
                selectAllInvoices.checked = allSelected;
                selectAllInvoices.indeterminate = visibleCheckedCount > 0 && !allSelected;
            }
        }

        selectAllInvoices?.addEventListener('change', function() {
            const visibleCheckboxes = getVisibleInvoiceCheckboxes();
            visibleCheckboxes.forEach(cb => {
                cb.checked = selectAllInvoices.checked;
            });
            updateBulkActionState();
        });

        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('invoice-checkbox')) {
                updateBulkActionState();
            }
        });

        bulkDeleteInvoicesBtn?.addEventListener('click', function() {
            const selectedIds = getSelectedInvoiceIds();
            if (selectedIds.length === 0) return;

            if (!confirm(`Delete ${selectedIds.length} selected invoice(s)? This action cannot be undone.`)) {
                return;
            }

            bulkDeleteForm.querySelectorAll('[data-bulk-invoice-id]').forEach(input => input.remove());
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                input.dataset.bulkInvoiceId = 'true';
                bulkDeleteForm.appendChild(input);
            });

            bulkDeleteInvoicesBtn.disabled = true;
            bulkDeleteForm.submit();
        });

        filterForm?.addEventListener('submit', (event) => {
            event.preventDefault();
            applyInvoiceFilters();
        });

        Object.values(filters).forEach((field) => {
            field?.addEventListener('input', applyInvoiceFilters);
            field?.addEventListener('change', applyInvoiceFilters);
        });

        clearButton?.addEventListener('click', () => {
            Object.values(filters).forEach((field) => {
                if (field) {
                    field.value = '';
                }
            });
            applyInvoiceFilters();
        });

        applyInvoiceFilters();

        receiptIframe?.addEventListener('load', () => {
            if (receiptIframe.dataset.loadingReceipt === '1') {
                receiptLoading?.classList.add('hidden');
                if (receiptPrintButton) {
                    receiptPrintButton.disabled = false;
                }
                receiptIframe.dataset.loadingReceipt = '0';
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeInvoiceReceiptModal();
            }
        });
    });
</script>
@endpush
