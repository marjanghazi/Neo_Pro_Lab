<?php
// app/Observers/BaseNotificationObserver.php

namespace App\Observers;

use App\Services\NotificationService;

abstract class BaseNotificationObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
}