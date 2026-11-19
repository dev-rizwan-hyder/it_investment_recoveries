<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Repair Status Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
            line-height: 1.5;
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
            background: #0f172a;
            color: #ffffff;
            padding: 28px 24px;
            text-align: center;
        }
        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 6px 0;
            color: #ffffff;
        }
        .header-subtitle {
            margin: 0;
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }
        .content {
            padding: 28px 24px;
        }
        .status-hero {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 24px;
        }
        .status-hero p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #2563eb;
            color: #ffffff;
        }
        .status-completed { background: #0f172a; color: #ffffff; }
        .status-ready { background: #4f46e5; color: #ffffff; }
        .status-pickup { background: #0284c7; color: #ffffff; }
        .status-cancelled { background: #dc2626; color: #ffffff; }
        .section-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin: 0 0 14px 0;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            border-bottom: 1px dashed #f1f5f9;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #64748b;
            font-weight: 600;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        .footer p { margin: 4px 0; }
        .btn-website {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 22px;
            border-radius: 10px;
            text-decoration: none;
            margin-top: 14px;
        }
    </style>
</head>
<body>
    @php
        $customerName = $repair->customer_name ?: ($repair->client?->name ?: ($repair->user?->name ?: 'Customer'));
        $itemTitle = trim(($repair->brand_category ? $repair->brand_category . ' ' : '') . ($repair->device_type ?: ($repair->item_description ?: 'Repair Item')));
        $denverTimezone = \App\Models\RepairEntry::DENVER_TIMEZONE;
        $updatedAt = $repair->updated_at
            ? $repair->updated_at->copy()->timezone($denverTimezone)->format('F j, Y, g:i A T')
            : now($denverTimezone)->format('F j, Y, g:i A T');

        $statusSlug = strtolower(str_replace(' ', '-', (string) $repair->status));
        $badgeClass = match($statusSlug) {
            'completed' => 'status-completed',
            'ready-for-pickup' => 'status-pickup',
            'ready-for-shipment', 'shipped' => 'status-ready',
            'cancelled' => 'status-cancelled',
            default => '',
        };
    @endphp

    <div class="container">
        <div class="header">
            <h1 class="brand-name">IT INVESTMENT RECOVERIES</h1>
            <p class="header-subtitle">Professional Device Repair & IT Services</p>
        </div>

        <div class="content">
            <p style="font-size: 15px; margin: 0 0 16px 0; color: #1e293b;">
                Hello <strong>{{ $customerName }}</strong>,
            </p>
            <p style="font-size: 14px; margin: 0 0 20px 0; color: #475569;">
                The status of your repair service has been updated. Here are the latest details:
            </p>

            <div class="status-hero">
                <p>Current Repair Status</p>
                <span class="status-badge {{ $badgeClass }}">{{ $repair->status_label }}</span>
                @if(!empty($previousStatus) && $previousStatus !== $repair->status)
                    <div style="font-size: 12px; color: #94a3b8; margin-top: 10px;">
                        Previous Status: <span>{{ \App\Models\RepairEntry::statusLabel($previousStatus) }}</span>
                    </div>
                @endif
            </div>

            <div class="section-box">
                <div class="section-title">Repair Details</div>
                <div class="detail-row">
                    <span class="detail-label">Repair Entry:</span>
                    <span class="detail-value">#{{ $repair->id }}</span>
                </div>
                @if($repair->job_no)
                    <div class="detail-row">
                        <span class="detail-label">Job Number:</span>
                        <span class="detail-value">#{{ $repair->job_no }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Device / Item:</span>
                    <span class="detail-value">{{ $itemTitle }}</span>
                </div>
                @if($repair->serial_number)
                    <div class="detail-row">
                        <span class="detail-label">Serial Number:</span>
                        <span class="detail-value font-mono">{{ $repair->serial_number }}</span>
                    </div>
                @endif
                @if($repair->item_barcode)
                    <div class="detail-row">
                        <span class="detail-label">Barcode:</span>
                        <span class="detail-value font-mono">{{ $repair->item_barcode }}</span>
                    </div>
                @endif
                @if($repair->technician)
                    <div class="detail-row">
                        <span class="detail-label">Technician:</span>
                        <span class="detail-value">{{ $repair->technician }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Last Updated:</span>
                    <span class="detail-value">{{ $updatedAt }}</span>
                </div>
            </div>

            @if($repair->notes)
                <div class="section-box">
                    <div class="section-title">Technician & Service Notes</div>
                    <div style="font-size: 13px; color: #334155; line-height: 1.6; white-space: pre-line;">
                        {{ $repair->notes }}
                    </div>
                </div>
            @endif

            <p style="font-size: 13px; color: #64748b; margin: 20px 0 0 0; text-align: center;">
                If you have questions regarding your repair, please feel free to contact us or visit our store.
            </p>

            <div style="text-align: center; margin-top: 16px;">
                @if(\Illuminate\Support\Facades\Route::has('store.index'))
                    <a href="{{ route('store.index') }}" class="btn-website">Visit Our Website</a>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><strong>IT Investment Recoveries</strong></p>
            <p>&copy; {{ date('Y') }} IT Investment Recoveries. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
