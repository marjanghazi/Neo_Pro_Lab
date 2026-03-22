<!DOCTYPE html>
<html>
<head>
    <title>New Contact Form Submission</title>
    <meta charset="UTF-8">
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #f5f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <!-- Header with gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0D1B2A 0%, #00A9A5 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">📬 New Contact Form Submission</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">Someone just reached out to you</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 8px; padding: 20px;">
                                <!-- Sender Info -->
                                <div style="margin-bottom: 30px;">
                                    <h3 style="color: #0D1B2A; margin: 0 0 15px 0; font-size: 18px;">👤 Sender Information</h3>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="100" style="padding: 8px 0; color: #4a5568;"><strong>Name:</strong></td>
                                            <td style="padding: 8px 0; color: #2d3748;">{{ $data['contactName'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; color: #4a5568;"><strong>Email:</strong></td>
                                            <td style="padding: 8px 0;"><a href="mailto:{{ $data['contactEmail'] }}" style="color: #00A9A5; text-decoration: none;">{{ $data['contactEmail'] }}</a></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; color: #4a5568;"><strong>Phone:</strong></td>
                                            <td style="padding: 8px 0; color: #2d3748;">{{ $data['contactPhone'] ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; color: #4a5568;"><strong>Subject:</strong></td>
                                            <td style="padding: 8px 0;"><span style="background-color: #00A9A5; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block;">{{ $data['subject'] }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <!-- Message Content -->
                                <div style="margin-top: 30px;">
                                    <h3 style="color: #0D1B2A; margin: 0 0 15px 0; font-size: 18px;">💬 Message</h3>
                                    <div style="background-color: #f8f9fa; border-left: 4px solid #00A9A5; padding: 20px; border-radius: 8px;">
                                        <p style="margin: 0; color: #2d3748; line-height: 1.6;">{{ $data['message'] }}</p>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <a href="mailto:{{ $data['contactEmail'] }}" style="display: inline-block; background: linear-gradient(135deg, #00A9A5 0%, #008B85 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-right: 10px;">📧 Reply Now</a>
                                                <span style="color: #718096; font-size: 14px;">or call <strong style="color: #00A9A5;">{{ $data['contactPhone'] ?? '(774) 297-0597' }}</strong></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #718096; font-size: 12px; margin: 0;">Sent from Neoprolab Contact Form • {{ date('F j, Y, g:i a') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>