<?php
// app/Http/Controllers/Client/ClientController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Facility;
use App\Models\Notification;
use App\Models\PickupProof;
use App\Models\RequestDocument;
use App\Models\Signature;
use App\Models\SystemSetting;
use App\Models\CourierLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Services\PaymentService;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:client');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();

        $stats = [
            'total_requests' => $user->createdRequests()->count(),
            'pending_requests' => $user->createdRequests()->where('status', 'pending_approval')->count(),
            'in_progress' => $user->createdRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])->count(),
            'completed' => $user->createdRequests()->where('status', 'completed')->count(),
        ];

        $recentRequests = $user->createdRequests()
            ->with(['courier', 'facility'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('client.dashboard', compact('stats', 'recentRequests', 'facility'));
    }

    public function requests()
    {
        $status = request('status');
        $user = Auth::user();

        $query = $user->createdRequests()->with(['courier', 'facility']);

        if ($status) {
            if ($status === 'in_transit') {
                $query->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery']);
            } else {
                $query->where('status', $status);
            }
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('client.requests.index', compact('requests', 'status'));
    }

    public function createRequest()
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();

        return view('client.requests.create', compact('facility'));
    }

    /**
     * Calculate price estimate for client request using Google Maps Distance Matrix API
     */
    public function calculateRequestPrice(Request $httpRequest)
    {
        try {
            $validated = $httpRequest->validate([
                'pickup_address' => 'required|string',
                'delivery_address' => 'required|string',
                'pickup_date' => 'required|date',
                'pickup_time' => 'required|string',
                'priority_level' => 'required|string',
                'specimen_type' => 'required|string',
                'temperature_requirement' => 'required|string',
                'stops' => 'nullable|array',
                'stops.*.type' => 'nullable|string',
                'stops.*.address' => 'nullable|string',
            ]);

            // Calculate distance using Google Maps Distance Matrix API
            $distanceMiles = $this->calculateDistanceWithGoogleMaps(
                $validated['pickup_address'],
                $validated['delivery_address']
            );

            // Base price
            $basePrice = 50.00;

            // Distance charge (beyond 15 miles)
            $distanceCharge = 0.00;
            if ($distanceMiles > 15) {
                $distanceCharge = ($distanceMiles - 15) * 2.00;
            }

            // Priority charge (STAT/Urgent)
            $priorityCharge = 0.00;
            if (strtolower($validated['priority_level']) == 'stat') {
                $priorityCharge = 20.00;
            }

            // Parse pickup time for date/time calculations
            $pickupDateTime = $this->parsePickupDateTime(
                $validated['pickup_date'],
                $validated['pickup_time']
            );

            // Night service charge (after 6PM)
            $nightCharge = 0.00;
            if ($pickupDateTime->hour >= 18) { // 6PM or later
                $nightCharge = 25.00;
            }

            // Weekend/Holiday charge
            $weekendCharge = 0.00;
            if ($this->isWeekendOrHoliday($pickupDateTime)) {
                $weekendCharge = $basePrice * 0.35; // 35% of base rate
            }

            // Temperature requirement charge (Cold-Chain Handling)
            $temperatureCharge = 0.00;
            if (in_array(strtolower($validated['temperature_requirement']), ['2-8c', '-20c', '-80c', 'cold_chain', 'refrigerated'])) {
                $temperatureCharge = 7.00;
            }

            // Additional stops charge
            $additionalStops = isset($validated['stops']) ? count($validated['stops']) : 0;
            $additionalStopsCharge = $additionalStops * 10.00;

            // Calculate subtotal and tax
            $taxRate = 0.085; // 8.5% tax
            $subtotal = $basePrice + $distanceCharge + $priorityCharge + $nightCharge +
                $weekendCharge + $temperatureCharge + $additionalStopsCharge;
            $taxAmount = $subtotal * $taxRate;
            $totalPrice = $subtotal + $taxAmount;

            // Detailed breakdown for display
            $priceBreakdown = [
                'base_price' => number_format($basePrice, 2),
                'distance_miles' => round($distanceMiles, 1),
                'distance_charge' => number_format($distanceCharge, 2),
                'distance_calculation_note' => 'One-way distance calculated via Google Maps',
                'priority_charge' => number_format($priorityCharge, 2),
                'priority_note' => $priorityCharge > 0 ? 'STAT / Urgent Delivery Surcharge' : 'Standard Priority',
                'night_charge' => number_format($nightCharge, 2),
                'night_note' => $nightCharge > 0 ? 'Night After-Hours Service (After 6PM)' : 'Daytime Service',
                'weekend_charge' => number_format($weekendCharge, 2),
                'weekend_note' => $weekendCharge > 0 ? 'Weekend/Holiday Surcharge (35% of base rate)' : 'Weekday Service',
                'temperature_charge' => number_format($temperatureCharge, 2),
                'temperature_note' => $temperatureCharge > 0 ? 'Cold-Chain Handling' : 'No Temperature Control Required',
                'additional_stops' => $additionalStops,
                'additional_stops_charge' => number_format($additionalStopsCharge, 2),
                'additional_stops_note' => $additionalStops > 0 ? "{$additionalStops} additional stop(s) @ $10.00 each" : 'No additional stops',
                'subtotal' => number_format($subtotal, 2),
                'tax_rate' => $taxRate * 100,
                'tax_amount' => number_format($taxAmount, 2),
                'total_price' => number_format($totalPrice, 2),
                'estimated_total' => number_format($totalPrice, 2),
                'currency' => 'USD',
                'calculation_time' => now()->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $priceBreakdown
            ]);
        } catch (\Exception $e) {
            Log::error('Price calculation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error calculating price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance using Google Maps Distance Matrix API
     */
    private function calculateDistanceWithGoogleMaps($origin, $destination)
    {
        $cacheKey = 'distance_' . md5($origin . $destination);

        // Check cache first (cache for 30 days since distances don't change often)
        $cachedDistance = Cache::get($cacheKey);
        if ($cachedDistance !== null) {
            return $cachedDistance;
        }

        try {
            $apiKey = config('services.google.maps_api_key');

            if (empty($apiKey)) {
                throw new \Exception('Google Maps API key not configured');
            }

            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => $apiKey,
                'units' => 'imperial', // Get distance in miles
                'mode' => 'driving', // One-way driving distance
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (
                    $data['status'] === 'OK' &&
                    isset($data['rows'][0]['elements'][0]['distance']['value'])
                ) {

                    // Convert meters to miles
                    $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                    $distanceMiles = $distanceMeters / 1609.344;

                    // Cache the result for 30 days
                    Cache::put($cacheKey, $distanceMiles, 2592000);

                    return $distanceMiles;
                } else {
                    // Log the error response
                    $errorMessage = $data['error_message'] ?? 'Unknown Google Maps API error';
                    Log::warning('Google Maps Distance API error: ' . $errorMessage . ' - Status: ' . $data['status']);
                }
            } else {
                Log::warning('Google Maps API request failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Google Maps API exception: ' . $e->getMessage());
        }

        // Fallback: Use simple estimation or geocoding-based calculation
        return $this->calculateFallbackDistance($origin, $destination);
    }

    /**
     * Fallback distance calculation when Google Maps API fails
     */
    private function calculateFallbackDistance($origin, $destination)
    {
        try {
            // Try to get coordinates for both addresses
            $originCoords = $this->geocodeAddress($origin);
            $destCoords = $this->geocodeAddress($destination);

            if (
                $originCoords['latitude'] && $originCoords['longitude'] &&
                $destCoords['latitude'] && $destCoords['longitude']
            ) {

                // Calculate straight-line distance (less accurate than road distance)
                $distanceKm = $this->calculateHaversineDistance(
                    $originCoords['latitude'],
                    $originCoords['longitude'],
                    $destCoords['latitude'],
                    $destCoords['longitude']
                );

                // Convert km to miles
                $distanceMiles = $distanceKm * 0.621371;

                // Add 20% for road distance estimation
                $estimatedRoadDistance = $distanceMiles * 1.2;

                Log::info('Used fallback distance calculation: ' . $estimatedRoadDistance . ' miles');

                return $estimatedRoadDistance;
            }
        } catch (\Exception $e) {
            Log::warning('Fallback distance calculation failed: ' . $e->getMessage());
        }

        // Ultimate fallback: return average distance of 10 miles
        return 10.00;
    }

    /**
     * Calculate distance using Haversine formula (straight line)
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // kilometers

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Check if date is weekend or holiday
     */
    private function isWeekendOrHoliday(Carbon $date)
    {
        // Check for weekend (Saturday = 6, Sunday = 0)
        if (in_array($date->dayOfWeek, [0, 6])) {
            return true;
        }

        // Check for holidays (add your specific holidays here)
        $holidays = $this->getHolidays($date->year);

        $dateString = $date->format('Y-m-d');
        return in_array($dateString, $holidays);
    }

    /**
     * Get list of holidays for a given year
     */
    private function getHolidays($year)
    {
        $cacheKey = 'holidays_' . $year;

        return Cache::remember($cacheKey, 86400, function () use ($year) {
            $holidays = [];

            // Fixed date holidays
            $fixedHolidays = [
                $year . '-01-01', // New Year's Day
                $year . '-07-04', // Independence Day
                $year . '-12-25', // Christmas Day
            ];

            // Floating holidays (examples - adjust as needed)
            // Memorial Day (last Monday in May)
            $memorialDay = new Carbon("last monday of May $year");
            $holidays[] = $memorialDay->format('Y-m-d');

            // Labor Day (first Monday in September)
            $laborDay = new Carbon("first monday of September $year");
            $holidays[] = $laborDay->format('Y-m-d');

            // Thanksgiving (fourth Thursday in November)
            $thanksgiving = new Carbon("fourth thursday of November $year");
            $holidays[] = $thanksgiving->format('Y-m-d');

            return array_merge($fixedHolidays, $holidays);
        });
    }

    /**
     * Parse pickup date and time into Carbon instance
     */
    private function parsePickupDateTime($date, $timeWindow)
    {
        $timeMap = [
            '8-10' => '09:00:00',
            '10-12' => '11:00:00',
            '12-14' => '13:00:00',
            '14-16' => '15:00:00',
            '16-18' => '17:00:00',
            '18-20' => '19:00:00',
            '20-22' => '21:00:00',
            'stat' => now()->format('H:i:s'),
            'asap' => now()->addMinutes(30)->format('H:i:s'),
        ];

        $time = $timeMap[$timeWindow] ?? '12:00:00';

        return Carbon::parse($date . ' ' . $time);
    }

    /**
     * Preview request with pricing
     */
    public function previewRequest(Request $request)
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();

        // Validate the form data
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:200',
            'contact_phone' => 'required|string|max:20',
            'pickup_address' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'delivery_address' => 'required|string',
            'delivery_instructions' => 'nullable|string',
            'specimen_type' => 'required|string',
            'temperature_requirement' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'priority_level' => 'required|string',
            'special_instructions' => 'nullable|string',
            'stops' => 'nullable|array',
            'stops.*.type' => 'nullable|string',
            'stops.*.contact_name' => 'nullable|string',
            'stops.*.address' => 'nullable|string',
            'stops.*.instructions' => 'nullable|string',
        ]);

        // Calculate price
        $priceResult = $this->calculateRequestPrice(new Request($validated));

        if ($priceResult->getStatusCode() !== 200) {
            return back()->withInput()->withErrors(['price_calculation' => 'Error calculating price. Please try again.']);
        }

        $priceData = json_decode($priceResult->getContent(), true);

        if (!$priceData['success']) {
            return back()->withInput()->withErrors(['price_calculation' => $priceData['message']]);
        }

        $priceBreakdown = $priceData['data'];

        // Store form data in session for final submission
        $request->session()->put('request_preview_data', [
            'form_data' => $validated,
            'price_data' => $priceBreakdown
        ]);

        return view('client.requests.preview', compact('validated', 'priceBreakdown', 'facility'));
    }

    /**
     * Store the request after preview
     */
    public function storeRequest(Request $request)
    {
        // Get data from session if available (from preview)
        $previewData = $request->session()->get('request_preview_data');

        if ($previewData) {
            $validated = $previewData['form_data'];
            $priceData = $previewData['price_data'];

            // Clear the session data
            $request->session()->forget('request_preview_data');
        } else {
            // Original validation for direct submission (fallback)
            $validated = $request->validate([
                'recipient_name' => 'required|string|max:200',
                'contact_phone' => 'required|string|max:20',
                'pickup_address' => 'required|string',
                'pickup_date' => 'required|date',
                'pickup_time' => 'required|string',
                'delivery_address' => 'required|string',
                'delivery_instructions' => 'nullable|string',
                'specimen_type' => 'required|string',
                'temperature_requirement' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'priority_level' => 'required|string',
                'special_instructions' => 'nullable|string',
                'stops' => 'nullable|array',
                'stops.*.type' => 'required|string',
                'stops.*.contact_name' => 'nullable|string',
                'stops.*.address' => 'required|string',
                'stops.*.instructions' => 'nullable|string',
                'documents' => 'nullable|array',
                'documents.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            // Calculate price for direct submission
            $priceResult = $this->calculateRequestPrice(new Request($validated));
            if ($priceResult->getStatusCode() === 200) {
                $priceData = json_decode($priceResult->getContent(), true);
                if ($priceData['success']) {
                    $priceData = $priceData['data'];
                } else {
                    $priceData = null;
                }
            } else {
                $priceData = null;
            }
        }

        $user = Auth::user();
        $facility = $user->facilities()->first();

        // Geocode addresses to get coordinates
        $pickupCoords = $this->geocodeAddress($validated['pickup_address']);
        $deliveryCoords = $this->geocodeAddress($validated['delivery_address']);

        // Parse scheduled pickup time
        $scheduledPickup = $this->parsePickupDateTime(
            $validated['pickup_date'],
            $validated['pickup_time']
        );

        // Create specimen request with coordinates
        $specimenRequest = SpecimenRequest::create([
            'facility_id' => $facility ? $facility->id : null,
            'client_id' => $user->id,
            'recipient_name' => $validated['recipient_name'],
            'pickup_address' => $validated['pickup_address'],
            'pickup_latitude' => $pickupCoords['latitude'] ?? null,
            'pickup_longitude' => $pickupCoords['longitude'] ?? null,
            'delivery_address' => $validated['delivery_address'],
            'delivery_latitude' => $deliveryCoords['latitude'] ?? null,
            'delivery_longitude' => $deliveryCoords['longitude'] ?? null,
            'delivery_instructions' => $validated['delivery_instructions'],
            'specimen_type' => $validated['specimen_type'],
            'temperature_requirement' => $validated['temperature_requirement'],
            'quantity' => $validated['quantity'],
            'priority_level' => $validated['priority_level'],
            'special_instructions' => $validated['special_instructions'],
            'scheduled_pickup_time' => $scheduledPickup,
            'status' => 'pending_approval',
            'payment_status' => 'pending',
            'payment_required' => true,
            'estimated_price' => $priceData ? $priceData['estimated_total'] : null,
            'price_breakdown' => $priceData ? json_encode($priceData) : null,
            'is_price_estimated' => $priceData ? true : false,
            'distance_miles' => $priceData ? $priceData['distance_miles'] : null,
            'additional_stops' => $priceData ? $priceData['additional_stops'] : 0,
        ]);

        // Add additional stops with geocoding
        if (!empty($validated['stops'])) {
            $stopOrder = 1;
            foreach ($validated['stops'] as $stop) {
                $stopCoords = $this->geocodeAddress($stop['address']);
                $specimenRequest->stops()->create([
                    'stop_type' => $stop['type'],
                    'stop_order' => $stopOrder++,
                    'contact_name' => $stop['contact_name'],
                    'address' => $stop['address'],
                    'instructions' => $stop['instructions'],
                    'latitude' => $stopCoords['latitude'] ?? null,
                    'longitude' => $stopCoords['longitude'] ?? null,
                ]);
            }
        }

        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('request_documents', 'public');

                $specimenRequest->documents()->create([
                    'document_type' => 'other',
                    'file_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $document->getSize(),
                    'mime_type' => $document->getMimeType(),
                    'uploaded_by' => $user->id,
                ]);
            }
        }

        // Create initial payment record
        if ($specimenRequest->estimated_price > 0) {
            $paymentService = new PaymentService();
            $paymentService->createPayment($specimenRequest, $user);
        }

        // CREATE NOTIFICATION FOR ADMINS
        $adminUsers = \App\Models\User::whereHas('role', function ($query) {
            $query->where('slug', 'admin');
        })->get();

        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'request_id' => $specimenRequest->id,
                'type' => 'new_request',
                'title' => 'New Specimen Request',
                'message' => "New specimen request submitted by {$user->first_name} {$user->last_name}. Request ID: {$specimenRequest->request_number}",
                'data' => json_encode(['request_id' => $specimenRequest->id, 'client_id' => $user->id]),
            ]);
        }

        // Send notification to client about payment
        Notification::create([
            'user_id' => $user->id,
            'request_id' => $specimenRequest->id,
            'type' => 'payment_required',
            'title' => 'Payment Required',
            'message' => "Payment is required for your request #{$specimenRequest->request_number}. Please complete payment to schedule pickup.",
            'data' => json_encode(['request_id' => $specimenRequest->id]),
        ]);

        return redirect()->route('client.requests.index')
            ->with('success', 'Specimen request submitted successfully! It is now pending approval. Please complete payment to schedule pickup.');
    }

    /**
     * Geocode address to get coordinates
     */
    private function geocodeAddress($address)
    {
        if (empty($address)) {
            return ['latitude' => null, 'longitude' => null];
        }

        // Cache geocoding results for 30 days
        $cacheKey = 'geocode_' . md5($address);
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult) {
            return $cachedResult;
        }

        try {
            // Try Google Geocoding API first
            $apiKey = config('services.google.maps_api_key');
            if (!empty($apiKey)) {
                $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address,
                    'key' => $apiKey,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && isset($data['results'][0]['geometry']['location'])) {
                        $location = $data['results'][0]['geometry']['location'];
                        $result = [
                            'latitude' => (float) $location['lat'],
                            'longitude' => (float) $location['lng'],
                            'display_name' => $data['results'][0]['formatted_address'] ?? $address,
                        ];

                        Cache::put($cacheKey, $result, 2592000);
                        return $result;
                    }
                }
            }

            // Fallback to OpenStreetMap Nominatim
            $response = Http::withHeaders([
                'User-Agent' => config('app.name') . '/1.0',
                'Accept' => 'application/json',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $address,
                'limit' => 1,
                'addressdetails' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    $result = [
                        'latitude' => (float) $data[0]['lat'],
                        'longitude' => (float) $data[0]['lon'],
                        'display_name' => $data[0]['display_name'] ?? $address,
                    ];

                    Cache::put($cacheKey, $result, 2592000);
                    return $result;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding failed for address: ' . $address . ' - ' . $e->getMessage());
        }

        return ['latitude' => null, 'longitude' => null];
    }

    /**
     * Track individual request
     */
    public function trackRequest(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops', 'documents', 'pickupProofs', 'signatures', 'payment']);

        return view('client.requests.track', compact('request'));
    }

    public function showRequest(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops', 'documents', 'pickupProofs', 'signatures', 'payment']);

        return view('client.requests.show', compact('request'));
    }

    public function cancelRequest(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->client_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($specimenRequest->status, ['pending_approval', 'approved'])) {
            return back()->with('error', 'Request cannot be cancelled at this stage.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ]);

        // Check if payment was made and needs refund
        if ($specimenRequest->payment && $specimenRequest->payment->isPaid()) {
            // Initiate refund process
            $paymentService = new PaymentService();
            $refundResult = $paymentService->refundPayment(
                $specimenRequest->payment,
                null, // full refund
                "Request cancelled: " . $validated['cancellation_reason']
            );

            if (!$refundResult['success']) {
                return back()->with('error', 'Cancellation failed: Could not process refund. Please contact support.');
            }
        }

        $specimenRequest->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        // Create notification
        Notification::create([
            'type' => 'request_cancelled',
            'title' => 'Request Cancelled',
            'message' => "Request {$specimenRequest->request_number} has been cancelled by the client.",
            'data' => json_encode(['request_id' => $specimenRequest->id]),
            'user_role' => 'admin',
        ]);

        return redirect()->route('client.requests.index')
            ->with('success', 'Request cancelled successfully. ' .
                ($specimenRequest->payment && $specimenRequest->payment->isPaid() ?
                    'Refund has been initiated.' : ''));
    }

    public function confirmDelivery(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'delivered') {
            return back()->with('error', 'Request must be in delivered status to confirm receipt.');
        }

        return view('client.requests.confirm', compact('request'));
    }

    public function submitConfirmation(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'signature' => 'required|string',
            'recipient_name' => 'required|string|max:200',
        ]);

        // Save signature
        $signature = Signature::create([
            'request_id' => $specimenRequest->id,
            'signature_type' => 'delivery',
            'signed_by' => Auth::id(),
            'recipient_name' => $validated['recipient_name'],
            'signature_data' => $validated['signature'],
            'ip_address' => $request->ip(),
            'device_info' => $request->header('User-Agent'),
        ]);

        // Update request status
        $specimenRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'type' => 'request_completed',
            'title' => 'Request Completed',
            'message' => "Request {$specimenRequest->request_number} has been completed and receipt confirmed.",
            'data' => json_encode(['request_id' => $specimenRequest->id]),
            'user_role' => 'admin',
        ]);

        return redirect()->route('client.requests.track', $specimenRequest)
            ->with('success', 'Delivery confirmed successfully! Request completed.');
    }

    public function tracking()
    {
        $user = Auth::user();
        $activeRequests = $user->createdRequests()
            ->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])
            ->with(['courier', 'stops'])
            ->get();

        return view('client.tracking.index', compact('activeRequests'));
    }

    public function getActiveTracking()
    {
        $user = Auth::user();
        $requests = $user->createdRequests()
            ->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])
            ->with(['courier', 'courier.currentLocation', 'stops'])
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'request_number' => $request->request_number,
                    'status' => $request->status,
                    'courier' => $request->courier ? [
                        'id' => $request->courier->id,
                        'name' => $request->courier->full_name,
                        'phone' => $request->courier->phone,
                        'location' => $request->courier->currentLocation,
                    ] : null,
                    'stops' => $request->stops->map(function ($stop) {
                        return [
                            'id' => $stop->id,
                            'type' => $stop->stop_type,
                            'address' => $stop->address,
                            'completed' => $stop->completed,
                        ];
                    }),
                    'pickup_address' => $request->pickup_address,
                    'delivery_address' => $request->delivery_address,
                ];
            });

        return response()->json($requests);
    }

    public function reports()
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();

        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $requests = $user->createdRequests()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['courier', 'facility', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $requests->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'in_progress' => $requests->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])->count(),
            'cancelled' => $requests->where('status', 'cancelled')->count(),
            'pending' => $requests->where('status', 'pending_approval')->count(),
        ];

        // Payment statistics
        $paymentStats = [
            'total_paid' => $requests->filter(function ($request) {
                return $request->payment && $request->payment->isPaid();
            })->count(),
            'total_pending' => $requests->filter(function ($request) {
                return $request->needsPayment();
            })->count(),
            'total_revenue' => $requests->sum(function ($request) {
                return $request->payment && $request->payment->isPaid() ? $request->payment->amount : 0;
            }),
        ];

        // Group by specimen type
        $specimenTypes = $requests->groupBy('specimen_type')->map->count();

        // Group by priority
        $priorities = $requests->groupBy('priority_level')->map->count();

        // Monthly trend
        $monthlyTrend = $requests->groupBy(function ($item) {
            return $item->created_at->format('Y-m');
        })->map->count();

        return view('client.reports.index', compact(
            'requests',
            'stats',
            'paymentStats',
            'specimenTypes',
            'priorities',
            'monthlyTrend',
            'startDate',
            'endDate',
            'facility'
        ));
    }

    public function downloadReport(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,csv',
        ]);

        $requests = $user->createdRequests()
            ->whereBetween('created_at', [$validated['start_date'] . ' 00:00:00', $validated['end_date'] . ' 23:59:59'])
            ->with(['courier', 'facility', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($validated['format'] === 'csv') {
            return $this->generateCSV($requests, $validated);
        }

        return $this->generatePDF($requests, $validated);
    }

    private function generateCSV($requests, $params)
    {
        $filename = "specimen_requests_{$params['start_date']}_to_{$params['end_date']}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($requests) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Request Number',
                'Date',
                'Specimen Type',
                'Priority',
                'Status',
                'Payment Status',
                'Amount',
                'Payment Method',
                'Pickup Address',
                'Delivery Address',
                'Distance (miles)',
                'Estimated Price',
                'Courier',
                'Created At',
                'Completed At',
            ]);

            // Data
            foreach ($requests as $request) {
                $priceBreakdown = $request->price_breakdown ? json_decode($request->price_breakdown, true) : null;

                $paymentStatus = 'N/A';
                $amount = 'N/A';
                $paymentMethod = 'N/A';

                if ($request->payment) {
                    $paymentStatus = $request->payment->payment_status;
                    $amount = $request->payment->isPaid() ? '$' . number_format($request->payment->amount, 2) : '$0.00';
                    $paymentMethod = $request->payment->payment_method ?? 'N/A';
                }

                fputcsv($file, [
                    $request->request_number,
                    $request->created_at->format('Y-m-d'),
                    ucfirst($request->specimen_type),
                    ucfirst($request->priority_level),
                    str_replace('_', ' ', $request->status),
                    $paymentStatus,
                    $amount,
                    $paymentMethod,
                    $request->pickup_address,
                    $request->delivery_address,
                    $priceBreakdown['distance_miles'] ?? $request->distance_miles ?? 'N/A',
                    $request->estimated_price ? '$' . number_format($request->estimated_price, 2) : 'N/A',
                    $request->courier ? $request->courier->full_name : 'Not Assigned',
                    $request->created_at->format('Y-m-d H:i:s'),
                    $request->completed_at ? $request->completed_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generatePDF($requests, $params)
    {
        return response()->json([
            'message' => 'PDF generation requires additional setup. Please install dompdf package.',
            'data' => [
                'count' => $requests->count(),
                'period' => "{$params['start_date']} to {$params['end_date']}",
            ]
        ]);
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('client.notifications.index', compact('notifications'));
    }

    public function markNotificationAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function profile()
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();

        return view('client.profile.index', compact('user', 'facility'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'current_password' => 'required_with:new_password|current_password',
            'new_password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
        ];

        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $updateData['profile_image'] = $path;
        }

        if (!empty($validated['new_password'])) {
            $updateData['password'] = bcrypt($validated['new_password']);
        }

        $user->update($updateData);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function documents(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $documents = $request->documents()->orderBy('created_at', 'desc')->get();

        return view('client.requests.documents', compact('request', 'documents'));
    }

    public function downloadDocument(RequestDocument $document)
    {
        if ($document->request->client_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function proofs(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $proofs = $request->pickupProofs()->orderBy('created_at', 'desc')->get();

        return view('client.requests.proofs', compact('request', 'proofs'));
    }

    /**
     * Reverse geocode coordinates to get address IN ENGLISH
     */
    private function reverseGeocode($latitude, $longitude)
    {
        // Return coordinates if invalid
        if (!$latitude || !$longitude) {
            return "Location not available";
        }

        // Try to get from cache first (cache for 1 hour)
        $cacheKey = 'reverse_geocode_eng_' . round($latitude, 6) . '_' . round($longitude, 6);
        $cachedAddress = Cache::get($cacheKey);

        if ($cachedAddress) {
            return $cachedAddress;
        }

        // Using OpenStreetMap Nominatim API - FORCE ENGLISH LANGUAGE
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name') . '/1.0',
                'Accept' => 'application/json',
                'Accept-Language' => 'en',
            ])->timeout(3)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
                'accept-language' => 'en',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['display_name'])) {
                    $address = $data['display_name'];

                    // Cache for 24 hours
                    Cache::put($cacheKey, $address, 86400);

                    return $address;
                }
            }
        } catch (\Exception $e) {
            Log::info('Reverse geocoding failed: ' . $e->getMessage());
        }

        // Fallback: Create a readable location from coordinates
        return $this->getReadableLocation($latitude, $longitude);
    }

    /**
     * Get readable location from coordinates (fallback method)
     */
    private function getReadableLocation($latitude, $longitude)
    {
        // Simple fallback - just format the coordinates nicely
        $latDir = $latitude >= 0 ? 'N' : 'S';
        $lngDir = $longitude >= 0 ? 'E' : 'W';

        $latAbs = abs($latitude);
        $lngAbs = abs($longitude);

        return sprintf(
            "Location: %.4f°%s, %.4f°%s",
            $latAbs,
            $latDir,
            $lngAbs,
            $lngDir
        );
    }

    /**
     * Get real-time courier location for a specific request - UPDATED
     */
    public function getCourierLocation(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        // Check if request is assigned to a courier
        if (!$request->courier) {
            return response()->json([
                'error' => 'No courier assigned to this request yet.',
                'courier' => null,
                'location' => null,
                'status' => 'offline',
            ]);
        }

        $courier = $request->courier;

        // Get location from cache first (real-time updates)
        $cachedLocation = Cache::get('courier_location_' . $courier->id);

        // If not in cache, check database
        if (!$cachedLocation && class_exists(CourierLocation::class)) {
            $location = CourierLocation::where('courier_id', $courier->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($location) {
                $cachedLocation = [
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
                    'speed' => $location->speed ? (float) $location->speed : null,
                    'heading' => $location->heading ? (float) $location->heading : null,
                    'altitude' => $location->altitude ? (float) $location->altitude : null,
                    'battery_level' => $location->battery_level,
                    'is_online' => (bool) $location->is_online,
                    'timestamp' => $location->created_at->timestamp,
                    'last_update' => $location->last_update ?? $location->created_at,
                ];
            }
        }

        // If still no location, return empty
        if (!$cachedLocation) {
            return response()->json([
                'courier' => [
                    'id' => $courier->id,
                    'name' => $courier->full_name,
                    'phone' => $courier->phone,
                    'vehicle_type' => $courier->vehicle_type,
                    'profile_image' => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
                ],
                'location' => null,
                'status' => 'offline',
                'message' => 'Courier location not available yet.',
            ]);
        }

        // Get formatted address from coordinates IN ENGLISH
        $formattedAddress = $this->reverseGeocode(
            $cachedLocation['latitude'] ?? null,
            $cachedLocation['longitude'] ?? null
        );

        // Calculate distance to pickup and delivery if coordinates are available
        $distanceToPickup = null;
        $distanceToDelivery = null;
        $etaToPickup = null;
        $etaToDelivery = null;

        if ($request->pickup_latitude && $request->pickup_longitude) {
            $distanceToPickup = $this->calculateDistance(
                $cachedLocation['latitude'],
                $cachedLocation['longitude'],
                $request->pickup_latitude,
                $request->pickup_longitude
            );
            $etaToPickup = $this->calculateETA($distanceToPickup, $cachedLocation['speed'] ?? 0);
        }

        if ($request->delivery_latitude && $request->delivery_longitude) {
            $distanceToDelivery = $this->calculateDistance(
                $cachedLocation['latitude'],
                $cachedLocation['longitude'],
                $request->delivery_latitude,
                $request->delivery_longitude
            );
            $etaToDelivery = $this->calculateETA($distanceToDelivery, $cachedLocation['speed'] ?? 0);
        }

        return response()->json([
            'courier' => [
                'id' => $courier->id,
                'name' => $courier->full_name,
                'phone' => $courier->phone,
                'vehicle_type' => $courier->vehicle_type,
                'profile_image' => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
                'last_seen' => isset($cachedLocation['last_update'])
                    ? Carbon::parse($cachedLocation['last_update'])->diffForHumans()
                    : (isset($cachedLocation['timestamp'])
                        ? Carbon::createFromTimestamp($cachedLocation['timestamp'])->diffForHumans()
                        : 'Just now'),
                'rating' => $courier->rating ?? 4.5,
            ],
            'location' => [
                'latitude' => (float) ($cachedLocation['latitude'] ?? 0),
                'longitude' => (float) ($cachedLocation['longitude'] ?? 0),
                'accuracy' => isset($cachedLocation['accuracy']) ? (float) $cachedLocation['accuracy'] : null,
                'speed' => isset($cachedLocation['speed']) ? (float) $cachedLocation['speed'] : 0,
                'heading' => isset($cachedLocation['heading']) ? (float) $cachedLocation['heading'] : 0,
                'altitude' => isset($cachedLocation['altitude']) ? (float) $cachedLocation['altitude'] : null,
                'timestamp' => $cachedLocation['timestamp'] ?? time(),
                'formatted_time' => isset($cachedLocation['timestamp'])
                    ? date('Y-m-d H:i:s', $cachedLocation['timestamp'])
                    : date('Y-m-d H:i:s'),
                'is_online' => (bool) ($cachedLocation['is_online'] ?? false),
                'formatted_address' => $formattedAddress,
                'battery_level' => $cachedLocation['battery_level'] ?? null,
                'coordinates' => [
                    'latitude' => (float) ($cachedLocation['latitude'] ?? 0),
                    'longitude' => (float) ($cachedLocation['longitude'] ?? 0),
                    'formatted' => sprintf(
                        '%.6f, %.6f',
                        (float) ($cachedLocation['latitude'] ?? 0),
                        (float) ($cachedLocation['longitude'] ?? 0)
                    ),
                ],
            ],
            'distances' => [
                'to_pickup_km' => $distanceToPickup ? round($distanceToPickup, 2) : null,
                'to_delivery_km' => $distanceToDelivery ? round($distanceToDelivery, 2) : null,
                'eta_to_pickup_minutes' => $etaToPickup,
                'eta_to_delivery_minutes' => $etaToDelivery,
            ],
            'status' => ($cachedLocation['is_online'] ?? false) ? 'online' : 'offline',
            'request_status' => $request->status,
            'payment_status' => $request->payment_status,
            'last_updated' => $cachedLocation['last_update'] ?? now()->toDateTimeString(),
        ]);
    }

    /**
     * Get detailed tracking information for a request - UPDATED
     */
    public function getTrackingDetails(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops', 'pickupProofs', 'signatures', 'payment']);

        // Get courier location
        $courierLocation = null;
        $courier = $request->courier;

        if ($courier) {
            $cachedLocation = Cache::get('courier_location_' . $courier->id);
            if ($cachedLocation) {
                $courierLocation = $cachedLocation;
            } else {
                $location = CourierLocation::where('courier_id', $courier->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                if ($location) {
                    $courierLocation = [
                        'latitude' => (float) $location->latitude,
                        'longitude' => (float) $location->longitude,
                        'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
                        'speed' => $location->speed ? (float) $location->speed : null,
                        'heading' => $location->heading ? (float) $location->heading : null,
                        'altitude' => $location->altitude ? (float) $location->altitude : null,
                        'battery_level' => $location->battery_level,
                        'is_online' => (bool) $location->is_online,
                        'timestamp' => $location->created_at->timestamp,
                        'last_update' => $location->last_update ?? $location->created_at,
                    ];
                }
            }
        }

        // Calculate progress based on status
        $progress = $this->calculateDeliveryProgress($request);

        // Prepare stops with coordinates
        $stopsWithCoords = $request->stops->map(function ($stop) {
            return [
                'id' => $stop->id,
                'type' => $stop->stop_type,
                'address' => $stop->address,
                'contact_name' => $stop->contact_name,
                'instructions' => $stop->instructions,
                'completed' => $stop->completed,
                'completed_at' => $stop->completed_at?->format('Y-m-d H:i:s'),
                'latitude' => $stop->latitude ? (float) $stop->latitude : null,
                'longitude' => $stop->longitude ? (float) $stop->longitude : null,
            ];
        });

        // Calculate distances if courier location is available
        $distances = [];
        if ($courierLocation && $courierLocation['latitude'] && $courierLocation['longitude']) {
            if ($request->pickup_latitude && $request->pickup_longitude) {
                $distances['to_pickup_km'] = round($this->calculateDistance(
                    $courierLocation['latitude'],
                    $courierLocation['longitude'],
                    $request->pickup_latitude,
                    $request->pickup_longitude
                ), 2);
            }

            if ($request->delivery_latitude && $request->delivery_longitude) {
                $distances['to_delivery_km'] = round($this->calculateDistance(
                    $courierLocation['latitude'],
                    $courierLocation['longitude'],
                    $request->delivery_latitude,
                    $request->delivery_longitude
                ), 2);
            }
        }

        // Payment information
        $paymentInfo = null;
        if ($request->payment) {
            $paymentInfo = [
                'status' => $request->payment->payment_status,
                'amount' => number_format($request->payment->amount, 2),
                'method' => $request->payment->payment_method,
                'paid_at' => $request->payment->paid_at?->format('Y-m-d H:i:s'),
                'receipt_url' => $request->payment->receipt_url,
                'is_paid' => $request->payment->isPaid(),
            ];
        }

        return response()->json([
            'request' => [
                'id' => $request->id,
                'request_number' => $request->request_number,
                'status' => $request->status,
                'status_display' => str_replace('_', ' ', $request->status),
                'pickup_address' => $request->pickup_address,
                'pickup_latitude' => $request->pickup_latitude ? (float) $request->pickup_latitude : null,
                'pickup_longitude' => $request->pickup_longitude ? (float) $request->pickup_longitude : null,
                'delivery_address' => $request->delivery_address,
                'delivery_latitude' => $request->delivery_latitude ? (float) $request->delivery_latitude : null,
                'delivery_longitude' => $request->delivery_longitude ? (float) $request->delivery_longitude : null,
                'scheduled_pickup_time' => $request->scheduled_pickup_time?->format('Y-m-d H:i:s'),
                'scheduled_delivery_time' => $request->scheduled_delivery_time?->format('Y-m-d H:i:s'),
                'priority_level' => $request->priority_level,
                'specimen_type' => $request->specimen_type,
                'temperature_requirement' => $request->temperature_requirement,
                'quantity' => $request->quantity,
                'estimated_price' => $request->estimated_price,
                'distance_miles' => $request->distance_miles,
                'payment_status' => $request->payment_status,
                'payment_required' => $request->payment_required,
                'payment_due_at' => $request->payment_due_at?->format('Y-m-d H:i:s'),
                'needs_payment' => $request->needsPayment(),
                'is_payment_overdue' => $request->isPaymentOverdue(),
            ],
            'courier' => $courier ? [
                'id' => $courier->id,
                'name' => $courier->full_name,
                'phone' => $courier->phone,
                'email' => $courier->email,
                'vehicle_type' => $courier->vehicle_type,
                'vehicle_number' => $courier->vehicle_number,
                'profile_image' => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
                'rating' => $courier->rating ?? 4.5,
            ] : null,
            'courier_location' => $courierLocation ? array_merge($courierLocation, [
                'formatted_address' => $this->reverseGeocode(
                    $courierLocation['latitude'] ?? null,
                    $courierLocation['longitude'] ?? null
                ),
                'coordinates' => [
                    'latitude' => $courierLocation['latitude'] ?? null,
                    'longitude' => $courierLocation['longitude'] ?? null,
                    'formatted' => $courierLocation['latitude'] && $courierLocation['longitude']
                        ? sprintf('%.6f, %.6f', $courierLocation['latitude'], $courierLocation['longitude'])
                        : null,
                ]
            ]) : null,
            'stops' => $stopsWithCoords,
            'progress' => $progress,
            'distances' => $distances,
            'payment' => $paymentInfo,
            'proofs' => [
                'pickup_proofs' => $request->pickupProofs->count(),
                'delivery_signatures' => $request->signatures->where('signature_type', 'delivery')->count(),
            ],
            'timestamps' => [
                'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                'accepted_at' => $request->accepted_at?->format('Y-m-d H:i:s'),
                'pickup_started_at' => $request->pickup_started_at?->format('Y-m-d H:i:s'),
                'pickup_completed_at' => $request->pickup_completed_at?->format('Y-m-d H:i:s'),
                'transit_started_at' => $request->transit_started_at?->format('Y-m-d H:i:s'),
                'arrived_at_destination_at' => $request->arrived_at_destination_at?->format('Y-m-d H:i:s'),
                'delivered_at' => $request->delivered_at?->format('Y-m-d H:i:s'),
                'completed_at' => $request->completed_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Calculate delivery progress percentage
     */
    private function calculateDeliveryProgress($request)
    {
        $statusProgress = [
            'pending_approval' => 10,
            'approved' => 20,
            'assigned' => 30,
            'accepted_by_courier' => 40,
            'at_stop' => 50,
            'picked_up' => 60,
            'in_transit' => 70,
            'arrived_at_destination' => 80,
            'delivered' => 90,
            'completed' => 100,
            'cancelled' => 0,
        ];

        $progress = $statusProgress[$request->status] ?? 0;

        // If courier is en route, calculate distance-based progress
        if (in_array($request->status, ['in_transit', 'picked_up', 'accepted_by_courier']) && $request->courier) {
            $progress += 5; // Add small buffer for "en route"
        }

        return min(100, $progress);
    }

    /**
     * Get courier location by ID (API endpoint)
     */
    public function getCourierLocationApi($courierId)
    {
        // Check if courier exists
        $courier = \App\Models\User::where('id', $courierId)
            ->whereHas('role', function ($q) {
                $q->where('slug', 'courier');
            })->first();

        if (!$courier) {
            return response()->json(['error' => 'Courier not found'], 404);
        }

        // Get location from cache first
        $cachedLocation = Cache::get('courier_location_' . $courierId);

        // If not in cache, check database
        if (!$cachedLocation && class_exists(CourierLocation::class)) {
            $location = CourierLocation::where('courier_id', $courierId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($location) {
                $cachedLocation = [
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
                    'speed' => $location->speed ? (float) $location->speed : null,
                    'heading' => $location->heading ? (float) $location->heading : null,
                    'altitude' => $location->altitude ? (float) $location->altitude : null,
                    'battery_level' => $location->battery_level,
                    'is_online' => (bool) $location->is_online,
                    'timestamp' => $location->created_at->timestamp,
                    'last_update' => $location->last_update ?? $location->created_at,
                ];
            }
        }

        if (!$cachedLocation) {
            return response()->json([
                'courier' => [
                    'id' => $courier->id,
                    'name' => $courier->full_name,
                ],
                'location' => null,
                'status' => 'offline',
            ]);
        }

        // Get formatted address IN ENGLISH
        $formattedAddress = $this->reverseGeocode(
            $cachedLocation['latitude'] ?? null,
            $cachedLocation['longitude'] ?? null
        );

        return response()->json([
            'courier' => [
                'id' => $courier->id,
                'name' => $courier->full_name,
                'phone' => $courier->phone,
                'vehicle_type' => $courier->vehicle_type,
                'profile_image' => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
            ],
            'location' => array_merge($cachedLocation, ['formatted_address' => $formattedAddress]),
            'status' => ($cachedLocation['is_online'] ?? false) ? 'online' : 'offline',
        ]);
    }

    /**
     * Calculate distance between two coordinates in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        return $this->calculateHaversineDistance($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Calculate estimated time of arrival in minutes
     */
    private function calculateETA($distanceKm, $speedKmh)
    {
        if ($speedKmh <= 0) {
            $speedKmh = 30; // Default speed in city traffic
        }

        $timeHours = $distanceKm / $speedKmh;
        return round($timeHours * 60); // Convert to minutes
    }

    /**
     * Show payment page for a request
     */
    public function showPayment(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        if (!$request->needsPayment()) {
            return redirect()->route('client.requests.show', $request)
                ->with('info', 'No payment required or payment already completed.');
        }

        // Create payment if doesn't exist
        if (!$request->payment) {
            $paymentService = new PaymentService();
            $payment = $paymentService->createPayment($request, Auth::user());
        } else {
            $payment = $request->payment;
        }

        $paymentService = new PaymentService();
        $config = $paymentService->getConfig();

        return view('client.payments.payment', compact('request', 'payment', 'config'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $httpRequest, SpecimenRequest $request, PaymentService $paymentService)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $payment = $request->payment;
        if (!$payment) {
            return back()->with('error', 'Payment record not found.');
        }

        $validated = $httpRequest->validate([
            'payment_method' => 'required|string|in:card,bank_transfer,cash,check',
            'stripe_token' => 'required_if:payment_method,card',
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string',
            'terms' => 'required|accepted',
        ]);

        // Update billing information
        $payment->update([
            'billing_name' => $validated['billing_name'],
            'billing_email' => $validated['billing_email'],
            'billing_phone' => $validated['billing_phone'],
            'billing_address' => $validated['billing_address'],
        ]);

        // Process payment based on method
        if ($validated['payment_method'] === 'card') {
            $result = $paymentService->processStripePayment($payment, $validated['stripe_token']);
        } else {
            $result = $paymentService->processOfflinePayment($payment, $validated);
        }

        if ($result['success']) {
            // Update request payment status
            $request->update([
                'payment_status' => $validated['payment_method'] === 'card' ? 'paid' : 'pending',
            ]);

            // Create notification
            Notification::create([
                'user_id' => Auth::id(),
                'request_id' => $request->id,
                'type' => 'payment_completed',
                'title' => 'Payment Completed',
                'message' => "Payment of $" . number_format($payment->amount, 2) . " completed for request #{$request->request_number}",
                'data' => json_encode(['request_id' => $request->id, 'payment_id' => $payment->id]),
            ]);

            // Notify admin
            $adminUsers = \App\Models\User::whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })->get();

            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'request_id' => $request->id,
                    'type' => 'payment_received',
                    'title' => 'Payment Received',
                    'message' => "Payment received for request #{$request->request_number} from " . Auth::user()->full_name,
                    'data' => json_encode(['request_id' => $request->id, 'payment_id' => $payment->id]),
                ]);
            }

            return redirect()->route('client.payments.success', $payment->id)
                ->with('success', $result['message'] ?? 'Payment processed successfully.');
        }

        return back()->with('error', $result['error'] ?? 'Payment processing failed.');
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        $payment->load(['request']);

        return view('client.payments.success', compact('payment'));
    }

    /**
     * Payment callback (for Stripe redirect)
     */
    public function paymentCallback(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        // Check payment status
        if ($payment->isPaid()) {
            return redirect()->route('client.requests.show', $payment->request_id)
                ->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('client.payments.show', $payment->request_id)
            ->with('error', 'Payment is still pending or failed.');
    }

    /**
     * Download payment receipt
     */
    public function downloadReceipt(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$payment->isPaid()) {
            abort(404, 'Payment receipt not available.');
        }

        // Generate PDF receipt
        $pdf = \PDF::loadView('client.payments.receipt', compact('payment'));

        return $pdf->download("receipt-{$payment->id}.pdf");
    }

    /**
     * View payment history
     */
    public function paymentHistory()
    {
        $user = Auth::user();

        $payments = Payment::where('user_id', $user->id)
            ->with(['request'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('client.payments.history', compact('payments'));
    }

    /**
     * View payment details
     */
    public function viewPayment(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        $payment->load(['request', 'request.courier']);

        return view('client.payments.view', compact('payment'));
    }
}
