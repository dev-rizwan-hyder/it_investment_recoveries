@extends('layouts.admin')

@section('content')
    @php
        $statusChoices = \App\Models\Order::statusChoices();
        $statusLabel = $statusChoices[$order->status] ?? \App\Models\Order::statusLabel($order->status);
        $statusBadgeClass = \App\Models\Order::statusBadgeClass($order->status);
        $paymentStatusBadgeClass = \App\Models\Order::paymentStatusBadgeClass($order->payment_status);

        $paymentMethodRaw = strtolower((string) ($order->payment_method ?? ''));
        $paymentMethodMap = [
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'square_card' => 'Square Card',
            'square_pos' => 'Square POS',
            'online' => 'Online',
            'card' => 'Card',
        ];
        $paymentMethodLabel = $paymentMethodMap[$paymentMethodRaw] ?? ($order->payment_method ? ucwords(str_replace('_', ' ', $order->payment_method)) : 'N/A');

        $totalQty = $order->items->sum('quantity');
        $fulfillmentType = $order->fulfillment_type ?? 'shipping';
        $customerInitial = strtoupper(mb_substr(trim((string) ($order->customer_name ?: 'C')), 0, 1));
        $salesInvoice = $salesInvoice ?? $order->sales_invoice;
    @endphp

    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500 space-y-6">

        {{-- Top Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <nav class="flex items-center text-[10px] font-black uppercase tracking-[0.25em] text-blue-600 mb-2">
                        <a href="{{ route('admin.orders.index') }}" class="hover:text-blue-800 transition-colors">Orders</a>
                        <span class="mx-2 text-slate-300">/</span>
                        <span class="text-slate-500 tracking-widest">Order Details</span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Order #{{ $order->order_number }}
                        </h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $statusBadgeClass }}">
                            {{ $statusLabel }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $paymentStatusBadgeClass }}">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                    <p class="text-slate-500 text-sm mt-1.5 flex items-center gap-2">
                        <i class="far fa-clock text-slate-400"></i>
                        Placed on {{ $order->formatPlacedAtDenver('M d, Y \a\t h:i A T') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    @if($salesInvoice)
                        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200/80 px-3 py-1.5 rounded-xl shadow-sm">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white text-xs shadow-sm">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </span>
                            <div class="text-left pr-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-black text-slate-800 font-mono">{{ $salesInvoice->invoice_number }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $salesInvoice->isPaid() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                                        {{ $salesInvoice->isPaid() ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </div>
                                <p class="text-[11px] font-bold text-slate-500">${{ number_format($salesInvoice->total, 2) }}</p>
                            </div>
                            <a href="{{ route('admin.sales.invoices.show', $salesInvoice->id) }}"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg font-bold text-xs flex items-center gap-1 transition-all shadow-sm">
                                View <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    @else
                        <a href="{{ route('admin.sales.invoices.create', ['order_id' => $order->id]) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-md shadow-blue-500/20 transition-all">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Create Invoice
                        </a>
                    @endif
                    <a href="{{ route('admin.orders.receipt', $order->id) }}" target="_blank" rel="noopener"
                        class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-sm transition-all">
                        <i class="fas fa-print mr-2 text-slate-400"></i> Receipt PDF
                    </a>
                    <a href="{{ route('admin.orders.index') }}"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all">
                        <i class="fas fa-arrow-left mr-2 text-slate-500"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>

        {{-- Already Created Invoice Banner --}}
        @if($salesInvoice)
            <div class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-teal-50/40 to-white p-4 sm:p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-500/20 text-lg">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-wider text-emerald-800">Sales Invoice Already Created</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $salesInvoice->isPaid() ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                                {{ $salesInvoice->isPaid() ? 'PAID' : 'UNPAID' }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-baseline gap-2 mt-0.5">
                            <span class="font-extrabold text-slate-900 text-base font-mono">{{ $salesInvoice->invoice_number }}</span>
                            <span class="text-xs text-slate-500">&bull; Total: <strong class="text-slate-800">${{ number_format($salesInvoice->total, 2) }}</strong></span>
                            @if($salesInvoice->service_date)
                                <span class="text-xs text-slate-500">&bull; Issued: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($salesInvoice->service_date)->format('M d, Y') }}</strong></span>
                            @endif
                            <span class="text-xs text-slate-500">&bull; Payment Method: <strong class="text-slate-800 capitalize">{{ $salesInvoice->payment_method_label }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                    <a href="{{ route('admin.sales.invoices.show', $salesInvoice->id) }}"
                        class="inline-flex items-center rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-bold shadow-sm transition-all">
                        <i class="fas fa-eye mr-1.5 text-[11px]"></i> View Full Invoice
                    </a>
                    <a href="{{ route('admin.sales.invoices.edit', $salesInvoice->id) }}"
                        class="inline-flex items-center rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-3.5 py-2 text-xs font-bold shadow-sm transition-all">
                        <i class="fas fa-edit mr-1 text-[11px]"></i> Edit
                    </a>
                </div>
            </div>
        @endif

        {{-- Summary Strip --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-dollar-sign text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Order Total</p>
                    <p class="text-xl font-extrabold text-slate-900 mt-0.5">${{ number_format($order->total, 2) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-cubes text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Quantity</p>
                    <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $totalQty }} <span class="text-xs font-semibold text-slate-400">({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})</span></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas {{ $fulfillmentType === 'pickup' ? 'fa-store' : 'fa-truck' }} text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Fulfillment</p>
                    <p class="text-xl font-extrabold text-slate-900 mt-0.5 capitalize">{{ $fulfillmentType }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-credit-card text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Payment Method</p>
                    <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $paymentMethodLabel }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Order Items --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50/70 border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                        <h2 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                <i class="fas fa-box"></i>
                            </span>
                            Order Items
                        </h2>
                        <span class="text-xs font-bold text-slate-500 bg-white px-3 py-1 rounded-full border border-slate-200">
                            {{ $totalQty }} {{ Str::plural('Unit', $totalQty) }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50/50 border-b border-slate-100">
                                <tr class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                                    <th class="px-6 py-3.5">Product</th>
                                    <th class="px-6 py-3.5">Brand &amp; Model</th>
                                    <th class="px-6 py-3.5">Specs &amp; OS</th>
                                    <th class="px-6 py-3.5 text-center">Location</th>
                                    <th class="px-6 py-3.5 text-center">Qty</th>
                                    <th class="px-6 py-3.5 text-right">Unit Price</th>
                                    <th class="px-6 py-3.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($order->items as $item)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-slate-900">{{ $item->product_name ?: $item->device_type }}</div>
                                            @if($item->device_type && $item->product_name && $item->device_type !== $item->product_name)
                                                <div class="text-xs text-slate-500 mt-0.5">{{ $item->device_type }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-700 font-medium">
                                            {{ $item->brand_category }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-700 font-medium">{{ $item->device_specs }}</div>
                                            @if($item->operating_system && $item->operating_system !== 'N/A' && $item->operating_system !== $item->product_name)
                                                <div class="text-xs text-slate-400 mt-0.5">{{ $item->operating_system }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                                {{ $item->location }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full bg-blue-50 text-blue-700 font-extrabold text-xs border border-blue-100">
                                                {{ $item->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-slate-700 font-medium">
                                            ${{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-extrabold text-slate-900">
                                            ${{ number_format($item->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            <i class="fas fa-box-open text-3xl mb-2 block text-slate-300"></i>
                                            No items in this order
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Order Financial Summary --}}
                    <div class="bg-slate-50/70 border-t border-slate-100 px-6 py-5">
                        <div class="ml-auto max-w-sm space-y-2.5 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Subtotal</span>
                                <span class="text-slate-800 font-bold">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if((float) ($order->discount ?? 0) > 0)
                                <div class="flex justify-between items-center text-emerald-600 font-medium">
                                    <span>Discount ({{ strtoupper($order->discount_code ?? '10%') }})</span>
                                    <span class="font-bold">-${{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Tax</span>
                                <span class="text-slate-800 font-bold">${{ number_format($order->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Shipping</span>
                                <span class="text-slate-800 font-bold">${{ number_format($order->shipping, 2) }}</span>
                            </div>
                            <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                                <span class="text-slate-900 font-extrabold text-base">Grand Total</span>
                                <span class="text-2xl font-black text-emerald-600">${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fulfillment Details --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50/70 border-b border-slate-100 px-6 py-4">
                        <h2 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            Fulfillment Details
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Fulfillment Type</p>
                                <p class="font-bold text-slate-900 uppercase">{{ $fulfillmentType }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                                    {{ $fulfillmentType === 'pickup' ? 'Pickup Location' : 'Shipping Address' }}
                                </p>
                                <p class="font-semibold text-slate-800">{{ $order->shipping_address ?? ($order->pickup_location ?? 'N/A') }}</p>
                            </div>

                            @if($fulfillmentType === 'pickup')
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pickup Contact</p>
                                    <p class="font-semibold text-slate-800">
                                        {{ $order->pickup_contact_name ?: 'N/A' }}
                                        @if($order->pickup_contact_phone)
                                            <span class="text-slate-500">&bull; {{ $order->pickup_contact_phone }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pickup Slot</p>
                                    <p class="font-semibold text-slate-800">{{ $order->pickup_slot ?: 'N/A' }}</p>
                                </div>
                            @endif

                            @if($order->tracking_number || $order->carrier)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tracking Information</p>
                                    <div class="flex flex-wrap items-center gap-4">
                                        <span class="font-semibold text-slate-800">Carrier: {{ $order->carrier ?? 'N/A' }}</span>
                                        <span class="font-mono font-bold text-blue-600">Tracking #: {{ $order->tracking_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($fulfillmentType === 'shipping' && is_array($order->shipping_selection_payload) && count($order->shipping_selection_payload))
                            <div class="mt-5 pt-5 border-t border-slate-100">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Selected Shipping Services</p>
                                @php
                                    $shippingPayload = $order->shipping_selection_payload;
                                    $isSingleRate = isset($shippingPayload['provider']) || isset($shippingPayload['servicelevel']) || isset($shippingPayload['amount']);
                                @endphp

                                @if($isSingleRate)
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div>
                                            <span class="text-xs text-slate-400 block">Carrier</span>
                                            <span class="font-bold text-slate-800">{{ $shippingPayload['provider'] ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-400 block">Service</span>
                                            <span class="font-bold text-slate-800">{{ $shippingPayload['servicelevel'] ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-400 block">Rate</span>
                                            <span class="font-bold text-emerald-600">{{ $shippingPayload['currency'] ?? 'USD' }} {{ number_format((float)($shippingPayload['amount'] ?? 0), 2) }}</span>
                                        </div>
                                        @if(!empty($shippingPayload['estimated_days']))
                                            <div>
                                                <span class="text-xs text-slate-400 block">Estimated Days</span>
                                                <span class="font-semibold text-slate-700">{{ $shippingPayload['estimated_days'] }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($shippingPayload['shipment_id']))
                                            <div>
                                                <span class="text-xs text-slate-400 block">Shipment ID</span>
                                                <span class="font-mono text-xs text-slate-700">{{ $shippingPayload['shipment_id'] }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($shippingPayload['id']))
                                            <div>
                                                <span class="text-xs text-slate-400 block">Rate ID</span>
                                                <span class="font-mono text-xs text-slate-700">{{ $shippingPayload['id'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="space-y-2 text-sm">
                                        @foreach($shippingPayload as $key => $service)
                                            @if(is_array($service))
                                                <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                    <span class="font-semibold text-slate-700">Item {{ $key }}: {{ $service['provider'] ?? 'N/A' }} - {{ $service['servicelevel'] ?? 'N/A' }}</span>
                                                    <span class="font-bold text-emerald-600">{{ $service['currency'] ?? 'USD' }} {{ number_format((float)($service['amount'] ?? 0), 2) }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Notes --}}
                @if($order->notes)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="bg-slate-50/70 border-b border-slate-100 px-6 py-4">
                            <h2 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-sticky-note"></i>
                                </span>
                                Order Notes
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-slate-700 text-sm whitespace-pre-line">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar (View Only) --}}
            <div class="space-y-6">

                {{-- Customer Information --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50/70 border-b border-slate-100 px-6 py-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                <i class="fas fa-user"></i>
                            </span>
                            Customer
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                            <div class="w-11 h-11 rounded-full bg-blue-600 text-white font-black flex items-center justify-center text-base shrink-0">
                                {{ $customerInitial }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-900 font-bold text-base truncate">{{ $order->customer_name ?: 'Walk-in Customer' }}</p>
                                <p class="text-xs text-slate-400">Customer Account</p>
                            </div>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-slate-400 text-[11px] uppercase font-bold tracking-wider">Email</p>
                                @if($order->customer_email)
                                    <a href="mailto:{{ $order->customer_email }}" class="text-blue-600 hover:underline font-medium truncate block mt-0.5">{{ $order->customer_email }}</a>
                                @else
                                    <p class="text-slate-500 font-medium mt-0.5">N/A</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-slate-400 text-[11px] uppercase font-bold tracking-wider">Phone</p>
                                @if($order->customer_phone)
                                    <a href="tel:{{ $order->customer_phone }}" class="text-slate-900 hover:text-blue-600 font-medium block mt-0.5">{{ $order->customer_phone }}</a>
                                @else
                                    <p class="text-slate-500 font-medium mt-0.5">N/A</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales Invoice Card (if created) --}}
                @if($salesInvoice)
                    <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden">
                        <div class="bg-emerald-50/70 border-b border-emerald-100 px-6 py-4 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </span>
                                Sales Invoice
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider {{ $salesInvoice->isPaid() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                                {{ $salesInvoice->isPaid() ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>
                        <div class="p-6 space-y-3.5 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Invoice #</span>
                                <a href="{{ route('admin.sales.invoices.show', $salesInvoice->id) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $salesInvoice->invoice_number }}
                                </a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Invoice Total</span>
                                <span class="font-extrabold text-slate-900">${{ number_format($salesInvoice->total, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Payment Method</span>
                                <span class="font-semibold text-slate-700 capitalize">{{ $salesInvoice->payment_method_label }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Issued Date</span>
                                <span class="font-medium text-slate-700">{{ $salesInvoice->service_date ? \Carbon\Carbon::parse($salesInvoice->service_date)->format('M d, Y') : ($salesInvoice->created_at ? $salesInvoice->created_at->format('M d, Y') : 'N/A') }}</span>
                            </div>
                            <div class="pt-3 border-t border-slate-100 flex gap-2">
                                <a href="{{ route('admin.sales.invoices.show', $salesInvoice->id) }}" class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-3 rounded-xl text-xs transition-colors shadow-sm">
                                    <i class="fas fa-eye mr-1"></i> View Full
                                </a>
                                <a href="{{ route('admin.sales.invoices.edit', $salesInvoice->id) }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-3 rounded-xl text-xs transition-colors">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Payment Information (View Only) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50/70 border-b border-slate-100 px-6 py-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            Payment
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 text-xs uppercase font-bold tracking-wider">Method</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold text-xs">
                                {{ $paymentMethodLabel }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <span class="text-slate-500 text-xs uppercase font-bold tracking-wider">Payment Status</span>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide {{ $paymentStatusBadgeClass }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </div>

                        @if($order->payment_id)
                            <div class="pt-3 border-t border-slate-100">
                                <p class="text-slate-400 text-[11px] uppercase font-bold tracking-wider">Payment Reference</p>
                                <p class="text-slate-700 font-mono text-xs mt-1 break-all">{{ $order->payment_id }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Status (View Only) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50/70 border-b border-slate-100 px-6 py-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            Status
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 text-xs uppercase font-bold tracking-wider">Order Status</span>
                            <span class="inline-flex px-3.5 py-1.5 rounded-lg text-xs font-black uppercase tracking-wide {{ $statusBadgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                            <div class="flex justify-between">
                                <span>Placed</span>
                                <span class="font-semibold text-slate-700">{{ $order->formatPlacedAtDenver('M d, Y h:i A') }}</span>
                            </div>
                            @if($order->updated_at)
                                <div class="flex justify-between">
                                    <span>Last Updated</span>
                                    <span class="font-semibold text-slate-700">{{ $order->updated_at->setTimezone(\App\Models\Order::DENVER_TIMEZONE)->format('M d, Y h:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
