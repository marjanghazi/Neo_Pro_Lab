<?php
// app/Observers/NotificationObserver.php

namespace App\Observers;

use App\Models\SpecimenRequest;
use App\Models\Payment;
use App\Models\PickupProof;
use App\Models\Signature;
use App\Models\CourierQuote;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * ============================================
     * SPECIMEN REQUEST OBSERVER METHODS
     * ============================================
     */

    public function createdSpecimenRequest(SpecimenRequest $request)
    {
        try {
            $this->notificationService->newRequestCreated($request);
        } catch (\Exception $e) {
            Log::error('Failed to send new request notification: ' . $e->getMessage());
        }
    }

    public function updatedSpecimenRequest(SpecimenRequest $request)
    {
        try {
            // Check if status changed
            if ($request->isDirty('status')) {
                $oldStatus = $request->getOriginal('status');
                $newStatus = $request->status;

                $this->notificationService->requestStatusChanged($request, $oldStatus, $newStatus);

                // Specific status notifications
                if ($newStatus === 'approved') {
                    $this->notificationService->requestApproved($request);
                } elseif ($newStatus === 'rejected') {
                    $reason = $request->rejection_reason ?? 'No reason provided';
                    $this->notificationService->requestRejected($request, $reason);
                } elseif ($newStatus === 'accepted_by_courier') {
                    $this->notificationService->courierAccepted($request);
                } elseif ($newStatus === 'picked_up') {
                    $this->notificationService->pickupCompleted($request, $request->pickupProofs()->latest()->first());
                } elseif ($newStatus === 'in_transit') {
                    $this->notificationService->transitStarted($request);
                } elseif ($newStatus === 'arrived_at_destination') {
                    $this->notificationService->arrivedAtDestination($request);
                } elseif ($newStatus === 'completed') {
                    $this->notificationService->requestCompleted($request);
                } elseif ($newStatus === 'cancelled') {
                    $reason = $request->cancellation_reason ?? null;
                    $cancelledBy = $request->cancelled_by ?? auth()->id() ?? 1;
                    $this->notificationService->requestCancelled($request, $cancelledBy, $reason);
                }
            }

            // Check if assigned_to changed (courier assigned)
            if ($request->isDirty('assigned_to') && $request->assigned_to) {
                $this->notificationService->courierAssigned($request, auth()->id());
            }
        } catch (\Exception $e) {
            Log::error('Failed to send request update notification: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * PAYMENT OBSERVER METHODS
     * ============================================
     */

    public function createdPayment(Payment $payment)
    {
        try {
            $this->notificationService->paymentRequired($payment);
        } catch (\Exception $e) {
            Log::error('Failed to send payment created notification: ' . $e->getMessage());
        }
    }

    public function updatedPayment(Payment $payment)
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

    /**
     * ============================================
     * PICKUP PROOF OBSERVER METHODS
     * ============================================
     */

    public function createdPickupProof(PickupProof $proof)
    {
        try {
            $proofType = $proof->proof_type ?? 'pickup';
            $this->notificationService->proofUploaded($proof, $proofType);
        } catch (\Exception $e) {
            Log::error('Failed to send proof upload notification: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * SIGNATURE OBSERVER METHODS
     * ============================================
     */

    public function createdSignature(Signature $signature)
    {
        try {
            $this->notificationService->deliveryCompleted($signature->request, $signature);
        } catch (\Exception $e) {
            Log::error('Failed to send delivery completion notification: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * COURIER QUOTE OBSERVER METHODS
     * ============================================
     */

    public function createdCourierQuote(CourierQuote $quote)
    {
        try {
            $this->notificationService->quoteCreated($quote);
        } catch (\Exception $e) {
            Log::error('Failed to send quote created notification: ' . $e->getMessage());
        }
    }

    public function updatedCourierQuote(CourierQuote $quote)
    {
        try {
            if ($quote->isDirty('status')) {
                $oldStatus = $quote->getOriginal('status');
                $newStatus = $quote->status;

                if ($newStatus === 'accepted') {
                    $this->notificationService->quoteAccepted($quote);
                } elseif ($newStatus === 'declined') {
                    $reason = $quote->decline_reason ?? null;
                    $this->notificationService->quoteDeclined($quote, $reason);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send quote update notification: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * USER OBSERVER METHODS
     * ============================================
     */

    public function createdUser(User $user)
    {
        try {
            $this->notificationService->newUserRegistered($user);
        } catch (\Exception $e) {
            Log::error('Failed to send new user notification: ' . $e->getMessage());
        }
    }

    public function updatedUser(User $user)
    {
        try {
            // Check if critical fields were updated
            $criticalFields = ['first_name', 'last_name', 'email', 'phone', 'password'];
            $updated = array_intersect($criticalFields, array_keys($user->getDirty()));

            if (!empty($updated)) {
                $this->notificationService->userAccountUpdated($user, auth()->id() ?? 1);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send user update notification: ' . $e->getMessage());
        }
    }
}