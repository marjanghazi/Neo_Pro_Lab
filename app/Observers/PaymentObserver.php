<?php
// app/Observers/PaymentObserver.php

namespace App\Observers;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentObserver extends BaseNotificationObserver
{
    public function created(Payment $payment)
    {
        try {
            $this->notificationService->paymentRequired($payment);
        } catch (\Exception $e) {
            Log::error('Failed to send payment created notification: ' . $e->getMessage());
        }
    }

    public function updated(Payment $payment)
    {
        try {
            if ($payment->isDirty('payment_status')) {
                $oldStatus = $payment->getOriginal('payment_status');
                $newStatus = $payment->payment_status;

                if ($newStatus === 'paid' || $newStatus === 'completed') {
                    $this->notificationService->paymentReceived($payment);
                } elseif ($newStatus === 'failed') {
                    $error = $payment->failure_reason ?? null;
                    $this->notificationService->paymentFailed($payment, $error);
                } elseif ($newStatus === 'refunded') {
                    $reason = $payment->refund_reason ?? null;
                    $this->notificationService->paymentRefunded($payment, null, $reason);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment update notification: ' . $e->getMessage());
        }
    }
}