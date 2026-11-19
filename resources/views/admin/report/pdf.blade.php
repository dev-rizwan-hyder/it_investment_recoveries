<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ESG Impact Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .header {
            background-color: #1b4332;
            color: #ffffff;
            padding: 28px 32px;
        }

        .badge {
            display: inline-block;
            background-color: #24523e;
            color: #4ade80;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0 0 12px 0;
            font-size: 28px;
            font-weight: 800;
        }

        .client-name {
            color: #4ade80;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .prepared-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 24px;
        }

        .prepared-box table {
            width: 100%;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            margin: 20px 24px 10px 24px;
        }

        .metrics-table {
            width: calc(100% - 48px);
            margin: 0 24px 20px 24px;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .metric-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            vertical-align: top;
        }

        .metric-card.highlight {
            background-color: #ffffff;
            border: 2px solid #1b4332;
        }

        .metric-label {
            font-size: 8px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .metric-val {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 4px 0;
        }

        .metric-sub {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }

        .breakdown-table {
            width: calc(100% - 48px);
            margin: 0 24px 20px 24px;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .breakdown-table th {
            background-color: #1b4332;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 14px;
            text-align: left;
        }

        .breakdown-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            font-weight: bold;
        }

        .cert-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 0 24px 20px 24px;
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }

        .signature-area {
            margin: 0 24px 24px 24px;
        }

        .signature-name {
            font-family: 'Georgia', serif;
            font-size: 20px;
            font-style: italic;
            color: #0f172a;
            border-b: 1px solid #94a3b8;
            display: inline-block;
            padding-bottom: 4px;
        }

        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 24px;
            font-size: 8px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @php
        $reportMeta = $items->first();
        $clientDisplayName = $reportMeta->client_display_name ?? 'Client';
        $companyName = $reportMeta->company ?? 'No Company Registered';
        $address = $reportMeta->address ?? '';
    @endphp

    <div class="container">
        <div class="header">
            <div class="badge"># Environmental, Social & Governance</div>
            <h1>ESG Impact Report</h1>
            <p style="font-size: 8px; margin: 0 0 2px 0; color: #a7f3d0; text-transform: uppercase; font-weight: bold;">REPORTING ENTITY</p>
            <div class="client-name">{{ $clientDisplayName }}</div>
        </div>

        <div class="prepared-box">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div style="font-size: 8px; font-weight: bold; color: #166534; text-transform: uppercase; margin-bottom: 4px;">PREPARED FOR</div>
                        <div style="font-size: 16px; font-weight: bold; color: #0f172a;">{{ $clientDisplayName }}</div>
                        <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">{{ $companyName }}</div>
                        <div style="font-size: 9px; color: #94a3b8; text-transform: uppercase;">{{ $address }}</div>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top;">
                        <div style="font-size: 8px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 4px;">PREPARED BY</div>
                        <div style="font-size: 12px; font-weight: bold; color: #0f172a;">IT Investment Recoveries</div>
                        <div style="font-size: 9px; color: #94a3b8; font-weight: bold; text-transform: uppercase;">{{ date('F d, Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">1. CORE ESG ENVIRONMENTAL METRICS</div>
        <table class="metrics-table">
            <tr>
                <td class="metric-card" style="width: 25%;">
                    <div class="metric-label">TOTAL DEVICES RECYCLED</div>
                    <div class="metric-val">{{ number_format((float) $results['totalUnits']) }}</div>
                    <div class="metric-sub">• UNITS PROCESSED</div>
                </td>
                <td class="metric-card" style="width: 25%;">
                    <div class="metric-label">LANDFILL DIVERSION</div>
                    <div class="metric-val">{{ number_format((float) $results['totalWeightLbs'], 1) }} lbs</div>
                    <div class="metric-sub">{{ number_format((float) $results['totalWeightLbs'] / 2204, 2) }} TONNES DIVERTED</div>
                </td>
                <td class="metric-card" style="width: 25%;">
                    <div class="metric-label">CO₂e EMISSIONS AVOIDED</div>
                    <div class="metric-val">{{ number_format((float) $results['co2AvoidedLbs'], 1) }} lbs</div>
                    <div class="metric-sub">CARBON FOOTPRINT REDUCTION</div>
                </td>
                <td class="metric-card highlight" style="width: 25%;">
                    <div class="metric-label">WATER CONSERVED</div>
                    <div class="metric-val">{{ number_format((float) $results['waterSavedGallons']) }} gal</div>
                    <div class="metric-sub">INDUSTRIAL WATER RECOVERY</div>
                </td>
            </tr>
        </table>

        <div class="section-title">2. ENVIRONMENTAL EQUIVALENCY IMPACT</div>
        <table class="metrics-table">
            <tr>
                <td class="metric-card" style="width: 33%; text-align: center;">
                    <div class="metric-val">{{ number_format((float) $results['treesEquivalent']) }}</div>
                    <div class="metric-label">MATURE TREES PLANTED EQUIVALENT</div>
                </td>
                <td class="metric-card" style="width: 33%; text-align: center;">
                    <div class="metric-val">{{ number_format((float) $results['carsOffRoadDays']) }}</div>
                    <div class="metric-label">CAR DAYS OFF THE ROAD</div>
                </td>
                <td class="metric-card" style="width: 33%; text-align: center;">
                    <div class="metric-val">{{ number_format((float) $results['homesEnergyDays']) }}</div>
                    <div class="metric-label">HOME POWER DAYS CONSERVED</div>
                </td>
            </tr>
        </table>

        <div class="section-title">3. DETAILED ITEM BREAKDOWN BY CATEGORY</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>CATEGORY STREAM</th>
                    <th style="text-align: center;">DEVICES / UNITS</th>
                    <th style="text-align: center;">NET WEIGHT (LBS)</th>
                    <th style="text-align: right;">% CONTRIBUTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items->groupBy('category') as $category => $group)
                    @php
                        $weight = (float) $group->sum('weight');
                        $percent = (float) $results['totalWeightLbs'] > 0 ? ($weight / $results['totalWeightLbs']) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ strtoupper($category ?: 'GENERAL PERIPHERALS') }}</td>
                        <td style="text-align: center;">{{ number_format($group->sum('quantity')) }}</td>
                        <td style="text-align: center;">{{ number_format($weight, 1) }}</td>
                        <td style="text-align: right; color: #166534;">{{ number_format($percent, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cert-box">
            <strong>IT Investment Recoveries</strong> certifies that the materials referenced in this ESG report have been recycled and recovered in accordance with established IT Asset Management & E-Waste recycling procedures and in compliance with applicable <strong>NIST SP 800-88 Rev. 2</strong> Guidelines for Media Sanitization / Physical Destruction, as well as all applicable local, city, state, and federal environmental regulations.
        </div>

        <div class="signature-area">
            <div style="font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">AUTHORIZED COMPLIANCE OFFICER</div>
            <div class="signature-name">Salvaster Daniel</div>
        </div>

        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td>IT INVESTMENT RECOVERIES • ESG ENVIRONMENTAL REPORT</td>
                    <td style="text-align: right;">GENERATED: {{ date('F d, Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>