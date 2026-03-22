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
        .status-badge {
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
        .status-message {
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
            width: 140px;
            color: #0D1B2A;
        }
        .courier-info {
            background: #f0f8ff;
            padding: 15px;
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
            <div class="status-badge">{{ strtoupper(str_replace('_', ' ', $data['status'])) }}</div>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $data['client']->first_name }} {{ $data['client']->last_name }},</strong>
            </div>

            <div class="status-message">
                <p><strong>{{ $data['status_message'] }}</strong></p>
                <p>Request #{{ $data['request']->request_number }} has been updated to: <strong>{{ ucfirst(str_replace('_', ' ', $data['status'])) }}</strong></p>
                @if(isset($data['updated_at']))
                <p><small>Updated on: {{ $data['updated_at']->format('F j, Y g:i A') }}</small></p>
                @endif
            </div>

            <div class="request-details">
                <h3>Request Details</h3>
                <div class="detail-row">
                    <strong>Request Number:</strong> {{ $data['request']->request_number }}
                </div>
                <div class="detail-row">
                    <strong>Specimen Type:</strong> {{ ucfirst($data['request']->specimen_type) }}
                </div>
                <div class="detail-row">
                    <strong>Priority Level:</strong> {{ ucfirst($data['request']->priority_level) }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Address:</strong> {{ $data['pickup_address'] ?? $data['request']->pickup_address }}
                </div>
                <div class="detail-row">
                    <strong>Delivery Address:</strong> {{ $data['delivery_address'] ?? $data['request']->delivery_address }}
                </div>
                @if(isset($data['recipient_name']))
                <div class="detail-row">
                    <strong>Recipient:</strong> {{ $data['recipient_name'] }}
                </div>
                @endif
            </div>

            @if(isset($data['courier']))
            <div class="courier-info">
                <h4>🚚 Courier Information</h4>
                <p><strong>Name:</strong> {{ $data['courier']->first_name }} {{ $data['courier']->last_name }}</p>
                <p><strong>Phone:</strong> {{ $data['courier']->phone }}</p>
                @if(isset($data['courier']->vehicle_type))
                <p><strong>Vehicle:</strong> {{ $data['courier']->vehicle_type }}</p>
                @endif
            </div>
            @endif

            @if(isset($data['delivery_notes']))
            <div class="courier-info">
                <h4>📝 Delivery Notes</h4>
                <p>{{ $data['delivery_notes'] }}</p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ $data['dashboard_url'] }}" class="button">View Request Details</a>
                @if(isset($data['tracking_url']))
                <a href="{{ $data['tracking_url'] }}" class="button" style="background: #6c757d;">Track in Real-Time</a>
                @endif
                @if(isset($data['feedback_url']))
                <a href="{{ $data['feedback_url'] }}" class="button" style="background: #ffc107;">Leave Feedback</a>
                @endif
            </div>

            <div class="footer">
                <p>If you have any questions, please contact our support team at <a href="mailto:support@neoprolab.com">support@neoprolab.com</a> or call (774) 297-0597.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>