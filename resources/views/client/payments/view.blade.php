@extends('layouts.client')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
    <div class="flex justify-between items-start">
        <div><h2 class="text-2xl font-bold">Payment #{{ $payment->id }}</h2><p class="text-gray-500">Request {{ $payment->request->request_number ?? 'N/A' }}</p></div>
        <span class="px-3 py-1 rounded-full {{ $payment->status_badge }}">{{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}</span>
    </div>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-gray-500">Amount</dt><dd class="font-semibold">${{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</dd></div>
        <div><dt class="text-gray-500">Paid At</dt><dd>{{ $payment->paid_at?->format('M d, Y h:i A') ?? 'Not paid yet' }}</dd></div>
        <div><dt class="text-gray-500">Method</dt><dd>{{ $payment->payment_method ? str_replace('_', ' ', ucfirst($payment->payment_method)) : 'Pending' }}</dd></div>
        <div><dt class="text-gray-500">Gateway</dt><dd>{{ ucfirst($payment->payment_gateway ?? 'N/A') }}</dd></div>
    </dl>
    <div class="pt-4 border-t flex gap-3">
        <a href="{{ route('client.payments.history') }}" class="px-4 py-2 border rounded-lg">Back</a>
        @if($payment->isPaid())
            <a href="{{ route('client.payments.receipt', $payment) }}" class="px-4 py-2 bg-teal-600  rounded-lg">Download Receipt</a>
        @elseif($payment->payment_status === 'pending')
            <a href="{{ route('client.payments.show', $payment->request) }}" class="px-4 py-2 bg-teal-600  rounded-lg hover:bg-teal-700">Pay Now</a>
        @endif
    </div>
</div>
@endsection