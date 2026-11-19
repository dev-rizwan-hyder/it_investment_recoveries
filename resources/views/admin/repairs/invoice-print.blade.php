@php
    $denverTimezone = \App\Models\RepairEntry::DENVER_TIMEZONE;
    $generatedAt = now($denverTimezone);
    $receivedAt = $entry->created_at?->copy()->timezone($denverTimezone);
    $updatedAt = $entry->updated_at?->copy()->timezone($denverTimezone);
    $photos = collect(json_decode($entry->photos ?: '[]', true) ?: [])->filter()->values();
    $categoryParts = array_values(array_filter([$entry->category, $entry->sub_category, $entry->sub_sub_category]));
    $categoryLine = !empty($categoryParts) ? implode(' > ', $categoryParts) : 'N/A';
    $quantity = max(1, (int) ($entry->quantity ?: 1));
    $unitPrice = (float) ($entry->price ?? 0);
    $subtotal = $quantity * $unitPrice;
    $amountPaid = $entry->payment_status === \App\Models\RepairEntry::PAYMENT_STATUS_PAID ? $subtotal : 0;
    $balanceDue = max(0, $subtotal - $amountPaid);
    $customerName = $entry->customer_name
        ?: ($entry->user?->name
            ?: ($entry->customer_email
                ?: ($entry->customer_phone ?: 'No customer')));
    $repairNumber = $entry->item_barcode ?: 'REP-' . $entry->id;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Invoice {{ $repairNumber }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e2e8f0;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.35;
        }
        .toolbar {
            display: flex;
            justify-content: flex-end;
            max-width: 900px;
            margin: 22px auto 0;
        }
        .toolbar button {
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
            padding: 10px 14px;
        }
        .receipt {
            max-width: 820px;
            min-height: 1040px;
            margin: 18px auto 40px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.16);
        }
        .topbar {
            background: #111827;
            color: #fff;
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr auto;
            padding: 26px 34px;
        }
        .brand {
            font-size: 24px;
            font-weight: 800;
        }
        .brand-subtitle {
            color: #cbd5e1;
            margin-top: 5px;
        }
        .receipt-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
            text-align: right;
            text-transform: uppercase;
        }
        .receipt-number {
            color: #93c5fd;
            font-size: 14px;
            font-weight: 800;
            margin-top: 7px;
            text-align: right;
        }
        .content { padding: 26px 34px 24px; }
        .summary-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: 1.1fr 0.9fr;
            margin-bottom: 18px;
        }
        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 13px 15px;
        }
        .panel.shaded { background: #f8fafc; }
        .label {
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.4px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .customer-name {
            color: #111827;
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        p { margin: 2px 0; }
        .meta {
            display: grid;
            gap: 5px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
        }
        .meta-row span:first-child { color: #64748b; }
        .meta-row span:last-child {
            color: #111827;
            font-weight: 800;
            text-align: right;
        }
        .status {
            border-radius: 999px;
            display: inline-block;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 4px 8px;
            text-transform: uppercase;
        }
        .status-repair { background: #dbeafe; color: #1d4ed8; }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-paid { background: #dcfce7; color: #047857; }
        .status-refunded { background: #e0f2fe; color: #0369a1; }
        .status-cancelled,
        .status-failed { background: #fee2e2; color: #b91c1c; }
        .detail-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, 1fr);
            margin: 18px 0;
        }
        .detail-box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
        }
        .detail-title {
            color: #64748b;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .detail-value {
            color: #111827;
            font-size: 12px;
            font-weight: 800;
            word-break: break-word;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 9px 10px;
            text-align: left;
            text-transform: uppercase;
        }
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 10px;
            vertical-align: top;
        }
        .right { text-align: right; }
        .center { text-align: center; }
        .item-title {
            color: #111827;
            font-weight: 800;
        }
        .item-subtitle {
            color: #64748b;
            font-size: 10px;
            margin-top: 2px;
            white-space: pre-line;
        }
        .payment-grid {
            align-items: start;
            display: grid;
            gap: 18px;
            grid-template-columns: 1fr 330px;
            margin-top: 16px;
        }
        .notes {
            border-left: 4px solid #2563eb;
            color: #334155;
            min-height: 46px;
            padding: 6px 0 6px 12px;
            white-space: pre-line;
        }
        .totals {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 13px;
        }
        .total-row + .total-row { border-top: 1px solid #e5e7eb; }
        .grand {
            background: #111827;
            color: #fff;
            font-size: 16px;
            font-weight: 800;
        }
        .paid { color: #047857; font-weight: 800; }
        .balance { color: #b45309; font-weight: 800; }
        .pictures {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(4, 1fr);
            margin-top: 8px;
        }
        .pictures img {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            height: 88px;
            object-fit: cover;
            width: 100%;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 11px;
            margin-top: 18px;
            padding-top: 10px;
            text-align: center;
        }
        @media print {
            @page { size: letter portrait; margin: 0.22in; }
            html,
            body {
                background: #fff;
                min-height: 10.56in;
                width: 8.06in;
            }
            .no-print { display: none !important; }
            .receipt {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                min-height: auto;
                overflow: visible;
                width: 8.06in;
            }
            .topbar { padding: 16px 22px; }
            .content { padding: 16px 22px 14px; }
            .summary-grid { margin-bottom: 12px; }
            .panel { padding: 9px 11px; }
            .detail-grid {
                gap: 8px;
                margin: 12px 0;
            }
            .detail-box { padding: 8px 10px; }
            th { padding: 7px 8px; }
            td { padding: 7px 8px; }
            .payment-grid {
                gap: 12px;
                grid-template-columns: 1fr 280px;
                margin-top: 12px;
            }
            .total-row { padding: 6px 10px; }
            .pictures img { height: 62px; }
            section[style] { margin-top: 12px !important; }
            .footer {
                margin-top: 12px;
                padding-top: 8px;
            }
        }
    </style>
</head>
<body>
    @unless(request()->boolean('embedded'))
        <div class="toolbar no-print">
            <button type="button" onclick="window.print()">Print Repair Invoice</button>
        </div>
    @endunless

    <main class="receipt">
        <header class="topbar">
            <div>
                <div class="brand">DENVER DISCOUNT COMPUTERS</div>
                <div class="brand-subtitle">Professional repair service invoice</div>
            </div>
            <div>
                <div class="receipt-title">Repair Invoice</div>
                <div class="receipt-number">{{ $repairNumber }}</div>
            </div>
        </header>

        <section class="content">
            <div class="summary-grid">
                <div class="panel">
                    <div class="label">Bill To</div>
                    <div class="customer-name">{{ $customerName }}</div>
                    <p>Email: {{ $entry->customer_email ?: 'N/A' }}</p>
                    <p>Phone: {{ $entry->customer_phone ?: 'N/A' }}</p>
                    @if($entry->user?->name && $entry->user->name !== $customerName)
                        <p>Account: {{ $entry->user->name }}</p>
                    @endif
                </div>

                <div class="panel shaded">
                    <div class="label">Repair Details</div>
                    <div class="meta">
                        <div class="meta-row">
                            <span>Repair #</span>
                            <span>{{ $repairNumber }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Received</span>
                            <span>{{ $receivedAt?->format('M d, Y h:i A T') ?: 'N/A' }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Generated</span>
                            <span>{{ $generatedAt->format('M d, Y h:i A T') }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Status</span>
                            <span><span class="status status-repair">{{ $entry->status_label }}</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-box">
                    <div class="detail-title">Technician</div>
                    <div class="detail-value">{{ $entry->technician ?: 'N/A' }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-title">Payment Method</div>
                    <div class="detail-value">{{ $entry->payment_method_label }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-title">Payment Status</div>
                    <div class="detail-value">
                        <span class="status status-{{ $entry->payment_status ?: \App\Models\RepairEntry::PAYMENT_STATUS_PENDING }}">
                            {{ $entry->payment_status_label }}
                        </span>
                    </div>
                </div>
                <div class="detail-box">
                    <div class="detail-title">Location</div>
                    <div class="detail-value">{{ $entry->put_away_location ?: 'N/A' }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-title">Last Updated</div>
                    <div class="detail-value">{{ $updatedAt?->format('M d, Y h:i A T') ?: 'N/A' }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-title">Category</div>
                    <div class="detail-value">{{ $categoryLine }}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Repair Item</th>
                        <th>Device Details</th>
                        <th class="center">Qty</th>
                        <th class="right">Unit Price</th>
                        <th class="right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="item-title">{{ $entry->item_description ?: ($entry->device_type ?: 'Repair Service') }}</div>
                            <div class="item-subtitle">Barcode: {{ $repairNumber }}</div>
                        </td>
                        <td>
                            <div class="item-title">{{ $entry->device_type ?: 'N/A' }}</div>
                            <div class="item-subtitle">
                                Brand / Model: {{ $entry->brand_category ?: 'N/A' }}
                                Operating System: {{ $entry->operating_system ?: 'N/A' }}
                                Specs: {{ $entry->device_specs ?: 'N/A' }}
                            </div>
                        </td>
                        <td class="center">{{ $quantity }}</td>
                        <td class="right">${{ number_format($unitPrice, 2) }}</td>
                        <td class="right"><strong>${{ number_format($subtotal, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="payment-grid">
                <div>
                    <div class="label">Repair Notes</div>
                    <div class="notes">{{ $entry->notes ?: 'No repair notes recorded.' }}</div>
                </div>

                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="total-row grand">
                        <span>Total</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>Amount Paid</span>
                        <span class="paid">${{ number_format($amountPaid, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>Balance Due</span>
                        <span class="balance">${{ number_format($balanceDue, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($photos->isNotEmpty())
                <section style="margin-top: 28px;">
                    <div class="label">Repair Photos</div>
                    <div class="pictures">
                        @foreach($photos as $photo)
                            <img src="{{ asset($photo) }}" alt="Repair photo">
                        @endforeach
                    </div>
                </section>
            @endif

            <footer class="footer">
                Thank you for your business. Repair invoice generated on {{ $generatedAt->format('M d, Y h:i A T') }}.
            </footer>
        </section>
    </main>

    @if(request()->boolean('autoprint'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 350);
            });
        </script>
    @endif
</body>
</html>
