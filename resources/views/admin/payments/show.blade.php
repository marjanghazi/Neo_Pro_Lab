@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <div class="flex justify-between items-start"><div><h2 class="text-2xl font-bold">Payment #{{ $payment->id }}</h2><p class="text-gray-500">Request {{ $payment->request->request_number ?? 'N/A' }}</p></div><span class="px-3 py-1 rounded-full {{ $payment->status_badge }}">{{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}</span></div>
    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm"><div><dt class="text-gray-500">Client</dt><dd class="font-semibold">{{ $payment->billing_name ?: ($payment->user->full_name ?? 'N/A') }}</dd></div><div><dt class="text-gray-500">Email</dt><dd>{{ $payment->billing_email ?: ($payment->user->email ?? 'N/A') }}</dd></div><div><dt class="text-gray-500">Amount</dt><dd class="font-semibold">${{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</dd></div><div><dt class="text-gray-500">Gateway</dt><dd>{{ ucfirst($payment->payment_gateway ?? 'N/A') }}</dd></div><div><dt class="text-gray-500">Method</dt><dd>{{ $payment->payment_method ? str_replace('_', ' ', ucfirst($payment->payment_method)) : 'Pending' }}</dd></div><div><dt class="text-gray-500">Paid At</dt><dd>{{ $payment->paid_at?->format('M d, Y h:i A') ?? 'Not paid' }}</dd></div></dl>
    <div class="pt-4 border-t flex flex-wrap gap-3"><a href="{{ route('admin.payments.index') }}" class="px-4 py-2 border rounded-lg">Back</a>@unless($payment->isPaid())<form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}">@csrf<button class="px-4 py-2 bg-green-600 text-white rounded-lg">Mark Paid</button></form>@endunless @if($payment->isPaid())<form method="POST" action="{{ route('admin.payments.refund', $payment) }}">@csrf<button class="px-4 py-2 bg-red-600 text-white rounded-lg">Refund / Mark Refunded</button></form>@endif</div>
</div>
@endsection
