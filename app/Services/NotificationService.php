<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\SpecimenRequest;
use App\Models\Payment;
use App\Models\PickupProof;
use App\Models\Signature;
use App\Models\CourierQuote;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to a specific user
     */
    public function toUser($userId, $type, $title, $message, $requestId = null, $data = [])
    {
        $user = User::find($userId);
        
        if (!$user) {
            return null;
        }

        return Notification::create([
            'user_id' => $userId,
            'for_role' => $user->role->slug,
            'request_id' => $requestId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to all admins
     */
    public function toAdmins($type, $title, $message, $requestId = null, $data = [])
    {
        $admins = User::whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $this->toUser($admin->id, $type, $title, $message, $requestId, $data);
        }

        return true;
    }

    /**
     * Send notification to a courier
     */
    public function toCourier($courierId, $type, $title, $message, $requestId = null, $data = [])
    {
        return $this->toUser($courierId, $type, $title, $message, $requestId, $data);
    }

    /**
     * Send notification to a client
     */
    public function toClient($clientId, $type, $title, $message, $requestId = null, $data = [])
    {
        return $this->toUser($clientId, $type, $title, $message, $requestId, $data);
    }

    /**
     * Send notification to users by role
     */
    public function toRole($roleSlug, $type, $title, $message, $requestId = null, $data = [])
    {
        $users = User::whereHas('role', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        })->get();

        foreach ($users as $user) {
            $this->toUser($user->id, $type, $title, $message, $requestId, $data);
        }

        return true;
    }

    /**
     * ============================================
     * REQUEST RELATED NOTIFICATIONS
     * ============================================
     */

    /**
     * New request created
     */
    public function newRequestCreated(SpecimenRequest $request)
    {
        $client = $request->client;
        
        // Notify admins
        $this->toAdmins(
            'new_request',
            'New Specimen Request',
            "New request #{$request->request_number} submitted by {$client->full_name}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'client_name' => $client->full_name,
                'priority' => $request->priority_level,
                'pickup_address' => $request->pickup_address,
                'delivery_address' => $request->delivery_address,
            ]
        );

        // Notify client (confirmation)
        $this->toClient(
            $client->id,
            'request_submitted',
            'Request Submitted Successfully',
            "Your request #{$request->request_number} has been submitted and is pending approval.",
            $request->id,
            ['request_number' => $request->request_number]
        );

        return true;
    }

    /**
     * Request status changed
     */
    public function requestStatusChanged(SpecimenRequest $request, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending_approval' => 'Your request is pending approval.',
            'approved' => 'Your request has been approved.',
            'rejected' => 'Your request has been rejected.',
            'assigned' => 'A courier has been assigned.',
            'accepted_by_courier' => 'Courier has accepted the request.',
            'picked_up' => 'Specimen has been picked up.',
            'in_transit' => 'Request is in transit.',
            'arrived_at_destination' => 'Courier has arrived at destination.',
            'delivered' => 'Request has been delivered.',
            'completed' => 'Request has been completed.',
            'cancelled' => 'Request has been cancelled.',
        ];

        $message = $statusMessages[$newStatus] ?? "Request status changed to " . str_replace('_', ' ', $newStatus);
        
        $data = [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'request_number' => $request->request_number,
        ];

        // Notify client
        if ($request->client_id) {
            $this->toClient(
                $request->client_id,
                'request_status_change',
                'Request Status Update',
                "Request #{$request->request_number}: {$message}",
                $request->id,
                $data
            );
        }

        // Notify courier if assigned
        if ($request->assigned_to) {
            $this->toCourier(
                $request->assigned_to,
                'request_status_change',
                'Request Status Update',
                "Request #{$request->request_number}: {$message}",
                $request->id,
                $data
            );
        }

        // Notify admins
        $this->toAdmins(
            'request_status_change',
            'Request Status Changed',
            "Request #{$request->request_number} changed from {$oldStatus} to {$newStatus}",
            $request->id,
            array_merge($data, [
                'client_id' => $request->client_id,
                'courier_id' => $request->assigned_to,
            ])
        );

        return true;
    }

    /**
     * Request approved by admin
     */
    public function requestApproved(SpecimenRequest $request)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'request_approved',
            'Request Approved',
            "Your request #{$request->request_number} has been approved and is ready for courier assignment.",
            $request->id,
            ['request_number' => $request->request_number]
        );

        // Notify admins
        $this->toAdmins(
            'request_approved',
            'Request Approved',
            "Request #{$request->request_number} has been approved.",
            $request->id,
            ['request_number' => $request->request_number]
        );

        return true;
    }

    /**
     * Request rejected by admin
     */
    public function requestRejected(SpecimenRequest $request, $reason)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'request_rejected',
            'Request Rejected',
            "Your request #{$request->request_number} has been rejected. Reason: {$reason}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'rejection_reason' => $reason,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'request_rejected',
            'Request Rejected',
            "Request #{$request->request_number} was rejected. Reason: {$reason}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'rejection_reason' => $reason,
            ]
        );

        return true;
    }

    /**
     * ============================================
     * COURIER RELATED NOTIFICATIONS
     * ============================================
     */

    /**
     * Courier assigned to request
     */
    public function courierAssigned(SpecimenRequest $request, $assignedBy = null)
    {
        // Notify courier
        if ($request->assigned_to) {
            $this->toCourier(
                $request->assigned_to,
                'request_assigned',
                'New Assignment',
                "You have been assigned to request #{$request->request_number}",
                $request->id,
                [
                    'request_number' => $request->request_number,
                    'pickup_address' => $request->pickup_address,
                    'delivery_address' => $request->delivery_address,
                ]
            );
        }

        // Notify client
        if ($request->client_id) {
            $this->toClient(
                $request->client_id,
                'courier_assigned',
                'Courier Assigned',
                "A courier has been assigned to your request #{$request->request_number}",
                $request->id,
                ['request_number' => $request->request_number]
            );
        }

        // Notify admins
        $this->toAdmins(
            'courier_assigned',
            'Courier Assigned',
            "Courier assigned to request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'courier_id' => $request->assigned_to,
            ]
        );

        return true;
    }

    /**
     * Courier accepted assignment
     */
    public function courierAccepted(SpecimenRequest $request)
    {
        $courier = $request->courier;

        // Notify client
        $this->toClient(
            $request->client_id,
            'courier_accepted',
            'Courier Accepted',
            "Courier {$courier->full_name} has accepted your request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'courier_name' => $courier->full_name,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'courier_accepted',
            'Courier Accepted',
            "Courier {$courier->full_name} accepted request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'courier_id' => $courier->id,
            ]
        );

        return true;
    }

    /**
     * Courier declined assignment
     */
    public function courierDeclined(SpecimenRequest $request, $reason = null)
    {
        $courier = $request->courier;

        // Notify admins
        $this->toAdmins(
            'courier_declined',
            'Courier Declined',
            "Courier {$courier->full_name} declined request #{$request->request_number}" . ($reason ? ": {$reason}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'courier_id' => $courier->id,
                'decline_reason' => $reason,
            ]
        );

        return true;
    }

    /**
     * ============================================
     * PICKUP & DELIVERY NOTIFICATIONS
     * ============================================
     */

    /**
     * Pickup started
     */
    public function pickupStarted(SpecimenRequest $request)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'pickup_started',
            'Pickup Started',
            "Courier has started pickup for request #{$request->request_number}",
            $request->id,
            ['request_number' => $request->request_number]
        );

        // Notify admins
        $this->toAdmins(
            'pickup_started',
            'Pickup Started',
            "Courier started pickup for request #{$request->request_number}",
            $request->id,
            ['request_number' => $request->request_number]
        );

        return true;
    }

    /**
     * Pickup completed
     */
    public function pickupCompleted(SpecimenRequest $request, PickupProof $proof)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'pickup_completed',
            'Pickup Completed',
            "Specimen picked up for request #{$request->request_number}. Proof uploaded.",
            $request->id,
            [
                'request_number' => $request->request_number,
                'proof_id' => $proof->id,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'pickup_completed',
            'Pickup Completed',
            "Pickup completed for request #{$request->request_number}. Proof uploaded.",
            $request->id,
            [
                'request_number' => $request->request_number,
                'proof_id' => $proof->id,
                'courier_id' => $request->assigned_to,
            ]
        );

        return true;
    }

    /**
     * Proof uploaded
     */
    public function proofUploaded(PickupProof $proof, $proofType = 'pickup')
    {
        $request = $proof->request;
        $courier = $proof->courier;

        $typeMessages = [
            'pickup' => 'Pickup proof uploaded',
            'transit' => 'Transit proof uploaded',
            'arrival' => 'Arrival proof uploaded',
        ];

        $message = $typeMessages[$proofType] ?? 'Proof uploaded';

        // Notify client
        if ($request->client_id) {
            $this->toClient(
                $request->client_id,
                'proof_uploaded',
                ucfirst($proofType) . ' Proof Uploaded',
                "{$message} for request #{$request->request_number}",
                $request->id,
                [
                    'request_number' => $request->request_number,
                    'proof_type' => $proofType,
                    'proof_id' => $proof->id,
                ]
            );
        }

        // Notify admins
        $this->toAdmins(
            'proof_uploaded',
            ucfirst($proofType) . ' Proof Uploaded',
            "{$message} for request #{$request->request_number} by " . ($courier ? $courier->full_name : 'courier'),
            $request->id,
            [
                'request_number' => $request->request_number,
                'proof_type' => $proofType,
                'proof_id' => $proof->id,
                'courier_id' => $courier->id,
            ]
        );

        return true;
    }

    /**
     * Transit started
     */
    public function transitStarted(SpecimenRequest $request)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'transit_started',
            'In Transit',
            "Specimen for request #{$request->request_number} is now in transit",
            $request->id,
            ['request_number' => $request->request_number]
        );

        // Notify admins
        $this->toAdmins(
            'transit_started',
            'Transit Started',
            "Request #{$request->request_number} is now in transit",
            $request->id,
            ['request_number' => $request->request_number]
        );

        return true;
    }

    /**
     * Arrived at destination
     */
    public function arrivedAtDestination(SpecimenRequest $request)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'arrived_at_destination',
            'Arrived at Destination',
            "Courier has arrived at destination for request #{$request->request_number}",
            $request->id,
            ['request_number' => $request->request_number]
        );

        // Notify admins
        $this->toAdmins(
            'arrived_at_destination',
            'Arrived at Destination',
            "Courier arrived at destination for request #{$request->request_number}",
            $request->id,
            ['request_number' => $request->request_number]
        );

        return true;
    }

    /**
     * Delivery completed with signature
     */
    public function deliveryCompleted(SpecimenRequest $request, Signature $signature)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'delivery_completed',
            'Delivery Completed',
            "Request #{$request->request_number} has been delivered. Signature captured.",
            $request->id,
            [
                'request_number' => $request->request_number,
                'signature_id' => $signature->id,
                'recipient_name' => $signature->recipient_name,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'delivery_completed',
            'Delivery Completed',
            "Delivery completed for request #{$request->request_number}. Signature captured by " . ($signature->recipient_name ?? 'recipient'),
            $request->id,
            [
                'request_number' => $request->request_number,
                'signature_id' => $signature->id,
                'courier_id' => $request->assigned_to,
            ]
        );

        return true;
    }

    /**
     * Request completed
     */
    public function requestCompleted(SpecimenRequest $request)
    {
        // Notify client
        $this->toClient(
            $request->client_id,
            'request_completed',
            'Request Completed',
            "Your request #{$request->request_number} has been completed successfully.",
            $request->id,
            ['request_number' => $request->request_number]
        );

        // Notify courier
        if ($request->assigned_to) {
            $this->toCourier(
                $request->assigned_to,
                'request_completed',
                'Request Completed',
                "Request #{$request->request_number} has been marked as completed.",
                $request->id,
                ['request_number' => $request->request_number]
            );
        }

        // Notify admins
        $this->toAdmins(
            'request_completed',
            'Request Completed',
            "Request #{$request->request_number} has been completed.",
            $request->id,
            [
                'request_number' => $request->request_number,
                'client_id' => $request->client_id,
                'courier_id' => $request->assigned_to,
            ]
        );

        return true;
    }

    /**
     * Request cancelled
     */
    public function requestCancelled(SpecimenRequest $request, $cancelledBy, $reason = null)
    {
        $canceller = User::find($cancelledBy);
        $cancellerName = $canceller ? $canceller->full_name : 'System';

        // Notify client (if not cancelled by client)
        if ($request->client_id && $cancelledBy != $request->client_id) {
            $this->toClient(
                $request->client_id,
                'request_cancelled',
                'Request Cancelled',
                "Your request #{$request->request_number} has been cancelled." . ($reason ? " Reason: {$reason}" : ""),
                $request->id,
                [
                    'request_number' => $request->request_number,
                    'cancelled_by' => $cancellerName,
                    'cancellation_reason' => $reason,
                ]
            );
        }

        // Notify courier (if not cancelled by courier and courier assigned)
        if ($request->assigned_to && $cancelledBy != $request->assigned_to) {
            $this->toCourier(
                $request->assigned_to,
                'request_cancelled',
                'Request Cancelled',
                "Request #{$request->request_number} has been cancelled." . ($reason ? " Reason: {$reason}" : ""),
                $request->id,
                [
                    'request_number' => $request->request_number,
                    'cancelled_by' => $cancellerName,
                    'cancellation_reason' => $reason,
                ]
            );
        }

        // Notify admins
        $this->toAdmins(
            'request_cancelled',
            'Request Cancelled',
            "Request #{$request->request_number} was cancelled by {$cancellerName}" . ($reason ? ": {$reason}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'cancelled_by' => $cancelledBy,
                'canceller_name' => $cancellerName,
                'cancellation_reason' => $reason,
                'client_id' => $request->client_id,
                'courier_id' => $request->assigned_to,
            ]
        );

        return true;
    }

    /**
     * ============================================
     * PAYMENT NOTIFICATIONS
     * ============================================
     */

    /**
     * Payment required
     */
    public function paymentRequired(Payment $payment)
    {
        $request = $payment->request;

        // Notify client
        $this->toClient(
            $payment->user_id,
            'payment_required',
            'Payment Required',
            "Payment of $" . number_format($payment->amount, 2) . " is required for request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'payment_required',
            'Payment Required',
            "Payment of $" . number_format($payment->amount, 2) . " required for request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'client_id' => $payment->user_id,
            ]
        );

        return true;
    }

    /**
     * Payment received (completed)
     */
    public function paymentReceived(Payment $payment)
    {
        $request = $payment->request;
        $user = $payment->user;

        // Notify client
        $this->toClient(
            $payment->user_id,
            'payment_received',
            'Payment Received',
            "Payment of $" . number_format($payment->amount, 2) . " received for request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'payment_received',
            'Payment Received',
            "Payment of $" . number_format($payment->amount, 2) . " received from {$user->full_name} for request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'client_id' => $payment->user_id,
            ]
        );

        return true;
    }

    /**
     * Payment failed
     */
    public function paymentFailed(Payment $payment, $error = null)
    {
        $request = $payment->request;

        // Notify client
        $this->toClient(
            $payment->user_id,
            'payment_failed',
            'Payment Failed',
            "Payment for request #{$request->request_number} failed." . ($error ? " Error: {$error}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'error' => $error,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'payment_failed',
            'Payment Failed',
            "Payment failed for request #{$request->request_number} from {$payment->user->full_name}" . ($error ? ": {$error}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'error' => $error,
                'client_id' => $payment->user_id,
            ]
        );

        return true;
    }

    /**
     * Payment refunded
     */
    public function paymentRefunded(Payment $payment, $amount = null, $reason = null)
    {
        $request = $payment->request;
        $refundAmount = $amount ?? $payment->amount;

        // Notify client
        $this->toClient(
            $payment->user_id,
            'payment_refunded',
            'Payment Refunded',
            "Refund of $" . number_format($refundAmount, 2) . " processed for request #{$request->request_number}" . ($reason ? ". Reason: {$reason}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'refund_amount' => $refundAmount,
                'reason' => $reason,
            ]
        );

        // Notify admins
        $this->toAdmins(
            'payment_refunded',
            'Payment Refunded',
            "Refund of $" . number_format($refundAmount, 2) . " processed for request #{$request->request_number} from {$payment->user->full_name}" . ($reason ? ". Reason: {$reason}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'payment_id' => $payment->id,
                'refund_amount' => $refundAmount,
                'reason' => $reason,
                'client_id' => $payment->user_id,
            ]
        );

        return true;
    }

    /**
     * ============================================
     * QUOTE NOTIFICATIONS
     * ============================================
     */

    /**
     * Quote created
     */
    public function quoteCreated(CourierQuote $quote)
    {
        $request = $quote->request;
        $courier = $quote->courier;

        // Notify courier
        $this->toCourier(
            $courier->id,
            'quote_created',
            'New Quote Available',
            "A quote has been created for request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'quote_id' => $quote->id,
                'amount' => $quote->amount,
                'valid_until' => $quote->valid_until?->format('Y-m-d H:i'),
            ]
        );

        // Notify admins
        $this->toAdmins(
            'quote_created',
            'Quote Created',
            "Quote created for request #{$request->request_number} for courier {$courier->full_name}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'quote_id' => $quote->id,
                'courier_id' => $courier->id,
                'amount' => $quote->amount,
            ]
        );

        return true;
    }

    /**
     * Quote accepted
     */
    public function quoteAccepted(CourierQuote $quote)
    {
        $request = $quote->request;
        $courier = $quote->courier;

        // Notify client
        if ($request->client_id) {
            $this->toClient(
                $request->client_id,
                'quote_accepted',
                'Quote Accepted',
                "Your request #{$request->request_number} has been accepted by a courier.",
                $request->id,
                [
                    'request_number' => $request->request_number,
                    'quote_id' => $quote->id,
                ]
            );
        }

        // Notify admins
        $this->toAdmins(
            'quote_accepted',
            'Quote Accepted',
            "Courier {$courier->full_name} accepted quote for request #{$request->request_number}",
            $request->id,
            [
                'request_number' => $request->request_number,
                'quote_id' => $quote->id,
                'courier_id' => $courier->id,
                'amount' => $quote->amount,
            ]
        );

        return true;
    }

    /**
     * Quote declined
     */
    public function quoteDeclined(CourierQuote $quote, $reason = null)
    {
        $request = $quote->request;
        $courier = $quote->courier;

        // Notify admins
        $this->toAdmins(
            'quote_declined',
            'Quote Declined',
            "Courier {$courier->full_name} declined quote for request #{$request->request_number}" . ($reason ? ": {$reason}" : ""),
            $request->id,
            [
                'request_number' => $request->request_number,
                'quote_id' => $quote->id,
                'courier_id' => $courier->id,
                'decline_reason' => $reason,
            ]
        );

        return true;
    }

    /**
     * ============================================
     * USER ACCOUNT NOTIFICATIONS
     * ============================================
     */

    /**
     * New user registered
     */
    public function newUserRegistered(User $user)
    {
        // Notify admins
        $this->toAdmins(
            'new_user',
            'New User Registered',
            "New {$user->role->name} registered: {$user->full_name} ({$user->email})",
            null,
            [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'user_email' => $user->email,
                'user_role' => $user->role->name,
            ]
        );

        return true;
    }

    /**
     * User account updated
     */
    public function userAccountUpdated(User $user, $updatedBy)
    {
        $updater = User::find($updatedBy);

        // Notify user
        $this->toUser(
            $user->id,
            'account_updated',
            'Account Updated',
            "Your account information has been updated.",
            null,
            ['updated_by' => $updater ? $updater->full_name : 'System']
        );

        // Notify admins
        if ($updatedBy != $user->id) {
            $this->toAdmins(
                'account_updated',
                'User Account Updated',
                "User account for {$user->full_name} was updated by {$updater->full_name}",
                null,
                [
                    'user_id' => $user->id,
                    'user_name' => $user->full_name,
                    'updated_by' => $updater->full_name,
                ]
            );
        }

        return true;
    }

    /**
     * ============================================
     * SYSTEM NOTIFICATIONS
     * ============================================
     */

    /**
     * System notification (can be sent to any user/role)
     */
    public function systemNotification($recipientType, $recipientId = null, $title, $message, $data = [])
    {
        if ($recipientType === 'all') {
            // Send to all users
            $users = User::all();
            foreach ($users as $user) {
                $this->toUser($user->id, 'system', $title, $message, null, $data);
            }
        } elseif ($recipientType === 'role') {
            // Send to all users with a specific role
            $this->toRole($recipientId, 'system', $title, $message, null, $data);
        } elseif ($recipientType === 'user') {
            // Send to specific user
            $this->toUser($recipientId, 'system', $title, $message, null, $data);
        }

        return true;
    }

    /**
     * Test notification (for development)
     */
    public function testNotification($userId)
    {
        return $this->toUser(
            $userId,
            'test',
            'Test Notification',
            'This is a test notification to verify the notification system is working correctly.',
            null,
            ['test' => true, 'timestamp' => now()->toDateTimeString()]
        );
    }

    /**
     * Helper to get icon for notification type
     */
    public static function getIcon($type)
    {
        $icons = [
            'new_request' => 'fas fa-file-circle-plus',
            'request_submitted' => 'fas fa-check-circle',
            'request_approved' => 'fas fa-check-circle',
            'request_rejected' => 'fas fa-times-circle',
            'request_status_change' => 'fas fa-sync-alt',
            'request_assigned' => 'fas fa-truck-fast',
            'courier_assigned' => 'fas fa-user-plus',
            'courier_accepted' => 'fas fa-check-circle',
            'courier_declined' => 'fas fa-times-circle',
            'pickup_started' => 'fas fa-play-circle',
            'pickup_completed' => 'fas fa-check-circle',
            'transit_started' => 'fas fa-truck',
            'arrived_at_destination' => 'fas fa-location-dot',
            'delivery_completed' => 'fas fa-check-double',
            'request_completed' => 'fas fa-check-double',
            'request_cancelled' => 'fas fa-ban',
            'proof_uploaded' => 'fas fa-camera',
            'signature_captured' => 'fas fa-pen',
            'payment_required' => 'fas fa-credit-card',
            'payment_received' => 'fas fa-circle-check',
            'payment_failed' => 'fas fa-circle-exclamation',
            'payment_refunded' => 'fas fa-rotate-left',
            'quote_created' => 'fas fa-file-invoice',
            'quote_accepted' => 'fas fa-file-signature',
            'quote_declined' => 'fas fa-file-excel',
            'new_user' => 'fas fa-user-plus',
            'account_updated' => 'fas fa-user-edit',
            'system' => 'fas fa-gear',
            'test' => 'fas fa-vial',
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    /**
     * Helper to get color for notification type
     */
    public static function getColor($type)
    {
        $colors = [
            'new_request' => 'blue',
            'request_submitted' => 'green',
            'request_approved' => 'green',
            'request_rejected' => 'red',
            'request_status_change' => 'teal',
            'request_assigned' => 'purple',
            'courier_assigned' => 'purple',
            'courier_accepted' => 'green',
            'courier_declined' => 'red',
            'pickup_started' => 'yellow',
            'pickup_completed' => 'green',
            'transit_started' => 'blue',
            'arrived_at_destination' => 'green',
            'delivery_completed' => 'green',
            'request_completed' => 'green',
            'request_cancelled' => 'red',
            'proof_uploaded' => 'blue',
            'signature_captured' => 'green',
            'payment_required' => 'orange',
            'payment_received' => 'green',
            'payment_failed' => 'red',
            'payment_refunded' => 'yellow',
            'quote_created' => 'blue',
            'quote_accepted' => 'green',
            'quote_declined' => 'red',
            'new_user' => 'teal',
            'account_updated' => 'gray',
            'system' => 'gray',
            'test' => 'purple',
        ];

        return $colors[$type] ?? 'blue';
    }
}