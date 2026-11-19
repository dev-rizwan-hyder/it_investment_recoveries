@extends('layouts.admin')

@section('content')
    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Order #{{ $order->order_number }}</h1>
                <p class="text-slate-500 mt-1">Order placed on {{ $order->formatPlacedAtDenver('M d, Y \a\t h:i A T') }}</p>
            </div>
            <a href="{{ route('admin.orders.index') }}"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl font-bold text-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Order Items --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 border-b border-slate-100 px-6 py-4">
                        <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                            <i class="fas fa-box text-blue-600"></i> Order Items
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr class="text-[11px] uppercase font-bold text-slate-600 tracking-wider">
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
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">
                                            {{ $item->device_type }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">
                                            {{ $item->brand_category }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">
                                            {{ $item->device_specs }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">
                                            {{ $item->operating_system }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-slate-600">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-slate-900 font-medium">
                                            {{ $item->location }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-slate-900 font-medium">
                                            ${{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-slate-900">
                                            ${{ number_format($item->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                            No items in this order
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Order Summary --}}
                    <div class="bg-slate-50 border-t border-slate-100 px-6 py-4">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Subtotal:</span>
                                <span class="text-slate-900 font-bold">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if((float) ($order->discount ?? 0) > 0)
                            <div class="flex justify-between items-center text-emerald-600 font-medium">
                                <span>Discount ({{ strtoupper($order->discount_code ?? '10%') }}):</span>
                                <span class="font-bold">-${{ number_format($order->discount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Tax:</span>
                                <span class="text-slate-900 font-bold">${{ number_format($order->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Shipping:</span>
                                <span class="text-slate-900 font-bold">${{ number_format($order->shipping, 2) }}</span>
                            </div>
                            <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                                <span class="text-slate-800 font-bold">Total:</span>
                                <span class="text-2xl font-bold text-emerald-600">${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fulfillment Details --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-600"></i> Fulfillment Details
                    </h2>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-bold text-slate-600">Type:</span> <span class="uppercase font-semibold">{{ $order->fulfillment_type ?? 'shipping' }}</span></p>
                        <p><span class="font-bold text-slate-600">{{ ($order->fulfillment_type ?? 'shipping') === 'pickup' ? 'Pickup Location' : 'Shipping Address' }}:</span> {{ $order->shipping_address ?? 'N/A' }}</p>
                        @if(($order->fulfillment_type ?? 'shipping') === 'pickup')
                            <p><span class="font-bold text-slate-600">Pickup Contact:</span> {{ $order->pickup_contact_name ?: 'N/A' }} {{ $order->pickup_contact_phone ? ' - '.$order->pickup_contact_phone : '' }}</p>
                            <p><span class="font-bold text-slate-600">Pickup Slot:</span> {{ $order->pickup_slot ?: 'N/A' }}</p>
                        @endif
                        @if(($order->fulfillment_type ?? 'shipping') === 'shipping' && is_array($order->shipping_selection_payload) && count($order->shipping_selection_payload))
                            <div class="pt-2 border-t border-slate-100">
                                <p class="font-bold text-slate-700 mb-2">Selected Shipping Services</p>
                                @php
                                    $shippingPayload = $order->shipping_selection_payload;
                                    $isSingleRate = isset($shippingPayload['provider']) || isset($shippingPayload['servicelevel']) || isset($shippingPayload['amount']);
                                @endphp

                                @if($isSingleRate)
                                    <div class="space-y-1 text-slate-700">
                                        <p><span class="font-semibold">Carrier:</span> {{ $shippingPayload['provider'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Service:</span> {{ $shippingPayload['servicelevel'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Amount:</span> {{ $shippingPayload['currency'] ?? 'USD' }} {{ number_format((float)($shippingPayload['amount'] ?? 0), 2) }}</p>
                                        <p><span class="font-semibold">Estimated Days:</span> {{ $shippingPayload['estimated_days'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Duration Terms:</span> {{ $shippingPayload['duration_terms'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Shipment ID:</span> {{ $shippingPayload['shipment_id'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Rate ID:</span> {{ $shippingPayload['id'] ?? 'N/A' }}</p>
                                    </div>
                                @else
                                    @foreach($shippingPayload as $key => $service)
                                        @if(is_array($service))
                                            <p class="text-slate-700">
                                                <span class="font-semibold">Item {{ $key }}:</span>
                                                {{ $service['provider'] ?? 'N/A' }} - {{ $service['servicelevel'] ?? 'N/A' }}
                                                ({{ $service['currency'] ?? 'USD' }} {{ number_format((float)($service['amount'] ?? 0), 2) }})
                                            </p>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Notes --}}
                @if($order->notes)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-blue-600"></i> Order Notes
                        </h2>
                        <p class="text-slate-700">{{ $order->notes }}</p>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Customer Information --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i> Customer
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-slate-500 text-xs uppercase font-bold">Name</p>
                            <p class="text-slate-900 font-medium">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs uppercase font-bold">Email</p>
                            <p class="text-slate-900 font-medium truncate">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs uppercase font-bold">Phone</p>
                            <p class="text-slate-900 font-medium">{{ $order->customer_phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Information --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-credit-card text-blue-600"></i> Payment
                    </h3>
                    @php
                        $paymentMethod = strtolower((string) ($order->payment_method ?? ''));
                        $paymentOptions = [
                            'cash' => 'Cash',
                            'cheque' => 'Cheque',
                            'square_card' => 'Square Card',
                        ];
                        $paymentStatusChoices = \App\Models\Order::paymentStatusChoices();
                    @endphp
                    <form action="{{ route('admin.orders.update-payment-method', $order->id) }}" method="POST" class="space-y-3 text-sm">
                        @csrf
                        @method('PUT')
                        <div>
                            <p class="text-slate-500 text-xs uppercase font-bold">Method</p>
                            <select name="payment_method" class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-lg bg-white text-slate-900 font-medium capitalize outline-none focus:ring-2 focus:ring-blue-500/20">
                                @foreach($paymentOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $paymentMethod === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold text-sm transition-all">
                            Update Payment Method
                        </button>
                    </form>

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <div class="mb-3">
                            <p class="text-slate-500 text-xs uppercase font-bold">Payment Status</p>
                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide {{ \App\Models\Order::paymentStatusBadgeClass($order->payment_status) }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </div>

                        <form action="{{ route('admin.orders.update-payment-status', $order->id) }}" method="POST" class="space-y-3 text-sm">
                            @csrf
                            @method('PUT')
                            <select name="payment_status" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-white text-slate-900 font-medium outline-none focus:ring-2 focus:ring-blue-500/20">
                                @foreach($paymentStatusChoices as $value => $label)
                                    <option value="{{ $value }}" {{ $order->payment_status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-sm transition-all">
                                Update Payment Status
                            </button>
                        </form>
                    </div>
                </div>

                 {{-- Order Status --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i> Status
                    </h3>
                    
                    @php
                        $statusChoices = \App\Models\Order::statusChoices();
                        $style = \App\Models\Order::statusBadgeClass($order->status);
                    @endphp
                    
                    <div class="mb-4">
                        <span class="px-4 py-2 rounded-lg text-sm font-bold uppercase {{ $style }}">
                            {{ $statusChoices[$order->status] ?? $order->status_label }}
                        </span>
                    </div>

                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        
                        <select name="status" class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                            @foreach($statusChoices as $status => $label)
                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold text-sm transition-all">
                            Update Status
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
@endsection
