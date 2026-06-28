@extends('layouts.client')

@section('title', 'Payment Successful')
@section('page-title', 'Payment Successful')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-8 text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-4">
            <i class="fas fa-check text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful</h2>
        <p class="text-gray-600 mb-6">Thank you. Your NeoProLab invoice payment has been confirmed and receipt emails have been sent.</p>

        <div class="bg-gray-50 rounded-lg p-4 text-left mb-6">
            <div class="flex justify-between py-2"><span class="text-gray-600">Invoice Number</span><span class="font-medium">{{ $request->request_number }}</span></div>
            <div class="flex justify-between py-2"><span class="text-gray-600">Client Name</span><span class="font-medium">{{ $payment->billing_name ?? auth()->user()->full_name }}</span></div>
            <div class="flex justify-between py-2"><span class="text-gray-600">Email Address</span><span class="font-medium">{{ $payment->billing_email ?? auth()->user()->email }}</span></div>
            <div class="flex justify-between py-2"><span class="text-gray-600">Amount Paid</span><span class="font-bold text-teal-600">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span></div>
            <div class="flex justify-between py-2"><span class="text-gray-600">Payment Method</span><span class="font-medium">{{ strtoupper(str_replace('_', ' ', $payment->payment_method ?? 'Stripe')) }}</span></div>
        </div>

        <div class="flex justify-center gap-3">
            <a href="{{ route('client.requests.show', $request) }}" class="btn-primary px-5 py-2">View Request</a>
            <a href="{{ route('client.payments.history') }}" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Payment History</a>
        </div>
    </div>
</div>
@endsection
