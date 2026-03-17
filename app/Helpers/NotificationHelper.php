<?php

if (!function_exists('notify')) {
    /**
     * Get the notification service instance
     * 
     * @return \App\Services\NotificationService
     */
    function notify()
    {
        return app(\App\Services\NotificationService::class);
    }
}