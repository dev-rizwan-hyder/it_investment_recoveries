@extends('layouts.admin')

@php
    $picturePaths = collect($invoice->picture_paths ?? [])->filter()->values();

    // Check for linked inventory item from items, message, or description
    $linkedInventory = $linkedInventory ?? $invoice->items->pluck('inventory')->filter()->first();
    if (!$linkedInventory && !empty($invoice->message)) {
        if (preg_match('/Inventory Item #(\d+)/i', $invoice->message, $m)) {
            $linkedInventory = \App\Models\Inventory::find((int) $m[1]);
        } elseif (preg_match('/Barcode:\s*([^\s\|]+)/i', $invoice->message, $m)) {
            $linkedInventory = \App\Models\Inventory::where('barcode', trim($m[1]))->first();
        }
    }
    if (!$linkedInventory) {
        foreach ($invoice->items as $iItem) {
            if (preg_match('/Barcode:\s*([^\s\|]+)/i', $iItem->description, $m)) {
                $linkedInventory = \App\Models\Inventory::where('barcode', trim($m[1]))->first();
                if ($linkedInventory) break;
            }
        }
    }

    $isPaid = $invoice->status === \App\Models\SalesInvoice::STATUS_PAID || $invoice->isSquarePaid();
    $amountPaid = $isPaid ? (float) $invoice->total : 0;
    $balanceDue = max(0, (float) $invoice->total - $amountPaid);

    $isOverdue = !$isPaid && $invoice->due_date && $invoice->due_date->isPast() && !$invoice->due_date->isToday();
    $isDueToday = !$isPaid && $invoice->due_date && $invoice->due_date->isToday();

    $remindMsgB64 = base64_encode(\App\Models\SalesInvoice::defaultReminderMessage($invoice));
@endphp

@section('content')
<div class="w-full space-y-5 animate-in fade-in duration-300">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Back navigation -->
        <a href="{{ route('admin.sales.invoices') }}"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fas fa-arrow-left text-[10px]"></i>
            <span>All Invoices</span>
        </a>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- 1-Click Status Toggle -->
            <form action="{{ route('admin.sales.invoices.toggle-status', $invoice) }}" method="POST" class="inline-block">
                @csrf
                @if($isPaid)
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-800 transition hover:bg-amber-100 active:scale-95">
                        <i class="fas fa-undo text-[10px]"></i> Mark Unpaid
                    </button>
                @else
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 active:scale-95">
                        <i class="fas fa-check-circle text-[10px] text-emerald-600"></i> Mark Paid
                    </button>
                @endif
            </form>

            <!-- Print Receipt Modal -->
            <button type="button" onclick="openInvoiceReceiptModal('{{ route('admin.sales.invoices.print', $invoice) }}?embedded=1')"
                class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-3.5 py-1.5 text-xs font-bold text-slate-700 shadow-xs transition hover:bg-slate-50">
                <i class="fas fa-receipt mr-1.5 text-[10px] text-slate-500"></i> Receipt
            </button>

            <!-- Direct Print / PDF -->
            <a href="{{ route('admin.sales.invoices.print', $invoice) }}" target="_blank"
                class="inline-flex items-center rounded-lg bg-slate-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-slate-800 active:scale-95">
                <i class="fas fa-print mr-1.5 text-[10px]"></i> Print
            </a>

            <!-- Send via Email -->
            @if(!empty($invoice->customer_email))
                <form action="{{ route('admin.sales.invoices.send-email', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-3.5 py-1.5 text-xs font-bold text-slate-700 shadow-xs transition hover:bg-slate-50"
                        title="Send invoice via email to customer ({{ $invoice->customer_email }})">
                        <i class="fas fa-envelope mr-1.5 text-[10px] text-blue-600"></i> Email
                    </button>
                </form>
            @endif

            <!-- Square Remind / Send -->
            @if($invoice->hasSquareInvoice())
                @if(!$invoice->isSquarePaid() && $invoice->status !== \App\Models\SalesInvoice::STATUS_PAID)
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
                        class="inline-flex items-center rounded-lg bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-amber-600">
                        <i class="fas fa-bell mr-1.5 text-[10px]"></i> Remind
                    </button>
                @endif
                <form action="{{ route('admin.sales.invoices.sync-square', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-3.5 py-1.5 text-xs font-bold text-slate-700 shadow-xs transition hover:bg-slate-50"
                        title="Sync latest Square status">
                        <i class="fas fa-sync mr-1.5 text-[10px] text-slate-500"></i> Sync Square
                    </button>
                </form>
            @else
                <form action="{{ route('admin.sales.invoices.send-square', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700">
                        <i class="fas fa-paper-plane mr-1.5 text-[10px]"></i> Send Square
                    </button>
                </form>
            @endif

            <!-- Edit -->
            <a href="{{ route('admin.sales.invoices.edit', $invoice) }}"
                class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs transition hover:bg-slate-50">
                <i class="fas fa-edit mr-1 text-[10px]"></i> Edit
            </a>

            <!-- Delete -->
            <form action="{{ route('admin.sales.invoices.destroy', $invoice) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="inline-flex items-center rounded-lg bg-white border border-red-200 px-2.5 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50"
                    onclick="if (confirm('Delete invoice {{ $invoice->invoice_number }}?')) this.closest('form').submit();"
                    title="Delete invoice">
                    <i class="fas fa-trash text-[10px]"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-xs"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-bold text-amber-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                <span>{{ session('warning') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700 text-xs"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-bold text-red-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-times-circle text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- CLEAN & PROFESSIONAL INVOICE SHEET CONTAINER                              -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">

        <!-- 1. Document Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">IT INVESTMENT RECOVERIES</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sales & Service Invoice Receipt</p>

                <!-- Linked Origin Badge (If Inventory or Repair) -->
                @if($linkedInventory)
                    <div class="mt-3 inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs">
                        <i class="fas fa-boxes-stacked text-blue-600 text-[11px]"></i>
                        <span class="text-slate-600">Product: <strong class="text-slate-800">{{ $linkedInventory->name }}</strong></span>
                        <span class="text-slate-300">|</span>
                        <a href="{{ route('admin.inventory.index') }}?search={{ urlencode($linkedInventory->barcode ?? $linkedInventory->name) }}"
                            class="text-blue-600 hover:underline font-bold flex items-center gap-1">
                            <span>Inv #{{ $linkedInventory->id }}</span>
                            <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    </div>
                @elseif($invoice->repairEntry)
                    <div class="mt-3 inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs">
                        <i class="fas fa-wrench text-blue-600 text-[11px]"></i>
                        <span class="text-slate-600">Repair: <strong class="text-slate-800">{{ $invoice->repairEntry->first_item_name ?: 'Repair Intake' }}</strong></span>
                        <span class="text-slate-300">|</span>
                        <a href="{{ route('admin.repair.entries.show', $invoice->repairEntry->id) }}"
                            class="text-blue-600 hover:underline font-bold flex items-center gap-1">
                            <span>Repair #{{ $invoice->repairEntry->id }}</span>
                            <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Invoice Number & Status on the Right -->
            <div class="text-left sm:text-right space-y-1.5">
                <div class="text-xs font-black uppercase tracking-wider text-slate-400">Invoice</div>
                <div class="text-2xl font-black text-slate-900 font-mono flex items-center sm:justify-end gap-1.5">
                    <span>{{ $invoice->invoice_number }}</span>
                    <button type="button" onclick="copyInvoiceText('{{ $invoice->invoice_number }}', this)"
                        class="text-slate-400 hover:text-slate-700 text-xs transition" title="Copy number">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-black uppercase tracking-wider {{ $isPaid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $isPaid ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $isPaid ? 'Paid' : 'Unpaid' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Two-Column Billing & Invoice Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs">
            <!-- Billed To -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Billed To</span>
                <p class="text-base font-extrabold text-slate-900">{{ $invoice->customer_name ?: 'Walk-in Customer' }}</p>
                @if($invoice->customer_company)
                    <p class="font-medium text-slate-600">{{ $invoice->customer_company }}</p>
                @endif
                @if($invoice->customer_email)
                    <p><a href="mailto:{{ $invoice->customer_email }}" class="text-blue-600 hover:underline font-medium">{{ $invoice->customer_email }}</a></p>
                @endif
                @if($invoice->customer_phone)
                    <p><a href="tel:{{ $invoice->customer_phone }}" class="text-slate-700 hover:underline font-medium">{{ $invoice->customer_phone }}</a></p>
                @endif
                @if($invoice->customer_address)
                    <p class="text-slate-600 whitespace-pre-line leading-relaxed pt-0.5">{{ $invoice->customer_address }}</p>
                @endif
            </div>

            <!-- Invoice Details -->
            <div class="space-y-2 sm:pl-8 sm:border-l sm:border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Invoice Details</span>
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="text-slate-500">Invoice Date:</span>
                        <span class="font-bold text-slate-800">{{ $invoice->service_date?->format('M d, Y') ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="text-slate-500">Payment Due:</span>
                        <span class="font-bold text-slate-800">
                            {{ $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: '-') }}
                            @if($isOverdue)
                                <span class="text-[10px] font-black text-red-600 bg-red-50 px-1 py-0.2 rounded ml-1">Overdue</span>
                            @elseif($isPaid)
                                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-1 py-0.2 rounded ml-1">Settled</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="text-slate-500">Payment Method:</span>
                        <span class="font-bold text-slate-800">{{ $invoice->payment_method_label }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="text-slate-500">Payment Type:</span>
                        <span class="font-bold text-slate-800">{{ $invoice->payment_type_label }}</span>
                    </div>
                    @if($invoice->hasSquareInvoice())
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="text-slate-500">Square Status:</span>
                            <span class="font-bold uppercase {{ $invoice->square_status_badge_class }} px-1.5 py-0.2 rounded text-[10px]">
                                {{ $invoice->square_status ?: 'Sent' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Clean Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="border-y border-slate-200 bg-slate-50/70 text-[10px] font-black uppercase tracking-wider text-slate-500">
                        <th class="py-2.5 px-3 w-10 text-center">#</th>
                        <th class="py-2.5 px-3">Description</th>
                        <th class="py-2.5 px-3 text-center w-16">Qty</th>
                        <th class="py-2.5 px-3 text-right w-24">Unit Price</th>
                        <th class="py-2.5 px-3 text-right w-20">Tax</th>
                        <th class="py-2.5 px-3 text-right w-28">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($invoice->items as $idx => $item)
                        @php
                            $rawDesc = (string)$item->description;
                            $pipeParts = array_map('trim', explode('|', $rawDesc));
                            $mainTitle = $pipeParts[0] ?? $rawDesc;
                            $tagParts = array_slice($pipeParts, 1);
                        @endphp
                        <tr>
                            <td class="py-3.5 px-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-3">
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm leading-snug">{{ $mainTitle }}</p>
                                    @if(!empty($tagParts))
                                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                            {{ implode(' • ', $tagParts) }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-center font-black text-slate-700">{{ $item->quantity }}</td>
                            <td class="py-3.5 px-3 text-right font-mono font-medium text-slate-600">${{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="py-3.5 px-3 text-right font-mono text-slate-500">
                                @if((float)$item->tax_amount > 0)
                                    ${{ number_format((float) $item->tax_amount, 2) }}
                                @else
                                    <span class="text-[10px] text-slate-400">0.00</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-right font-mono font-black text-slate-900 text-sm">
                                ${{ number_format((float) $item->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-slate-400">No items listed</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 4. Bottom Totals & Notes Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-4 border-t border-slate-200">
            <!-- Left: Message & Extra Info -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-4">
                @if($invoice->message)
                    <div class="space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Notes & Terms</span>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-3.5 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $invoice->message }}
                        </div>
                    </div>
                @endif

                @if($invoice->square_public_url)
                    <div class="pt-1">
                        <a href="{{ $invoice->square_public_url }}" target="_blank"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3.5 py-2 rounded-lg hover:bg-emerald-100 transition shadow-2xs">
                            <i class="fas fa-credit-card text-[11px]"></i>
                            <span>Pay online via Square payment page &rarr;</span>
                        </a>
                    </div>
                @endif

                <!-- Attached media thumbnails if any -->
                @if($picturePaths->isNotEmpty())
                    <div class="space-y-1.5 pt-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Attached Photos / Files ({{ $picturePaths->count() }})</span>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $allPicUrls = $picturePaths->map(fn($p) => $invoice->pictureUrl($p))->values()->all();
                                $allPicJson = json_encode($allPicUrls);
                            @endphp
                            @foreach($picturePaths as $path)
                                @php
                                    $picUrl = $invoice->pictureUrl($path);
                                    $isImg = \App\Models\SalesInvoice::isImageFile($path);
                                    $fileName = \App\Models\SalesInvoice::fileName($path);
                                @endphp
                                @if($isImg)
                                    <div onclick="openImageGallery({{ $allPicJson }}, 'Invoice {{ $invoice->invoice_number }}')"
                                        class="h-14 w-14 rounded-lg border border-slate-200 overflow-hidden cursor-pointer hover:border-blue-400 transition shrink-0 shadow-2xs">
                                        <img src="{{ $picUrl }}" alt="{{ $fileName }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <a href="{{ $picUrl }}" target="_blank" download="{{ $fileName }}"
                                        class="h-14 px-3 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white text-slate-700 hover:text-blue-600 transition flex items-center gap-1.5 text-xs font-bold shrink-0">
                                        <i class="fas fa-file text-sm text-slate-400"></i>
                                        <span class="truncate max-w-[120px]">{{ $fileName }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Financial Breakdown -->
            <div class="lg:col-span-5 xl:col-span-4 text-xs">
                <div class="bg-slate-50/70 rounded-xl p-5 border border-slate-200/80 space-y-3">
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-medium">Subtotal</span>
                        <span class="font-mono font-bold text-slate-800 text-sm">${{ number_format((float) $invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600 border-b border-slate-200/80 pb-3">
                        <span class="font-medium">Tax ({{ number_format((float) $invoice->tax_rate, 0) }}%)</span>
                        <span class="font-mono font-bold text-slate-800 text-sm">${{ number_format((float) $invoice->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-sm font-black text-slate-900 uppercase tracking-tight">Total</span>
                        <span class="font-mono font-black text-xl text-slate-900">${{ number_format((float) $invoice->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-medium">Amount Paid</span>
                        <span class="font-mono font-bold text-emerald-600">${{ number_format($amountPaid, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200/80 font-bold">
                        <span class="text-xs uppercase tracking-tight text-slate-700">Balance Due</span>
                        <span class="font-mono text-base {{ $balanceDue > 0 ? 'text-amber-700 font-black' : 'text-slate-400' }}">
                            ${{ number_format($balanceDue, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Minimalist Footer -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400">
            <span>Thank you for your business!</span>
            <span>Questions? Contact <strong class="text-slate-600 font-semibold">customerservice@it-investmentrecoveries.com</strong></span>
        </div>

    </div>

    <!-- Printable Receipt Modal (Embedded iframe) -->
    <div id="invoiceReceiptModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-3 sm:p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeInvoiceReceiptModal()"></div>
        <div class="relative flex h-[94vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <!-- Modal Header -->
            <div class="flex flex-col gap-2 border-b border-slate-200 bg-slate-50 px-6 py-3.5 sm:flex-row sm:items-center sm:justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        <i class="fas fa-receipt"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black uppercase text-slate-900 leading-tight">Invoice Receipt Preview</h3>
                        <p class="text-[11px] font-semibold text-slate-500">{{ $invoice->invoice_number }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:justify-end">
                    <button id="invoiceReceiptPrintButton" type="button" onclick="printInvoiceReceiptPreview()" disabled
                        class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i class="fas fa-print text-xs"></i>
                        <span>Print Document</span>
                    </button>
                    <button type="button" onclick="closeInvoiceReceiptModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Iframe Container -->
            <div class="relative flex-1 bg-slate-100 overflow-hidden">
                <div id="invoiceReceiptLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-slate-100">
                    <div class="rounded-2xl border border-slate-200 bg-white px-6 py-5 text-center shadow-sm">
                        <div class="mx-auto mb-3 h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-blue-600"></div>
                        <div class="text-xs font-black uppercase tracking-wider text-slate-800">Preparing Receipt Preview</div>
                    </div>
                </div>
                <iframe id="invoiceReceiptIframe" src="about:blank" class="h-full w-full border-0 bg-white"></iframe>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('admin.partials.image-gallery-modal')
    @include('admin.sales.partials.square-remind-modal')

</div>
@endsection

@push('scripts')
<script>
    function copyInvoiceText(text, btn) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-emerald-600"></i>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
            }, 1500);
        }).catch(() => {
            alert('Copied: ' + text);
        });
    }

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
