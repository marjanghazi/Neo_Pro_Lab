<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="color: #0f766e;">Payment Confirmed</h2>
    <p>Hi {{ $payment->billing_name ?? 'there' }},</p>
    <p>Thank you. NeoProLab Couriers LLC has received your payment for invoice <strong>{{ $invoiceNumber }}</strong>.</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr><td style="border: 1px solid #e5e7eb;">Invoice Number</td><td style="border: 1px solid #e5e7eb;"><strong>{{ $invoiceNumber }}</strong></td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Amount Paid</td><td style="border: 1px solid #e5e7eb;"><strong>${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong></td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Payment Method</td><td style="border: 1px solid #e5e7eb;">{{ strtoupper(str_replace('_', ' ', $payment->payment_method ?? 'Stripe')) }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Payment ID</td><td style="border: 1px solid #e5e7eb;">{{ $payment->payment_id }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Paid At</td><td style="border: 1px solid #e5e7eb;">{{ optional($payment->paid_at)->format('M d, Y g:i A') }}</td></tr>
    </table>

    @if($receiptUrl)
        <p><a href="{{ $receiptUrl }}" style="color: #0f766e;">View receipt or request details</a></p>
    @endif

    <p>No card or bank account details were stored on the NeoProLab website. Payment processing was handled securely by Stripe.</p>
    <p>Thank you,<br>NeoProLab Couriers LLC</p>
</body>
</html>
