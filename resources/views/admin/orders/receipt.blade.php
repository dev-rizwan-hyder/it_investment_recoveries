<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.45;
            background: #ffffff;
        }

        .sheet {
            width: 100%;
            background: #ffffff;
            border: 1px solid #d9e2ec;
            border-top: 6px solid #0f766e;
            padding: 24px;
        }

        .topbar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .topbar td {
            vertical-align: top;
            padding: 0;
        }

        .brand-lockup {
            width: 58%;
            padding-top: 1px;
        }

        .brand-copy {
            display: block;
        }

        .brand-copy h1 {
            margin: 0 0 4px;
            font-size: 21px;
            line-height: 1.05;
            color: #0f172a;
        }

        .brand-copy p {
            margin: 0;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: bold;
        }

        .receipt-meta {
            width: 42%;
            text-align: right;
        }

        .document-title {
            margin: 0;
            color: #0f766e;
            font-size: 27px;
            line-height: 0.95;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .receipt-number {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: bold;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .status-completed {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #166534;
        }

        .status-cancelled {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .status-shipped {
            background: #e0e7ff;
            border-color: #c7d2fe;
            color: #3730a3;
        }

        .status-ready-for-pickup,
        .status-ready-for-shipment {
            background: #dbeafe;
            border-color: #bfdbfe;
            color: #1e40af;
        }

        .status-in-store-reserved {
            background: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }

        .status-in-store-purchased {
            background: #e0f2fe;
            border-color: #bae6fd;
            color: #075985;
        }

        .status-online-purchased {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #166534;
        }

        .receipt-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border-top: 1px solid #d9e2ec;
            border-bottom: 1px solid #d9e2ec;
        }

        .receipt-info td {
            width: 25%;
            padding: 9px 12px;
            border-right: 1px solid #e5edf5;
            background: #fbfdff;
            vertical-align: middle;
        }

        .receipt-info-label {
            margin-bottom: 2px;
            color: #64748b;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .receipt-info-value {
            color: #0f172a;
            font-size: 10px;
            font-weight: bold;
        }

        .summary-strip {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .summary-strip td {
            width: 20%;
            padding: 10px 12px;
            border: 1px solid #dbe3ee;
            border-left: none;
            background: #f8fafc;
            vertical-align: top;
        }

        .summary-strip td:first-child {
            border-left: 1px solid #dbe3ee;
        }

        .summary-label {
            margin-bottom: 3px;
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .summary-value {
            color: #0f172a;
            font-size: 14px;
            font-weight: bold;
        }

        .panel-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 16px;
        }

        .panel {
            width: 33.33%;
            padding: 13px 14px;
            border: 1px solid #dbe3ee;
            vertical-align: top;
            background: #ffffff;
        }

        .panel + .panel {
            border-left: none;
        }

        .section-title {
            margin: 0 0 9px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5edf5;
            color: #0f172a;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .field {
            margin-bottom: 8px;
        }

        .label {
            margin-bottom: 1px;
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .value {
            color: #111827;
            font-size: 11px;
            word-break: break-word;
        }

        .muted {
            color: #64748b;
        }

        .strong {
            font-weight: bold;
        }

        .items-title {
            margin: 2px 0 8px;
            color: #0f172a;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .items-table th {
            padding: 9px 8px;
            border: 1px solid #dbe3ee;
            background: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .items-table td {
            padding: 10px 8px;
            border: 1px solid #dbe3ee;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) td {
            background: #fbfdff;
        }

        .item-name {
            margin-bottom: 3px;
            color: #0f172a;
            font-weight: bold;
        }

        .item-meta {
            color: #64748b;
            font-size: 10px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .closing-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 2px;
        }

        .closing-grid td {
            vertical-align: top;
        }

        .service-panel {
            width: 58%;
            padding-right: 16px;
        }

        .service-box {
            min-height: 112px;
            padding: 13px 14px;
            border: 1px solid #dbe3ee;
            background: #f8fafc;
        }

        .totals-panel {
            width: 42%;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e5edf5;
            color: #334155;
        }

        .totals-table .grand td {
            padding-top: 11px;
            border-bottom: none;
            color: #0f172a;
            font-size: 16px;
            font-weight: bold;
        }

        .grand-total {
            color: #0f766e;
            font-size: 18px;
        }

        .note-box {
            margin-top: 11px;
            padding: 10px 12px;
            border: 1px dashed #b6c6d8;
            background: #ffffff;
            color: #475569;
            font-size: 10px;
        }

        .footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #dbe3ee;
            color: #64748b;
            font-size: 10px;
            text-align: center;
        }

        @media screen {
            body {
                background: #e8eef5;
                padding: 22px;
            }

            .sheet {
                max-width: 980px;
                margin: 0 auto;
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.14);
            }
        }

        @media print {
            body {
                background: #ffffff;
            }

            .sheet {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    @php
        $placedAt = $order->placedAtDenver();
        $generatedAt = now(\App\Models\Order::DENVER_TIMEZONE);
        $shippingPayload = $order->shipping_selection_payload;
        $singleRate = is_array($shippingPayload) && (isset($shippingPayload['provider']) || isset($shippingPayload['servicelevel']) || isset($shippingPayload['amount']));
        $itemsCount = $order->items->sum('quantity');
        $statusSlug = preg_replace('/[^a-z0-9-]+/', '-', str_replace('_', '-', strtolower($order->status ?? 'unknown')));
        $paymentMethod = $order->payment_method ? ucwords(str_replace('_', ' ', $order->payment_method)) : 'N/A';
        $fulfillmentType = $order->fulfillment_type ?: 'shipping';
        $fulfillmentLabel = ucwords(str_replace('_', ' ', $fulfillmentType));
    @endphp

    <div class="sheet">
        <table class="topbar">
            <tr>
                <td class="brand-lockup">
                    <span class="brand-copy">
                        <h1>IT Investment Recoveries</h1>
                        <p>Official Order Receipt</p>
                    </span>
                </td>
                <td class="receipt-meta">
                    <div class="document-title">Receipt</div>
                    <div class="receipt-number">#{{ $order->order_number }}</div>
                </td>
            </tr>
        </table>

        <table class="receipt-info">
            <tr>
                <td>
                    <div class="receipt-info-label">Status</div>
                    <div class="receipt-info-value"><span class="status-pill status-{{ $statusSlug }}">{{ $order->status_label }}</span></div>
                </td>
                <td>
                    <div class="receipt-info-label">Fulfillment</div>
                    <div class="receipt-info-value">{{ $fulfillmentLabel }}</div>
                </td>
                <td>
                    <div class="receipt-info-label">Order Placed</div>
                    <div class="receipt-info-value">{{ $placedAt ? $placedAt->format('M d, Y h:i A T') : 'N/A' }}</div>
                </td>
                <td style="border-right: none;">
                    <div class="receipt-info-label">Receipt Generated</div>
                    <div class="receipt-info-value">{{ $generatedAt->format('M d, Y h:i A T') }}</div>
                </td>
            </tr>
        </table>

        <table class="summary-strip">
            <tr>
                <td>
                    <div class="summary-label">Items</div>
                    <div class="summary-value">{{ $itemsCount }}</div>
                </td>
                <td>
                    <div class="summary-label">Subtotal</div>
                    <div class="summary-value">USD {{ number_format((float) $order->subtotal, 2) }}</div>
                </td>
                @if((float) ($order->discount ?? 0) > 0)
                <td>
                    <div class="summary-label">Discount ({{ strtoupper($order->discount_code ?? '10%') }})</div>
                    <div class="summary-value" style="color: #059669;">-USD {{ number_format((float) $order->discount, 2) }}</div>
                </td>
                @endif
                <td>
                    <div class="summary-label">Tax</div>
                    <div class="summary-value">USD {{ number_format((float) $order->tax, 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Shipping</div>
                    <div class="summary-value">USD {{ number_format((float) $order->shipping, 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Grand Total</div>
                    <div class="summary-value">USD {{ number_format((float) $order->total, 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="panel-grid">
            <tr>
                <td class="panel">
                    <h2 class="section-title">Bill To</h2>
                    <div class="field">
                        <div class="label">Customer</div>
                        <div class="value strong">{{ $order->customer_name ?: 'N/A' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Email</div>
                        <div class="value">{{ $order->customer_email ?: 'N/A' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Phone</div>
                        <div class="value">{{ $order->customer_phone ?: 'N/A' }}</div>
                    </div>
                </td>
                <td class="panel">
                    <h2 class="section-title">Fulfillment</h2>
                    <div class="field">
                        <div class="label">Type</div>
                        <div class="value strong">{{ $fulfillmentLabel }}</div>
                    </div>
                    @if($fulfillmentType === 'pickup')
                        <div class="field">
                            <div class="label">Pickup Contact</div>
                            <div class="value">{{ $order->pickup_contact_name ?: 'N/A' }}{{ $order->pickup_contact_phone ? ' - ' . $order->pickup_contact_phone : '' }}</div>
                        </div>
                        <div class="field">
                            <div class="label">Pickup Slot</div>
                            <div class="value">{{ $order->pickup_slot ?: 'N/A' }}</div>
                        </div>
                    @else
                        <div class="field">
                            <div class="label">Shipping Address</div>
                            <div class="value">{{ $order->shipping_address ?: 'N/A' }}</div>
                        </div>
                    @endif
                    <div class="field">
                        <div class="label">Tracking</div>
                        <div class="value">{{ $order->tracking_number ?: 'N/A' }}</div>
                    </div>
                </td>
                <td class="panel">
                    <h2 class="section-title">Payment</h2>
                    <div class="field">
                        <div class="label">Method</div>
                        <div class="value strong">{{ $paymentMethod }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Payment ID</div>
                        <div class="value">{{ $order->payment_id ?: 'N/A' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Payment Status</div>
                        <div class="value strong">{{ $order->payment_status_label }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Order Status</div>
                        <div class="value"><span class="status-pill status-{{ $statusSlug }}">{{ $order->status_label }}</span></div>
                    </div>
                </td>
            </tr>
        </table>

        <h2 class="items-title">Order Items</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 34%;">Item</th>
                    <th style="width: 30%;">Details</th>
                    <th style="width: 8%;" class="center">Qty</th>
                    <th style="width: 14%;" class="right">Unit Price</th>
                    <th style="width: 14%;" class="right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product_name ?: $item->device_type }}</div>
                            <div class="item-meta">Type: {{ $item->device_type ?: 'N/A' }}</div>
                            <div class="item-meta">Location: {{ $item->location ?: 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="item-meta"><span class="strong">Brand/Model:</span> {{ $item->brand_category ?: 'N/A' }}</div>
                            <div class="item-meta"><span class="strong">Specs:</span> {{ $item->device_specs ?: 'N/A' }}</div>
                            <div class="item-meta"><span class="strong">OS:</span> {{ $item->operating_system ?: 'N/A' }}</div>
                        </td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">USD {{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="right strong">USD {{ number_format((float) $item->total_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No items found for this order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="closing-grid">
            <tr>
                <td class="service-panel">
                    <div class="service-box">
                        <h2 class="section-title">Shipping Service</h2>
                        @if($singleRate)
                            <div class="field">
                                <div class="label">Carrier & Service</div>
                                <div class="value strong">{{ $shippingPayload['provider'] ?? 'N/A' }} - {{ $shippingPayload['servicelevel'] ?? 'N/A' }}</div>
                            </div>
                            <div class="field">
                                <div class="label">Rate</div>
                                <div class="value">{{ $shippingPayload['currency'] ?? 'USD' }} {{ number_format((float)($shippingPayload['amount'] ?? 0), 2) }}{{ isset($shippingPayload['estimated_days']) ? ' | ' . $shippingPayload['estimated_days'] . ' day(s)' : '' }}</div>
                            </div>
                            <div class="field">
                                <div class="label">References</div>
                                <div class="value">Shipment: {{ $shippingPayload['shipment_id'] ?? 'N/A' }} | Rate: {{ $shippingPayload['id'] ?? 'N/A' }}</div>
                            </div>
                        @elseif(is_array($shippingPayload) && count($shippingPayload))
                            @foreach($shippingPayload as $key => $service)
                                @if(is_array($service))
                                    <div class="field">
                                        <div class="label">Item {{ $key }}</div>
                                        <div class="value">{{ $service['provider'] ?? 'N/A' }} - {{ $service['servicelevel'] ?? 'N/A' }} ({{ $service['currency'] ?? 'USD' }} {{ number_format((float)($service['amount'] ?? 0), 2) }})</div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="field">
                                <div class="label">Carrier</div>
                                <div class="value">{{ $order->carrier ?: 'N/A' }}</div>
                            </div>
                            <div class="field">
                                <div class="label">Tracking</div>
                                <div class="value">{{ $order->tracking_number ?: 'N/A' }}</div>
                            </div>
                        @endif

                        @if($order->notes)
                            <div class="note-box">{{ $order->notes }}</div>
                        @endif
                    </div>
                </td>
                <td class="totals-panel">
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal</td>
                            <td class="right">USD {{ number_format((float) $order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td class="right">USD {{ number_format((float) $order->tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td class="right">USD {{ number_format((float) $order->shipping, 2) }}</td>
                        </tr>
                        <tr class="grand">
                            <td>Grand Total</td>
                            <td class="right grand-total">USD {{ number_format((float) $order->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer">
            Thank you for your business. This receipt was generated on {{ $generatedAt->format('M d, Y h:i A T') }}.
        </div>
    </div>
</body>
</html>
