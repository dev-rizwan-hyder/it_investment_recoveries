@extends('user.layouts.app')

@section('title', 'Order Details - #' . $order->order_number)

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 10px 30px rgba(15, 23, 42, 0.04);
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    @php
        $isPickup = ($order->fulfillment_type ?? 'shipping') === 'pickup';
        $shippingPayload = $order->shipping_selection_payload;
        $isSingleRate = is_array($shippingPayload)
            && (isset($shippingPayload['provider']) || isset($shippingPayload['servicelevel']) || isset($shippingPayload['amount']));
        $paymentLabels = [
            'pay_on_pickup' => 'Payment upon pickup',
            'square' => 'Square card',
            'square_card' => 'Square card',
            'card' => 'Card',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'cash_on_delivery' => 'Cash on delivery',
        ];
        $paymentMethod = strtolower((string) ($order->payment_method ?? ''));
        $paymentLabel = $paymentLabels[$paymentMethod] ?? ucwords(str_replace('_', ' ', $paymentMethod ?: 'N/A'));

        $statusLower = strtolower($order->status ?? 'pending');
        $stepIndex = match($statusLower) {
            'pending', 'awaiting_payment' => 1,
            'processing', 'paid' => 2,
            'shipped', 'out_for_delivery' => 3,
            'completed', 'delivered' => 4,
            'cancelled' => 0,
            default => 2
        };
    @endphp

    {{-- Breadcrumb & Top Bar --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.orders.index') }}"
               class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200/80 bg-white text-slate-600 shadow-sm transition hover:border-amber-300 hover:bg-slate-50 hover:text-amber-600">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Order Summary</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-500">{{ $order->formatPlacedAtDenver('M d, Y \a\t g:i A T') }}</span>
                </div>
                <div class="flex items-center gap-3 mt-0.5">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Order #{{ $order->order_number }}</h1>
                    <button onclick="copyToClipboard('{{ $order->order_number }}', 'Order number copied!')"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 hover:bg-amber-50 hover:text-amber-600 border border-slate-200/80 transition" title="Copy Order Number">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copy</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-2xs transition">
                <i class="fa-solid fa-print text-slate-400"></i> Print Invoice
            </button>
            <span class="px-4 py-2 rounded-2xl text-xs font-black uppercase tracking-wider border shadow-2xs {{ \App\Models\Order::statusBadgeClass($order->status) }}">
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    {{-- Order Progress Timeline --}}
    @if($stepIndex > 0)
        <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 mb-8">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-6">Order Status Progress</h3>
            <div class="relative flex items-center justify-between max-w-4xl mx-auto px-4">
                {{-- Progress Bar Line --}}
                <div class="absolute left-10 right-10 top-5 h-1 bg-slate-100 -z-0">
                    <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-500 transition-all duration-500" 
                         style="width: {{ (($stepIndex - 1) / 3) * 100 }}%;"></div>
                </div>

                {{-- Step 1 --}}
                <div class="relative z-10 flex flex-col items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $stepIndex >= 1 ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 text-slate-400' }} font-bold text-sm transition">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <span class="text-xs font-black text-slate-900 mt-2">Order Placed</span>
                    <span class="text-[10px] font-semibold text-slate-400">Received</span>
                </div>

                {{-- Step 2 --}}
                <div class="relative z-10 flex flex-col items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $stepIndex >= 2 ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 text-slate-400' }} font-bold text-sm transition">
                        <i class="fa-solid fa-gears"></i>
                    </div>
                    <span class="text-xs font-black {{ $stepIndex >= 2 ? 'text-slate-900' : 'text-slate-400' }} mt-2">Processing</span>
                    <span class="text-[10px] font-semibold text-slate-400">Preparing Items</span>
                </div>

                {{-- Step 3 --}}
                <div class="relative z-10 flex flex-col items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $stepIndex >= 3 ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 text-slate-400' }} font-bold text-sm transition">
                        <i class="fa-solid {{ $isPickup ? 'fa-store' : 'fa-truck-fast' }}"></i>
                    </div>
                    <span class="text-xs font-black {{ $stepIndex >= 3 ? 'text-slate-900' : 'text-slate-400' }} mt-2">{{ $isPickup ? 'Ready for Pickup' : 'Dispatched' }}</span>
                    <span class="text-[10px] font-semibold text-slate-400">{{ $isPickup ? 'In Store' : 'In Transit' }}</span>
                </div>

                {{-- Step 4 --}}
                <div class="relative z-10 flex flex-col items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $stepIndex >= 4 ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400' }} font-bold text-sm transition">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <span class="text-xs font-black {{ $stepIndex >= 4 ? 'text-emerald-700' : 'text-slate-400' }} mt-2">Completed</span>
                    <span class="text-[10px] font-semibold text-slate-400">Delivered</span>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Left Column (2 cols) - Order Items & Summary --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Order Items Table Card --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 p-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 font-bold">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Purchased Hardware / Services</h2>
                            <p class="text-xs font-medium text-slate-400">Items included in this invoice</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $order->items->count() }} {{ Str::plural('Item', $order->items->count()) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] divide-y divide-slate-100">
                        <thead class="bg-slate-900 text-white">
                            <tr class="text-[10px] font-extrabold uppercase tracking-wider">
                                <th class="px-6 py-3.5 text-left">Product / Device</th>
                                <th class="px-6 py-3.5 text-left">Brand & Specs</th>
                                <th class="px-6 py-3.5 text-left">OS</th>
                                <th class="px-6 py-3.5 text-center">Qty</th>
                                <th class="px-6 py-3.5 text-right">Unit Price</th>
                                <th class="px-6 py-3.5 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($order->items as $item)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-extrabold text-slate-900 text-sm">{{ $item->device_type }}</p>
                                        <p class="text-[11px] font-bold text-slate-400">Location: {{ $item->location ?: 'Standard' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-800 text-xs">{{ $item->brand_category }}</p>
                                        @if($item->device_specs)
                                            <p class="text-[11px] font-medium text-slate-500 mt-0.5 line-clamp-1" title="{{ $item->device_specs }}">{{ $item->device_specs }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                        {{ $item->operating_system ?: 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-xl bg-slate-100 font-extrabold text-slate-800 text-xs">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-700">
                                        ${{ number_format($item->unit_price ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-slate-900">
                                        ${{ number_format($item->total_price ?? 0, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">No items found in this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Price Calculation Breakdown --}}
                <div class="border-t border-slate-200/80 bg-slate-50/60 p-6">
                    <div class="max-w-xs ml-auto space-y-2.5 text-xs font-semibold">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Subtotal:</span>
                            <span class="font-extrabold text-slate-900">${{ number_format($order->subtotal ?? 0, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Tax:</span>
                            <span class="font-extrabold text-slate-900">${{ number_format($order->tax ?? 0, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Shipping Fee:</span>
                            <span class="font-extrabold text-slate-900">${{ number_format($order->shipping ?? 0, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200/80 pt-3 text-sm font-black text-slate-900">
                            <span>Total Amount:</span>
                            <span class="text-xl text-emerald-600 font-black">${{ number_format($order->total ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fulfillment & Shipping Details --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 font-bold">
                        <i class="fa-solid {{ $isPickup ? 'fa-store' : 'fa-truck-ramp-box' }}"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Fulfillment Method</h2>
                        <p class="text-xs font-medium text-slate-400">Shipping or store pickup details</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Type:</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-800 font-extrabold text-xs uppercase">
                            <i class="fa-solid {{ $isPickup ? 'fa-store text-amber-600' : 'fa-truck text-blue-600' }}"></i>
                            {{ $order->fulfillment_type ?? 'shipping' }}
                        </span>
                    </div>

                    @if($isPickup)
                        <div class="p-5 rounded-2xl border border-amber-200/80 bg-amber-50/40 space-y-2 text-xs">
                            <p class="font-extrabold text-amber-900 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-amber-600"></i> Pickup Location: Denver Discount Computers
                            </p>
                            <p class="text-amber-800 font-medium"><strong>Address:</strong> 5765 W 52nd Ave, Denver, CO 80212, USA</p>
                            <p class="text-amber-800 font-medium"><strong>Phone:</strong> 303-434-7465</p>
                            <p class="text-amber-700/80 text-[11px] font-semibold mt-2">Please present your Order #{{ $order->order_number }} when picking up your items.</p>
                        </div>
                    @endif

                    @if(!$isPickup && is_array($shippingPayload) && count($shippingPayload))
                        <div class="border-t border-slate-100 pt-4">
                            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3">Selected Carrier Rate Details</h4>
                            @if($isSingleRate)
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Carrier</span>
                                        <span class="text-xs font-extrabold text-slate-900">{{ $shippingPayload['provider'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Service Level</span>
                                        <span class="text-xs font-extrabold text-slate-900">{{ $shippingPayload['servicelevel'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Est. Delivery</span>
                                        <span class="text-xs font-extrabold text-slate-900">{{ $shippingPayload['estimated_days'] ? $shippingPayload['estimated_days'] . ' Days' : 'N/A' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($shippingPayload as $key => $service)
                                        @if(is_array($service))
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs font-medium">
                                                <strong class="text-slate-800">Item {{ $key }}:</strong> {{ $service['provider'] ?? 'N/A' }} - {{ $service['servicelevel'] ?? 'N/A' }} ({{ $service['currency'] ?? 'USD' }} ${{ number_format((float) ($service['amount'] ?? 0), 2) }})
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($order->notes)
                <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Order Notes</h3>
                    <p class="whitespace-pre-line text-slate-700 text-sm font-medium bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        {{ $order->notes }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Right Column (1 col) - Customer & Payment Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Customer Card --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-600 font-bold">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-950">Customer Contact</h3>
                </div>
                <div class="space-y-4 text-xs font-medium">
                    <div>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Full Name</span>
                        <p class="mt-0.5 text-sm font-black text-slate-900">{{ $order->customer_name ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Email Address</span>
                        <p class="mt-0.5 text-slate-800 font-bold break-all">{{ $order->customer_email ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Phone Number</span>
                        <p class="mt-0.5 text-slate-800 font-bold">{{ $order->customer_phone ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Card --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-950">Payment Status</h3>
                </div>
                <div class="space-y-4 text-xs font-medium">
                    <div>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Payment Method</span>
                        <p class="mt-0.5 text-sm font-extrabold text-slate-900">{{ $paymentLabel }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Status</span>
                        <div class="mt-1">
                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-extrabold uppercase tracking-wide border shadow-2xs {{ \App\Models\Order::paymentStatusBadgeClass($order->payment_status) }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

