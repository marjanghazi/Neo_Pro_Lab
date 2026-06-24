<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Received - {{ $appName }}</title>
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
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .user-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .user-details h3 {
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
            width: 100px;
            color: #0D1B2A;
        }
        .next-steps {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
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
            <h1>Welcome to {{ $appName }}!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $user->first_name }} {{ $user->last_name }},</strong>
            </div>

            <p>Thank you for registering as a {{ $roleName }} with {{ $appName }}. We have received your registration request.</p>

            <div class="info-box">
                <p><strong>📋 Registration Status:</strong> Pending Approval</p>
                <p>Your account is currently pending review by our admin team.</p>
            </div>

            <div class="user-details">
                <h3>Registration Details</h3>
                <div class="detail-row">
                    <strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}
                </div>
                <div class="detail-row">
                    <strong>Email:</strong> {{ $user->email }}
                </div>
                <div class="detail-row">
                    <strong>Phone:</strong> {{ $user->phone }}
                </div>
                <div class="detail-row">
                    <strong>Role:</strong> {{ ucfirst($roleName) }}
                </div>
                <div class="detail-row">
                    <strong>Registered On:</strong> {{ $user->created_at->format('F j, Y g:i A') }}
                </div>
            </div>

            <div class="next-steps">
                <h4>📝 What's Next?</h4>
                <ol style="margin: 0; padding-left: 20px;">
                    <li>Our admin team will review your registration</li>
                    @if($role === 'courier')
                    <li>We will verify your submitted documents</li>
                    @endif
                    <li>You will receive an email notification once your account is approved</li>
                    <li>After approval, you can log in to your dashboard</li>
                </ol>
            </div>

            <div class="footer">
                <p>If you have any questions, please contact our support team at <a href="mailto:support@neoprolab.com">support@neoprolab.com</a> or call (508) 933-6750.</p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>