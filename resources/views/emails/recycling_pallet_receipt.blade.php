<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Recycling Intake Receipt</title>
    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            box-sizing: border-box;
        }
        table, td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        a {
            text-decoration: none;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff !important;
            display: inline-block;
            font-weight: 700;
            font-size: 14px;
            line-height: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
            transition: background-color 0.2s;
        }
        .btn-secondary {
            background-color: #ffffff;
            color: #1e293b !important;
            border: 1px solid #cbd5e1;
            display: inline-block;
            font-weight: 600;
            font-size: 13px;
            line-height: 20px;
            padding: 11px 20px;
            border-radius: 8px;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: auto !important;
                padding: 10px !important;
            }
            .stack-column {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            .mobile-padding {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            .metric-card {
                margin-bottom: 8px !important;
            }
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .hide-mobile {
                display: none !important;
            }
        }
    </style>
</head>
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f5f9;">
    @php
        $firstPallet = $pallets->first();
        $rawDate = $firstPallet?->created_at ? (string) $firstPallet->created_at : null;
        if ($rawDate && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $rawDate, $m)) {
            $createdAt = \Carbon\Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3]);
        } else {
            $createdAt = $firstPallet?->created_at ? \Carbon\Carbon::parse($firstPallet->created_at) : now();
        }
        $portalLink = $registerUrl ?? $trackingUrl ?? 'https://it-investmentrecoveries.com/register';
        $directLoginLink = $loginUrl ?? 'https://it-investmentrecoveries.com/login';

        $jobNoDisplay = $jobNo ?? $pallets->pluck('job_no')->filter()->first() ?? $firstPallet?->job_no;
        $headerReference = !empty($jobNoDisplay)
            ? (preg_match('/^job/i', trim($jobNoDisplay)) ? trim($jobNoDisplay) : 'Job #: ' . trim($jobNoDisplay))
            : ($receiptNumber ?? ('REC-' . now('America/Denver')->format('YmdHis')));
    @endphp

    <center style="width: 100%; background-color: #f1f5f9; padding: 24px 0;">
        <!--[if mso | IE]>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="640" align="center" style="width:640px;">
        <tr>
        <td>
        <![endif]-->

        <div class="email-container" style="max-width: 640px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            
            <!-- HEADER -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0f172a; border-bottom: 3px solid #2563eb;">
                <tr>
                    <td class="mobile-padding" style="padding: 32px 28px; text-align: left;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td>
                                    <span style="display: inline-block; padding: 4px 10px; background-color: rgba(37, 99, 235, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 20px; color: #60a5fa; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">
                                        IT Assets & E-waste Recycling Receipt
                                    </span>
                                    <h1 style="margin: 4px 0 0 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
                                        Recycling Intake Confirmation
                                    </h1>
                                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8;">
                                        IT Investment Recoveries
                                    </p>
                                </td>
                                <td style="text-align: right; vertical-align: top;" class="hide-mobile">
                                    <span style="display: inline-block; background-color: #1e293b; color: #e2e8f0; padding: 6px 12px; border-radius: 6px; font-family: monospace; font-size: 12px; font-weight: 700; border: 1px solid #334155;">
                                        {{ $headerReference }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- INTRO & KEY METRICS -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 28px 28px 16px 28px;">
                <tr>
                    <td>
                        <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 24px; color: #334155;">
                            Dear <strong>{{ $client->name ?? 'Valued Client' }}</strong>,
                        </p>
                        <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #475569;">
                            We have successfully received your e-waste recycling intake. Below is the official summary receipt for your Pickup/Drop-off recorded on <strong>{{ $createdAt->format('F j, Y') }}</strong>.
                        </p>

                        <!-- Metric Summary Grid -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
                            <tr>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Pallets</div>
                                        <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $totals['items'] }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Est. Count</div>
                                        <div style="font-size: 20px; font-weight: 800; color: #2563eb; margin-top: 2px;">{{ $totals['estimated_count'] }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Gross Wt</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ number_format($totals['gross_weight'], 1) }}<span style="font-size: 11px; font-weight: 600; color: #64748b;"> lbs</span></div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.05em;">Net Weight</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #15803d; margin-top: 2px;">{{ number_format($totals['net_weight'], 1) }}<span style="font-size: 11px; font-weight: 600; color: #166534;"> lbs</span></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- CLIENT PORTAL CTA SECTION -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 24px 28px;">
                <tr>
                    <td>
                        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 24px; color: #ffffff; text-align: center; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);">
                            <div style="display: inline-block; background-color: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 20px; padding: 4px 12px; font-size: 11px; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">
                                Customer Portal Feature
                            </div>
                            <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: #ffffff;">
                                Track Your Intake & Asset Recovery Status
                            </h2>
                            <p style="margin: 0 0 18px 0; font-size: 13px; line-height: 20px; color: #cbd5e1; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Access live processing progress, itemized refurbishing reports, certificates of destruction, and asset photos directly through your client dashboard with <strong>{{ $client->email ?? 'your email' }}</strong>.
                            </p>
                            
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
                                <tr>
                                    <td style="padding: 4px;">
                                        <a href="{{ $portalLink }}" target="_blank" class="btn-primary" style="background-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px; display: inline-block;">
                                            Register to view complete recycling details
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 8px; text-align: center;">
                                        <a href="{{ $directLoginLink }}" target="_blank" style="color: #94a3b8; font-size: 12px; text-decoration: underline; font-weight: 500;">
                                            Or sign in directly to Customer Portal (&rarr;)
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- CLIENT & RECEIPT DETAILS -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 24px 28px;">
                <tr>
                    <td>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background-color: #f1f5f9; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                    <span style="font-size: 12px; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 0.08em;">
                                        Client & Receipt Details
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td width="50%" style="vertical-align: top; padding-right: 12px;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Client Information</div>
                                                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 4px;">{{ $client->name ?? 'N/A' }}</div>
                                                @if (!empty($client?->company))
                                                    <div style="font-size: 13px; color: #475569; margin-top: 2px;">{{ $client->company }}</div>
                                                @endif
                                                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                                    Email: {{ $client?->email ?: 'N/A' }}<br>
                                                    Phone: {{ $client?->phone ?: 'N/A' }}
                                                </div>
                                                @if (!empty($client?->address))
                                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                        Address: {{ $client->address }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td width="50%" style="vertical-align: top; padding-left: 12px; border-left: 1px solid #e2e8f0;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Intake Reference</div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 4px;">
                                                    @if (!empty($jobNoDisplay))
                                                        Job #: <strong>{{ $jobNoDisplay }}</strong>
                                                    @else
                                                        Receipt ID: <strong>{{ $receiptNumber }}</strong>
                                                    @endif
                                                </div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                    Main Barcode: <strong>{{ $firstPallet?->barcode_number ?? 'N/A' }}</strong>
                                                </div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                    Received Date: <strong>{{ $createdAt->format('M j, Y') }}</strong>
                                                </div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                    Initial Status: <span style="display: inline-block; padding: 2px 8px; background-color: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: capitalize;">{{ $firstPallet?->status ?? 'pending' }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- PALLET ITEMIZATION TABLE -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 28px 28px;">
                <tr>
                    <td>
                        <div style="font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">
                            Received Itemization & Pallets
                        </div>

                        <div class="table-responsive">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background-color: #0f172a; color: #ffffff;">
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; width: 50px;">Unit</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em;">Description & Metadata</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 60px;">Count</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 75px;">Gross</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 65px;">Tare</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 80px;">Net Wt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pallets as $index => $pallet)
                                        @php
                                            $rowBg = ($index % 2 === 0) ? '#ffffff' : '#f8fafc';
                                        @endphp
                                        <tr style="background-color: {{ $rowBg }}; border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 12px; font-size: 12px; font-weight: 700; color: #2563eb; vertical-align: top;">
                                                #{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #0f172a; vertical-align: top;">
                                                <strong style="color: #0f172a;">{{ $pallet->description ?: 'Recycling Material / Pallet' }}</strong>
                                                
                                                <div style="font-size: 12px; color: #64748b; margin-top: 4px; line-height: 1.4;">
                                                    Location: <span style="color: #334155; font-weight: 600;">{{ $pallet->put_away_location ?: 'N/A' }}</span>
                                                    @if (!empty($pallet->marketing_reference) && $pallet->marketing_reference !== 'No reference')
                                                        &bull; Ref: <span style="color: #334155;">{{ $pallet->marketing_reference }}</span>
                                                    @endif
                                                    @if (!empty($pallet->receiving_employee))
                                                        <br>Receiving Tech: <span style="color: #334155;">{{ $pallet->receiving_employee }}</span>
                                                    @endif
                                                    @if (!empty($pallet->job_no))
                                                        &bull; Job #: <span style="color: #334155; font-weight: 600;">{{ $pallet->job_no }}</span>
                                                    @endif
                                                    @if (!empty($pallet->processing_employee))
                                                        &bull; Processing Tech: <span style="color: #334155;">{{ $pallet->processing_employee }}</span>
                                                    @endif
                                                    @if (!empty($pallet->notes))
                                                        <br><span style="color: #475569; font-style: italic;">Notes: {{ $pallet->notes }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #334155; text-align: right; vertical-align: top; font-weight: 600;">
                                                {{ (int) ($pallet->estimated_count ?? 0) }}
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #64748b; text-align: right; vertical-align: top;">
                                                {{ number_format((float) ($pallet->gross_weight ?? 0), 1) }}
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #64748b; text-align: right; vertical-align: top;">
                                                {{ number_format((float) ($pallet->tare_weight ?? 0), 1) }}
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #166534; text-align: right; vertical-align: top; font-weight: 800;">
                                                {{ number_format((float) ($pallet->net_weight ?? 0), 1) }} lbs
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f1f5f9; font-weight: 800; color: #0f172a; border-top: 2px solid #cbd5e1;">
                                        <td colspan="2" style="padding: 12px; font-size: 12px; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">
                                            Total Receipt Summary:
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; text-align: right; color: #2563eb;">
                                            {{ $totals['estimated_count'] }}
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; text-align: right; color: #475569;">
                                            {{ number_format($totals['gross_weight'], 1) }}
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; text-align: right; color: #475569;">
                                            {{ number_format($totals['tare_weight'], 1) }}
                                        </td>
                                        <td style="padding: 12px; font-size: 14px; text-align: right; color: #15803d; font-weight: 800;">
                                            {{ number_format($totals['net_weight'], 1) }} lbs
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- FOOTER -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 28px;">
                <tr>
                    <td style="text-align: center; color: #64748b; font-size: 12px; line-height: 18px;">
                        <p style="margin: 0 0 8px 0; font-weight: 700; color: #334155; font-size: 13px;">
                            IT Investment Recoveries
                        </p>
                        <p style="margin: 0 0 12px 0; color: #64748b;">
                            Thank you for partnering with us for responsible electronics recycling and asset recovery.
                        </p>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                            If you have questions regarding this receipt, please contact our support team at
                            <a href="mailto:customerservice@it-investmentrecoveries.com" style="color: #2563eb; text-decoration: none; font-weight: 600;">customerservice@it-investmentrecoveries.com</a>
                            or call us at
                            <a href="tel:+13034724701" style="color: #2563eb; text-decoration: none; font-weight: 600;">+1-303-472-4701</a>.
                        </div>
                    </td>
                </tr>
            </table>

        </div>

        <!--[if mso | IE]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </center>
</body>
</html>
