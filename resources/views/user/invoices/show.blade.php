@extends('user.layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@php
    $picturePaths = collect($invoice->picture_paths ?? [])->filter()->values();
    $isPaid = strtolower((string) $invoice->status) === \App\Models\SalesInvoice::STATUS_PAID;
@endphp

@section('content')
<div class="min-h-screen bg-slate-50/50 py-6 sm:py-8 font-sans">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Top Navigation & Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <a href="{{ route('user.invoices.index') }}" 
                   class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 shadow-xs">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                            Service Invoice
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $invoice->status_badge_class }}">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $invoice->invoice_number }}</h1>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if(!$isPaid)
                    <form action="{{ route('user.invoices.pay-square', $invoice->id) }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm px-5 py-2.5 shadow-md shadow-emerald-600/20 transition active:scale-95">
                            <i class="fab fa-square text-base"></i>
                            <span>Pay via Square</span>
                        </button>
                    </form>
                @endif

                @if($invoice->hasSquareInvoice())
                    <form action="{{ route('user.invoices.sync-square', $invoice->id) }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" title="Sync payment status from Square"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm px-4 py-2.5 transition active:scale-95">
                            <i class="fas fa-sync text-xs"></i>
                            <span>Sync Payment</span>
                        </button>
                    </form>
                @endif

                <a href="{{ route('user.invoices.print', $invoice->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-4 py-2.5 shadow-xs transition active:scale-95">
                    <i class="fas fa-print text-xs"></i>
                    <span>Print Receipt</span>
                </a>
            </div>
        </div>

        {{-- Alerts --}}
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
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Unpaid Pay Banner --}}
        @if(!$isPaid)
            <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-300/80 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-12 w-12 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">Payment Pending: ${{ number_format((float) $invoice->total, 2) }}</h2>
                        <p class="text-xs text-slate-600 mt-0.5">
                            @if($invoice->due_date)
                                Due by <strong class="text-slate-900">{{ $invoice->due_date->format('F d, Y') }}</strong>.
                            @endif
                            You can pay securely online via Square using Credit Card, Debit Card, or Apple/Google Pay.
                        </p>
                    </div>
                </div>
                <form action="{{ route('user.invoices.pay-square', $invoice->id) }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition active:scale-95">
                        <i class="fab fa-square text-lg"></i>
                        <span>Pay ${{ number_format((float) $invoice->total, 2) }} via Square</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_360px]">
            {{-- Left: Items & Details --}}
            <div class="space-y-6">
                {{-- Line items table --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">Service Line Items</h2>
                        <span class="text-xs font-bold text-slate-500">{{ $invoice->items->count() }} {{ Str::plural('Item', $invoice->items->count()) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-slate-100 bg-white text-[11px] font-black uppercase tracking-[0.14em] text-slate-400">
                                <tr>
                                    <th class="px-6 py-3.5">Category</th>
                                    <th class="px-6 py-3.5">Description</th>
                                    <th class="px-6 py-3.5 text-center">Qty</th>
                                    <th class="px-6 py-3.5 text-right">Unit Price</th>
                                    <th class="px-6 py-3.5 text-right">Subtotal</th>
                                    <th class="px-6 py-3.5 text-right">Tax</th>
                                    <th class="px-6 py-3.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($invoice->items as $item)
                                    <tr class="text-sm hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700 border border-blue-200/70">
                                                {{ $item->invoice_category ?: 'General Service' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-slate-800">{{ $item->description }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-slate-700">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">${{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">${{ number_format((float) $item->subtotal, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">${{ number_format((float) $item->tax_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-black text-slate-900">${{ number_format((float) $item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-400">
                                            No line items listed on this invoice.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Invoice Note / Message --}}
                @if($invoice->message)
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Note / Instructions</h2>
                        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                            {{ $invoice->message }}
                        </p>
                    </div>
                @endif

                {{-- Attachments & Files --}}
                @if($picturePaths->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">Attached Pictures & Files</h2>
                            <span class="text-xs font-bold text-slate-400">{{ $picturePaths->count() }} {{ Str::plural('attachment', $picturePaths->count()) }}</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($picturePaths as $path)
                                @php
                                    $picUrl = $invoice->pictureUrl($path);
                                    $isImg = \App\Models\SalesInvoice::isImageFile($path);
                                    $fileName = \App\Models\SalesInvoice::fileName($path);
                                    $fileExt = \App\Models\SalesInvoice::fileExtension($path);
                                    $fileIcon = \App\Models\SalesInvoice::fileIconClass($path);
                                @endphp
                                @if($isImg)
                                    <button type="button" onclick="window.openImageModal('{{ $picUrl }}', '{{ addslashes($fileName) }}')"
                                       class="group relative block overflow-hidden rounded-xl border border-slate-200 bg-slate-100 hover:border-blue-400 transition w-full text-left cursor-pointer">
                                        <img src="{{ $picUrl }}" alt="{{ $fileName }}" class="h-32 w-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition">
                                            <i class="fas fa-magnifying-glass-plus mr-1"></i> View
                                        </div>
                                    </button>
                                @else
                                    <a href="{{ $picUrl }}" target="_blank" download="{{ $fileName }}"
                                       class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50/50 hover:border-blue-300 transition group text-center">
                                        <i class="fas {{ $fileIcon }} text-2xl text-slate-500 group-hover:text-blue-600 mb-2 transition-colors"></i>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600 line-clamp-2 break-all">{{ $fileName }}</span>
                                        <span class="rounded bg-slate-200 px-2 py-0.5 text-[9px] font-black uppercase text-slate-600 mt-1.5">{{ $fileExt }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Sidebar Details & Summary --}}
            <div class="space-y-6">
                {{-- Payment / Square Status Card --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-400">Payment Status</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $invoice->status_badge_class }}">
                            {{ $invoice->status_label }}
                        </span>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-semibold">Method:</span>
                            <span class="font-black text-slate-800">{{ $invoice->payment_method_label }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-semibold">Service Date:</span>
                            <span class="font-bold text-slate-800">{{ $invoice->service_date?->format('m/d/Y') ?: 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-semibold">Due Date:</span>
                            <span class="font-bold text-slate-800">{{ $invoice->due_date?->format('m/d/Y') ?: ($invoice->service_date?->format('m/d/Y') ?: 'N/A') }}</span>
                        </div>

                        @if($invoice->hasSquareInvoice())
                            <div class="pt-3 border-t border-slate-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-slate-500 font-semibold">Square Status:</span>
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-black uppercase tracking-wide {{ $invoice->square_status_badge_class }}">
                                        <i class="fab fa-square text-[8px]"></i> {{ strtoupper($invoice->square_status) }}
                                    </span>
                                </div>
                                @if($invoice->square_sent_at)
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-slate-500 font-semibold">Square Issued:</span>
                                        <span class="font-bold text-slate-700">{{ $invoice->square_sent_at->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                @if($invoice->square_public_url && !$isPaid)
                                    <a href="{{ $invoice->square_public_url }}" target="_blank"
                                       class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-emerald-700 shadow-sm">
                                        <i class="fab fa-square"></i>
                                        <span>Open Square Payment Portal</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Invoice Financial Totals Card --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100">
                        Financial Summary
                    </h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="font-semibold">Subtotal</span>
                            <span class="font-bold">${{ number_format((float) $invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="font-semibold">Tax ({{ number_format((float) $invoice->tax_rate, 0) }}%)</span>
                            <span class="font-bold">${{ number_format((float) $invoice->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                            <span class="font-black text-slate-900 text-sm">Total Amount</span>
                            <span class="text-xl font-black text-emerald-600">${{ number_format((float) $invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Customer Info Card --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3 pb-2 border-b border-slate-100">
                        Billing Details
                    </h2>
                    <div class="space-y-2.5 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase">Name</span>
                            <p class="font-black text-slate-900 mt-0.5">{{ $invoice->customer_name }}</p>
                        </div>
                        @if($invoice->customer_company)
                            <div>
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Company</span>
                                <p class="font-bold text-slate-700 mt-0.5">{{ $invoice->customer_company }}</p>
                            </div>
                        @endif
                        @if($invoice->customer_email)
                            <div>
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Email</span>
                                <p class="font-bold text-slate-700 mt-0.5">{{ $invoice->customer_email }}</p>
                            </div>
                        @endif
                        @if($invoice->customer_phone)
                            <div>
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Phone</span>
                                <p class="font-bold text-slate-700 mt-0.5">{{ $invoice->customer_phone }}</p>
                            </div>
                        @endif
                        @if($invoice->customer_address)
                            <div>
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Address</span>
                                <p class="font-bold text-slate-700 mt-0.5">{{ $invoice->customer_address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
