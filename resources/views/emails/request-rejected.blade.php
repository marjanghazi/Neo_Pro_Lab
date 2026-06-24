<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Update - {{ $data['request']->request_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0D1B2A 0%, #1a2f47 50%, #dc3545 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header .status-badge {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 15px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .request-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .request-details h3 {
            margin-top: 0;
            color: #dc3545;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        .detail-row {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row strong {
            display: inline-block;
            width: 140px;
            color: #0D1B2A;
        }
        .reason-box {
            background: #fff5f5;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .reason-box h4 {
            margin-top: 0;
            color: #dc3545;
        }
        .reason-box p {
            margin: 5px 0;
        }
        .support-box {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #00A9A5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        .button-secondary {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            margin-left: 10px;
            font-weight: bold;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Request Update</h1>
            <div class="status-badge">NOT APPROVED</div>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $data['client']->first_name }} {{ $data['client']->last_name }},</strong>
            </div>

            <p>We regret to inform you that your specimen pickup request could not be approved at this time.</p>

            <div class="request-details">
                <h3>Request Details</h3>
                <div class="detail-row">
                    <strong>Request Number:</strong> {{ $data['request']->request_number }}
                </div>
                <div class="detail-row">
                    <strong>Status:</strong> <span style="color: #dc3545; font-weight: bold;">Not Approved</span>
                </div>
                <div class="detail-row">
                    <strong>Reviewed By:</strong> {{ $data['admin_name'] }}
                </div>
                <div class="detail-row">
                    <strong>Reviewed On:</strong> {{ now()->format('F j, Y g:i A') }}
                </div>
                <div class="detail-row">
                    <strong>Specimen Type:</strong> {{ ucfirst($data['request']->specimen_type) }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Address:</strong> {{ $data['request']->pickup_address }}
                </div>
            </div>

            @if(!empty($data['rejection_reason']))
            <div class="reason-box">
                <h4>📝 Reason for Not Approving</h4>
                <p>{{ $data['rejection_reason'] }}</p>
            </div>
            @endif

            <div class="support-box">
                <h4 style="margin-top: 0;">💡 What Can You Do?</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Review the reason provided above</li>
                    <li>Update your request with the required information</li>
                    <li>Submit a new request with corrected details</li>
                    <li>Contact our support team for assistance</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('client.requests.create') }}" class="button">Submit New Request</a>
                <a href="mailto:support@neoprolab.com" class="button-secondary">Contact Support</a>
            </div>

            <div class="footer">
                <p>If you need help understanding why your request wasn't approved or need assistance with a new request, please don't hesitate to contact us at <a href="mailto:support@neoprolab.com">support@neoprolab.com</a> or call (508) 933-6750.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>