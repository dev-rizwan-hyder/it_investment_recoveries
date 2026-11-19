<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Repair Intake Details</title>
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
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: #1e293b;
            color: #ffffff;
            padding: 28px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 6px 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #94a3b8;
        }
        .content {
            padding: 24px;
        }
        .section-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin: 0 0 14px 0;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }
        .grid-2 {
            display: table;
            width: 100%;
        }
        .grid-row {
            display: table-row;
        }
        .grid-cell {
            display: table-cell;
            width: 50%;
            padding: 4px 8px 4px 0;
            vertical-align: top;
            font-size: 13px;
        }
        .data-label {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: block;
            margin-bottom: 2px;
        }
        .data-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 13px;
            word-break: break-word;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: #e0f2fe;
            color: #0369a1;
        }
        .badge-amber {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }
        .item-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 14px;
            overflow: hidden;
            background: #ffffff;
        }
        .item-header {
            background: #f8fafc;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-body {
            padding: 12px 16px;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-table td {
            padding: 6px 4px;
            font-size: 13px;
            vertical-align: top;
        }
        .item-table td.label-col {
            width: 32%;
            color: #64748b;
            font-weight: 600;
        }
        .item-table td.val-col {
            color: #0f172a;
            font-weight: 500;
        }
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #92400e;
            margin-top: 10px;
            white-space: pre-wrap;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        .footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @php
        $firstRepair = $primaryRepair ?? $repairs->first();
        $palletObj = $pallet ?? ($firstRepair?->pallet_info ? \App\Models\Pallet::where('barcode_number', $firstRepair->pallet_info)->first() : null);
        $clientObj = $palletObj?->client ?? ($firstRepair?->user_id ? \App\Models\Client::find($firstRepair->user_id) : null);

        $customerName = $firstRepair?->customer_name ?: ($clientObj?->name ?: 'Valued Customer');
        $customerEmail = $firstRepair?->customer_email ?: ($clientObj?->email ?: 'N/A');
        $customerPhone = $firstRepair?->customer_phone ?: ($clientObj?->phone ?: 'N/A');
        $companyName = $clientObj?->company ?: null;
        $address = $clientObj?->address ?: null;

        $palletBarcode = $palletObj?->barcode_number ?: ($firstRepair?->pallet_info ?: 'N/A');
        $jobNo = $palletObj?->job_no ?: 'N/A';
        $location = $palletObj?->put_away_location ?: ($firstRepair?->put_away_location ?: 'N/A');
        $marketingRef = $palletObj?->marketing_reference ?: 'N/A';
        $receivingEmployee = $palletObj?->receiving_employee ?: 'N/A';
        $technician = $firstRepair?->technician ?: 'N/A';
        $repairStatus = $firstRepair?->status ?: ($firstRepair?->repair_status ?: 'Received');

        $intakeDate = $firstRepair
            ? $firstRepair->formatCreatedAtDenver('F j, Y g:i A T')
            : now(\App\Models\RepairEntry::DENVER_TIMEZONE)->format('F j, Y g:i A T');
    @endphp

    <div class="container">
        {{-- Header Banner --}}
        <div class="header">
            <h1>IT Investment Recoveries</h1>
            <p>Repair Intake Receipt & Details</p>
        </div>

        <div class="content">
            <p style="font-size: 15px; margin-top: 0; color: #1e293b;">
                Hello <strong>{{ $customerName }}</strong>,
            </p>
            <p style="font-size: 14px; color: #475569; margin-bottom: 20px;">
                Your repair intake has been successfully recorded. Below are all the details on file for your repair request.
            </p>

            {{-- SECTION 1: Client & Contact Info --}}
            <div class="section-box">
                <div class="section-title">Client Information</div>
                <div class="grid-2">
                    <div class="grid-row">
                        <div class="grid-cell">
                            <span class="data-label">Customer Name</span>
                            <span class="data-value">{{ $customerName }}</span>
                        </div>
                        <div class="grid-cell">
                            <span class="data-label">Email Address</span>
                            <span class="data-value">{{ $customerEmail }}</span>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell" style="padding-top: 10px;">
                            <span class="data-label">Phone Number</span>
                            <span class="data-value">{{ $customerPhone }}</span>
                        </div>
                        <div class="grid-cell" style="padding-top: 10px;">
                            <span class="data-label">Company</span>
                            <span class="data-value">{{ $companyName ?: 'Individual' }}</span>
                        </div>
                    </div>
                    @if ($address)
                        <div class="grid-row">
                            <div class="grid-cell" colspan="2" style="width: 100%; padding-top: 10px;">
                                <span class="data-label">Address</span>
                                <span class="data-value">{{ $address }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SECTION 2: Intake & Pallet Information --}}
            <div class="section-box">
                <div class="section-title">Intake & Pallet Details</div>
                <div class="grid-2">
                    <div class="grid-row">
                        <div class="grid-cell">
                            <span class="data-label">Pallet Barcode</span>
                            <span class="data-value font-mono">{{ $palletBarcode }}</span>
                        </div>
                        <div class="grid-cell">
                            <span class="data-label">Job #</span>
                            <span class="data-value">{{ $jobNo }}</span>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell" style="padding-top: 10px;">
                            <span class="data-label">Intake Date</span>
                            <span class="data-value">{{ $intakeDate }}</span>
                        </div>
                        <div class="grid-cell" style="padding-top: 10px;">
                            <span class="data-label">Put-away Location</span>
                            <span class="data-value">{{ $location }}</span>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell" style="padding-top: 10px;">
                            <span class="data-label">Receiving Employee</span>
                            <span class="data-value">{{ $receivingEmployee }}</span>
                        </div>
                        <div class="grid-cell" style="padding-top: 10px;">
                            <span class="data-label">Marketing Reference</span>
                            <span class="data-value">{{ $marketingRef }}</span>
                        </div>
                    </div>
                </div>

                @if ($palletObj?->notes)
                    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                        <span class="data-label">Receiving Notes</span>
                        <div class="notes-box">{{ $palletObj->notes }}</div>
                    </div>
                @endif
            </div>

            {{-- SECTION 3: Repair Items --}}
            @php
                $itemsToDisplay = [];
                if ($repairs->count() === 1 && !empty($firstRepair?->items_list)) {
                    $itemsToDisplay = $firstRepair->items_list;
                } else {
                    foreach ($repairs as $r) {
                        if (!empty($r->items_list) && count($r->items_list) > 1) {
                            foreach ($r->items_list as $it) {
                                $itemsToDisplay[] = $it;
                            }
                        } else {
                            $itemsToDisplay[] = [
                                'name' => $r->device_type ?: 'Repair Item',
                                'brand' => $r->brand_category ?: 'N/A',
                                'model' => $r->item_description ?: 'N/A',
                                'serial' => $r->serial_number ?: 'N/A',
                                'barcode' => $r->item_barcode ?: 'N/A',
                                'status' => $r->status ?: 'Received',
                            ];
                        }
                    }
                }
            @endphp
            <div style="margin-bottom: 24px;">
                <div class="section-title" style="margin-bottom: 14px;">
                    Repair Items ({{ count($itemsToDisplay) }})
                </div>

                @foreach ($itemsToDisplay as $idx => $item)
                    @php
                        $iName = $item['name'] ?? 'Repair Item';
                        $iBrand = $item['brand'] ?? 'N/A';
                        $iModel = $item['model'] ?? 'N/A';
                        $iSerial = $item['serial'] ?? 'N/A';
                        $iBarcode = $item['barcode'] ?? ($firstRepair?->item_barcode ?? 'N/A');
                        $iStatus = $item['status'] ?? ($firstRepair?->status ?: 'Received');
                    @endphp
                    <div class="item-card">
                        <div class="item-header">
                            <span>Item #{{ $idx + 1 }}: {{ $iName }}</span>
                            <span class="badge {{ strtolower($iStatus) === 'completed' ? 'badge-green' : 'badge-amber' }}">
                                {{ $iStatus }}
                            </span>
                        </div>
                        <div class="item-body">
                            <table class="item-table" role="presentation">
                                <tr>
                                    <td class="label-col">Item Name:</td>
                                    <td class="val-col"><strong>{{ $iName }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="label-col">Brand:</td>
                                    <td class="val-col">{{ $iBrand ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Model / Description:</td>
                                    <td class="val-col">{{ $iModel ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Serial Number:</td>
                                    <td class="val-col">
                                        <code style="font-size: 13px; font-weight: 700; color: #1e293b;">{{ $iSerial ?: 'N/A' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-col">Item Barcode:</td>
                                    <td class="val-col">
                                        <code style="font-size: 13px; font-weight: 700; color: #2563eb;">{{ $iBarcode ?: 'N/A' }}</code>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- SECTION 4: Technician / Service Information --}}
            <div class="section-box" style="background: #f0f7ff; border-color: #dbeafe;">
                <div class="section-title" style="color: #1e40af; border-color: #bfdbfe;">
                    Technician & Service Information
                </div>
                <div class="grid-2">
                    <div class="grid-row">
                        <div class="grid-cell">
                            <span class="data-label">Assigned Technician</span>
                            <span class="data-value">{{ $technician }}</span>
                        </div>
                        <div class="grid-cell">
                            <span class="data-label">Repair Status</span>
                            <span class="badge {{ strtolower($repairStatus) === 'completed' ? 'badge-green' : 'badge-amber' }}">
                                {{ $repairStatus }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($firstRepair?->notes)
                    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #bfdbfe;">
                        <span class="data-label">Service & Technician Notes</span>
                        <div class="notes-box" style="background: #ffffff; border-color: #bfdbfe; color: #1e3a8a;">{{ $firstRepair->notes }}</div>
                    </div>
                @endif
            </div>

            <p style="font-size: 13px; color: #64748b; margin-bottom: 0;">
                If you have any questions about your repair service, please reply directly to this email or call us with your Job # <strong>{{ $jobNo }}</strong>.
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p style="margin: 0 0 6px 0;"><strong>IT Investment Recoveries</strong></p>
            <p style="margin: 0;">Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
