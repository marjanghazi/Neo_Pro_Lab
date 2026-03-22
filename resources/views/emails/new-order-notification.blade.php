<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Order Notification</title>
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0D1B2A 0%, #1a2f47 50%, #00A9A5 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .order-details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .order-details h3 {
            margin-top: 0;
            color: #00A9A5;
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
        .status-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background: #00A9A5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .button:hover {
            background: #008b85;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
        }
        .price-breakdown {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .price-row {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #00A9A5;
            border-top: 2px solid #ddd;
            margin-top: 10px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Order Received!</h1>
            <p>Order #{{ $data['request']->request_number }}</p>
        </div>

        <div class="content">
            <p>Hello Admin,</p>
            <p>A new specimen pickup request has been submitted and is awaiting approval.</p>

            <div class="order-details">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <strong>Request Number:</strong> {{ $data['request']->request_number }}
                </div>
                <div class="detail-row">
                    <strong>Status:</strong> <span class="status-badge">{{ ucfirst(str_replace('_', ' ', $data['request']->status)) }}</span>
                </div>
                <div class="detail-row">
                    <strong>Priority:</strong> {{ ucfirst($data['request']->priority_level) }}
                </div>
                <div class="detail-row">
                    <strong>Specimen Type:</strong> {{ ucfirst($data['request']->specimen_type) }}
                </div>
                <div class="detail-row">
                    <strong>Quantity:</strong> {{ $data['request']->quantity }}
                </div>
                <div class="detail-row">
                    <strong>Temperature:</strong> {{ ucfirst($data['request']->temperature_requirement) }}
                </div>
            </div>

            <div class="order-details">
                <h3>Client Information</h3>
                <div class="detail-row">
                    <strong>Name:</strong> {{ $data['client']->first_name }} {{ $data['client']->last_name }}
                </div>
                <div class="detail-row">
                    <strong>Email:</strong> {{ $data['client']->email }}
                </div>
                <div class="detail-row">
                    <strong>Phone:</strong> {{ $data['client']->phone }}
                </div>
                @if($data['facility'])
                <div class="detail-row">
                    <strong>Facility:</strong> {{ $data['facility']->name }}
                </div>
                @endif
            </div>

            <div class="order-details">
                <h3>Pickup Details</h3>
                <div class="detail-row">
                    <strong>Recipient:</strong> {{ $data['request']->recipient_name }}
                </div>
                <div class="detail-row">
                    <strong>Contact Phone:</strong> {{ $data['validated_data']['contact_phone'] }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Address:</strong> {{ $data['request']->pickup_address }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Date:</strong> {{ \Carbon\Carbon::parse($data['validated_data']['pickup_date'])->format('F j, Y') }}
                </div>
                <div class="detail-row">
                    <strong>Pickup Time:</strong> {{ $data['validated_data']['pickup_time'] }}
                </div>
                <div class="detail-row">
                    <strong>Delivery Address:</strong> {{ $data['request']->delivery_address }}
                </div>
                @if($data['request']->delivery_instructions)
                <div class="detail-row">
                    <strong>Delivery Instructions:</strong> {{ $data['request']->delivery_instructions }}
                </div>
                @endif
            </div>

            @if($data['price_data'])
            <div class="price-breakdown">
                <h3>Price Breakdown</h3>
                <div class="price-row">
                    <span>Base Price:</span>
                    <span>${{ $data['price_data']['base_price'] }}</span>
                </div>
                @if($data['price_data']['distance_charge'] > 0)
                <div class="price-row">
                    <span>Distance Charge ({{ $data['price_data']['distance_miles'] }} miles):</span>
                    <span>${{ $data['price_data']['distance_charge'] }}</span>
                </div>
                @endif
                @if($data['price_data']['priority_charge'] > 0)
                <div class="price-row">
                    <span>Priority Charge:</span>
                    <span>${{ $data['price_data']['priority_charge'] }}</span>
                </div>
                @endif
                @if($data['price_data']['night_charge'] > 0)
                <div class="price-row">
                    <span>Night Service Charge:</span>
                    <span>${{ $data['price_data']['night_charge'] }}</span>
                </div>
                @endif
                @if($data['price_data']['weekend_charge'] > 0)
                <div class="price-row">
                    <span>Weekend/Holiday Charge:</span>
                    <span>${{ $data['price_data']['weekend_charge'] }}</span>
                </div>
                @endif
                @if($data['price_data']['temperature_charge'] > 0)
                <div class="price-row">
                    <span>Temperature Control:</span>
                    <span>${{ $data['price_data']['temperature_charge'] }}</span>
                </div>
                @endif
                @if($data['price_data']['additional_stops'] > 0)
                <div class="price-row">
                    <span>Additional Stops ({{ $data['price_data']['additional_stops'] }}):</span>
                    <span>${{ $data['price_data']['additional_stops_charge'] }}</span>
                </div>
                @endif
                <div class="price-row">
                    <span>Subtotal:</span>
                    <span>${{ $data['price_data']['subtotal'] }}</span>
                </div>
                <div class="price-row">
                    <span>Tax ({{ $data['price_data']['tax_rate'] }}%):</span>
                    <span>${{ $data['price_data']['tax_amount'] }}</span>
                </div>
                <div class="price-row total">
                    <span>Total:</span>
                    <span>${{ $data['price_data']['total_price'] }}</span>
                </div>
            </div>
            @endif

            <div class="order-details">
                <h3>Special Instructions</h3>
                <p>{{ $data['request']->special_instructions ?: 'No special instructions provided.' }}</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $data['dashboard_url'] }}" class="button">View in Admin Dashboard</a>
                <a href="{{ $data['request_url'] }}" class="button" style="background: #0D1B2A;">View Order Details</a>
            </div>

            <div class="footer">
                <p>This is an automated notification. Please review and approve this request.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>