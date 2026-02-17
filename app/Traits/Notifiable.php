<?php

namespace App\Traits;

use App\Models\Notification;

trait Notifiable
{
    /**
     * Create a notification
     */
    public function createNotification($userId, $type, $title, $message, $requestId = null, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'request_id' => $requestId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to admin users
     */
    public function notifyAdmins($type, $title, $message, $requestId = null, $data = [])
    {
        $admins = \App\Models\User::whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $this->createNotification($admin->id, $type, $title, $message, $requestId, $data);
        }
    }

    /**
     * Send notification to courier
     */
    public function notifyCourier($courierId, $type, $title, $message, $requestId = null, $data = [])
    {
        return $this->createNotification($courierId, $type, $title, $message, $requestId, $data);
    }

    /**
     * Send notification to client
     */
    public function notifyClient($clientId, $type, $title, $message, $requestId = null, $data = [])
    {
        return $this->createNotification($clientId, $type, $title, $message, $requestId, $data);
    }
}