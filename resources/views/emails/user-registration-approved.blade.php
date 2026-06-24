<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Approved - Welcome to {{ $appName }}</title>
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
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .credentials {
            background: #f9f9f9;
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
        .button:hover {
            background: #008b85;
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
            <h1>Account Approved! 🎉</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Congratulations {{ $user->first_name }} {{ $user->last_name }}!</strong>
            </div>

            <div class="success-box">
                <p><strong>✅ Your account has been approved!</strong></p>
                <p>You can now log in to your {{ $roleName }} dashboard and start using our services.</p>
            </div>

            <div class="credentials">
                <h3>Your Account Information</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ ucfirst($roleName) }}</p>
                <p><strong>Status:</strong> Active</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
            </div>

            @if($role === 'courier')
            <div class="success-box" style="background: #e8f5e9; margin-top: 20px;">
                <h4>🚚 Next Steps for Couriers:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Log in to your courier dashboard</li>
                    <li>Complete your profile information</li>
                    <li>Set up your availability</li>
                    <li>Start accepting pickup requests</li>
                </ul>
            </div>
            @else
            <div class="success-box" style="background: #e8f5e9; margin-top: 20px;">
                <h4>📦 Next Steps for Clients:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Log in to your client dashboard</li>
                    <li>Complete your facility/profile information</li>
                    <li>Create your first specimen pickup request</li>
                    <li>Track your deliveries in real-time</li>
                </ul>
            </div>
            @endif

            <div class="footer">
                <p>If you have any questions or need assistance, please contact our support team at <a href="mailto:support@neoprolab.com">support@neoprolab.com</a> or call (508) 933-6750.</p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>