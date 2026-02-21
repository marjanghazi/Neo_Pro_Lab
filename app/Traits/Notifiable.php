<?php
// app/Traits/Notifiable.php

namespace App\Traits;

use App\Models\Notification;
use App\Models\User;

trait Notifiable
{
    /**
     * Create a notification for a specific user
     */
    public function notifyUser($userId, $type, $title, $message, $requestId = null, $data = [])
    {
        $user = User::find($userId);

        return Notification::create([
            'user_id' => $userId,
            'for_role' => $user ? $user->role->slug : null,
            'request_id' => $requestId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to all admin users
     */
    public function notifyAdmins($type, $title, $message, $requestId = null, $data = [])
    {
        $admins = User::whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $this->notifyUser($admin->id, $type, $title, $message, $requestId, $data);
        }
    }

    /**
     * Send notification to a specific courier
     */
    public function notifyCourier($courierId, $type, $title, $message, $requestId = null, $data = [])
    {
        return $this->notifyUser($courierId, $type, $title, $message, $requestId, $data);
    }

    /**
     * Send notification to a specific client
     */
    public function notifyClient($clientId, $type, $title, $message, $requestId = null, $data = [])
    {
        return $this->notifyUser($clientId, $type, $title, $message, $requestId, $data);
    }

    /**
     * Send notification to multiple users by role
     */
    public function notifyByRole($roleSlug, $type, $title, $message, $requestId = null, $data = [])
    {
        $users = User::whereHas('role', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        })->get();

        foreach ($users as $user) {
            $this->notifyUser($user->id, $type, $title, $message, $requestId, $data);
        }
    }

    /**
     * Send notification about request status change
     */
    public function notifyRequestStatusChange($request, $oldStatus, $newStatus)
    {
        $clientId = $request->client_id;
        $courierId = $request->assigned_to;
        $requestNumber = $request->request_number;

        // Status change messages
        $statusMessages = [
            'pending_approval' => 'Your request #' . $requestNumber . ' is pending approval.',
            'approved' => 'Your request #' . $requestNumber . ' has been approved.',
            'rejected' => 'Your request #' . $requestNumber . ' has been rejected.',
            'assigned' => 'A courier has been assigned to request #' . $requestNumber . '.',
            'accepted_by_courier' => 'Courier has accepted request #' . $requestNumber . '.',
            'picked_up' => 'Specimen has been picked up for request #' . $requestNumber . '.',
            'in_transit' => 'Request #' . $requestNumber . ' is in transit.',
            'arrived_at_destination' => 'Courier has arrived at destination for request #' . $requestNumber . '.',
            'delivered' => 'Request #' . $requestNumber . ' has been delivered.',
            'completed' => 'Request #' . $requestNumber . ' has been completed.',
            'cancelled' => 'Request #' . $requestNumber . ' has been cancelled.',
        ];

        $title = 'Request Status Update';
        $message = $statusMessages[$newStatus] ?? 'Request #' . $requestNumber . ' status changed to ' . str_replace('_', ' ', $newStatus);

        // Notify client
        if ($clientId) {
            $this->notifyClient($clientId, 'request_status_change', $title, $message, $request->id, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'request_number' => $requestNumber,
            ]);
        }

        // Notify courier if assigned
        if ($courierId) {
            $this->notifyCourier($courierId, 'request_status_change', $title, $message, $request->id, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'request_number' => $requestNumber,
            ]);
        }

        // Notify admins
        $this->notifyAdmins('request_status_change', $title, 'Request #' . $requestNumber . ' status changed from ' . $oldStatus . ' to ' . $newStatus, $request->id, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'request_number' => $requestNumber,
            'client_id' => $clientId,
            'courier_id' => $courierId,
        ]);
    }

    /**
     * Send notification about payment
     */
    public function notifyPayment($payment, $status)
    {
        $request = $payment->request;
        $clientId = $payment->user_id;

        switch ($status) {
            case 'created':
                $title = 'Payment Required';
                $message = 'Payment of $' . number_format($payment->amount, 2) . ' is required for request #' . $request->request_number;
                $type = 'payment_required';
                break;
            case 'completed':
                $title = 'Payment Completed';
                $message = 'Payment of $' . number_format($payment->amount, 2) . ' has been completed for request #' . $request->request_number;
                $type = 'payment_completed';

                // Notify admins
                $this->notifyAdmins(
                    'payment_received',
                    'Payment Received',
                    'Payment of $' . number_format($payment->amount, 2) . ' received from client for request #' . $request->request_number,
                    $request->id,
                    ['payment_id' => $payment->id]
                );
                break;
            case 'failed':
                $title = 'Payment Failed';
                $message = 'Payment for request #' . $request->request_number . ' failed. Please try again.';
                $type = 'payment_failed';

                // Notify admins
                $this->notifyAdmins(
                    'payment_failed',
                    'Payment Failed',
                    'Payment failed for request #' . $request->request_number,
                    $request->id,
                    ['payment_id' => $payment->id]
                );
                break;
        }

        // Notify client
        if ($clientId) {
            $this->notifyClient($clientId, $type, $title, $message, $request->id, [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'request_number' => $request->request_number,
            ]);
        }
    }

    /**
     * Send notification about new request
     */
    public function notifyNewRequest($request)
    {
        $client = $request->client;
        $clientName = $client ? $client->full_name : 'A client';

        // Notify admins
        $this->notifyAdmins(
            'new_request',
            'New Specimen Request',
            $clientName . ' submitted a new request #' . $request->request_number,
            $request->id,
            [
                'client_id' => $request->client_id,
                'priority' => $request->priority_level,
            ]
        );

        // Notify client (confirmation)
        if ($request->client_id) {
            $this->notifyClient(
                $request->client_id,
                'new_request',
                'Request Submitted Successfully',
                'Your request #' . $request->request_number . ' has been submitted and is pending approval.',
                $request->id,
                ['request_number' => $request->request_number]
            );
        }
    }

    /**
     * Send notification about assignment
     */
    public function notifyAssignment($request, $courier)
    {
        $requestNumber = $request->request_number;

        // Notify courier
        if ($courier) {
            $this->notifyCourier(
                $courier->id,
                'request_assigned',
                'New Assignment',
                'You have been assigned to request #' . $requestNumber,
                $request->id,
                ['request_number' => $requestNumber]
            );
        }

        // Notify client
        if ($request->client_id) {
            $this->notifyClient(
                $request->client_id,
                'request_assigned',
                'Courier Assigned',
                'A courier has been assigned to your request #' . $requestNumber,
                $request->id,
                ['request_number' => $requestNumber]
            );
        }
    }

    /**
     * Send notification about proof upload
     */
    public function notifyProofUpload($proof)
    {
        $request = $proof->request;
        $courier = $proof->courier;

        // Notify client
        if ($request->client_id) {
            $this->notifyClient(
                $request->client_id,
                'proof_uploaded',
                'Proof Uploaded',
                'Pickup proof has been uploaded for request #' . $request->request_number,
                $request->id,
                ['proof_id' => $proof->id]
            );
        }

        // Notify admins
        $this->notifyAdmins(
            'proof_uploaded',
            'Proof Uploaded',
            'Pickup proof uploaded by ' . ($courier ? $courier->full_name : 'courier') . ' for request #' . $request->request_number,
            $request->id,
            ['proof_id' => $proof->id]
        );
    }

    /**
     * Send notification about signature capture
     */
    public function notifySignatureCapture($signature)
    {
        $request = $signature->request;
        $courier = $signature->courier;

        // Notify client
        if ($request->client_id) {
            $this->notifyClient(
                $request->client_id,
                'signature_captured',
                'Delivery Signature Captured',
                'Delivery signature has been captured for request #' . $request->request_number,
                $request->id,
                ['signature_id' => $signature->id]
            );
        }

        // Notify admins
        $this->notifyAdmins(
            'signature_captured',
            'Signature Captured',
            'Delivery signature captured by ' . ($courier ? $courier->full_name : 'courier') . ' for request #' . $request->request_number,
            $request->id,
            ['signature_id' => $signature->id]
        );
    }

    /**
     * Send notification about quote
     */
    public function notifyQuote($quote, $action)
    {
        $request = $quote->request;
        $courier = $quote->courier;

        switch ($action) {
            case 'created':
                $title = 'New Quote Available';
                $message = 'A price quote is available for request #' . $request->request_number;
                $type = 'quote_created';
                break;
            case 'accepted':
                $title = 'Quote Accepted';
                $message = ($courier ? $courier->full_name : 'Courier') . ' accepted the quote for request #' . $request->request_number;
                $type = 'quote_accepted';
                break;
            case 'declined':
                $title = 'Quote Declined';
                $message = ($courier ? $courier->full_name : 'Courier') . ' declined the quote for request #' . $request->request_number;
                $type = 'quote_declined';
                break;
        }

        // Notify admins
        $this->notifyAdmins($type, $title, $message, $request->id, [
            'quote_id' => $quote->id,
            'request_number' => $request->request_number,
        ]);

        // Notify client if quote accepted/created
        if ($action === 'accepted' && $request->client_id) {
            $this->notifyClient(
                $request->client_id,
                $type,
                $title,
                'Your request #' . $request->request_number . ' has been accepted by a courier.',
                $request->id,
                ['quote_id' => $quote->id]
            );
        }
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }
}
