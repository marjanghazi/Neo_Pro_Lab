@extends('layouts.client')

@section('title', 'Make Payment')
@section('page-title', 'Make Payment')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">My Orders</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.show', $request) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Request #{{ $request->request_number }}</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Make Payment</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">Payment for Request #{{ $request->request_number }}</h2>

        <!-- Payment Summary -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Request Number:</span>
                                <span class="font-medium">{{ $request->request_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pickup Date:</span>
                                <span>{{ $request->scheduled_pickup_time?->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Specimen Type:</span>
                                <span>{{ ucfirst($request->specimen_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Priority:</span>
                                <span>{{ ucfirst($request->priority_level) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
                    <div class="bg-teal-50 border border-teal-200 p-4 rounded-lg">
                        <div class="space-y-3">
                            <div class="flex justify-between text-lg">
                                <span class="font-medium">Amount Due:</span>
                                <span class="font-bold text-teal-600">${{ number_format($payment->amount, 2) }}</span>
                            </div>

                            @if($request->payment_due_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Due Date:</span>
                                <span class="{{ $request->isPaymentOverdue() ? 'text-red-600 font-medium' : '' }}">
                                    {{ $request->payment_due_at->format('M d, Y') }}
                                    @if($request->isPaymentOverdue())
                                    (Overdue)
                                    @endif
                                </span>
                            </div>
                            @endif

                            <div class="pt-3 border-t border-teal-200">
                                <p class="text-sm text-teal-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Payment is required before specimen pickup.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Method</h3>

            <form action="{{ route('client.payments.process', $request) }}" method="POST" id="paymentForm">
                @csrf

                <!-- Billing Information -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-4">Billing Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text"
                                name="billing_name"
                                value="{{ auth()->user()->full_name }}"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email"
                                name="billing_email"
                                value="{{ auth()->user()->email }}"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                            <input type="tel"
                                name="billing_phone"
                                value="{{ auth()->user()->phone }}"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Billing Address *</label>
                            <textarea name="billing_address"
                                rows="3"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ auth()->user()->address ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-4">Select Payment Method</h4>

                    <div class="space-y-4">
                        @if($config['gateway'] === 'stripe' && $config['stripe_public_key'])
                        <div class="border border-gray-300 rounded-lg p-4 hover:border-teal-500 cursor-pointer payment-method-option">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="payment_method" value="card" class="mr-3" checked>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-medium">Credit/Debit Card</span>
                                            <p class="text-sm text-gray-600 mt-1">Pay securely with your card</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <i class="fab fa-cc-visa text-blue-600 text-2xl"></i>
                                            <i class="fab fa-cc-mastercard text-red-600 text-2xl"></i>
                                            <i class="fab fa-cc-amex text-blue-400 text-2xl"></i>
                                            <i class="fab fa-cc-discover text-orange-600 text-2xl"></i>
                                        </div>
                                    </div>

                                    <!-- Card Details (shown when selected) -->
                                    <div id="cardDetails" class="mt-4">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                            <div id="cardNumber" class="border border-gray-300 rounded-lg px-4 py-2 bg-white"></div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                                <div id="cardExpiry" class="border border-gray-300 rounded-lg px-4 py-2 bg-white"></div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                                                <div id="cardCvc" class="border border-gray-300 rounded-lg px-4 py-2 bg-white"></div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="stripe_token" id="stripeToken">
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endif

                        <div class="border border-gray-300 rounded-lg p-4 hover:border-teal-500 cursor-pointer payment-method-option">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                                <div>
                                    <span class="font-medium">Bank Transfer</span>
                                    <p class="text-sm text-gray-600 mt-1">Transfer funds directly to our bank account</p>
                                    <div id="bankDetails" class="mt-2 hidden">
                                        <div class="bg-blue-50 p-3 rounded text-sm">
                                            <p><strong>Account Name:</strong> Your Company Name</p>
                                            <p><strong>Account Number:</strong> 1234567890</p>
                                            <p><strong>Bank:</strong> Your Bank Name</p>
                                            <p><strong>Routing:</strong> 021000021</p>
                                            <p class="mt-2 text-blue-700">Please include Request #{{ $request->request_number }} in the transfer reference.</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="border border-gray-300 rounded-lg p-4 hover:border-teal-500 cursor-pointer payment-method-option">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" class="mr-3">
                                <div>
                                    <span class="font-medium">Cash Payment</span>
                                    <p class="text-sm text-gray-600 mt-1">Pay in cash upon pickup or delivery</p>
                                    <div id="cashInstructions" class="mt-2 hidden">
                                        <div class="bg-yellow-50 p-3 rounded text-sm">
                                            <p class="text-yellow-700">Please have exact amount ready for the courier. Receipt will be provided upon payment.</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <div class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" required class="mt-1 mr-3">
                        <label for="terms" class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-teal-600 hover:underline">Terms of Service</a> and
                            <a href="#" class="text-teal-600 hover:underline">Privacy Policy</a>. I authorize this payment
                            and understand that it is non-refundable unless the request is cancelled before pickup.
                        </label>
                    </div>
                    @error('terms')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('client.requests.show', $request) }}" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Request
                        </a>

                        <button type="submit" id="submitPayment" class="btn-primary px-6 py-2">
                            <i class="fas fa-lock mr-2"></i> Pay ${{ number_format($payment->amount, 2) }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Security Information -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-shield-alt text-teal-600 text-xl mr-3"></i>
                <div>
                    <p class="font-medium text-gray-800">Secure Payment</p>
                    <p class="text-sm text-gray-600">Your payment information is encrypted and secure. We never store your card details on our servers.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($config['gateway'] === 'stripe' && $config['stripe_public_key'])
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ $config["stripe_public_key"] }}');
        const elements = stripe.elements();

        // Create card elements
        const cardNumber = elements.create('cardNumber');
        const cardExpiry = elements.create('cardExpiry');
        const cardCvc = elements.create('cardCvc');

        cardNumber.mount('#cardNumber');
        cardExpiry.mount('#cardExpiry');
        cardCvc.mount('#cardCvc');

        // Handle payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const method = this.value;

                // Hide all method details
                document.querySelectorAll('[id$="Details"], [id$="Instructions"]').forEach(el => {
                    el.classList.add('hidden');
                });

                // Show selected method details
                if (method === 'card') {
                    document.getElementById('cardDetails').classList.remove('hidden');
                } else if (method === 'bank_transfer') {
                    document.getElementById('bankDetails').classList.remove('hidden');
                } else if (method === 'cash') {
                    document.getElementById('cashInstructions').classList.remove('hidden');
                }
            });
        });

        // Handle form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitPayment');
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (paymentMethod === 'card') {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

                try {
                    // Create payment method
                    const {
                        paymentMethod,
                        error
                    } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardNumber,
                        billing_details: {
                            name: document.querySelector('input[name="billing_name"]').value,
                            email: document.querySelector('input[name="billing_email"]').value,
                            phone: document.querySelector('input[name="billing_phone"]').value,
                            address: {
                                line1: document.querySelector('textarea[name="billing_address"]').value,
                            }
                        }
                    });

                    if (error) {
                        throw new Error(error.message);
                    }

                    // Set token and submit form
                    document.getElementById('stripeToken').value = paymentMethod.id;
                    this.submit();

                } catch (error) {
                    alert('Error: ' + error.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i> Pay ${{ number_format($payment->amount, 2) }}';
                }
            } else {
                // For non-card payments, submit directly
                this.submit();
            }
        });
    });
</script>
@endpush
@endif

<style>
    .payment-method-option {
        transition: all 0.3s ease;
    }

    .payment-method-option:hover {
        border-color: #0d9488;
    }

    .StripeElement {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
    }

    .StripeElement--focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }
</style>
@endsection