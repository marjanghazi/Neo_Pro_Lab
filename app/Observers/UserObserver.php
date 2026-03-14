<?php
// app/Observers/UserObserver.php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver extends BaseNotificationObserver
{
    public function created(User $user)
    {
        try {
            $this->notificationService->newUserRegistered($user);
        } catch (\Exception $e) {
            Log::error('Failed to send new user notification: ' . $e->getMessage());
        }
    }

    public function updated(User $user)
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