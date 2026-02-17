<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\CourierQuote;
use App\Traits\Notifiable;

class AdminRequestController extends Controller
{
    use Notifiable;

    public function index(Request $request)
    {
        $query = SpecimenRequest::with(['client', 'facility', 'courier']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'LIKE', "%{$search}%")
                    ->orWhere('recipient_name', 'LIKE', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.requests.index', compact('requests'));
    }

    public function show(SpecimenRequest $request)
    {
        // The parameter is named $request but it's a SpecimenRequest model
        $request->load(['client', 'facility', 'courier', 'stops', 'documents']);
        $couriers = User::whereHas('role', function ($q) {
            $q->where('slug', 'courier');
        })->where('is_active', true)->get();

        return view('admin.requests.show', [
            'request' => $request,
            'couriers' => $couriers
        ]);
    }

    public function assignCourier(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $request->update([
            'assigned_to' => $validated['courier_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        // Create notification for courier using the trait
        $this->notifyCourier(
            $validated['courier_id'],
            'request_assigned',
            'New Assignment',
            "You have been assigned to request #{$request->request_number}",
            $request->id,
            [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'assigned_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'assigned_at' => now()->toDateTimeString()
            ]
        );

        // Also notify admins about the assignment
        $this->notifyAdmins(
            'request_assigned',
            'Request Assigned',
            "Request #{$request->request_number} has been assigned to a courier",
            $request->id,
            [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'courier_id' => $validated['courier_id'],
                'assigned_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
            ]
        );

        // Handle AJAX response
        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Courier assigned successfully.',
                'redirect' => route('admin.requests.show', $request)
            ]);
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', 'Courier assigned successfully.');
    }

    public function updateStatus(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:approved,rejected,cancelled'
        ]);

        // Update status based on the submitted value
        $request->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] == 'approved' ? now() : null,
            'cancelled_at' => $validated['status'] == 'cancelled' ? now() : null,
            'cancelled_by' => $validated['status'] == 'cancelled' ? Auth::id() : null,
        ]);

        // Create notification for client
        if (in_array($validated['status'], ['approved', 'rejected'])) {
            if ($request->client_id) {
                $this->notifyClient(
                    $request->client_id,
                    'status_update',
                    'Request ' . ucfirst($validated['status']),
                    "Your request " . ($request->request_number ?: '#' . $request->id) . " has been {$validated['status']}.",
                    $request->id,
                    [
                        'request_id' => $request->id,
                        'request_number' => $request->request_number,
                        'status' => $validated['status'],
                        'updated_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'updated_at' => now()->toDateTimeString()
                    ]
                );
            }
        }

        // Notify admins about the status change
        $this->notifyAdmins(
            'status_update',
            'Request Status Updated',
            "Request #{$request->request_number} has been {$validated['status']}",
            $request->id,
            [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'status' => $validated['status'],
                'updated_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
            ]
        );

        // Handle AJAX response
        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Request {$validated['status']} successfully!",
                'redirect' => route('admin.requests.show', $request)
            ]);
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', "Request {$validated['status']} successfully!");
    }

    public function calculatePrice(Request $httpRequest, SpecimenRequest $request)
    {
        try {
            // Calculate distance
            $distanceMiles = $this->calculateDistance($request);

            // Base price
            $basePrice = 50.00;

            // Distance charge
            $distanceCharge = 0.00;
            if ($distanceMiles > 15) {
                $distanceCharge = ($distanceMiles - 15) * 2.00;
            }

            // Additional charges
            $statUrgentCharge = $request->priority_level == 'stat' ? 20.00 : 0.00;

            // Night service (check if pickup time is after 6PM)
            $pickupTime = $request->scheduled_pickup_time;
            $nightHoursCharge = 0.00;
            if ($pickupTime && $pickupTime->hour >= 18) {
                $nightHoursCharge = 25.00;
            }

            // Weekend charge (check if pickup is on weekend or holiday)
            $weekendCharge = 0.00;
            if ($pickupTime && in_array($pickupTime->dayOfWeek, [0, 6])) { // 0 = Sunday, 6 = Saturday
                $weekendCharge = $basePrice * 0.35;
            }

            // Cold chain handling
            $coldChainCharge = in_array($request->temperature_requirement, ['cold', 'frozen']) ? 7.00 : 0.00;

            // Additional stops
            $additionalStopCharge = $request->stops->count() * 10.00;

            // Calculate total
            $totalPrice = $basePrice + $distanceCharge + $statUrgentCharge + $nightHoursCharge +
                $weekendCharge + $coldChainCharge + $additionalStopCharge;

            // Calculate courier fee (70% of total price as an example)
            $courierFee = $totalPrice * 0.70;
            $adminFee = $totalPrice * 0.20;
            $profitMargin = $totalPrice * 0.10;

            // Update request with pricing
            $request->update([
                'base_price' => $basePrice,
                'distance_miles' => $distanceMiles,
                'distance_charge' => $distanceCharge,
                'stat_urgent_charge' => $statUrgentCharge,
                'night_hours_charge' => $nightHoursCharge,
                'weekend_charge' => $weekendCharge,
                'cold_chain_charge' => $coldChainCharge,
                'additional_stop_charge' => $additionalStopCharge,
                'total_price' => $totalPrice,
                'courier_fee' => $courierFee,
                'admin_fee' => $adminFee,
                'profit_margin' => $profitMargin,
                'has_stat_urgent' => $request->priority_level == 'stat',
                'has_night_service' => $nightHoursCharge > 0,
                'has_weekend_service' => $weekendCharge > 0,
                'has_cold_chain' => $coldChainCharge > 0,
                'additional_stops' => $request->stops->count(),
                'is_price_quoted' => true,
            ]);

            // Notify admins about price calculation
            $this->notifyAdmins(
                'price_calculated',
                'Price Calculated',
                "Price has been calculated for request #{$request->request_number}: $" . number_format($totalPrice, 2),
                $request->id,
                [
                    'request_id' => $request->id,
                    'request_number' => $request->request_number,
                    'total_price' => $totalPrice,
                    'calculated_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
                ]
            );

            // Check if it's an AJAX request
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Price calculated successfully! Total: $" . number_format($totalPrice, 2),
                    'data' => [
                        'base_price' => $basePrice,
                        'distance_miles' => $distanceMiles,
                        'distance_charge' => $distanceCharge,
                        'stat_urgent_charge' => $statUrgentCharge,
                        'night_hours_charge' => $nightHoursCharge,
                        'weekend_charge' => $weekendCharge,
                        'cold_chain_charge' => $coldChainCharge,
                        'additional_stop_charge' => $additionalStopCharge,
                        'total_price' => $totalPrice,
                        'courier_fee' => $courierFee,
                        'admin_fee' => $adminFee,
                        'profit_margin' => $profitMargin,
                        'has_stat_urgent' => $request->priority_level == 'stat',
                        'has_night_service' => $nightHoursCharge > 0,
                        'has_weekend_service' => $weekendCharge > 0,
                        'has_cold_chain' => $coldChainCharge > 0,
                        'additional_stops' => $request->stops->count(),
                        'is_price_quoted' => true,
                    ]
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', "Price calculated successfully! Total: $" . number_format($totalPrice, 2));
        } catch (\Exception $e) {
            // Handle AJAX error response
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error calculating price: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('error', 'Error calculating price: ' . $e->getMessage());
        }
    }

    private function calculateDistance(SpecimenRequest $request): float
    {
        // This is a placeholder - implement actual Google Maps distance calculation
        // For now, return a static value or calculate using coordinates
        if (
            $request->pickup_latitude && $request->pickup_longitude &&
            $request->delivery_latitude && $request->delivery_longitude
        ) {
            // Simple distance calculation using Haversine formula
            $earthRadius = 3959; // miles

            $latFrom = deg2rad($request->pickup_latitude);
            $lonFrom = deg2rad($request->pickup_longitude);
            $latTo = deg2rad($request->delivery_latitude);
            $lonTo = deg2rad($request->delivery_longitude);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

            return $angle * $earthRadius;
        }

        // If no coordinates, estimate based on address or use default
        return 10.00; // Default 10 miles for calculation
    }

    public function createQuote(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id' => 'required|exists:users,id',
            'courier_fee' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'valid_hours' => 'nullable|integer|min:1|max:72',
        ]);

        try {
            // Calculate pricing if not already calculated
            if (!$request->is_price_quoted) {
                // Calculate pricing directly without redirect
                $distanceMiles = $this->calculateDistance($request);
                $basePrice = 50.00;
                $distanceCharge = $distanceMiles > 15 ? ($distanceMiles - 15) * 2.00 : 0.00;
                $statUrgentCharge = $request->priority_level == 'stat' ? 20.00 : 0.00;

                $pickupTime = $request->scheduled_pickup_time;
                $nightHoursCharge = ($pickupTime && $pickupTime->hour >= 18) ? 25.00 : 0.00;
                $weekendCharge = ($pickupTime && in_array($pickupTime->dayOfWeek, [0, 6])) ? $basePrice * 0.35 : 0.00;
                $coldChainCharge = in_array($request->temperature_requirement, ['cold', 'frozen']) ? 7.00 : 0.00;
                $additionalStopCharge = $request->stops->count() * 10.00;

                $totalPrice = $basePrice + $distanceCharge + $statUrgentCharge + $nightHoursCharge +
                    $weekendCharge + $coldChainCharge + $additionalStopCharge;
                $courierFee = $totalPrice * 0.70;
                $adminFee = $totalPrice * 0.20;
                $profitMargin = $totalPrice * 0.10;

                $request->update([
                    'base_price' => $basePrice,
                    'distance_miles' => $distanceMiles,
                    'distance_charge' => $distanceCharge,
                    'stat_urgent_charge' => $statUrgentCharge,
                    'night_hours_charge' => $nightHoursCharge,
                    'weekend_charge' => $weekendCharge,
                    'cold_chain_charge' => $coldChainCharge,
                    'additional_stop_charge' => $additionalStopCharge,
                    'total_price' => $totalPrice,
                    'courier_fee' => $courierFee,
                    'admin_fee' => $adminFee,
                    'profit_margin' => $profitMargin,
                    'has_stat_urgent' => $request->priority_level == 'stat',
                    'has_night_service' => $nightHoursCharge > 0,
                    'has_weekend_service' => $weekendCharge > 0,
                    'has_cold_chain' => $coldChainCharge > 0,
                    'additional_stops' => $request->stops->count(),
                    'is_price_quoted' => true,
                ]);

                // Refresh the request to get updated values
                $request->refresh();
            }

            // Create quote
            $quote = CourierQuote::create([
                'request_id' => $request->id,
                'courier_id' => $validated['courier_id'],
                'courier_fee' => $validated['courier_fee'],
                'total_price' => $validated['total_price'],
                'breakdown' => [
                    'base_price' => $request->base_price,
                    'distance_charge' => $request->distance_charge,
                    'stat_urgent_charge' => $request->stat_urgent_charge,
                    'night_hours_charge' => $request->night_hours_charge,
                    'weekend_charge' => $request->weekend_charge,
                    'cold_chain_charge' => $request->cold_chain_charge,
                    'additional_stop_charge' => $request->additional_stop_charge,
                    'admin_fee' => $request->admin_fee,
                    'profit_margin' => $request->profit_margin,
                ],
                'valid_until' => now()->addHours($validated['valid_hours'] ?? 24),
            ]);

            // Update request with quote reference
            $request->update([
                'courier_quote_id' => $quote->id,
                'courier_can_accept' => true,
                'acceptance_deadline' => $quote->valid_until,
            ]);

            // Create notification for courier using the trait
            $this->notifyCourier(
                $validated['courier_id'],
                'quote_received',
                'New Price Quote Received',
                "You have received a price quote for request #{$request->request_number}",
                $request->id,
                [
                    'request_id' => $request->id,
                    'request_number' => $request->request_number,
                    'quote_id' => $quote->id,
                    'courier_fee' => $quote->courier_fee,
                    'total_price' => $quote->total_price,
                    'valid_until' => $quote->valid_until->toDateTimeString(),
                    'created_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
                ]
            );

            // Notify admins about the quote creation
            $this->notifyAdmins(
                'quote_created',
                'New Quote Created',
                "A new price quote has been created for request #{$request->request_number}",
                $request->id,
                [
                    'request_id' => $request->id,
                    'request_number' => $request->request_number,
                    'quote_id' => $quote->id,
                    'courier_id' => $validated['courier_id'],
                    'total_price' => $quote->total_price,
                    'created_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
                ]
            );

            // Handle AJAX response
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Price quote sent to courier successfully.',
                    'data' => [
                        'quote_id' => $quote->id,
                        'courier_fee' => $quote->courier_fee,
                        'total_price' => $quote->total_price,
                        'valid_until' => $quote->valid_until->format('Y-m-d H:i:s'),
                    ]
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', 'Price quote sent to courier successfully.');
        } catch (\Exception $e) {
            // Handle AJAX error response
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending quote: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('error', 'Error sending quote: ' . $e->getMessage());
        }
    }

    public function assignWithQuote(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id' => 'required|exists:users,id',
            'valid_hours' => 'nullable|integer|min:1|max:72',
        ]);

        try {
            // Calculate pricing if not already calculated
            if (!$request->is_price_quoted) {
                // Calculate pricing directly
                $distanceMiles = $this->calculateDistance($request);
                $basePrice = 50.00;
                $distanceCharge = $distanceMiles > 15 ? ($distanceMiles - 15) * 2.00 : 0.00;
                $statUrgentCharge = $request->priority_level == 'stat' ? 20.00 : 0.00;

                $pickupTime = $request->scheduled_pickup_time;
                $nightHoursCharge = ($pickupTime && $pickupTime->hour >= 18) ? 25.00 : 0.00;
                $weekendCharge = ($pickupTime && in_array($pickupTime->dayOfWeek, [0, 6])) ? $basePrice * 0.35 : 0.00;
                $coldChainCharge = in_array($request->temperature_requirement, ['cold', 'frozen']) ? 7.00 : 0.00;
                $additionalStopCharge = $request->stops->count() * 10.00;

                $totalPrice = $basePrice + $distanceCharge + $statUrgentCharge + $nightHoursCharge +
                    $weekendCharge + $coldChainCharge + $additionalStopCharge;
                $courierFee = $totalPrice * 0.70;
                $adminFee = $totalPrice * 0.20;
                $profitMargin = $totalPrice * 0.10;

                $request->update([
                    'base_price' => $basePrice,
                    'distance_miles' => $distanceMiles,
                    'distance_charge' => $distanceCharge,
                    'stat_urgent_charge' => $statUrgentCharge,
                    'night_hours_charge' => $nightHoursCharge,
                    'weekend_charge' => $weekendCharge,
                    'cold_chain_charge' => $coldChainCharge,
                    'additional_stop_charge' => $additionalStopCharge,
                    'total_price' => $totalPrice,
                    'courier_fee' => $courierFee,
                    'admin_fee' => $adminFee,
                    'profit_margin' => $profitMargin,
                    'has_stat_urgent' => $request->priority_level == 'stat',
                    'has_night_service' => $nightHoursCharge > 0,
                    'has_weekend_service' => $weekendCharge > 0,
                    'has_cold_chain' => $coldChainCharge > 0,
                    'additional_stops' => $request->stops->count(),
                    'is_price_quoted' => true,
                ]);

                // Refresh the request to get updated values
                $request->refresh();
            }

            $courierFee = $request->courier_fee;
            $totalPrice = $request->total_price;

            // Create quote
            $quote = CourierQuote::create([
                'request_id' => $request->id,
                'courier_id' => $validated['courier_id'],
                'courier_fee' => $courierFee,
                'total_price' => $totalPrice,
                'breakdown' => [
                    'base_price' => $request->base_price,
                    'distance_charge' => $request->distance_charge,
                    'stat_urgent_charge' => $request->stat_urgent_charge,
                    'night_hours_charge' => $request->night_hours_charge,
                    'weekend_charge' => $request->weekend_charge,
                    'cold_chain_charge' => $request->cold_chain_charge,
                    'additional_stop_charge' => $request->additional_stop_charge,
                    'admin_fee' => $request->admin_fee,
                    'profit_margin' => $request->profit_margin,
                ],
                'valid_until' => now()->addHours($validated['valid_hours'] ?? 24),
            ]);

            // Update request
            $request->update([
                'assigned_to' => $validated['courier_id'],
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'courier_quote_id' => $quote->id,
                'courier_can_accept' => true,
                'acceptance_deadline' => $quote->valid_until,
                'status' => 'pending_courier_acceptance', // New status
            ]);

            // Create notification for courier using the trait
            $this->notifyCourier(
                $validated['courier_id'],
                'request_assigned_with_quote',
                'New Assignment with Price Quote',
                "You have been assigned to request #{$request->request_number} with a price quote",
                $request->id,
                [
                    'request_id' => $request->id,
                    'request_number' => $request->request_number,
                    'quote_id' => $quote->id,
                    'courier_fee' => $quote->courier_fee,
                    'total_price' => $quote->total_price,
                    'deadline' => $quote->valid_until->format('Y-m-d H:i:s'),
                    'assigned_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
                ]
            );

            // Notify client about the assignment (if client exists)
            if ($request->client_id) {
                $this->notifyClient(
                    $request->client_id,
                    'request_assigned',
                    'Request Assigned to Courier',
                    "Your request #{$request->request_number} has been assigned to a courier and is awaiting acceptance",
                    $request->id,
                    [
                        'request_id' => $request->id,
                        'request_number' => $request->request_number,
                        'status' => 'pending_courier_acceptance',
                        'assigned_at' => now()->toDateTimeString()
                    ]
                );
            }

            // Notify admins about the assignment with quote
            $this->notifyAdmins(
                'request_assigned_with_quote',
                'Request Assigned with Quote',
                "Request #{$request->request_number} has been assigned to a courier with a price quote",
                $request->id,
                [
                    'request_id' => $request->id,
                    'request_number' => $request->request_number,
                    'courier_id' => $validated['courier_id'],
                    'quote_id' => $quote->id,
                    'total_price' => $quote->total_price,
                    'assigned_by' => auth()->user()->full_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name
                ]
            );

            // Handle AJAX response
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Courier assigned with price quote. Waiting for acceptance.',
                    'data' => [
                        'quote_id' => $quote->id,
                        'courier_fee' => $quote->courier_fee,
                        'total_price' => $quote->total_price,
                        'valid_until' => $quote->valid_until->format('Y-m-d H:i:s'),
                        'status' => 'pending_courier_acceptance'
                    ]
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', 'Courier assigned with price quote. Waiting for acceptance.');
        } catch (\Exception $e) {
            // Handle AJAX error response
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error assigning courier: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('error', 'Error assigning courier: ' . $e->getMessage());
        }
    }

    /**
     * Get calculated price data (for AJAX updates without calculation)
     */
    public function getPriceData(Request $httpRequest, SpecimenRequest $request)
    {
        try {
            $data = [
                'base_price' => $request->base_price ?? 0,
                'distance_miles' => $request->distance_miles ?? 0,
                'distance_charge' => $request->distance_charge ?? 0,
                'stat_urgent_charge' => $request->stat_urgent_charge ?? 0,
                'night_hours_charge' => $request->night_hours_charge ?? 0,
                'weekend_charge' => $request->weekend_charge ?? 0,
                'cold_chain_charge' => $request->cold_chain_charge ?? 0,
                'additional_stop_charge' => $request->additional_stop_charge ?? 0,
                'total_price' => $request->total_price ?? 0,
                'courier_fee' => $request->courier_fee ?? 0,
                'admin_fee' => $request->admin_fee ?? 0,
                'profit_margin' => $request->profit_margin ?? 0,
                'has_stat_urgent' => $request->has_stat_urgent ?? false,
                'has_night_service' => $request->has_night_service ?? false,
                'has_weekend_service' => $request->has_weekend_service ?? false,
                'has_cold_chain' => $request->has_cold_chain ?? false,
                'additional_stops' => $request->additional_stops ?? 0,
                'is_price_quoted' => $request->is_price_quoted ?? false,
            ];

            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return $data;
        } catch (\Exception $e) {
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching price data: ' . $e->getMessage()
                ], 500);
            }

            throw $e;
        }
    }
}
