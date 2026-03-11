<?php
// app/Providers/NotificationServiceProvider.php

namespace App\Providers;

use App\Models\SpecimenRequest;
use App\Models\Payment;
use App\Models\PickupProof;
use App\Models\Signature;
use App\Models\CourierQuote;
use App\Models\User;
use App\Observers\SpecimenRequestObserver;
use App\Observers\PaymentObserver;
use App\Observers\PickupProofObserver;
use App\Observers\SignatureObserver;
use App\Observers\CourierQuoteObserver;
use App\Observers\UserObserver;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;
use App\Observers\BaseNotificationObserver;



class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Get the notification service instance
        $notificationService = app(NotificationService::class);

        // Register observers with the service injected
        if (class_exists(SpecimenRequest::class)) {
            SpecimenRequest::observe(new SpecimenRequestObserver($notificationService));
        }
        
        if (class_exists(Payment::class)) {
            Payment::observe(new PaymentObserver($notificationService));
        }
        
        if (class_exists(PickupProof::class)) {
            PickupProof::observe(new PickupProofObserver($notificationService));
        }
        
        if (class_exists(Signature::class)) {
            Signature::observe(new SignatureObserver($notificationService));
        }
        
        if (class_exists(CourierQuote::class)) {
            CourierQuote::observe(new CourierQuoteObserver($notificationService));
        }
        
        if (class_exists(User::class)) {
            User::observe(new UserObserver($notificationService));
        }
    }
}