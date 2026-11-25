<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Issue Alert - Product #{{ $product['product_no'] ?? 'N/A' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
            padding: 24px 32px;
            color: #ffffff;
        }
        .header-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-bottom: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #fee2e2;
        }
        .content {
            padding: 32px;
        }
        .issue-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .issue-box-title {
            color: #991b1b;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .issue-box-msg {
            color: #7f1d1d;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
            margin: 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 12px;
        }
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 24px;
        }
        .specs-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .specs-table td.label {
            width: 35%;
            color: #64748b;
            font-weight: 600;
        }
        .specs-table td.val {
            color: #0f172a;
            font-weight: 700;
        }
        .badge-prod-no {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        .btn-action {
            display: inline-block;
            background: #0f172a;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            text-align: center;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 32px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <span class="header-badge">⚠️ Action Required</span>
            <h1>Product Issue Alert</h1>
            <p>An issue was flagged for Product #{{ $product['product_no'] ?? 'N/A' }}{{ !empty($isBlinking) ? ' with Blinking Red Alert status' : '' }}.</p>
        </div>

        <!-- Body -->
        <div class="content">
            <!-- Issue Box -->
            <div class="issue-box">
                <div class="issue-box-title">Issue Description / Message:</div>
                <p class="issue-box-msg">{{ !empty($issueMessage) ? $issueMessage : 'Item has been flagged with an issue requiring inspection.' }}</p>
            </div>

            <!-- Product Specifications -->
            <div class="section-title">Product Details</div>
            <table class="specs-table">
                <tr>
                    <td class="label">Product No:</td>
                    <td class="val"><span class="badge-prod-no">#{{ $product['product_no'] ?? 'N/A' }}</span></td>
                </tr>
                <tr>
                    <td class="label">Product Name / Title:</td>
                    <td class="val">{{ $product['product_name'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Category:</td>
                    <td class="val">
                        {{ ($product['category'] ?? '') ?: '-' }}
                        @if(!empty($product['sub_category']))
                            &rsaquo; {{ $product['sub_category'] }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Condition:</td>
                    <td class="val">{{ ($product['condition'] ?? '') ?: 'Used' }}</td>
                </tr>
                <tr>
                    <td class="label">Location:</td>
                    <td class="val">{{ ($product['location'] ?? '') ?: 'Not Specified' }}</td>
                </tr>
                <tr>
                    <td class="label">Quantity:</td>
                    <td class="val">{{ $product['quantity'] ?? 1 }}</td>
                </tr>
                <tr>
                    <td class="label">Serial Number:</td>
                    <td class="val">{{ ($product['serial_number'] ?? '') ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Added / Intake User:</td>
                    <td class="val">{{ ($product['user'] ?? '') ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Folder Reference:</td>
                    <td class="val"><code style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $product['folder'] ?? '' }}</code></td>
                </tr>
                @if(!empty($reporterName))
                <tr>
                    <td class="label">Reported By:</td>
                    <td class="val">{{ $reporterName }}</td>
                </tr>
                @endif
            </table>

            <!-- Button Link -->
            <div style="text-align: center; margin-top: 28px; margin-bottom: 8px;">
                <a href="{{ url('/admin/ebay-products') }}?search={{ urlencode((string) ($product['product_no'] ?? $product['folder'] ?? '')) }}" class="btn-action">
                    Open Product in Admin Dashboard &rarr;
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">IT Investment Recoveries System &bull; Automated Issue Alert Notification</p>
            <p style="margin: 4px 0 0 0;">This email was sent to {{ $recipientDisplay ?? 'sal@it-investmentrecoveries.com' }} regarding flagged eBay inventory item #{{ $product['product_no'] ?? 'N/A' }}.</p>
        </div>
    </div>
</body>
</html>
