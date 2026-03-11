<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Account Verification Update</title>
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
        .warning-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
        }
        .warning-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
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
        .rejection-box {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .rejection-box h4 {
            color: #991B1B;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .rejection-box p {
            color: #7F1D1D;
            margin: 0;
            font-size: 15px;
        }
        .tips-box {
            background: #F1F5F9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .tips-box h4 {
            color: #0B1E33;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .tips-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-start;
        }
        .tips-list li:last-child {
            border-bottom: none;
        }
        .tips-list li svg {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            margin-top: 2px;
            color: #0A9396;
            flex-shrink: 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #64748B;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
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
            <p style="opacity: 0.9; margin: 10px 0 0;">Account Verification Update</p>
        </div>
        
        <div class="content">
            <div class="warning-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <circle cx="12" cy="16" r="1" fill="white" />
                </svg>
            </div>
            
            <h2 style="color: #0B1E33; text-align: center; margin-bottom: 20px;">
                Hello {{ $courier->first_name }},
            </h2>
            
            <p style="font-size: 16px; margin-bottom: 20px; text-align: center;">
                We've reviewed your courier application and noticed some issues with your submitted documents.
            </p>
            
            <div class="rejection-box">
                <h4>📋 Reason for Rejection:</h4>
                <p>{{ $rejectionReason }}</p>
            </div>
            
            <p style="font-size: 15px; margin-bottom: 20px;">
                Don't worry! You can resubmit your documents after making the necessary corrections.
            </p>
            
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 8px; display: inline-block; vertical-align: middle;">
                        <path d="M12 4v16M4 12h16" />
                    </svg>
                    Resubmit Documents
                </a>
            </div>
            
            <div class="tips-box">
                <h4>💡 Tips for Successful Verification:</h4>
                <ul class="tips-list">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4M12 8h.01" />
                        </svg>
                        Ensure all documents are clear and legible (no blurry images)
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4M12 8h.01" />
                        </svg>
                        Documents must be valid and not expired
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4M12 8h.01" />
                        </svg>
                        Make sure all information matches your profile
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4M12 8h.01" />
                        </svg>
                        Upload color copies when possible
                    </li>
                </ul>
            </div>
            
            <hr>
            
            <p style="font-size: 14px; color: #64748B; text-align: center;">
                Need help? Our support team is here for you. 
                <a href="#" style="color: #0A9396; text-decoration: none;">Contact Support</a>
            </p>
        </div>
        
        <div class="footer">
            <p style="margin-bottom: 10px;">&copy; {{ date('Y') }} NeoProLab. All rights reserved.</p>
            <p style="margin-bottom: 5px;">This email was sent to {{ $courier->email }}</p>
            <p>123 Business Ave, Suite 100, City, ST 12345</p>
        </div>
    </div>
</body>
</html>