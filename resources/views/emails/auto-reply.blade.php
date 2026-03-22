<!DOCTYPE html>
<html>
<head>
    <title>Thank you for contacting Neoprolab</title>
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
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">Thank You!</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">We've received your message</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #0D1B2A; font-size: 18px; margin-bottom: 25px;">Dear <strong style="color: #00A9A5;">{{ $data['contactName'] }}</strong>,</p>
                            
                            <p style="color: #4a5568; line-height: 1.6; margin-bottom: 25px;">Thank you for reaching out to <strong style="color: #00A9A5;">Neoprolab</strong>. We have received your message and our team will get back to you within <strong>24 hours</strong>.</p>
                            
                            <!-- Message Summary Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-left: 4px solid #00A9A5; border-radius: 8px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #0D1B2A; margin: 0 0 15px 0; font-size: 18px;">📋 Your Message Summary</h3>
                                        <p style="margin: 10px 0; color: #4a5568;"><strong style="color: #0D1B2A;">Subject:</strong> {{ $data['subject'] }}</p>
                                        <p style="margin: 10px 0; color: #4a5568;"><strong style="color: #0D1B2A;">Message:</strong></p>
                                        <p style="margin: 10px 0 0 0; padding: 12px; background-color: #ffffff; border-radius: 6px; color: #4a5568; line-height: 1.5;">{{ $data['message'] }}</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Contact Info -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 8px; margin: 30px 0; padding: 20px;">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 10px 0; color: #0D1B2A;"><strong>📞 Need immediate assistance?</strong></p>
                                        <p style="margin: 0; font-size: 20px; color: #00A9A5;"><strong>(774) 297-0597</strong></p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #4a5568; line-height: 1.6; margin: 25px 0 0 0;">We look forward to assisting you!</p>
                            <p style="color: #4a5568; margin: 20px 0 0 0;">Best regards,<br>
                            <strong style="color: #00A9A5;">Neoprolab Team</strong></p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #718096; font-size: 12px; margin: 0;">This is an automated response. Please do not reply to this email.</p>
                            <p style="color: #718096; font-size: 12px; margin: 5px 0 0 0;">&copy; {{ date('Y') }} Neoprolab. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>