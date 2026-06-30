<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SpecimenRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentService
{
    protected string $gateway;
    protected bool $testMode;
    protected string $currency;

    public function __construct()
    {
        $this->gateway = config('services.payment.gateway', 'stripe');
        $this->testMode = (bool) config('services.payment.test_mode', true);
        $this->currency = strtoupper(config('services.payment.currency', config('services.stripe.currency', 'USD')));
    }

    public function createPayment(SpecimenRequest $request, User $user, array $data = []): Payment
    {
        return Payment::firstOrCreate(
            ['specimen_request_id' => $request->id, 'payment_status' => Payment::STATUS_PENDING],
            [
                'user_id' => $user->id,
                'specimen_request_id' => $request->id,
                'amount' => $this->amountDue($request),
                'currency' => $this->currency,
                'payment_method' => null,
                'payment_gateway' => Payment::GATEWAY_STRIPE,
                'billing_name' => $data['billing_name'] ?? $user->full_name,
                'billing_email' => $data['billing_email'] ?? $user->email,
                'billing_phone' => $data['billing_phone'] ?? $user->phone,
                'billing_address' => $data['billing_address'] ?? null,
            ]
        );
    }

    public function processPayment(SpecimenRequest $request, array $data): array
    {
        if ($this->gateway !== Payment::GATEWAY_STRIPE) {
            return ['success' => false, 'message' => 'Stripe is the configured payment gateway for online invoice payments.'];
        }

        if (blank($this->stripeSecret())) {
            return ['success' => false, 'message' => 'Stripe is not configured. Please set the appropriate Stripe secret key in the environment.'];
        }

        $modeError = $this->stripeModeError();
        if ($modeError) {
            return ['success' => false, 'message' => $modeError];
        }

        $user = auth()->user() ?: $request->client;
        $payment = $request->payments()
            ->where('payment_status', Payment::STATUS_PENDING)
            ->latest()
            ->first() ?: $this->createPayment($request, $user, $data);

        if ($this->amountDue($request) <= 0) {
            return ['success' => false, 'message' => 'This invoice does not have an amount due yet.'];
        }
        $payment->update([
            'amount' => $this->amountDue($request),
            'currency' => $this->currency,
            'payment_status' => Payment::STATUS_PENDING,
            'payment_gateway' => Payment::GATEWAY_STRIPE,
            'billing_name' => $data['billing_name'] ?? $payment->billing_name ?? $user?->full_name,
            'billing_email' => $data['billing_email'] ?? $payment->billing_email ?? $user?->email,
            'billing_phone' => $data['billing_phone'] ?? $payment->billing_phone,
            'billing_address' => $data['billing_address'] ?? $payment->billing_address,
        ]);

        Stripe::setApiKey($this->stripeSecret());

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card', 'us_bank_account'],
            'customer_email' => $payment->billing_email,
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($payment->currency),
                    'unit_amount' => $this->convertToCents($payment->amount),
                    'product_data' => [
                        'name' => 'NeoProLab Invoice ' . $request->request_number,
                        'description' => 'Secure courier invoice payment for ' . ($request->facility->name ?? 'NeoProLab Couriers LLC'),
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('client.payments.success', $payment->id) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('client.payments.show', $request) . '?payment=cancelled',
            'metadata' => [
                'payment_id' => (string) $payment->id,
                'request_id' => (string) $request->id,
                'invoice_number' => $request->request_number,
                'client_id' => (string) $request->client_id,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'payment_id' => (string) $payment->id,
                    'request_id' => (string) $request->id,
                    'invoice_number' => $request->request_number,
                ],
            ],
        ]);

        $payment->update([
            'payment_id' => $session->id,
            'payment_method' => 'stripe_checkout',
            'gateway_response' => array_merge((array) $payment->gateway_response, ['checkout_session_id' => $session->id]),
        ]);
        $payment->log('checkout_session_created', ['checkout_session_id' => $session->id]);

        return ['success' => true, 'payment' => $payment, 'checkout_url' => $session->url];
    }

    public function completeCheckout(Payment $payment, ?string $sessionId = null): Payment
    {
        if (blank($this->stripeSecret())) {
            throw new \RuntimeException('Stripe secret key is missing. Set the appropriate Stripe secret key in the environment.');
        }

        $modeError = $this->stripeModeError();
        if ($modeError) {
            throw new \RuntimeException($modeError);
        }

        Stripe::setApiKey($this->stripeSecret());
        $sessionId = $sessionId ?: ($payment->gateway_response['checkout_session_id'] ?? $payment->payment_id);
        $session = Session::retrieve($sessionId, ['expand' => ['payment_intent.latest_charge']]);

        if ($session->payment_status !== 'paid') {
            return $payment;
        }

        $intent = $session->payment_intent;
        $charge = is_object($intent) ? ($intent->latest_charge ?? null) : null;
        $methodDetails = $charge?->payment_method_details ?? null;
        $methodType = $methodDetails?->type ?? ($session->payment_method_types[0] ?? 'stripe_checkout');
        $card = $methodDetails?->card ?? null;
        $bankAccount = $methodDetails?->us_bank_account ?? null;

        $alreadyCompleted = $payment->payment_status === Payment::STATUS_COMPLETED;

        $payment->update([
            'payment_id' => is_object($intent) ? $intent->id : $session->id,
            'payment_method' => $methodType === 'us_bank_account' ? Payment::METHOD_BANK_TRANSFER : Payment::METHOD_CARD,
            'card_last_four' => $card?->last4 ?? $bankAccount?->last4,
            'card_brand' => $card?->brand ?? $bankAccount?->bank_name,
            'receipt_url' => $charge?->receipt_url,
            'gateway_response' => array_merge((array) $payment->gateway_response, ['checkout_session' => $session->toArray()]),
        ]);
        $payment->markAsCompleted($payment->payment_id, ['checkout_session_id' => $session->id]);
        $payment->request?->update(['payment_status' => 'paid']);

        if (!$alreadyCompleted) {
            $this->sendConfirmationEmails($payment->fresh(['request', 'user']));
        }

        return $payment->fresh(['request', 'user']);
    }

    public function handleStripeWebhook(string $payload, ?string $signature): void
    {
        $secret = config('services.stripe.webhook_secret');
        $event = $secret ? Webhook::constructEvent($payload, $signature, $secret) : json_decode($payload);

        $type = $event->type ?? null;

        if (in_array($type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
            $session = $event->data->object;
            $paymentId = $session->metadata->payment_id ?? null;
            $payment = $paymentId ? Payment::find($paymentId) : Payment::where('payment_id', $session->id)->first();
            if ($payment) {
                $this->completeCheckout($payment, $session->id);
            }
            return;
        }

        if ($type === 'checkout.session.async_payment_failed') {
            $session = $event->data->object;
            $paymentId = $session->metadata->payment_id ?? null;
            $payment = $paymentId ? Payment::find($paymentId) : Payment::where('payment_id', $session->id)->first();
            if ($payment) {
                $payment->update([
                    'payment_status' => Payment::STATUS_FAILED,
                    'gateway_response' => array_merge((array) $payment->gateway_response, ['checkout_session_failed' => method_exists($session, 'toArray') ? $session->toArray() : (array) $session]),
                ]);
                $payment->log('checkout_session_async_payment_failed', ['checkout_session_id' => $session->id]);
            }
        }
    }

    public function sendConfirmationEmails(Payment $payment): void
    {
        $request = $payment->request;
        $invoiceNumber = $request->request_number ?? $payment->id;
        $receiptUrl = $payment->receipt_url ?: ($request ? route('client.requests.show', $request) : null);

        $data = [
            'payment' => $payment,
            'request' => $request,
            'invoiceNumber' => $invoiceNumber,
            'receiptUrl' => $receiptUrl,
            'adminEmail' => config('services.payment.admin_email', 'info@neoprolab.com'),
        ];

        try {
            if ($payment->billing_email) {
                Mail::send('emails.payment-confirmation', $data, function ($message) use ($payment, $invoiceNumber) {
                    $message->to($payment->billing_email, $payment->billing_name)
                        ->subject('NeoProLab payment confirmation - Invoice ' . $invoiceNumber);
                });
            }

            Mail::send('emails.payment-notification', $data, function ($message) use ($invoiceNumber) {
                $message->to(config('services.payment.admin_email', 'info@neoprolab.com'))
                    ->subject('Payment received - Invoice ' . $invoiceNumber);
            });
        } catch (\Throwable $e) {
            Log::error('Payment email send failed: ' . $e->getMessage(), ['payment_id' => $payment->id]);
        }
    }


    public function handleCallback($payment, array $data = []): ?Payment
    {
        $paymentModel = $payment instanceof Payment ? $payment : Payment::find($payment);
        if (!$paymentModel) {
            return null;
        }

        $sessionId = $data['session_id'] ?? $data['checkout_session_id'] ?? null;
        return $this->completeCheckout($paymentModel, $sessionId);
    }

    public function refundPayment(Payment $payment, $amount = null, $reason = null): array
    {
        try {
            if ($payment->payment_gateway === Payment::GATEWAY_STRIPE && $payment->payment_id && !blank($this->stripeSecret())) {
                Stripe::setApiKey($this->stripeSecret());
                $refund = \Stripe\Refund::create(array_filter([
                    'payment_intent' => $payment->payment_id,
                    'amount' => $amount ? $this->convertToCents($amount) : null,
                    'reason' => in_array($reason, ['duplicate', 'fraudulent', 'requested_by_customer'], true) ? $reason : 'requested_by_customer',
                    'metadata' => [
                        'payment_id' => (string) $payment->id,
                        'request_id' => (string) $payment->request_id,
                        'note' => is_string($reason) ? $reason : null,
                    ],
                ], fn ($value) => $value !== null));

                $payment->update([
                    'payment_status' => $amount && $amount < $payment->amount ? Payment::STATUS_PARTIALLY_REFUNDED : Payment::STATUS_REFUNDED,
                    'refunded_at' => now(),
                    'gateway_response' => array_merge((array) $payment->gateway_response, ['refund' => $refund->toArray()]),
                    'notes' => trim(($payment->notes ? $payment->notes . PHP_EOL : '') . 'Refunded via Stripe. ' . ($reason ?: '')),
                ]);
                $payment->request?->update(['payment_status' => 'refunded']);
                $payment->log('payment_refunded', ['refund_id' => $refund->id ?? null]);

                return ['success' => true, 'refund' => $refund, 'message' => 'Payment refunded successfully.'];
            }

            $payment->update([
                'payment_status' => Payment::STATUS_REFUNDED,
                'refunded_at' => now(),
                'notes' => trim(($payment->notes ? $payment->notes . PHP_EOL : '') . 'Marked refunded manually. ' . ($reason ?: '')),
            ]);
            $payment->request?->update(['payment_status' => 'refunded']);
            $payment->log('payment_refunded_manually', ['reason' => $reason]);

            return ['success' => true, 'message' => 'Payment marked as refunded.'];
        } catch (\Throwable $e) {
            Log::error('Payment refund failed: ' . $e->getMessage(), ['payment_id' => $payment->id]);
            return ['success' => false, 'message' => 'Refund failed: ' . $e->getMessage()];
        }
    }

    public function isPaymentRequiredForPickup(): bool
    {
        return (bool) config('services.payment.required_before_pickup', true);
    }

    public function getConfig(): array
    {
        return [
            'gateway' => $this->gateway,
            'test_mode' => $this->testMode,
            'currency' => $this->currency,
            'stripe_public_key' => config('services.stripe.key'),
            'payment_required_before_pickup' => config('services.payment.required_before_pickup', true),
        ];
    }


    private function stripeSecret(): ?string
    {
        return config('services.stripe.secret');
    }

    private function stripeModeError(): ?string
    {
        $secret = (string) $this->stripeSecret();

        if ($this->testMode && str_starts_with($secret, 'sk_live_')) {
            return 'Stripe is configured for test mode but a live secret key is selected. Set PAYMENT_TEST_MODE=false for live checkout.';
        }

        if (! $this->testMode && str_starts_with($secret, 'sk_test_')) {
            return 'Stripe is configured for live mode but a test secret key is selected. Remove duplicate STRIPE_SECRET entries or set STRIPE_LIVE_SECRET.';
        }

        if (! $this->testMode && str_starts_with($secret, 'rk_test_')) {
            return 'Stripe is configured for live mode but a test restricted key is selected. Remove duplicate STRIPE_SECRET entries or set STRIPE_LIVE_SECRET.';
        }

        return null;
    }

    private function amountDue(SpecimenRequest $request): float
    {
        return (float) ($request->total_price ?? $request->estimated_price ?? 0);
    }

    private function convertToCents($amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
