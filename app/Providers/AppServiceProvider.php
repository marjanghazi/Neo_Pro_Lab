<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\ServiceProvider;

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
    }
}
