<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pickup Confirmation - {{ $data['request']->request_number }}</title>
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
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .proof-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .condition-good {
            color: #28a745;
            font-weight: bold;
        }
        .condition-acceptable {
            color: #ffc107;
            font-weight: bold;
        }
        .condition-damaged {
            color: #dc3545;
            font-weight: bold;
        }
        .temperature-good {
            color: #28a745;
        }
        .temperature-bad {
            color: #dc3545;
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
            <h1>Pickup Confirmation</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $data['client']->first_name }} {{ $data['client']->last_name }},</strong>
            </div>

            <p>Great news! Your specimen has been picked up successfully by our courier.</p>

            <div class="proof-details">
                <h3>Pickup Details</h3>
                <p><strong>Request Number:</strong> #{{ $data['request']->request_number }}</p>
                <p><strong>Pickup Time:</strong> {{ isset($data['updated_at']) ? $data['updated_at']->format('F j, Y g:i A') : 'Just now' }}</p>
                <p><strong>Specimen Condition:</strong> 
                    <span class="condition-{{ $data['specimen_condition'] }}">
                        {{ ucfirst(str_replace('_', ' ', $data['specimen_condition'])) }}
                    </span>
                </p>
                <p><strong>Temperature Check:</strong> 
                    <span class="temperature-{{ $data['temperature_check'] == 'within_range' ? 'good' : 'bad' }}">
                        {{ ucfirst(str_replace('_', ' ', $data['temperature_check'])) }}
                    </span>
                </p>
                @if(isset($data['pickup_notes']))
                <p><strong>Courier Notes:</strong> {{ $data['pickup_notes'] }}</p>
                @endif
            </div>

            <div class="proof-details">
                <h3>Courier Information</h3>
                <p><strong>Name:</strong> {{ $data['courier']->first_name }} {{ $data['courier']->last_name }}</p>
                <p><strong>Phone:</strong> {{ $data['courier']->phone }}</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $data['dashboard_url'] }}" class="button">Track Your Delivery</a>
            </div>

            <div class="footer">
                <p>Your specimen is now in transit to the delivery location. You will receive updates as it progresses.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>