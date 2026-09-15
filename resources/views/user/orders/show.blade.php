@extends('user.layouts.app')

@section('title', 'Order Details')

@section('content')
        <div class="max-w-8xl mx-auto px-8 py-10">
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
            @endphp

            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900">Order #{{ $order->order_number }}</h1>
                    <p class="mt-2 text-gray-600">Placed on {{ $order->formatPlacedAtDenver('F j, Y \a\t g:i A T') }}</p>
                </div>
                <a href="{{ route('user.orders.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to My Orders
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                        <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                            <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800">
                                <i class="fas fa-box text-blue-600"></i>Order Items
                            </h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[1100px]">
                                <thead class="border-b border-slate-100 bg-slate-50">
                                    <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                        <th class="px-6 py-3 text-left">Product</th>
                                        <th class="px-6 py-3 text-left">Brand & Model</th>
                                        <th class="px-6 py-3 text-left">Device Specs</th>
                                        <th class="px-6 py-3 text-left">Operating System</th>
                                        <th class="px-6 py-3 text-center">Qty</th>
                                        <th class="px-6 py-3 text-center">Location</th>
                                        <th class="px-6 py-3 text-right">Price</th>
                                        <th class="px-6 py-3 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($order->items as $item)
                                        <tr class="transition-colors hover:bg-slate-50">
                                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $item->device_type }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $item->brand_category }}</td>
                                            <td class="max-w-xs px-6 py-4 text-sm font-medium text-slate-900">{{ $item->device_specs }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $item->operating_system }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $item->quantity }}</td>
                                            <td class="px-6 py-4 text-center text-sm font-medium text-slate-900">{{ $item->location }}</td>
                                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-900">${{ number_format($item->unit_price ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 text-right text-sm font-bold text-slate-900">${{ number_format($item->total_price ?? 0, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-8 text-center text-slate-500">No items in this order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-slate-100 bg-slate-50 px-6 py-5">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span>Subtotal:</span>
                                    <span class="font-bold text-slate-900">${{ number_format($order->subtotal ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Tax:</span>
                                    <span class="font-bold text-slate-900">${{ number_format($order->tax, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Shipping:</span>
                                    <span class="font-bold text-slate-900">${{ number_format($order->shipping ?? 0, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200 pt-3 font-bold text-slate-800">
                                    <span>Total:</span>
                                    <span class="text-2xl text-emerald-600">${{ number_format($order->total ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-800">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>Fulfillment Details
                        </h2>
                        <div class="space-y-3 text-sm text-slate-700">
                            <p>
                                <span class="font-bold text-slate-600">Type:</span>
                                <span class="font-semibold uppercase">{{ $order->fulfillment_type ?? 'shipping' }}</span>
                            </p>

                            @if($isPickup)
                                <p>
                                    <span class="font-bold text-slate-600">Pickup Contact:</span>
                                    Denver Discount Computers
                                </p>
                                <p><span class="font-bold text-slate-600">Contact Phone:</span> 303-434-7465</p>
                                <p><span class="font-bold text-slate-600">Address</span> 5765 W 52nd Ave Denver Co 80212 USA</p>
                            @endif

                            @if(!$isPickup && is_array($shippingPayload) && count($shippingPayload))
                                <div class="border-t border-slate-100 pt-4">
                                    <p class="mb-3 font-bold text-slate-700">Selected Shipping Services</p>

                                    @if($isSingleRate)
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <div><span class="font-semibold">Carrier:</span> {{ $shippingPayload['provider'] ?? 'N/A' }}</div>
                                            <div><span class="font-semibold">Service:</span> {{ $shippingPayload['servicelevel'] ?? 'N/A' }}</div>
                                            <div><span class="font-semibold">Amount:</span> {{ $shippingPayload['currency'] ?? 'USD' }} {{ number_format((float) ($shippingPayload['amount'] ?? 0), 2) }}</div>
                                            <div><span class="font-semibold">Estimated Days:</span> {{ $shippingPayload['estimated_days'] ?? 'N/A' }}</div>
                                            <div><span class="font-semibold">Duration Terms:</span> {{ $shippingPayload['duration_terms'] ?? 'N/A' }}</div>
                                            <div><span class="font-semibold">Shipment ID:</span> {{ $shippingPayload['shipment_id'] ?? 'N/A' }}</div>
                                            <div class="sm:col-span-2"><span class="font-semibold">Rate ID:</span> {{ $shippingPayload['id'] ?? 'N/A' }}</div>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($shippingPayload as $key => $service)
                                                @if(is_array($service))
                                                    <p>
                                                        <span class="font-semibold">Item {{ $key }}:</span>
                                                        {{ $service['provider'] ?? 'N/A' }} - {{ $service['servicelevel'] ?? 'N/A' }}
                                                        ({{ $service['currency'] ?? 'USD' }} {{ number_format((float) ($service['amount'] ?? 0), 2) }})
                                                    </p>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-800">
                                <i class="fas fa-sticky-note text-blue-600"></i>Order Notes
                            </h2>
                            <p class="whitespace-pre-line text-slate-700">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <div>
                    <div class="sticky top-24 space-y-6">
                        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 flex items-center gap-2 font-bold text-slate-800">
                                <i class="fas fa-info-circle text-blue-600"></i>Status
                            </h3>
                            <span class="inline-block rounded-lg px-4 py-2 text-sm font-bold uppercase {{ \App\Models\Order::statusBadgeClass($order->status) }}">
                                {{ $order->status_label }}
                            </span>
                            <div class="mt-5 text-sm">
                                <p class="text-xs font-bold uppercase text-slate-500">Order Date</p>
                                <p class="mt-1 font-medium text-slate-900">{{ $order->formatPlacedAtDenver('F j, Y') }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 flex items-center gap-2 font-bold text-slate-800">
                                <i class="fas fa-user text-blue-600"></i>Customer
                            </h3>
                            <div class="space-y-4 text-sm">
                                <div>
                                    <p class="text-xs font-bold uppercase text-slate-500">Name</p>
                                    <p class="mt-1 font-medium text-slate-900">{{ $order->customer_name ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase text-slate-500">Email</p>
                                    <p class="mt-1 break-words font-medium text-slate-900">{{ $order->customer_email ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase text-slate-500">Phone</p>
                                    <p class="mt-1 font-medium text-slate-900">{{ $order->customer_phone ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 flex items-center gap-2 font-bold text-slate-800">
                                <i class="fas fa-credit-card text-blue-600"></i>Payment
                            </h3>
                            <div class="space-y-4">
                                <div>
                                <p class="text-xs font-bold uppercase text-slate-500">Method</p>
                                <p class="mt-1 font-medium text-slate-900">{{ $paymentLabel }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase text-slate-500">Status</p>
                                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide {{ \App\Models\Order::paymentStatusBadgeClass($order->payment_status) }}">
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
