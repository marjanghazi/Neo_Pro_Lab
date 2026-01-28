<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:client');
    }

    /**
     * Show payment form for a request
     */
    public function showPaymentForm(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        // Check if payment is required
        if ($request->status !== 'approved' || $request->payment_status === 'paid') {
            return redirect()->route('client.requests.show', $request)
                ->with('error', 'Payment not required for this request.');
        }

        // Calculate amount due
        $amountDue = $this->calculateAmountDue($request);

        return view('client.payments.create', compact('request', 'amountDue'));
    }

    /**
     * Create Stripe checkout session
     */
    public function createCheckoutSession(Request $httpRequest, SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Calculate amount in cents
            $amountCents = $this->calculateAmountDue($request) * 100;

            // Create checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower(config('services.stripe.currency', 'usd')),
                        'product_data' => [
                            'name' => "Specimen Delivery Request #{$request->request_number}",
                            'description' => "Specimen delivery from {$request->pickup_address} to {$request->delivery_address}",
                        ],
                        'unit_amount' => $amountCents,
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'request_id' => $request->id,
                    'client_id' => Auth::id(),
                    'request_number' => $request->request_number,
                ],
                'customer_email' => Auth::user()->email,
                'mode' => 'payment',
                'success_url' => route('client.payments.success', ['request' => $request->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('client.payments.cancel', ['request' => $request->id]),
            ]);

            // Create pending payment record
            Payment::create([
                'request_id' => $request->id,
                'client_id' => Auth::id(),
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'amount' => $amountCents / 100,
                'currency' => config('services.stripe.currency', 'usd'),
                'status' => 'pending',
                'payment_method' => 'stripe',
                'metadata' => json_encode([
                    'pickup_address' => $request->pickup_address,
                    'delivery_address' => $request->delivery_address,
                    'specimen_type' => $request->specimen_type,
                    'priority' => $request->priority_level,
                ]),
            ]);

            return response()->json(['id' => $session->id]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment processing error'], 500);
        }
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('client.requests.show', $specimenRequest)
                ->with('error', 'Invalid payment session');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = Session::retrieve($sessionId);

            // Find and update payment record
            $payment = Payment::where('stripe_session_id', $sessionId)->first();

            if ($payment && $session->payment_status === 'paid') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'payment_method_details' => json_encode([
                        'card_brand' => $session->payment_method_types[0] ?? 'card',
                        'last4' => $session->payment_intent ? $this->getPaymentMethodLast4($session->payment_intent) : null,
                    ]),
                ]);

                // Update request payment status
                $specimenRequest->update([
                    'payment_status' => 'paid',
                    'payment_received_at' => now(),
                ]);

                // Create notification for admins
                $this->createPaymentNotification($specimenRequest, $payment);

                return view('client.payments.success', compact('specimenRequest', 'payment'));
            }

        } catch (\Exception $e) {
            Log::error('Payment Success Handling Error: ' . $e->getMessage());
        }

        return redirect()->route('client.requests.show', $specimenRequest)
            ->with('error', 'Unable to verify payment. Please contact support.');
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(SpecimenRequest $request)
    {
        return view('client.payments.cancel', compact('request'));
    }

    /**
     * Webhook handler for Stripe events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;

            default:
                Log::info('Received unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle checkout.session.completed event
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $payment = Payment::where('stripe_session_id', $session->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'stripe_payment_intent_id' => $session->payment_intent,
            ]);

            // Update request status
            $request = $payment->request;
            if ($request) {
                $request->update([
                    'payment_status' => 'paid',
                    'payment_received_at' => now(),
                ]);

                // Create notification
                Notification::create([
                    'user_id' => $request->client_id,
                    'request_id' => $request->id,
                    'type' => 'payment_received',
                    'title' => 'Payment Received',
                    'message' => "Payment of $" . number_format($payment->amount, 2) . " received for request #{$request->request_number}",
                    'data' => json_encode([
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'request_number' => $request->request_number,
                    ]),
                ]);

                // Notify admins
                $adminUsers = \App\Models\User::whereHas('role', function ($query) {
                    $query->where('slug', 'admin');
                })->get();

                foreach ($adminUsers as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'request_id' => $request->id,
                        'type' => 'payment_received_admin',
                        'title' => 'Payment Received',
                        'message' => "Payment received for request #{$request->request_number}",
                        'data' => json_encode([
                            'payment_id' => $payment->id,
                            'amount' => $payment->amount,
                            'client_id' => $request->client_id,
                        ]),
                    ]);
                }
            }
        }
    }

    /**
     * Get last 4 digits of payment method
     */
    private function getPaymentMethodLast4($paymentIntentId)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->charges->data[0]->payment_method_details->card) {
                return $paymentIntent->charges->data[0]->payment_method_details->card->last4;
            }
        } catch (\Exception $e) {
            Log::error('Error getting payment method details: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Calculate amount due for a request
     */
    private function calculateAmountDue(SpecimenRequest $request)
    {
        // Use estimated price if available
        if ($request->estimated_price) {
            return (float) $request->estimated_price;
        }

        // Fallback calculation
        $basePrice = 50.00;
        $distanceCharge = $request->distance_miles > 15 ? ($request->distance_miles - 15) * 2.00 : 0;
        
        $subtotal = $basePrice + $distanceCharge;
        $taxAmount = $subtotal * 0.085; // 8.5% tax
        
        return $subtotal + $taxAmount;
    }

    /**
     * Create payment notification
     */
    private function createPaymentNotification($request, $payment)
    {
        Notification::create([
            'user_id' => $request->client_id,
            'request_id' => $request->id,
            'type' => 'payment_successful',
            'title' => 'Payment Successful',
            'message' => "Your payment of $" . number_format($payment->amount, 2) . " for request #{$request->request_number} has been received.",
            'data' => json_encode([
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'request_number' => $request->request_number,
            ]),
        ]);
    }

    /**
     * Show payment history
     */
    public function paymentHistory()
    {
        $user = Auth::user();
        $payments = Payment::where('client_id', $user->id)
            ->with('request')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.payments.history', compact('payments'));
    }

    /**
     * Show payment receipt
     */
    public function showReceipt(Payment $payment)
    {
        if ($payment->client_id !== Auth::id()) {
            abort(403);
        }

        $payment->load('request', 'request.facility');

        return view('client.payments.receipt', compact('payment'));
    }

    /**
     * Download payment receipt as PDF
     */
    public function downloadReceipt(Payment $payment)
    {
        if ($payment->client_id !== Auth::id()) {
            abort(403);
        }

        // You can implement PDF generation here using dompdf or similar
        // For now, return the view
        $payment->load('request', 'request.facility');
        
        return response()->view('client.payments.receipt-pdf', compact('payment'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="receipt-' . $payment->id . '.pdf"');
    }
}