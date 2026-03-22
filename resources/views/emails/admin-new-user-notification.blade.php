<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New User Registration - {{ $appName }}</title>
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
        .alert-box {
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
            width: 120px;
            color: #0D1B2A;
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
        .button-danger {
            background: #dc3545;
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
            <h1>New User Registration</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello Admin,</strong>
            </div>

            <div class="alert-box">
                <p><strong>⚠️ A new user has registered and is pending approval!</strong></p>
                <p>Please review their information and approve or reject the registration.</p>
            </div>

            <div class="user-details">
                <h3>User Information</h3>
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
                @if($role === 'courier')
                <div class="detail-row">
                    <strong>Documents:</strong> Submitted for verification
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ $adminUrl }}" class="button">Review Registration</a>
            </div>

            <div class="footer">
                <p>This is an automated notification. Please take action on this registration request.</p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>