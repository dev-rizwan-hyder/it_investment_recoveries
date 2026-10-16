@php
    $picturePaths = collect($invoice->picture_paths ?? [])->filter()->values();
    $denverTimezone = 'America/Denver';
    $generatedAt = now($denverTimezone);
    $isPaid = strtolower((string) $invoice->status) === \App\Models\SalesInvoice::STATUS_PAID;
    $amountPaid = $isPaid ? (float) $invoice->total : 0;
    $balanceDue = max(0, (float) $invoice->total - $amountPaid);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Receipt {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e2e8f0;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.32;
        }
        .toolbar {
            display: flex;
            gap: 10px;
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
        .status-paid { background: #dcfce7; color: #047857; }
        .status-unpaid { background: #fef3c7; color: #b45309; }
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
        .payment-grid {
            align-items: start;
            display: grid;
            gap: 18px;
            grid-template-columns: 1fr 330px;
            margin-top: 16px;
        }
        .message {
            border-left: 4px solid #2563eb;
            color: #334155;
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
            grid-template-columns: repeat(3, 1fr);
            margin-top: 8px;
        }
        .pictures img {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            height: 74px;
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
                height: 10.56in;
                overflow: hidden;
                width: 8.06in;
            }
            .no-print { display: none !important; }
            .receipt {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                min-height: auto;
                overflow: hidden;
                width: 8.06in;
            }
            .topbar { padding: 16px 22px; }
            .content { padding: 16px 22px 14px; }
            .summary-grid { margin-bottom: 12px; }
            .panel { padding: 9px 11px; }
            th { padding: 7px 8px; }
            td { padding: 7px 8px; }
            .payment-grid {
                gap: 12px;
                grid-template-columns: 1fr 280px;
                margin-top: 12px;
            }
            .total-row { padding: 6px 10px; }
            .pictures img { height: 52px; }
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
            <button type="button" onclick="window.print()">Print Invoice Receipt</button>
        </div>
    @endunless

    <main class="receipt">
        <header class="topbar">
            <div>
                <div class="brand">IT INVESTMENT RECOVERIES</div>
                <div class="brand-subtitle">Professional service invoice receipt</div>
            </div>
            <div>
                <div class="receipt-title">Invoice Receipt</div>
                <div class="receipt-number">{{ $invoice->invoice_number }}</div>
            </div>
        </header>

        <section class="content">
            <div class="summary-grid">
                <div class="panel">
                    <div class="label">Bill To</div>
                    <div class="customer-name">{{ $invoice->customer_name }}</div>
                    @if($invoice->customer_company)<p>{{ $invoice->customer_company }}</p>@endif
                    @if($invoice->customer_email)<p>{{ $invoice->customer_email }}</p>@endif
                    @if($invoice->customer_phone)<p>{{ $invoice->customer_phone }}</p>@endif
                    @if($invoice->customer_address)<p>{{ $invoice->customer_address }}</p>@endif
                </div>

                <div class="panel shaded">
                    <div class="label">Receipt Details</div>
                    <div class="meta">
                        <div class="meta-row">
                            <span>Date of Service</span>
                            <span>{{ $invoice->service_date?->format('M d, Y') ?: 'N/A' }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Due Date</span>
                            <span>{{ $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: 'N/A') }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Payment Method</span>
                            <span>{{ $invoice->payment_method_label }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Generated</span>
                            <span>{{ $generatedAt->format('M d, Y h:i A T') }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Status</span>
                            <span>
                                <span class="status status-{{ strtolower($invoice->status_label) }}">{{ $invoice->status_label }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th class="center">Qty</th>
                        <th class="right">Unit Price</th>
                        <th class="right">Subtotal</th>
                        <th class="right">Tax</th>
                        <th class="right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->invoice_category ?: 'Uncategorized' }}</strong>
                            </td>
                            <td>
                                <div class="item-title">{{ $item->description }}</div>
                            </td>
                            <td class="center">{{ $item->quantity }}</td>
                            <td class="right">${{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="right">${{ number_format((float) $item->subtotal, 2) }}</td>
                            <td class="right">${{ number_format((float) $item->tax_amount, 2) }}</td>
                            <td class="right"><strong>${{ number_format((float) $item->total, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="payment-grid">
                <div>
                    @if($invoice->message)
                        <div class="label">Message</div>
                        <div class="message">{{ $invoice->message }}</div>
                    @endif
                </div>

                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <strong>${{ number_format((float) $invoice->subtotal, 2) }}</strong>
                    </div>
                    <div class="total-row">
                        <span>Tax ({{ number_format((float) $invoice->tax_rate, 0) }}%)</span>
                        <strong>${{ number_format((float) $invoice->tax_amount, 2) }}</strong>
                    </div>
                    <div class="total-row grand">
                        <span>Total</span>
                        <span>${{ number_format((float) $invoice->total, 2) }}</span>
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

            @if($picturePaths->isNotEmpty())
                <section style="margin-top: 28px;">
                    <div class="label">Attached Pictures & Files</div>
                    <div class="pictures">
                        @foreach($picturePaths as $path)
                            @if(\App\Models\SalesInvoice::isImageFile($path))
                                <img src="{{ $invoice->pictureUrl($path) }}" alt="Invoice picture">
                            @else
                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 11px; font-weight: 700; color: #334155;">
                                    <span>📎</span>
                                    <span>{{ \App\Models\SalesInvoice::fileName($path) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            <footer class="footer">
                Thank you for your business. Receipt generated on {{ $generatedAt->format('M d, Y h:i A T') }}.
            </footer>
        </section>
    </main>
</body>
</html>
