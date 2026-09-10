<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 24px 12px;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .header {
            background-color: #035c43;
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #035c43;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .body-content {
            padding: 28px 24px;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            background-color: #ecfdf5;
            color: #035c43;
            font-weight: 700;
            font-size: 13px;
            border-radius: 20px;
            border: 1px solid #a7f3d0;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .table th, .table td {
            text-align: left;
            padding: 12px 14px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }
        .table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 700;
            width: 40%;
        }
        .table td {
            color: #111827;
            font-weight: 500;
        }
        .section-title {
            margin-top: 24px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #111827;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .message-card {
            background-color: #f9fafb;
            border-left: 4px solid #035c43;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            font-size: 14px;
            color: #374151;
            white-space: pre-line;
            line-height: 1.6;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #f3f4f6;
        }
        .footer strong {
            color: #035c43;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>IT Investment Recoveries</h1>
            <p>{{ $formType === 'pickup' ? 'New Pickup Inquiry' : 'New General Inquiry' }}</p>
        </div>

        <!-- Body Content -->
        <div class="body-content">
            <div class="badge">
                Source: {{ $formType === 'pickup' ? 'Submit a Pickup Inquiry' : 'Submit a General Inquiry' }}
            </div>

            <table class="table">
                <tr>
                    <th>Full Name</th>
                    <td>{{ $data['name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Email Address</th>
                    <td>
                        <a href="mailto:{{ $data['email'] ?? '' }}" style="color: #035c43; font-weight: 600; text-decoration: none;">
                            {{ $data['email'] ?? 'N/A' }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>{{ $data['phone'] ?? 'Not Provided' }}</td>
                </tr>
                @if($formType === 'pickup')
                <tr>
                    <th>Business Name</th>
                    <td>{{ $data['business_name'] ?? 'Not Provided' }}</td>
                </tr>
                <tr>
                    <th>Requested Service</th>
                    <td><strong style="color: #035c43;">{{ $data['service_type'] ?? 'Not Specified' }}</strong></td>
                </tr>
                <tr>
                    <th>Onsite Media Destruction</th>
                    <td>{{ $data['onsite_destruction'] ?? 'No' }}</td>
                </tr>
                <tr>
                    <th>Storage Media Destruction</th>
                    <td>{{ $data['destruction_type'] ?? 'N/A' }}</td>
                </tr>
                @endif
            </table>

            <div class="section-title">
                {{ $formType === 'pickup' ? 'Equipment / Pickup Description' : 'Message Details' }}
            </div>
            <div class="message-card">
                {{ $formType === 'pickup' ? ($data['description'] ?? 'No description provided.') : ($data['message'] ?? 'No message body provided.') }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Submitted via <strong>IT Investment Recoveries Website Contact Portal</strong><br>
            Date: {{ now()->format('F j, Y - g:i A T') }}
        </div>
    </div>
</body>
</html>
