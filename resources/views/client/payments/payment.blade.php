@extends('layouts.client')

@section('title', 'Pay Invoice')
@section('page-title', 'Pay Invoice')

@section('breadcrumbs')
<li><div class="flex items-center"><i class="fas fa-angle-right text-gray-400"></i><a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">My Orders</a></div></li>
<li><div class="flex items-center"><i class="fas fa-angle-right text-gray-400"></i><span class="ml-1 text-sm text-gray-500 md:ml-2">Pay Invoice</span></div></li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(request('payment') === 'cancelled')
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">Stripe checkout was cancelled. No payment was taken.</div>
    @endif

    <div class="card p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pay Invoice</h2>
                <p class="text-sm text-gray-600 mt-1">NeoProLab payments are processed securely by Stripe. Card and bank details are never stored on this website.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Amount Due</p>
                <p class="text-3xl font-bold text-teal-600">${{ number_format($payment->amount, 2) }}</p>
            </div>
        </div>

        <form action="{{ route('client.payments.process', $request) }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                    <input type="text" value="{{ $request->request_number }}" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Due</label>
                    <input type="text" value="${{ number_format($payment->amount, 2) }} {{ $payment->currency }}" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                    <input type="text" name="billing_name" value="{{ old('billing_name', $payment->billing_name ?: auth()->user()->full_name) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" name="billing_email" value="{{ old('billing_email', $payment->billing_email ?: auth()->user()->email) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="border border-teal-200 bg-teal-50 rounded-lg p-4">
                <h3 class="font-semibold text-teal-900 mb-2"><i class="fas fa-lock mr-2"></i>Stripe Checkout</h3>
                <ul class="text-sm text-teal-800 space-y-1 list-disc list-inside">
                    <li>Accepts Visa, Mastercard, American Express, Discover, and ACH bank transfers.</li>
                    <li>Mobile-friendly hosted checkout with SSL and Stripe security controls.</li>
                    <li>After payment, confirmation emails are sent to you and info@neoprolab.com.</li>
                </ul>
            </div>

            <div class="flex items-start">
                <input type="checkbox" name="terms" id="terms" required class="mt-1 mr-3">
                <label for="terms" class="text-sm text-gray-700">I authorize NeoProLab Couriers LLC to collect this invoice payment through Stripe.</label>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('client.requests.show', $request) }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-2"></i>Back to Request</a>
                <button type="submit" class="btn-primary px-6 py-3"><i class="fas fa-lock mr-2"></i>Pay Now</button>
            </div>
        </form>
    </div>
</div>
@endsection
