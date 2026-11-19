<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 640px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" cellspacing="0" cellpadding="0">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0f172a; padding: 28px 32px; text-align: left;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="color: #38bdf8; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;">Service Invoice</div>
                                        <div style="color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">IT INVESTMENT RECOVERIES</div>
                                        <div style="color: #94a3b8; font-size: 12px; margin-top: 2px;">Professional IT &amp; Computer Services</div>
                                    </td>
                                    <td align="right" style="vertical-align: top;">
                                        <div style="color: #93c5fd; font-size: 14px; font-weight: 800; text-align: right;">Invoice #</div>
                                        <div style="color: #ffffff; font-size: 15px; font-weight: 800; text-align: right; margin-top: 2px;">{{ $invoice->invoice_number }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 28px 32px;">
                            <!-- Greeting -->
                            <div style="font-size: 15px; color: #334155; margin-bottom: 20px;">
                                Hello <strong>{{ $invoice->customer_name ?: 'Valued Customer' }}</strong>,
                                <p style="margin: 6px 0 0 0; color: #64748b; font-size: 13px;">
                                    Thank you for your business. Please find the details of your invoice below.
                                </p>
                            </div>

                            <!-- Customer & Invoice Meta Grid -->
                            <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #f8fafc; overflow: hidden;">
                                <tr>
                                    <td width="55%" style="padding: 18px 20px; vertical-align: top; border-right: 1px solid #e2e8f0;">
                                        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 6px;">Billed To</div>
                                        <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">{{ $invoice->customer_name }}</div>
                                        @if($invoice->customer_company)
                                            <div style="font-size: 12px; color: #475569; font-weight: 600;">{{ $invoice->customer_company }}</div>
                                        @endif
                                        @if($invoice->customer_email)
                                            <div style="font-size: 12px; color: #64748b;">{{ $invoice->customer_email }}</div>
                                        @endif
                                        @if($invoice->customer_phone)
                                            <div style="font-size: 12px; color: #64748b;">{{ $invoice->customer_phone }}</div>
                                        @endif
                                        @if($invoice->customer_address)
                                            <div style="font-size: 12px; color: #64748b; margin-top: 4px; white-space: pre-line;">{{ $invoice->customer_address }}</div>
                                        @endif
                                    </td>
                                    <td width="45%" style="padding: 18px 20px; vertical-align: top;">
                                        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px;">Invoice Details</div>
                                        <table width="100%" cellspacing="0" cellpadding="0" style="font-size: 12px;">
                                            <tr>
                                                <td style="color: #64748b; padding-bottom: 5px;">Date of Service:</td>
                                                <td align="right" style="color: #0f172a; font-weight: 700; padding-bottom: 5px;">{{ $invoice->service_date?->format('M d, Y') ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; padding-bottom: 5px;">Due Date:</td>
                                                <td align="right" style="color: #0f172a; font-weight: 700; padding-bottom: 5px;">{{ $invoice->due_date?->format('M d, Y') ?: ($invoice->service_date?->format('M d, Y') ?: 'N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b; padding-bottom: 5px;">Payment Method:</td>
                                                <td align="right" style="color: #0f172a; font-weight: 700; padding-bottom: 5px;">{{ $invoice->payment_method_label }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #64748b;">Status:</td>
                                                <td align="right">
                                                    @if(strtolower((string)$invoice->status) === \App\Models\SalesInvoice::STATUS_PAID)
                                                        <span style="display: inline-block; background-color: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; text-transform: uppercase;">Paid</span>
                                                    @else
                                                        <span style="display: inline-block; background-color: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; text-transform: uppercase;">Unpaid</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message (if provided) -->
                            @if(!empty(trim((string)$invoice->message)))
                                <div style="background-color: #f8fafc; border-left: 4px solid #2563eb; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; line-height: 1.6; color: #334155;">
                                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #2563eb; margin-bottom: 4px;">Note from IT Investment Recoveries</div>
                                    <div style="white-space: pre-line;">{{ $invoice->message }}</div>
                                </div>
                            @endif

                            <!-- Line Items Table -->
                            <div style="margin-bottom: 24px;">
                                <table width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                    <thead>
                                        <tr style="background-color: #f8fafc;">
                                            <th style="padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Item / Service</th>
                                            <th style="padding: 10px 12px; text-align: center; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Qty</th>
                                            <th style="padding: 10px 12px; text-align: right; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Price</th>
                                            <th style="padding: 10px 12px; text-align: right; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b;">
                                                    <strong>{{ $item->invoice_category ?: 'Service / Item' }}</strong>
                                                    @if($item->description)
                                                        <br><span style="color: #64748b; font-size: 11px;">{{ $item->description }}</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #475569; text-align: center;">{{ $item->quantity }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #475569; text-align: right;">${{ number_format((float) $item->unit_price, 2) }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #0f172a; text-align: right; font-weight: bold;">${{ number_format((float) $item->total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totals Box -->
                            <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td width="50%" style="vertical-align: top; padding-right: 15px;">
                                        @if(!empty($invoice->picture_paths) && count($invoice->picture_paths) > 0)
                                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; font-size: 12px; color: #64748b;">
                                                <strong style="color: #334155;">Attachments:</strong>
                                                <div style="margin-top: 4px;">{{ count($invoice->picture_paths) }} file(s) attached to this email.</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td width="50%" align="right" style="vertical-align: top;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%; max-width: 260px; font-size: 13px;">
                                            <tr>
                                                <td style="padding: 6px 0; color: #64748b;">Subtotal:</td>
                                                <td align="right" style="padding: 6px 0; font-weight: 700; color: #1e293b;">${{ number_format((float) $invoice->subtotal, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #64748b;">Tax ({{ number_format((float) $invoice->tax_rate, 0) }}%):</td>
                                                <td align="right" style="padding: 6px 0; font-weight: 700; color: #1e293b;">${{ number_format((float) $invoice->tax_amount, 2) }}</td>
                                            </tr>
                                            <tr style="border-top: 2px solid #e2e8f0;">
                                                <td style="padding: 10px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Total:</td>
                                                <td align="right" style="padding: 10px 0; font-size: 20px; font-weight: 900; color: #16a34a;">${{ number_format((float) $invoice->total, 2) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Square Online Payment Button (if public URL available) -->
                            @if(!empty($invoice->square_public_url))
                                <div style="text-align: center; margin: 28px 0 20px 0; padding: 20px; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;">
                                    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #166534; margin-bottom: 8px;">Online Payment Option</div>
                                    <a href="{{ $invoice->square_public_url }}" target="_blank" style="display: inline-block; background-color: #16a34a; color: #ffffff; padding: 14px 32px; border-radius: 10px; font-size: 14px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                                        Pay Online with Square &rarr;
                                    </a>
                                    <div style="margin-top: 8px; font-size: 11px; color: #166534;">Pay securely using Credit/Debit Card or Cash App Pay.</div>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
                            <div style="font-weight: 800; color: #334155; margin-bottom: 4px;">IT INVESTMENT RECOVERIES</div>
                            <div style="margin-top: 8px; font-size: 11px; color: #94a3b8;">&copy; {{ date('Y') }} IT Investment Recoveries. All rights reserved.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
