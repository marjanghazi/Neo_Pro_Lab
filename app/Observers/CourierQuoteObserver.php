<?php
// app/Observers/CourierQuoteObserver.php

namespace App\Observers;

use App\Models\CourierQuote;
use Illuminate\Support\Facades\Log;

class CourierQuoteObserver extends BaseNotificationObserver
{
    /**
     * Handle the CourierQuote "created" event.
     */
    public function created(CourierQuote $quote)
    {
        try {
            $this->notificationService->quoteCreated($quote);
        } catch (\Exception $e) {
            Log::error('Failed to send quote created notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle the CourierQuote "updated" event.
     */
    public function updated(CourierQuote $quote)
    {
        try {
            // Check if status changed
            if ($quote->isDirty('status')) {
                $oldStatus = $quote->getOriginal('status');
                $newStatus = $quote->status;

                // Send appropriate notifications based on new status
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
     * Handle the CourierQuote "deleted" event.
     */
    public function deleted(CourierQuote $quote)
    {
        // Optional: Add notification for quote deletion if needed
        try {
            // You could add a notification here if you want to notify about quote deletion
            // $this->notificationService->quoteDeleted($quote);
        } catch (\Exception $e) {
            Log::error('Failed to send quote deletion notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle the CourierQuote "restored" event.
     */
    public function restored(CourierQuote $quote)
    {
        // Optional: Add notification for quote restoration if needed
    }

    /**
     * Handle the CourierQuote "force deleted" event.
     */
    public function forceDeleted(CourierQuote $quote)
    {
        // Optional: Add notification for force deletion if needed
    }
}