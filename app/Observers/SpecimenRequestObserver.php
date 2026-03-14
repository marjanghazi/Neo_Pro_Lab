<?php
// app/Observers/SpecimenRequestObserver.php

namespace App\Observers;

use App\Models\SpecimenRequest;
use Illuminate\Support\Facades\Log;

class SpecimenRequestObserver extends BaseNotificationObserver
{
    public function created(SpecimenRequest $request)
    {
        try {
            $this->notificationService->newRequestCreated($request);
        } catch (\Exception $e) {
            Log::error('Failed to send new request notification: ' . $e->getMessage());
        }
    }

    public function updated(SpecimenRequest $request)
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
}