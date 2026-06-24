<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Approved - {{ $data['request']->request_number }}</title>
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
            background: linear-gradient(135deg, #0D1B2A 0%, #1a2f47 50%, #00A9A5 100%);
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
            background: #28a745;
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
            color: #00A9A5;
            border-bottom: 2px solid #00A9A5;
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
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box p {
            margin: 5px 0;
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
        .button:hover {
            background: #008b85;
        }
        .next-steps {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .next-steps h4 {
            margin-top: 0;
            color: #00A9A5;
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
            <h1>Request Approved! 🎉</h1>
            <div class="status-badge">APPROVED</div>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $data['client']->first_name }} {{ $data['client']->last_name }},</strong>
            </div>

            <p>Great news! Your specimen pickup request has been <strong style="color: #28a745;">APPROVED</strong> by our admin team.</p>

            <div class="request-details">
                <h3>Request Details</h3>
                <div class="detail-row">
                    <strong>Request Number:</strong> {{ $data['request']->request_number }}
                </div>
                <div class="detail-row">
                    <strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Approved</span>
                </div>
                <div class="detail-row">
                    <strong>Approved By:</strong> {{ $data['admin_name'] }}
                </div>
                <div class="detail-row">
                    <strong>Approved On:</strong> {{ $data['approved_at']->format('F j, Y g:i A') }}
                </div>
                <div class="detail-row">
                    <strong>Specimen Type:</strong> {{ ucfirst($data['request']->specimen_type) }}
                </div>
                <div class="detail-row">
                    <strong>Priority Level:</strong> {{ ucfirst($data['request']->priority_level) }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Address:</strong> {{ $data['request']->pickup_address }}
                </div>
                <div class="detail-row">
                    <strong>Delivery Address:</strong> {{ $data['request']->delivery_address }}
                </div>
                <div class="detail-row">
                    <strong>Scheduled Pickup:</strong> {{ \Carbon\Carbon::parse($data['request']->scheduled_pickup_time)->format('F j, Y g:i A') }}
                </div>
            </div>

            @if($data['request']->estimated_price)
            <div class="info-box">
                <p><strong>💰 Estimated Price:</strong> ${{ number_format($data['request']->estimated_price, 2) }}</p>
                <p><strong>💳 Payment Status:</strong> {{ ucfirst($data['request']->payment_status) }}</p>
                @if($data['request']->payment_status == 'pending')
                <p style="margin-top: 10px;">Please complete the payment to proceed with the pickup.</p>
                @endif
            </div>
            @endif

            <div class="next-steps">
                <h4>📋 What's Next?</h4>
                <ol style="margin: 0; padding-left: 20px;">
                    <li>Complete payment if not already done</li>
                    <li>We'll assign a courier to your request</li>
                    <li>You'll receive updates when a courier is assigned</li>
                    <li>Track your request in real-time from your dashboard</li>
                </ol>
            </div>

            <div style="text-align: center;">
                <a href="{{ $data['dashboard_url'] }}" class="button">View Request Details</a>
            </div>

            <div class="footer">
                <p>If you have any questions, please contact our support team at <a href="mailto:support@neoprolab.com">support@neoprolab.com</a> or call us at (508) 933-6750.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>