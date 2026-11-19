<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Intake Completed</title>
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
            box-sizing: border-box;
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
            background-color: #16a34a;
            color: #ffffff !important;
            display: inline-block;
            font-weight: 700;
            font-size: 14px;
            line-height: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);
        }
        .status-completed {
            display: inline-block;
            padding: 4px 12px;
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body width="100%" style="margin: 0; padding: 0 !important; background-color: #f1f5f9;">
    @php
        $firstPallet = $pallet;
        $rawDate = $firstPallet->created_at ? (string) $firstPallet->created_at : null;
        if ($rawDate && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $rawDate, $m)) {
            $createdAt = \Carbon\Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3]);
        } else {
            $createdAt = $firstPallet->created_at ? \Carbon\Carbon::parse($firstPallet->created_at) : now();
        }
        $clientName = $client->name ?? 'Valued Client';
        $directLoginLink = $loginUrl ?? 'https://it-investmentrecoveries.com/login';
        $formattedBarcode = $displayBarcode ?? (method_exists($pallet, 'getDisplayBarcode') ? $pallet->getDisplayBarcode() : $pallet->barcode_number);

        $isMultiple = !empty($hasMultipleEntries) && isset($relatedPallets) && count($relatedPallets) > 1;
        $totalEstimatedCount = isset($totals['estimated_count']) ? $totals['estimated_count'] : (int) ($pallet->estimated_count ?? 0);
        $totalGrossWeight = isset($totals['gross_weight']) ? $totals['gross_weight'] : (float) ($pallet->gross_weight ?? 0);
        $totalNetWeight = isset($totals['net_weight']) ? $totals['net_weight'] : (float) ($pallet->net_weight ?? 0);
        $entriesCount = isset($totals['count']) ? $totals['count'] : (isset($relatedPallets) ? count($relatedPallets) : 1);
    @endphp

    <center style="width: 100%; background-color: #f1f5f9; padding: 24px 0;">
        <div style="max-width: 640px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            
            <!-- HEADER -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0f172a; border-bottom: 3px solid #16a34a;">
                <tr>
                    <td style="padding: 32px 28px; text-align: left;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td>
                                    <span style="display: inline-block; padding: 4px 10px; background-color: rgba(22, 163, 74, 0.2); border: 1px solid rgba(34, 197, 94, 0.4); border-radius: 20px; color: #4ade80; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">
                                        Processing Update Notification
                                    </span>
                                    <h1 style="margin: 4px 0 0 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
                                        Intake Processing Completed
                                    </h1>
                                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8;">
                                        IT Investment Recoveries
                                    </p>
                                </td>
                                <td style="text-align: right; vertical-align: top;">
                                    <span class="status-completed">
                                        ✓ Completed
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- INTRO & HIGHLIGHT -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 28px 28px 16px 28px;">
                <tr>
                    <td>
                        <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 24px; color: #334155;">
                            Dear <strong>{{ $clientName }}</strong>,
                        </p>
                        <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 22px; color: #475569;">
                            @if($isMultiple)
                                We are pleased to inform you that processing for all <strong>{{ $entriesCount }} entries</strong> of your intake pallet <strong>#{{ $formattedBarcode }}</strong> has been fully completed by E-Waste Recycling Standards & Procedures.
                            @else
                                We are pleased to inform you that processing for your intake pallet <strong>#{{ $formattedBarcode }}</strong> has been fully completed by E-Waste Recycling Standards & Procedures.
                            @endif
                        </p>

                        <!-- Key Metrics Grid -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                            <tr>
                                @if($isMultiple)
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Entries</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $entriesCount }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Total Count</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $totalEstimatedCount }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Gross Wt</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ number_format($totalGrossWeight, 1) }}<span style="font-size: 11px; font-weight: 600; color: #64748b;"> lbs</span></div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.05em;">Net Weight</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #15803d; margin-top: 2px;">{{ number_format($totalNetWeight, 1) }}<span style="font-size: 11px; font-weight: 600; color: #166534;"> lbs</span></div>
                                    </div>
                                </td>
                                @else
                                <td width="33%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Est. Count</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $totalEstimatedCount }}</div>
                                    </div>
                                </td>
                                <td width="33%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Gross Wt</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ number_format($totalGrossWeight, 1) }}<span style="font-size: 11px; font-weight: 600; color: #64748b;"> lbs</span></div>
                                    </div>
                                </td>
                                <td width="33%" style="padding: 4px;">
                                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.05em;">Net Weight</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #15803d; margin-top: 2px;">{{ number_format($totalNetWeight, 1) }}<span style="font-size: 11px; font-weight: 600; color: #166534;"> lbs</span></div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- PALLET DETAILS / ITEMIZATION SECTION -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 24px 28px;">
                <tr>
                    <td>
                        @if($isMultiple)
                        <div style="font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">
                            Completed Pallet Entries (All {{ $entriesCount }} Completed)
                        </div>

                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; margin-bottom: 8px;">
                            <thead>
                                <tr style="background-color: #0f172a; color: #ffffff;">
                                    <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; width: 45px;">Unit</th>
                                    <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em;">Description & Metadata</th>
                                    <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 55px;">Count</th>
                                    <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 75px;">Gross</th>
                                    <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 75px;">Net Wt</th>
                                    <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: center; text-transform: uppercase; letter-spacing: 0.05em; width: 80px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($relatedPallets as $index => $item)
                                    @php
                                        $rowBg = ($index % 2 === 0) ? '#ffffff' : '#f8fafc';
                                    @endphp
                                    <tr style="background-color: {{ $rowBg }}; border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 12px; font-size: 12px; font-weight: 800; color: #16a34a; vertical-align: top;">
                                            #{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; color: #0f172a; vertical-align: top;">
                                            <strong style="color: #0f172a;">{{ $item->description ?: 'Recycling Material / Pallet' }}</strong>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 4px; line-height: 1.4;">
                                                Location: <span style="color: #334155; font-weight: 600;">{{ $item->put_away_location ?: 'N/A' }}</span>
                                                @if (!empty($item->job_no))
                                                    &bull; Job #: <span style="color: #334155; font-weight: 600;">{{ $item->job_no }}</span>
                                                @endif
                                                @if (!empty($item->notes))
                                                    <br><span style="font-style: italic; color: #64748b;">Note: {{ $item->notes }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; font-weight: 700; text-align: right; color: #0f172a; vertical-align: top;">
                                            {{ (int) ($item->estimated_count ?? 0) }}
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; text-align: right; color: #475569; vertical-align: top;">
                                            {{ number_format((float) ($item->gross_weight ?? 0), 1) }}
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; font-weight: 700; text-align: right; color: #15803d; vertical-align: top;">
                                            {{ number_format((float) ($item->net_weight ?? 0), 1) }}
                                        </td>
                                        <td style="padding: 12px; text-align: center; vertical-align: top;">
                                            <span style="display: inline-block; padding: 2px 8px; background-color: #dcfce7; color: #15803d; border-radius: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                                ✓ Done
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #f1f5f9; font-weight: 800; border-top: 2px solid #cbd5e1;">
                                    <td colspan="2" style="padding: 10px 12px; font-size: 12px; text-transform: uppercase; color: #0f172a;">
                                        Total ({{ $entriesCount }} Pallet Entries)
                                    </td>
                                    <td style="padding: 10px 12px; font-size: 13px; text-align: right; color: #0f172a;">
                                        {{ $totalEstimatedCount }}
                                    </td>
                                    <td style="padding: 10px 12px; font-size: 13px; text-align: right; color: #475569;">
                                        {{ number_format($totalGrossWeight, 1) }} lbs
                                    </td>
                                    <td style="padding: 10px 12px; font-size: 13px; text-align: right; color: #15803d;">
                                        {{ number_format($totalNetWeight, 1) }} lbs
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        @else
                        <!-- Single Pallet Summary Details Card -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <tr>
                                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; background-color: #f1f5f9; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                    <span style="font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.08em;">
                                        Pallet Summary Details
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td width="50%" style="vertical-align: top; padding-right: 12px;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Pallet Barcode</div>
                                                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 2px;">{{ $formattedBarcode }}</div>
                                                
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 12px;">Description</div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">{{ $pallet->description ?: 'N/A' }}</div>
                                            </td>
                                            <td width="50%" style="vertical-align: top; padding-left: 12px; border-left: 1px solid #e2e8f0;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Intake Date</div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">{{ $createdAt->format('M j, Y') }}</div>

                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 12px;">Location</div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">{{ $pallet->put_away_location ?: 'N/A' }}</div>
                                            </td>
                                        </tr>
                                        @if(!empty($pallet->notes))
                                        <tr>
                                            <td colspan="2" style="padding-top: 12px; border-top: 1px dashed #cbd5e1; margin-top: 12px;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Notes</div>
                                                <div style="font-size: 12px; color: #475569; font-style: italic; margin-top: 2px;">{{ $pallet->notes }}</div>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- CTA SECTION -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 24px 28px;">
                <tr>
                    <td>
                        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 24px; color: #ffffff; text-align: center; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);">
                            <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: #ffffff;">
                                View Details in Customer Portal
                            </h2>
                            <p style="margin: 0 0 18px 0; font-size: 13px; line-height: 20px; color: #cbd5e1; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Log in to your customer dashboard to view full itemization breakdown, inventory status, and certificates.
                            </p>
                            
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
                                <tr>
                                    <td>
                                        <a href="{{ $directLoginLink }}" target="_blank" class="btn-primary">
                                            Sign In to Customer Portal (&rarr;)
                                        </a>
                                    </td>
                                </tr>
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
                            If you have questions regarding this processing update, please contact support at
                            <a href="mailto:customerservice@it-investmentrecoveries.com" style="color: #2563eb; text-decoration: none; font-weight: 600;">customerservice@it-investmentrecoveries.com</a>
                            or call us at
                            <a href="tel:+13034724701" style="color: #2563eb; text-decoration: none; font-weight: 600;">+1-303-472-4701</a>.
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </center>
</body>
</html>
