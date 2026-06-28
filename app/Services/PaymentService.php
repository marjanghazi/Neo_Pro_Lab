<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SpecimenRequest;
use App\Models\User;
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
            ['request_id' => $request->id, 'payment_status' => Payment::STATUS_PENDING],
            [
                'user_id' => $user->id,
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

        if (blank(config('services.stripe.secret'))) {
            return ['success' => false, 'message' => 'Stripe is not configured. Please set STRIPE_SECRET in the environment.'];
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

        Stripe::setApiKey(config('services.stripe.secret'));

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
        if (blank(config('services.stripe.secret'))) {
            throw new \RuntimeException('Stripe secret key is missing. Set STRIPE_SECRET in the environment.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $sessionId = $sessionId ?: ($payment->gateway_response['checkout_session_id'] ?? $payment->payment_id);
        $session = Session::retrieve($sessionId, ['expand' => ['payment_intent.latest_charge']]);

        if ($session->payment_status !== 'paid') {
            return $payment;
        }

        $intent = $session->payment_intent;
        $charge = is_object($intent) ? ($intent->latest_charge ?? null) : null;
        $methodType = $session->payment_method_types[0] ?? 'stripe_checkout';
        $card = $charge?->payment_method_details?->card ?? null;

        $alreadyCompleted = $payment->payment_status === Payment::STATUS_COMPLETED;

        $payment->update([
            'payment_id' => is_object($intent) ? $intent->id : $session->id,
            'payment_method' => $methodType === 'us_bank_account' ? Payment::METHOD_BANK_TRANSFER : Payment::METHOD_CARD,
            'card_last_four' => $card?->last4,
            'card_brand' => $card?->brand,
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

        if (($event->type ?? null) === 'checkout.session.completed') {
            $session = $event->data->object;
            $paymentId = $session->metadata->payment_id ?? null;
            $payment = $paymentId ? Payment::find($paymentId) : Payment::where('payment_id', $session->id)->first();
            if ($payment) {
                $this->completeCheckout($payment, $session->id);
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

    private function amountDue(SpecimenRequest $request): float
    {
        return (float) ($request->total_price ?? $request->estimated_price ?? 0);
    }

    private function convertToCents($amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
