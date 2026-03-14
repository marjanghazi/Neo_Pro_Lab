<?php
// app/Observers/PickupProofObserver.php

namespace App\Observers;

use App\Models\PickupProof;
use Illuminate\Support\Facades\Log;

class PickupProofObserver extends BaseNotificationObserver
{
    public function created(PickupProof $proof)
    {
        try {
            $proofType = $proof->proof_type ?? 'pickup';
            $this->notificationService->proofUploaded($proof, $proofType);
        } catch (\Exception $e) {
            Log::error('Failed to send proof upload notification: ' . $e->getMessage());
        }
    }
}