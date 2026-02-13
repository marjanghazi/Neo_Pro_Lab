{{-- resources/views/emails/courier-approved.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Account Approved</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 0;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background: linear-gradient(135deg, #059669, #047857);
        }
        .details {
            background-color: #f9fafb;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .details h3 {
            margin-top: 0;
            color: #059669;
            font-size: 16px;
        }
        .details p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Account Approved!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $courier->full_name }}</strong>,
            </div>
            
            <div class="message">
                <p>Great news! Your courier account has been <strong style="color: #10b981;">approved</strong> by our admin team. You can now start accepting delivery requests and earning with NeoPro Lab!</p>
            </div>
            
            <div class="details">
                <h3>Your Account Details:</h3>
                <p><strong>Name:</strong> {{ $courier->full_name }}</p>
                <p><strong>Email:</strong> {{ $courier->email }}</p>
                <p><strong>Status:</strong> <span style="color: #10b981;">Active & Verified</span></p>
                <p><strong>Approved on:</strong> {{ now()->format('F j, Y') }}</p>
            </div>
            
            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
            </p>
            
            <p>Once logged in, you can:</p>
            <ul style="color: #555;">
                <li>View available delivery requests</li>
                <li>Accept deliveries in your area</li>
                <li>Track your earnings</li>
                <li>Update your availability status</li>
            </ul>
            
            <p>If you have any questions, please don't hesitate to contact our support team.</p>
            
            <p>Best regards,<br><strong>The NeoPro Lab Team</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} NeoPro Lab. All rights reserved.</p>
            <p style="font-size: 12px;">This email was sent to {{ $courier->email }}</p>
        </div>
    </div>
</body>
</html>