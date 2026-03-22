<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Quote - {{ $data['request']->request_number }}</title>
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
        .quote-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 15px;
            font-weight: bold;
        }
        .deadline-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .price-box {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .price-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .button-accept {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 5px;
        }
        .button-decline {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 5px;
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
            <h1>New Quote Request</h1>
            <div class="quote-badge">AWAITING YOUR RESPONSE</div>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $data['courier']->first_name }} {{ $data['courier']->last_name }},</strong>
            </div>

            <p>You have received a new quote for a pickup request. Please review the details and accept or decline by the deadline.</p>

            <div class="deadline-box">
                <strong>⏰ Quote Deadline:</strong> {{ \Carbon\Carbon::parse($data['deadline'])->format('F j, Y g:i A') }}
                <br>
                <small>Please respond before the deadline to accept this request.</small>
            </div>

            <div class="price-box">
                <div>Your Fee:</div>
                <div class="amount">${{ number_format($data['courier_fee'], 2) }}</div>
                <div style="margin-top: 10px; font-size: 14px;">Total Request Value: ${{ number_format($data['estimated_price'], 2) }}</div>
            </div>

            <div class="action-buttons">
                <a href="{{ $data['dashboard_url'] }}?action=accept" class="button-accept">✓ Accept Quote</a>
                <a href="{{ $data['dashboard_url'] }}?action=decline" class="button-decline">✗ Decline Quote</a>
            </div>

            <div class="footer">
                <p>If you have any questions about this quote, please contact the admin.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>