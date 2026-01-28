<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SpecimenRequest;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $gateway;
    protected $testMode;
    protected $currency;

    public function __construct()
    {
        $this->gateway = config('services.payment.gateway', 'stripe');
        $this->testMode = config('services.payment.test_mode', true);
        $this->currency = config('services.payment.currency', 'USD');
    }

    /**
     * Create a payment for a request
     */
    public function createPayment(SpecimenRequest $request, User $user, array $data = [])
    {
        $payment = Payment::create([
            'request_id' => $request->id,
            'user_id' => $user->id,
            'amount' => $request->estimated_price ?? 0,
            'currency' => $this->currency,
            'payment_status' => Payment::STATUS_PENDING,
            'payment_gateway' => $this->gateway,
            'billing_name' => $data['billing_name'] ?? $user->full_name,
            'billing_email' => $data['billing_email'] ?? $user->email,
            'billing_phone' => $data['billing_phone'] ?? $user->phone,
            'billing_address' => $data['billing_address'] ?? null,
        ]);

        // Update request with payment due date
        $dueDays = config('services.payment.due_days', 7);
        $request->update([
            'payment_due_at' => now()->addDays($dueDays),
            'payment_status' => 'pending',
        ]);

        return $payment;
    }

    /**
     * Process payment via Stripe
     */
    public function processStripePayment(Payment $payment, $token, $saveCard = false)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $customer = $this->getOrCreateStripeCustomer($payment->user, $payment->billing_email);

            // Create payment intent
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $this->convertToCents($payment->amount),
                'currency' => strtolower($payment->currency),
                'customer' => $customer->id,
                'payment_method' => $token,
                'confirm' => true,
                'return_url' => route('client.payments.callback', $payment->id),
                'metadata' => [
                    'request_id' => $payment->request_id,
                    'user_id' => $payment->user_id,
                    'payment_id' => $payment->id,
                ],
            ]);

            if ($intent->status === 'succeeded') {
                $payment->update([
                    'payment_id' => $intent->id,
                    'payment_method' => Payment::METHOD_CARD,
                    'card_last_four' => $intent->charges->data[0]->payment_method_details->card->last4 ?? null,
                    'card_brand' => $intent->charges->data[0]->payment_method_details->card->brand ?? null,
                    'receipt_url' => $intent->charges->data[0]->receipt_url ?? null,
                    'gateway_response' => $intent->toArray(),
                ]);

                $payment->markAsCompleted($intent->id);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'receipt_url' => $payment->receipt_url,
                ];
            }

            $payment->update([
                'payment_status' => Payment::STATUS_FAILED,
                'gateway_response' => $intent->toArray(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment failed: ' . ($intent->last_payment_error->message ?? 'Unknown error'),
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());
            
            $payment->update([
                'payment_status' => Payment::STATUS_FAILED,
                'notes' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process offline payment (manual)
     */
    public function processOfflinePayment(Payment $payment, array $data)
    {
        $payment->update([
            'payment_method' => $data['payment_method'] ?? Payment::METHOD_CASH,
            'notes' => $data['notes'] ?? null,
            'payment_status' => Payment::STATUS_PENDING,
        ]);

        // For offline payments, admin needs to manually mark as completed
        return [
            'success' => true,
            'message' => 'Offline payment recorded. Waiting for admin confirmation.',
            'payment' => $payment,
        ];
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment, $amount = null, $reason = null)
    {
        try {
            if ($payment->payment_gateway === Payment::GATEWAY_STRIPE && $payment->payment_id) {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                $refund = \Stripe\Refund::create([
                    'payment_intent' => $payment->payment_id,
                    'amount' => $amount ? $this->convertToCents($amount) : $this->convertToCents($payment->amount),
                    'reason' => $reason ?? 'requested_by_customer',
                ]);

                $refundAmount = $amount ?? $payment->amount;
                $payment->update([
                    'payment_status' => $amount < $payment->amount ? Payment::STATUS_PARTIALLY_REFUNDED : Payment::STATUS_REFUNDED,
                    'refunded_at' => now(),
                    'notes' => ($payment->notes ? $payment->notes . "\n" : '') . "Refunded: $refundAmount. Reason: $reason",
                ]);

                return [
                    'success' => true,
                    'refund' => $refund,
                    'message' => 'Payment refunded successfully',
                ];
            }

            // For offline payments
            $payment->update([
                'payment_status' => Payment::STATUS_REFUNDED,
                'refunded_at' => now(),
                'notes' => ($payment->notes ? $payment->notes . "\n" : '') . "Refunded manually. Reason: $reason",
            ]);

            return [
                'success' => true,
                'message' => 'Payment marked as refunded',
            ];

        } catch (\Exception $e) {
            Log::error('Refund error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Refund failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get or create Stripe customer
     */
    private function getOrCreateStripeCustomer(User $user, $email)
    {
        if ($user->stripe_customer_id) {
            try {
                return \Stripe\Customer::retrieve($user->stripe_customer_id);
            } catch (\Exception $e) {
                // Customer not found, create new
            }
        }

        $customer = \Stripe\Customer::create([
            'email' => $email,
            'name' => $user->full_name,
            'phone' => $user->phone,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);
        return $customer;
    }

    /**
     * Convert amount to cents (Stripe requires cents)
     */
    private function convertToCents($amount)
    {
        return (int) round($amount * 100);
    }

    /**
     * Check if payment is required for pickup
     */
    public function isPaymentRequiredForPickup()
    {
        return config('services.payment.required_before_pickup', true);
    }

    /**
     * Get payment configuration
     */
    public function getConfig()
    {
        return [
            'gateway' => $this->gateway,
            'test_mode' => $this->testMode,
            'currency' => $this->currency,
            'stripe_public_key' => config('services.stripe.key'),
            'payment_required_before_pickup' => $this->isPaymentRequiredForPickup(),
        ];
    }
}