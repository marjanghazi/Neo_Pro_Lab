<?php

namespace App\Services;

use App\Mail\FacilityInvoiceGeneratedMail;
use App\Mail\FacilityInvoiceReminderMail;
use App\Models\Facility;
use App\Models\FacilityInvoice;
use App\Models\Payment;
use App\Models\SpecimenRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class FacilityBillingService
{
    public function generateDueInvoices(?Carbon $asOf = null): int
    {
        $asOf = $asOf ?: now();
        $count = 0;
        Facility::active()->chunkById(100, function ($facilities) use ($asOf, &$count) {
            foreach ($facilities as $facility) {
                if ($this->billingCycleEnded($facility, $asOf) && $this->generateInvoiceForFacility($facility, $asOf)) {
                    $count++;
                }
            }
        });
        return $count;
    }

    public function billingCycleEnded(Facility $facility, Carbon $asOf): bool
    {
        $last = $facility->last_invoiced_at ?: $facility->created_at ?: $asOf->copy()->subDays($facility->billingCycleDays());
        return $last->copy()->addDays($facility->billingCycleDays())->endOfDay()->lte($asOf);
    }

    public function generateInvoiceForFacility(Facility $facility, ?Carbon $asOf = null): ?FacilityInvoice
    {
        $asOf = $asOf ?: now();
        $periodStart = ($facility->last_invoiced_at ?: $facility->created_at ?: $asOf->copy()->subDays($facility->billingCycleDays()))->copy()->startOfDay();
        $periodEnd = $periodStart->copy()->addDays($facility->billingCycleDays())->subSecond();
        if ($periodEnd->gt($asOf)) return null;

        return DB::transaction(function () use ($facility, $periodStart, $periodEnd) {
            $deliveries = SpecimenRequest::where('facility_id', $facility->id)
                ->where('status', 'completed')
                ->where('billing_status', 'unbilled')
                ->whereNull('invoice_id')
                ->whereBetween(DB::raw('COALESCE(completed_at, delivered_at, updated_at)'), [$periodStart, $periodEnd])
                ->lockForUpdate()
                ->get();

            if ($deliveries->isEmpty()) {
                $facility->update(['last_invoiced_at' => $periodEnd]);
                return null;
            }

            $subtotal = $deliveries->sum(fn ($d) => (float) ($d->total_price ?? 0));
            $taxRate = (float) ($facility->tax_rate ?? 0);
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $invoice = FacilityInvoice::create([
                'invoice_number' => FacilityInvoice::generateInvoiceNumber(),
                'facility_id' => $facility->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays($facility->paymentTermDays())->toDateString(),
                'payment_terms' => $facility->payment_terms ?: 'net_15',
                'status' => FacilityInvoice::STATUS_PENDING,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'grand_total' => $subtotal + $taxAmount,
                'metadata' => ['delivery_count' => $deliveries->count()],
            ]);

            SpecimenRequest::whereIn('id', $deliveries->pluck('id'))->update(['invoice_id' => $invoice->id, 'billing_status' => 'billed', 'payment_status' => 'pending']);
            $facility->update(['last_invoiced_at' => $periodEnd]);
            $this->sendInvoice($invoice->fresh(['facility', 'deliveries']));
            return $invoice;
        });
    }

    public function sendInvoice(FacilityInvoice $invoice): void
    {
        try {
            $recipient = $invoice->facility->contact_person_email ?: $invoice->facility->email;
            if ($recipient) Mail::to($recipient)->send(new FacilityInvoiceGeneratedMail($invoice));
            $invoice->update(['status' => FacilityInvoice::STATUS_SENT, 'sent_at' => now()]);
        } catch (\Throwable $e) { Log::error('Facility invoice email failed: '.$e->getMessage(), ['invoice_id' => $invoice->id]); }
    }

    public function sendDueReminders(?Carbon $asOf = null): int
    {
        $asOf = ($asOf ?: now())->startOfDay();
        $sent = 0;
        FacilityInvoice::with('facility')->whereNotIn('status', [FacilityInvoice::STATUS_PAID, FacilityInvoice::STATUS_CANCELLED])->chunkById(100, function ($invoices) use ($asOf, &$sent) {
            foreach ($invoices as $invoice) {
                $days = $asOf->diffInDays($invoice->due_date->copy()->startOfDay(), false);
                $type = match ($days) { 3 => '3_days_before_due', 0 => 'due_today', -7 => '7_days_overdue', -15 => '15_days_overdue', -30 => '30_days_overdue', default => null };
                if (! $type || $invoice->last_reminder_type === $type) continue;
                if ($days < 0 && $invoice->status !== FacilityInvoice::STATUS_OVERDUE) $invoice->update(['status' => FacilityInvoice::STATUS_OVERDUE]);
                $recipient = $invoice->facility->contact_person_email ?: $invoice->facility->email;
                if ($recipient) { Mail::to($recipient)->send(new FacilityInvoiceReminderMail($invoice, $type)); $invoice->update(['last_reminder_type' => $type, 'last_reminder_sent_at' => now()]); $sent++; }
            }
        });
        return $sent;
    }

    public function createStripeCheckout(FacilityInvoice $invoice, User $user): array
    {
        if (! config('services.stripe.secret')) return ['success' => false, 'message' => 'Stripe is not configured.'];
        Stripe::setApiKey(config('services.stripe.secret'));
        $payment = Payment::create([
            'facility_invoice_id' => $invoice->id,
            'specimen_request_id' => $invoice->deliveries()->value('id'),
            'user_id' => $user->id,
            'amount' => max(0, (float) $invoice->grand_total - (float) $invoice->amount_paid),
            'currency' => strtoupper(config('services.payment.currency', 'USD')),
            'payment_status' => Payment::STATUS_PENDING,
            'payment_gateway' => Payment::GATEWAY_STRIPE,
            'billing_name' => $invoice->facility->name,
            'billing_email' => $invoice->facility->contact_person_email ?: $invoice->facility->email ?: $user->email,
        ]);
        $session = Session::create([
            'mode' => 'payment', 'payment_method_types' => ['card', 'us_bank_account'], 'customer_email' => $payment->billing_email,
            'line_items' => [[ 'price_data' => ['currency' => strtolower($payment->currency), 'unit_amount' => (int) round($payment->amount * 100), 'product_data' => ['name' => 'NeoProLab Invoice '.$invoice->invoice_number]], 'quantity' => 1]],
            'success_url' => route('client.invoices.show', $invoice).'?session_id={CHECKOUT_SESSION_ID}', 'cancel_url' => route('client.invoices.show', $invoice).'?payment=cancelled',
            'metadata' => ['payment_id' => (string) $payment->id, 'facility_invoice_id' => (string) $invoice->id, 'invoice_number' => $invoice->invoice_number],
        ]);
        $payment->update(['payment_id' => $session->id, 'payment_method' => 'stripe_checkout', 'gateway_response' => ['checkout_session_id' => $session->id]]);
        return ['success' => true, 'checkout_url' => $session->url];
    }
}
