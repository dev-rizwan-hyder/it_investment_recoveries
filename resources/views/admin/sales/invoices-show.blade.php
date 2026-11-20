@extends('layouts.admin')

@php
    $picturePaths = collect($invoice->picture_paths ?? [])->filter()->values();
@endphp

@section('content')
<div class="w-full animate-in fade-in duration-500">
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.24em] text-blue-500">Service Invoice</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-800">{{ $invoice->invoice_number }}</h1>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.sales.invoices') }}"
                class="inline-flex items-center rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-arrow-left mr-2 text-xs"></i> All Invoices
            </a>
            @if(!$invoice->isSquarePaid() && $invoice->status !== \App\Models\SalesInvoice::STATUS_PAID)
                @php
                    $remindMsgB64 = base64_encode(\App\Models\SalesInvoice::defaultReminderMessage($invoice));
                @endphp
                <button type="button"
                    data-square-remind-btn
                    onclick="triggerSquareRemind(this)"
                    data-url="{{ route('admin.sales.invoices.remind-square', $invoice) }}"
                    data-invoice-number="{{ $invoice->invoice_number }}"
                    data-customer-name="{{ $invoice->customer_name ?? '' }}"
                    data-customer-email="{{ $invoice->customer_email ?? '' }}"
                    data-amount="${{ number_format((float) $invoice->total, 2) }}"
                    data-due-date="{{ $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: 'Upon receipt') }}"
                    data-has-square="{{ $invoice->hasSquareInvoice() ? '1' : '0' }}"
                    data-square-url="{{ $invoice->square_public_url ?? '' }}"
                    data-reminder-count="{{ (int) ($invoice->square_reminder_count ?? 0) }}"
                    data-last-reminded="{{ $invoice->square_reminded_at?->format('M d, Y h:i A') ?? '' }}"
                    data-message-b64="{{ $remindMsgB64 }}"
                    class="inline-flex items-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-amber-600 shadow-md shadow-amber-500/20">
                    <i class="fas fa-bell mr-2 text-xs"></i> Square Remind
                    @if($invoice->square_reminder_count > 0)
                        <span class="ml-2 rounded-full bg-white/25 px-2 py-0.5 text-xs font-black">{{ $invoice->square_reminder_count }}</span>
                    @endif
                </button>
            @endif
            @if(!$invoice->hasSquareInvoice() || strtolower((string) $invoice->square_status) === 'draft')
                <form action="{{ route('admin.sales.invoices.send-square', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 shadow-md shadow-emerald-600/20">
                        <i class="fas fa-paper-plane mr-2 text-xs"></i> Send via Square
                    </button>
                </form>
            @elseif($invoice->hasSquareInvoice())
                <form action="{{ route('admin.sales.invoices.sync-square', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                        <i class="fas fa-sync mr-2 text-xs"></i> Sync Square Status
                    </button>
                </form>
            @endif
            @if(!empty($invoice->customer_email))
                <form action="{{ route('admin.sales.invoices.send-email', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700 shadow-md shadow-blue-600/20"
                        title="Send invoice in email to customer ({{ $invoice->customer_email }})">
                        <i class="fas fa-envelope mr-2 text-xs"></i> Send via Email
                    </button>
                </form>
            @endif
            <button type="button" onclick="openInvoiceReceiptModal('{{ route('admin.sales.invoices.print', $invoice) }}?embedded=1')"
                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                <i class="fas fa-print mr-2 text-xs"></i> Print
            </button>
            <a href="{{ route('admin.sales.invoices.edit', $invoice) }}"
                class="inline-flex items-center rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                <i class="fas fa-edit mr-2 text-xs"></i> Edit
            </a>
            <form action="{{ route('admin.sales.invoices.destroy', $invoice) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="inline-flex items-center rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-600 transition hover:bg-red-600 hover:text-white"
                    onclick="if (confirm('Delete this invoice?')) this.closest('form').submit();">
                    <i class="fas fa-trash mr-2 text-xs"></i> Delete
                </button>
            </form>
        </div>
    </div>

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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-black text-slate-800">Line Items</h2>
                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wide {{ $invoice->status_badge_class }}">
                        {{ $invoice->status_label }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[920px] w-full text-left">
                        <thead class="border-b border-slate-100 bg-white text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Unit</th>
                                <th class="px-6 py-4 text-right">Subtotal</th>
                                <th class="px-6 py-4 text-right">Tax</th>
                                <th class="px-6 py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($invoice->items as $item)
                                <tr class="text-sm">
                                    <td class="px-6 py-5">
                                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-blue-600">
                                            {{ $item->invoice_category ?: 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="font-black text-slate-800">{{ $item->description }}</p>
                                    </td>
                                    <td class="px-6 py-5 text-center font-black text-slate-600">{{ $item->quantity }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-slate-600">${{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-slate-600">${{ number_format((float) $item->subtotal, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-slate-600">${{ number_format((float) $item->tax_amount, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-black text-emerald-600">${{ number_format((float) $item->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm font-black uppercase tracking-wide text-slate-300">
                                        No line items saved
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($invoice->message)
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h2 class="mb-3 text-lg font-black text-slate-800">Message</h2>
                    <p class="whitespace-pre-line text-sm font-semibold leading-7 text-slate-600">{{ $invoice->message }}</p>
                </div>
            @endif

            @if($picturePaths->isNotEmpty())
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-black text-slate-800">Pictures & Files</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @php
                            $allPicUrls = $picturePaths->map(fn($p) => $invoice->pictureUrl($p))->values()->all();
                            $allPicJson = json_encode($allPicUrls);
                        @endphp
                        @foreach($picturePaths as $path)
                            @php
                                $picUrl = $invoice->pictureUrl($path);
                                $isImg = \App\Models\SalesInvoice::isImageFile($path);
                                $fileName = \App\Models\SalesInvoice::fileName($path);
                                $fileExt = \App\Models\SalesInvoice::fileExtension($path);
                                $fileIcon = \App\Models\SalesInvoice::fileIconClass($path);
                            @endphp
                            @if($isImg)
                                <a href="javascript:void(0)" onclick="openImageGallery({{ $allPicJson }}, 'Invoice {{ $invoice->invoice_number }}')"
                                    class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 transition hover:border-blue-200 block group shadow-sm">
                                    <img src="{{ $picUrl }}" alt="{{ $fileName }}" class="h-44 w-full object-cover transition-transform group-hover:scale-105">
                                </a>
                            @else
                                <a href="javascript:void(0)" onclick="openImageGallery({{ $allPicJson }}, 'Invoice {{ $invoice->invoice_number }}')"
                                    class="flex h-44 flex-col items-center justify-center gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-4 transition hover:border-blue-200 hover:bg-slate-100 text-center shadow-sm group">
                                    <i class="fas {{ $fileIcon }} text-4xl mb-1 transition-transform group-hover:scale-110"></i>
                                    <span class="max-w-full truncate text-xs font-bold text-slate-700 px-2">{{ $fileName }}</span>
                                    <span class="rounded bg-slate-200 px-2 py-0.5 text-[10px] font-black uppercase text-slate-600">{{ $fileExt }}</span>
                                    <span class="text-[11px] font-bold text-blue-600 flex items-center gap-1 mt-1">
                                        <i class="fas fa-external-link-alt text-[9px]"></i> View File
                                    </span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-800">Square Integration</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wide {{ $invoice->square_status_badge_class }}">
                        <i class="fas fa-credit-card text-[9px]"></i> {{ $invoice->square_status ? strtoupper($invoice->square_status) : 'NOT SENT' }}
                    </span>
                </div>

                @if($invoice->hasSquareInvoice())
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Square Invoice ID</p>
                            <p class="mt-0.5 font-mono text-xs font-bold text-slate-700 break-all">{{ $invoice->square_invoice_id }}</p>
                        </div>
                        @if($invoice->square_sent_at)
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Sent Date</p>
                                <p class="mt-0.5 font-bold text-slate-700">{{ $invoice->square_sent_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                        @if($invoice->square_reminded_at)
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Last Reminded</p>
                                <p class="mt-0.5 font-bold text-amber-700">
                                    {{ $invoice->square_reminded_at->format('M d, Y h:i A') }}
                                    <span class="text-xs font-semibold text-slate-400">({{ $invoice->square_reminder_count }} sent)</span>
                                </p>
                            </div>
                        @endif
                        @if($invoice->square_public_url)
                            <div class="pt-2">
                                <a href="{{ $invoice->square_public_url }}" target="_blank"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-black text-white transition hover:bg-slate-800">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> View Payment Page
                                </a>
                            </div>
                        @endif
                        @if(!$invoice->isSquarePaid())
                            <div class="pt-2">
                                <button type="button"
                                    data-square-remind-btn
                                    onclick="triggerSquareRemind(this)"
                                    data-url="{{ route('admin.sales.invoices.remind-square', $invoice) }}"
                                    data-invoice-number="{{ $invoice->invoice_number }}"
                                    data-customer-name="{{ $invoice->customer_name ?? '' }}"
                                    data-customer-email="{{ $invoice->customer_email ?? '' }}"
                                    data-amount="${{ number_format((float) $invoice->total, 2) }}"
                                    data-due-date="{{ $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: 'Upon receipt') }}"
                                    data-has-square="{{ $invoice->hasSquareInvoice() ? '1' : '0' }}"
                                    data-square-url="{{ $invoice->square_public_url ?? '' }}"
                                    data-reminder-count="{{ (int) ($invoice->square_reminder_count ?? 0) }}"
                                    data-last-reminded="{{ $invoice->square_reminded_at?->format('M d, Y h:i A') ?? '' }}"
                                    data-message-b64="{{ $remindMsgB64 ?? base64_encode(\App\Models\SalesInvoice::defaultReminderMessage($invoice)) }}"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-black text-white shadow-md shadow-amber-500/20 transition hover:bg-amber-600">
                                    <i class="fas fa-bell text-[10px]"></i> Send Payment Reminder
                                </button>
                            </div>
                        @endif

                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <form action="{{ route('admin.sales.invoices.sync-square', $invoice) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-200">
                                    <i class="fas fa-sync mr-1"></i> Sync Status
                                </button>
                            </form>
                            @if(!in_array(strtoupper((string) $invoice->square_status), ['PAID', 'CANCELED']))
                                <form action="{{ route('admin.sales.invoices.cancel-square', $invoice) }}" method="POST" class="shrink-0"
                                    onclick="return confirm('Cancel this Square invoice?');">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-100">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500 font-semibold mb-4 leading-relaxed">
                        Send this invoice directly to your customer's email with a Square hosted online payment page.
                    </p>
                    <form action="{{ route('admin.sales.invoices.send-square', $invoice) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-xs font-black text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-700">
                            <i class="fas fa-paper-plane text-xs"></i> Send Square Invoice Now
                        </button>
                    </form>
                @endif
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-black text-slate-800">Invoice Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-semibold">Subtotal</span>
                        <span class="font-bold">${{ number_format((float) $invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-semibold">Tax ({{ number_format((float) $invoice->tax_rate, 0) }}%)</span>
                        <span class="font-bold">${{ number_format((float) $invoice->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="font-black text-slate-800">Total</span>
                        <span class="text-2xl font-black text-emerald-600">${{ number_format((float) $invoice->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-black text-slate-800">Customer</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Name</p>
                        <p class="mt-1 font-bold text-slate-800">{{ $invoice->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Email</p>
                        <p class="mt-1 font-bold text-slate-700">{{ $invoice->customer_email ?: 'No email' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Phone</p>
                        <p class="mt-1 font-bold text-slate-700">{{ $invoice->customer_phone ?: 'No phone' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Company</p>
                        <p class="mt-1 font-bold text-slate-700">{{ $invoice->customer_company ?: 'No company' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Address</p>
                        <p class="mt-1 whitespace-pre-line font-bold text-slate-700">{{ $invoice->customer_address ?: 'No address' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-black text-slate-800">Details</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Date of Service</p>
                        <p class="mt-1 font-bold text-slate-800">{{ $invoice->service_date?->format('M d, Y') ?: 'No date' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Due Date</p>
                        <p class="mt-1 font-bold text-slate-800">{{ $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: 'No date') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Payment Method</p>
                        <p class="mt-1 font-bold text-slate-800">{{ $invoice->payment_method_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Payment Type</p>
                        <p class="mt-1 font-bold text-slate-800">{{ $invoice->payment_type_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Created</p>
                        <p class="mt-1 font-bold text-slate-800">{{ $invoice->created_at?->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </aside>
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
        const receiptIframe = document.getElementById('invoiceReceiptIframe');
        const receiptLoading = document.getElementById('invoiceReceiptLoading');
        const receiptPrintButton = document.getElementById('invoiceReceiptPrintButton');

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
