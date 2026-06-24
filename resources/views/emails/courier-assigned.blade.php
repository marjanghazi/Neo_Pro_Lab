<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Assignment - {{ $data['request']->request_number }}</title>
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
        .header .assignment-badge {
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
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            width: 160px;
            color: #0D1B2A;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background: #00A9A5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 5px;
        }
        .button-secondary {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 5px;
        }
        .button:hover {
            background: #008b85;
        }
        .quick-actions {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .quick-actions h4 {
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
        .priority-high {
            color: #dc3545;
            font-weight: bold;
        }
        .priority-normal {
            color: #ffc107;
            font-weight: bold;
        }
        .priority-low {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Assignment! 🚚</h1>
            <div class="assignment-badge">NEW ASSIGNMENT</div>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $data['courier']->first_name }} {{ $data['courier']->last_name }},</strong>
            </div>

            <p>You have been assigned to a new pickup request. Please review the details below and start the pickup process.</p>

            <div class="info-box">
                <p><strong>📋 Request Number:</strong> #{{ $data['request']->request_number }}</p>
                <p><strong>⏰ Assigned On:</strong> {{ $data['assigned_at']->format('F j, Y g:i A') }}</p>
                <p><strong>👤 Assigned By:</strong> {{ $data['admin']->first_name }} {{ $data['admin']->last_name }}</p>
            </div>

            <div class="request-details">
                <h3>📦 Request Details</h3>
                <div class="detail-row">
                    <strong>Priority Level:</strong>
                    @if($data['priority_level'] == 'stat')
                        <span class="priority-high">STAT - URGENT</span>
                    @elseif($data['priority_level'] == 'rush')
                        <span class="priority-normal">Rush</span>
                    @else
                        <span class="priority-low">Normal</span>
                    @endif
                </div>
                <div class="detail-row">
                    <strong>Specimen Type:</strong> {{ ucfirst($data['specimen_type']) }}
                </div>
                <div class="detail-row">
                    <strong>Quantity:</strong> {{ $data['request']->quantity }}
                </div>
                @if($data['estimated_price'])
                <div class="detail-row">
                    <strong>Estimated Price:</strong> ${{ number_format($data['estimated_price'], 2) }}
                </div>
                @endif
            </div>

            <div class="request-details">
                <h3>📍 Pickup Information</h3>
                <div class="detail-row">
                    <strong>Client Name:</strong> {{ $data['client']->first_name }} {{ $data['client']->last_name }}
                </div>
                <div class="detail-row">
                    <strong>Client Phone:</strong> {{ $data['client']->phone }}
                </div>
                <div class="detail-row">
                    <strong>Client Email:</strong> {{ $data['client']->email }}
                </div>
                <div class="detail-row">
                    <strong>Recipient Name:</strong> {{ $data['request']->recipient_name }}
                </div>
                <div class="detail-row">
                    <strong>Contact Phone:</strong> {{ $data['request']->contact_phone ?? $data['client']->phone }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Address:</strong> {{ $data['pickup_address'] }}
                </div>
                <div class="detail-row">
                    <strong>Scheduled Pickup:</strong> {{ \Carbon\Carbon::parse($data['scheduled_pickup'])->format('F j, Y g:i A') }}
                </div>
            </div>

            <div class="request-details">
                <h3>🏁 Delivery Information</h3>
                <div class="detail-row">
                    <strong>Delivery Address:</strong> {{ $data['delivery_address'] }}
                </div>
                @if($data['request']->delivery_instructions)
                <div class="detail-row">
                    <strong>Delivery Instructions:</strong> {{ $data['request']->delivery_instructions }}
                </div>
                @endif
            </div>

            @if($data['special_instructions'])
            <div class="request-details">
                <h3>📝 Special Instructions</h3>
                <p>{{ $data['special_instructions'] }}</p>
            </div>
            @endif

            <div class="quick-actions">
                <h4>⚡ Quick Actions</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Review all request details in your dashboard</li>
                    <li>Contact the client if you need more information</li>
                    <li>Navigate to the pickup location</li>
                    <li>Start the pickup process when you arrive</li>
                    <li>Update status as you complete each step</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="{{ $data['dashboard_url'] }}" class="button">View Request Details</a>
                <a href="tel:{{ $data['client']->phone }}" class="button-secondary">Call Client</a>
            </div>

            <div class="footer">
                <p><strong>Need Help?</strong> Contact admin at <a href="mailto:admin@neoprolab.com">admin@neoprolab.com</a> or call (508) 933-6750</p>
                <p>Please complete this assignment as soon as possible. Update the status in your dashboard to keep everyone informed.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>