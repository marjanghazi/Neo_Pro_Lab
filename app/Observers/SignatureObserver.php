<?php
// app/Observers/SignatureObserver.php

namespace App\Observers;

use App\Models\Signature;
use Illuminate\Support\Facades\Log;

class SignatureObserver extends BaseNotificationObserver
{
    public function created(Signature $signature)
    {
        try {
            $this->notificationService->deliveryCompleted($signature->request, $signature);
        } catch (\Exception $e) {
            Log::error('Failed to send delivery completion notification: ' . $e->getMessage());
        }
    }
}