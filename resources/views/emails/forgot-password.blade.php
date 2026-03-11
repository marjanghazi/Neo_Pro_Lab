<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0B1E33 0%, #1A2F48 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }
        .logo span {
            color: #0A9396;
        }
        .content {
            padding: 40px 30px;
            background: #fff;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #0A9396 0%, #005F73 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 10px rgba(10, 147, 150, 0.3);
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(10, 147, 150, 0.4);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #64748B;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .expiry-note {
            background: #F1F5F9;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            color: #334155;
        }
        .link {
            word-break: break-all;
            color: #0A9396;
            font-size: 14px;
            margin: 15px 0;
            padding: 10px;
            background: #F1F5F9;
            border-radius: 6px;
        }
        hr {
            border: none;
            border-top: 1px solid #e9ecef;
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Neo<span>ProLab</span></div>
            <p style="opacity: 0.9; margin: 10px 0 0;">Password Reset Request</p>
        </div>
        
        <div class="content">
            <h2 style="color: #0B1E33; margin-bottom: 20px;">Hello {{ $user->first_name }}!</h2>
            
            <p style="font-size: 16px; margin-bottom: 20px;">
                We received a request to reset the password for your NeoProLab account associated with 
                <strong style="color: #0A9396;">{{ $user->email }}</strong>.
            </p>
            
            <div class="expiry-note">
                <strong>⏰ Link Expiration:</strong> This password reset link will expire in 
                <strong style="color: #0A9396;">60 minutes</strong>.
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Reset Your Password</a>
            </div>
            
            <div class="link">
                <strong>Or copy this link:</strong><br>
                <span style="color: #0A9396;">{{ $resetLink }}</span>
            </div>
            
            <p style="color: #64748B; font-size: 15px; margin: 20px 0;">
                If you didn't request a password reset, please ignore this email or 
                <a href="{{ route('contact') }}" style="color: #0A9396; text-decoration: none;">contact support</a> 
                if you have concerns about your account security.
            </p>
            
            <hr>
            
            <p style="font-size: 14px; color: #64748B;">
                For your security, never share this link with anyone. Our team will never ask for your password.
            </p>
        </div>
        
        <div class="footer">
            <p style="margin-bottom: 10px;">&copy; {{ date('Y') }} NeoProLab. All rights reserved.</p>
            <p style="margin-bottom: 5px;">This email was sent to {{ $user->email }}</p>
            <p>123 Business Ave, Suite 100, City, ST 12345</p>
        </div>
    </div>
</body>
</html>