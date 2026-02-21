<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Create forms directory on boot if it doesn't exist
        $formsDir = storage_path('app/public/forms');
        if (!is_dir($formsDir)) {
            mkdir($formsDir, 0755, true);
        }

        // Set route model binding pattern
        Route::pattern('filename', '[a-zA-Z0-9_\-\.]+');

        // View composer for navbar to pass unread count
        view()->composer('layouts.app', function ($view) {
            $unreadCount = 0;
            if (Auth::check()) {
                $unreadCount = Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
            }
            $view->with('globalUnreadCount', $unreadCount);
        });

        // Blade directive for notification icon
        Blade::directive('notificationIcon', function ($type) {
            $icons = [
                'new_request' => 'fa-file-circle-plus',
                'payment_required' => 'fa-credit-card',
                'payment_received' => 'fa-circle-check',
                'payment_completed' => 'fa-circle-check',
                'payment_failed' => 'fa-circle-exclamation',
                'request_assigned' => 'fa-truck-fast',
                'request_cancelled' => 'fa-ban',
                'request_completed' => 'fa-circle-check',
                'pickup_started' => 'fa-cube',
                'pickup_completed' => 'fa-check-circle',
                'in_transit' => 'fa-truck',
                'arrived_at_destination' => 'fa-location-dot',
                'delivery_completed' => 'fa-check-double',
                'quote_accepted' => 'fa-file-signature',
                'quote_declined' => 'fa-file-excel',
                'proof_uploaded' => 'fa-camera',
                'signature_captured' => 'fa-pen',
            ];

            $icon = $icons[$type] ?? 'fa-bell';
            return "<?php echo 'fa-solid ' . \$icon; ?>";
        });
    }
}
