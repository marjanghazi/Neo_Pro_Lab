<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CourierLocation;
use App\Models\LocationHistory;

class CourierLocationTracking
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and is a courier
        if (auth()->check() && auth()->user()->isCourier()) {
            $user = auth()->user();
            
            // Check if location is available in the request (from mobile or browser)
            $latitude = $request->header('X-Latitude') ?: $request->input('latitude');
            $longitude = $request->header('X-Longitude') ?: $request->input('longitude');
            
            // Also check if we can get from browser geolocation (via JavaScript)
            // This would require JavaScript to send location via AJAX
            
            // Get active request for this courier
            $activeRequest = $user->assignedRequests()
                ->whereIn('status', ['accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])
                ->first();
            
            // If we have location data and courier is active
            if ($latitude && $longitude) {
                $this->logCourierLocation($user, $activeRequest, $latitude, $longitude);
            }
            // If courier has active request but no location in this request,
            // we'll track via JavaScript on the frontend instead
        }

        return $next($request);
    }
    
    protected function logCourierLocation($user, $activeRequest, $latitude, $longitude)
    {
        try {
            // Update or create current location
            CourierLocation::updateOrCreate(
                ['courier_id' => $user->id],
                [
                    'request_id' => $activeRequest?->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'accuracy' => request()->input('accuracy'),
                    'speed' => request()->input('speed'),
                    'heading' => request()->input('heading'),
                    'altitude' => request()->input('altitude'),
                    'battery_level' => request()->input('battery_level'),
                    'is_online' => true,
                    'created_at' => now(),
                ]
            );
            
            // Log to history (for tracking route)
            LocationHistory::create([
                'courier_id' => $user->id,
                'request_id' => $activeRequest?->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log courier location: ' . $e->getMessage());
        }
    }
}