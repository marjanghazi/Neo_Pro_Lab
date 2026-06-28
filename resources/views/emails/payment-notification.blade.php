<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="color: #0f766e;">Payment Received</h2>
    <p>A customer payment has been completed through Stripe Checkout.</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 620px;">
        <tr><td style="border: 1px solid #e5e7eb;">Invoice Number</td><td style="border: 1px solid #e5e7eb;"><strong>{{ $invoiceNumber }}</strong></td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Client Name</td><td style="border: 1px solid #e5e7eb;">{{ $payment->billing_name }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Client Email</td><td style="border: 1px solid #e5e7eb;">{{ $payment->billing_email }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Amount Paid</td><td style="border: 1px solid #e5e7eb;"><strong>${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong></td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Payment Method</td><td style="border: 1px solid #e5e7eb;">{{ strtoupper(str_replace('_', ' ', $payment->payment_method ?? 'Stripe')) }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Stripe Payment ID</td><td style="border: 1px solid #e5e7eb;">{{ $payment->payment_id }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Request ID</td><td style="border: 1px solid #e5e7eb;">{{ $request?->id }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb;">Paid At</td><td style="border: 1px solid #e5e7eb;">{{ optional($payment->paid_at)->format('M d, Y g:i A') }}</td></tr>
    </table>

    @if($receiptUrl)
        <p><a href="{{ $receiptUrl }}" style="color: #0f766e;">View request/payment details</a></p>
    @endif
</body>
</html>
