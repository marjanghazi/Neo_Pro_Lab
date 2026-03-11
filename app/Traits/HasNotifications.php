<?php
// app/Traits/HasNotifications.php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotifications
{
    /**
     * Get all notifications for this model (polymorphic relationship)
     * Renamed to avoid conflict with Laravel's Notifiable trait
     */
    public function modelNotifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Get unread notifications for this model
     */
    public function getUnreadNotifications()
    {
        return $this->modelNotifications()->where('is_read', false);
    }

    /**
     * Get read notifications for this model
     */
    public function getReadNotifications()
    {
        return $this->modelNotifications()->where('is_read', true);
    }

    /**
     * Mark all notifications as read for this model
     */
    public function markAllNotificationsAsRead()
    {
        return $this->getUnreadNotifications()->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Delete all notifications for this model
     */
    public function deleteAllNotifications()
    {
        return $this->modelNotifications()->delete();
    }
}