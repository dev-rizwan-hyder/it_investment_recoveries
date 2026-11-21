<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Repair Intake Receipt</title>
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
        $firstRepair = $primaryRepair ?? $repairs->first();
        $palletObj = $pallet ?? ($firstRepair?->pallet_info ? \App\Models\Pallet::where('barcode_number', $firstRepair->pallet_info)->first() : null);
        $clientObj = $client ?? $palletObj?->client ?? ($firstRepair?->user_id ? \App\Models\Client::find($firstRepair->user_id) : null);

        $customerName = $firstRepair?->customer_name ?: ($clientObj?->name ?: 'Valued Customer');
        $customerEmail = $firstRepair?->customer_email ?: ($clientObj?->email ?: 'N/A');
        $customerPhone = $firstRepair?->customer_phone ?: ($clientObj?->phone ?: 'N/A');
        $companyName = $clientObj?->company ?: null;
        $address = $clientObj?->address ?: null;

        $palletBarcode = $palletObj?->barcode_number ?: ($firstRepair?->pallet_info ?: 'N/A');
        $jobNoDisplay = $jobNo ?? $palletObj?->job_no ?? $firstRepair?->job_no;
        $headerReference = !empty($jobNoDisplay)
            ? (preg_match('/^job/i', trim($jobNoDisplay)) ? trim($jobNoDisplay) : 'Job #: ' . trim($jobNoDisplay))
            : ($receiptNumber ?? ($palletBarcode !== 'N/A' ? $palletBarcode : ('REP-' . now('America/Denver')->format('YmdHis'))));

        $location = $palletObj?->put_away_location ?: ($firstRepair?->put_away_location ?: 'N/A');
        $marketingRef = $palletObj?->marketing_reference ?: 'N/A';
        $receivingEmployee = $palletObj?->receiving_employee ?: 'N/A';
        $technician = $firstRepair?->technician ?: 'N/A';
        $repairStatus = $firstRepair?->status ?: ($firstRepair?->repair_status ?: 'Received');

        $rawDate = $firstRepair?->created_at ? (string) $firstRepair->created_at : ($palletObj?->created_at ? (string) $palletObj->created_at : null);
        if ($rawDate && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $rawDate, $m)) {
            $createdAt = \Carbon\Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3]);
        } else {
            $createdAt = $firstRepair?->created_at ? \Carbon\Carbon::parse($firstRepair->created_at) : now();
        }

        $intakeDateFormatted = $firstRepair && method_exists($firstRepair, 'formatCreatedAtDenver')
            ? $firstRepair->formatCreatedAtDenver('F j, Y g:i A T')
            : $createdAt->format('F j, Y');

        $portalLink = $registerUrl ?? $trackingUrl ?? 'https://it-investmentrecoveries.com/register';
        $directLoginLink = $loginUrl ?? 'https://it-investmentrecoveries.com/login';

        // Collect items to display
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
                        'brand' => $r->brand_category ?: '',
                        'model' => $r->item_description ?: '',
                        'serial' => $r->serial_number ?: '',
                        'barcode' => $r->item_barcode ?: '',
                        'status' => $r->status ?: ($r->repair_status ?: 'Received'),
                        'category' => $r->category ?: '',
                        'sub_category' => $r->sub_category ?: '',
                        'os' => $r->operating_system ?: '',
                        'specs' => $r->device_specs ?: '',
                        'price' => $r->price,
                        'cost_estimate' => $r->cost_estimate,
                    ];
                }
            }
        }
        $totalItemsCount = count($itemsToDisplay) ?: 1;
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
                                        Repair & Service Intake Receipt
                                    </span>
                                    <h1 style="margin: 4px 0 0 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
                                        Repair Intake Confirmation
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
                            Dear <strong>{{ $customerName }}</strong>,
                        </p>
                        <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #475569;">
                            We have successfully received your repair intake. Below is the official summary receipt and item details recorded on <strong>{{ $createdAt->format('F j, Y') }}</strong>.
                        </p>

                        <!-- Metric Summary Grid -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
                            <tr>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Items</div>
                                        <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $totalItemsCount }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Status</div>
                                        <div style="font-size: 12px; font-weight: 800; color: #2563eb; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $repairStatus }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Job #</div>
                                        <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 4px; font-family: monospace;">{{ $jobNoDisplay ?: 'N/A' }}</div>
                                    </div>
                                </td>
                                <td width="25%" style="padding: 4px;">
                                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 10px; text-align: center;">
                                        <div style="font-size: 10px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.05em;">Intake Ref</div>
                                        <div style="font-size: 13px; font-weight: 800; color: #15803d; margin-top: 4px; font-family: monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $palletBarcode !== 'N/A' ? $palletBarcode : ($firstRepair?->item_barcode ?: 'N/A') }}</div>
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
                                Track Your Repair & Service Status
                            </h2>
                            <p style="margin: 0 0 18px 0; font-size: 13px; line-height: 20px; color: #cbd5e1; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Access live diagnostic progress, service milestones, technician notes, and estimates directly through your client dashboard with <strong>{{ $customerEmail }}</strong>.
                            </p>
                            
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
                                <tr>
                                    <td style="padding: 4px;">
                                        <a href="{{ $portalLink }}" target="_blank" class="btn-primary" style="background-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px; display: inline-block;">
                                            Register to view complete repair details
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

            <!-- CLIENT & SERVICE DETAILS -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 24px 28px;">
                <tr>
                    <td>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background-color: #f1f5f9; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                    <span style="font-size: 12px; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 0.08em;">
                                        Client & Service Details
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td width="50%" style="vertical-align: top; padding-right: 12px;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Client Information</div>
                                                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 4px;">{{ $customerName }}</div>
                                                @if (!empty($companyName))
                                                    <div style="font-size: 13px; color: #475569; margin-top: 2px;">{{ $companyName }}</div>
                                                @endif
                                                <div style="font-size: 12px; color: #64748b; margin-top: 4px; line-height: 1.6;">
                                                    Email: {{ $customerEmail }}<br>
                                                    Phone: {{ $customerPhone }}
                                                </div>
                                                @if (!empty($address))
                                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                        Address: {{ $address }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td width="50%" style="vertical-align: top; padding-left: 12px; border-left: 1px solid #e2e8f0;">
                                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Intake Reference</div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 4px;">
                                                    @if (!empty($jobNoDisplay))
                                                        Job #: <strong>{{ $jobNoDisplay }}</strong>
                                                    @else
                                                        Receipt ID: <strong>{{ $headerReference }}</strong>
                                                    @endif
                                                </div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                    Main Barcode: <strong>{{ $palletBarcode !== 'N/A' ? $palletBarcode : ($firstRepair?->item_barcode ?: 'N/A') }}</strong>
                                                </div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                    Received Date: <strong>{{ $createdAt->format('M j, Y') }}</strong>
                                                </div>
                                                <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                    Initial Status: <span style="display: inline-block; padding: 2px 8px; background-color: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: capitalize;">{{ $repairStatus }}</span>
                                                </div>
                                                @if (!empty($location) && $location !== 'N/A')
                                                    <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                        Location: <strong>{{ $location }}</strong>
                                                    </div>
                                                @endif
                                                @if (!empty($technician) && $technician !== 'N/A')
                                                    <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                        Assigned Tech: <strong>{{ $technician }}</strong>
                                                    </div>
                                                @endif
                                                @if (!empty($receivingEmployee) && $receivingEmployee !== 'N/A')
                                                    <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                        Receiving Tech: <strong>{{ $receivingEmployee }}</strong>
                                                    </div>
                                                @endif
                                                @if (!empty($marketingRef) && $marketingRef !== 'N/A' && $marketingRef !== 'No reference')
                                                    <div style="font-size: 13px; color: #334155; margin-top: 2px;">
                                                        Ref: <strong>{{ $marketingRef }}</strong>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- REPAIR ITEMIZATION TABLE -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 28px 28px;">
                <tr>
                    <td>
                        <div style="font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">
                            Received Repair Itemization ({{ $totalItemsCount }})
                        </div>

                        <div class="table-responsive">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background-color: #0f172a; color: #ffffff;">
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; width: 45px;">Unit</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em;">Description & Specifications</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; width: 140px;">Identifier / Barcode</th>
                                        <th style="padding: 10px 12px; font-size: 11px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; width: 90px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemsToDisplay as $index => $item)
                                        @php
                                            $rowBg = ($index % 2 === 0) ? '#ffffff' : '#f8fafc';
                                            $iName = $item['name'] ?? ($item['device_type'] ?? 'Repair Item');
                                            $iBrand = $item['brand'] ?? ($item['brand_category'] ?? '');
                                            $iModel = $item['model'] ?? ($item['item_description'] ?? '');
                                            $iSerial = $item['serial'] ?? ($item['serial_number'] ?? '');
                                            $iBarcode = $item['barcode'] ?? ($item['item_barcode'] ?? ($firstRepair?->item_barcode ?? ''));
                                            $iStatus = $item['status'] ?? ($item['repair_status'] ?? ($firstRepair?->status ?: 'Received'));
                                            $isCompleted = strtolower($iStatus) === 'completed';
                                        @endphp
                                        <tr style="background-color: {{ $rowBg }}; border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 12px; font-size: 12px; font-weight: 700; color: #2563eb; vertical-align: top;">
                                                #{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #0f172a; vertical-align: top;">
                                                <strong style="color: #0f172a; font-size: 14px;">{{ $iName }}</strong>
                                                @if (!empty($iBrand))
                                                    <span style="display: inline-block; background-color: #f1f5f9; color: #475569; padding: 1px 6px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 4px;">{{ $iBrand }}</span>
                                                @endif
                                                
                                                @if (!empty($iModel))
                                                    <div style="font-size: 12px; color: #475569; margin-top: 4px; line-height: 1.4;">
                                                        Model / Desc: <span style="color: #0f172a; font-weight: 600;">{{ $iModel }}</span>
                                                    </div>
                                                @endif

                                                @if (!empty($item['specs']) || !empty($item['os']))
                                                    <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                                        @if(!empty($item['os'])) OS: <strong>{{ $item['os'] }}</strong> @endif
                                                        @if(!empty($item['os']) && !empty($item['specs'])) &bull; @endif
                                                        @if(!empty($item['specs'])) Specs: {{ $item['specs'] }} @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="padding: 12px; font-size: 12px; color: #334155; vertical-align: top;">
                                                @if (!empty($iSerial))
                                                    <div style="font-size: 11px; color: #64748b;">Serial:</div>
                                                    <code style="font-family: monospace; font-size: 12px; font-weight: 700; color: #0f172a;">{{ $iSerial }}</code>
                                                @endif
                                                @if (!empty($iBarcode))
                                                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Barcode:</div>
                                                    <code style="font-family: monospace; font-size: 12px; font-weight: 700; color: #2563eb;">{{ $iBarcode }}</code>
                                                @endif
                                                @if (empty($iSerial) && empty($iBarcode))
                                                    <span style="color: #94a3b8;">N/A</span>
                                                @endif
                                            </td>
                                            <td style="padding: 12px; text-align: right; vertical-align: top;">
                                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; background-color: {{ $isCompleted ? '#dcfce7' : '#eff6ff' }}; color: {{ $isCompleted ? '#15803d' : '#1d4ed8' }}; border: 1px solid {{ $isCompleted ? '#bbf7d0' : '#bfdbfe' }};">
                                                    {{ $iStatus }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f1f5f9; font-weight: 800; color: #0f172a; border-top: 2px solid #cbd5e1;">
                                        <td colspan="3" style="padding: 12px; font-size: 12px; text-align: right; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">
                                            Total Units Received:
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; text-align: right; color: #2563eb;">
                                            {{ $totalItemsCount }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- NOTES SECTION (IF ANY) -->
            @if (!empty($firstRepair?->notes) || !empty($palletObj?->notes))
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="padding: 0 28px 24px 28px;">
                    <tr>
                        <td>
                            @if (!empty($firstRepair?->notes))
                                <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px 18px; margin-bottom: 12px;">
                                    <div style="font-size: 11px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                        Technician & Service Notes
                                    </div>
                                    <div style="font-size: 13px; line-height: 20px; color: #1e3a8a; white-space: pre-wrap;">{{ $firstRepair->notes }}</div>
                                </div>
                            @endif

                            @if (!empty($palletObj?->notes))
                                <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 14px 18px;">
                                    <div style="font-size: 11px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                        Receiving Notes
                                    </div>
                                    <div style="font-size: 13px; line-height: 20px; color: #78350f; white-space: pre-wrap;">{{ $palletObj->notes }}</div>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            @endif

            <!-- FOOTER -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 28px;">
                <tr>
                    <td style="text-align: center; color: #64748b; font-size: 12px; line-height: 18px;">
                        <p style="margin: 0 0 8px 0; font-weight: 700; color: #334155; font-size: 13px;">
                            IT Investment Recoveries
                        </p>
                        <p style="margin: 0 0 12px 0; color: #64748b;">
                            Thank you for partnering with us for your electronics repair, recycling, and asset recovery needs.
                        </p>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                            If you have questions regarding this repair intake, please contact our support team at
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
