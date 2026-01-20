<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpecimenRequest;
use App\Models\CourierLocation;
use App\Models\LocationHistory;
use App\Models\PickupProof;
use App\Models\Signature;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Models\RequestStop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CourierController extends Controller
{
    /**
     * Show courier dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Statistics
        $stats = [
            'total_assignments' => $user->assignedRequests()->count(),
            'pending' => $user->assignedRequests()->where('status', 'assigned')->count(),
            'in_progress' => $user->assignedRequests()->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit'])->count(),
            'completed' => $user->assignedRequests()->where('status', 'completed')->count(),
            'today_pickups' => $user->assignedRequests()
                ->whereDate('scheduled_pickup_time', Carbon::today())
                ->whereIn('status', ['assigned', 'accepted_by_courier'])
                ->count(),
            'today_deliveries' => $user->assignedRequests()
                ->whereDate('scheduled_delivery_time', Carbon::today())
                ->whereIn('status', ['picked_up', 'in_transit'])
                ->count(),
        ];

        // Active requests
        $activeRequests = $user->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_delivery_time')
            ->limit(5)
            ->get();

        // Today's schedule
        $todaysSchedule = $user->assignedRequests()
            ->whereDate('scheduled_pickup_time', Carbon::today())
            ->orWhereDate('scheduled_delivery_time', Carbon::today())
            ->orderBy('scheduled_pickup_time')
            ->get();

        // Recent completions
        $recentCompletions = $user->assignedRequests()
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('courier.dashboard', compact('stats', 'activeRequests', 'todaysSchedule', 'recentCompletions'));
    }

    /**
     * Show assignments list
     */
    public function assignments(Request $request)
    {
        $user = Auth::user();
        $query = $user->assignedRequests()->with(['client', 'pickupProof', 'signature']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority_level', $request->priority);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('scheduled_pickup_time', $request->date);
        }

        $assignments = $query->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_pickup_time')
            ->paginate(10);

        // Get status counts
        $statusCounts = [
            'total' => $user->assignedRequests()->count(),
            'assigned' => $user->assignedRequests()->where('status', 'assigned')->count(),
            'accepted_by_courier' => $user->assignedRequests()->where('status', 'accepted_by_courier')->count(),
            'picked_up' => $user->assignedRequests()->where('status', 'picked_up')->count(),
            'in_transit' => $user->assignedRequests()->where('status', 'in_transit')->count(),
            'delivered' => $user->assignedRequests()->where('status', 'delivered')->count(),
            'completed' => $user->assignedRequests()->where('status', 'completed')->count(),
        ];

        return view('courier.assignments.index', compact('assignments', 'statusCounts'));
    }

    /**
     * Accept an assignment
     */
    /**
     * Accept an assignment
     */
    public function acceptAssignment(Request $request, $requestId)
    {
        // Get the request by ID instead of using route model binding
        $specimenRequest = SpecimenRequest::find($requestId);

        if (!$specimenRequest) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        // Debug: Log what we're getting
        \Log::info('Accept Assignment Debug:', [
            'courier_id' => Auth::id(),
            'specimenRequest_id' => $specimenRequest->id,
            'specimenRequest_assigned_to' => $specimenRequest->assigned_to,
            'specimenRequest_status' => $specimenRequest->status,
            'request_number' => $specimenRequest->request_number,
        ]);

        // Verify this request is assigned to the current courier
        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'This assignment is not assigned to you. Your ID: ' . Auth::id() . ', Assigned to: ' . $specimenRequest->assigned_to . ', Request: ' . $specimenRequest->request_number);
        }

        if ($specimenRequest->status != 'assigned') {
            return redirect()->back()->with('error', 'This assignment cannot be accepted. Current status: ' . $specimenRequest->status);
        }

        // Update request status
        $specimenRequest->update([
            'status' => 'accepted_by_courier',
            'accepted_at' => now(),
        ]);

        // Create notification for admin and client
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Courier Accepted Assignment',
            'message' => "Courier {$specimenRequest->courier->full_name} has accepted assignment #{$specimenRequest->request_number}",
            'type' => 'request_assigned',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => 1, // Assuming admin ID is 1
            'title' => 'Courier Accepted Assignment',
            'message' => "Courier {$specimenRequest->courier->full_name} has accepted assignment #{$specimenRequest->request_number}",
            'type' => 'request_assigned',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'accepted_assignment',
            'model_type' => SpecimenRequest::class, // Add this
            'model_id' => $specimenRequest->id,     // Add this
            'changes' => json_encode([               // Use 'changes' instead of 'description'
                'status' => 'assigned to accepted_by_courier',
                'accepted_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Start automatic location tracking for this request
        $this->startLocationTracking($specimenRequest);

        return redirect()->route('courier.requests.show', $specimenRequest)
            ->with('success', 'Assignment accepted successfully! Location tracking has been enabled.');
    }

    /**
     * Start location tracking for a request
     */
    private function startLocationTracking(SpecimenRequest $request)
    {
        // Store tracking start time in cache
        cache()->put("tracking_start_{$request->id}", now(), now()->addHours(24));

        // Set courier as online
        cache()->put("courier_online_{$request->assigned_to}", true, now()->addHours(24)); // CHANGED: courier_id -> assigned_to
    }

    /**
     * Update courier location
     */
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'altitude' => 'nullable|numeric',
            'request_id' => 'nullable|exists:specimen_requests,id',
        ]);

        $user = Auth::user();

        // Update or create current location
        $location = CourierLocation::updateOrCreate(
            ['courier_id' => $user->id],
            [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? 0,
                'speed' => $validated['speed'] ?? 0,
                'heading' => $validated['heading'] ?? 0,
                'altitude' => $validated['altitude'] ?? 0,
                'is_online' => true,
                'last_update' => now(),
                'battery_level' => $request->battery_level ?? null,
            ]
        );

        // Add to location history
        if ($validated['request_id']) {
            LocationHistory::create([
                'courier_id' => $user->id,
                'request_id' => $validated['request_id'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? 0,
                'speed' => $validated['speed'] ?? 0,
                'heading' => $validated['heading'] ?? 0,
                'altitude' => $validated['altitude'] ?? 0,
                'battery_level' => $request->battery_level ?? null,
            ]);
        }

        // Cache location for real-time tracking
        cache()->put('courier_location_' . $user->id, [
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy' => $validated['accuracy'] ?? 0,
            'timestamp' => now(),
            'courier_id' => $user->id,
            'courier_name' => $user->full_name,
            'speed' => $validated['speed'] ?? 0,
            'heading' => $validated['heading'] ?? 0,
        ], 35);

        return response()->json([
            'success' => true,
            'message' => 'Location updated',
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get location status
     */
    public function locationStatus()
    {
        $user = Auth::user();
        $location = CourierLocation::where('courier_id', $user->id)->first();

        return response()->json([
            'is_online' => $location ? $location->is_online : false,
            'last_update' => $location ? $location->last_update : null,
            'tracking_active' => cache()->has("courier_online_{$user->id}"),
        ]);
    }

    /**
     * Toggle location tracking
     */
    public function toggleLocation(Request $request)
    {
        $user = Auth::user();
        $isActive = $request->active;

        if ($isActive) {
            cache()->put("courier_online_{$user->id}", true, now()->addHours(24));
        } else {
            cache()->forget("courier_online_{$user->id}");

            // Update location record
            CourierLocation::where('courier_id', $user->id)->update(['is_online' => false]);
        }

        return response()->json([
            'success' => true,
            'tracking_active' => $isActive,
        ]);
    }

    /**
     * Show request details (HIPAA compliant view)
     */
    public function viewRequest($requestId)
    {
        // Get the request by ID instead of using route model binding
        $specimenRequest = SpecimenRequest::findOrFail($requestId);
        // Verify this request is assigned to the current courier
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            abort(403, 'You are not assigned to this request.');
        }

        // Load necessary relationships
        $specimenRequest->load([
            'client',
            'pickupProof',
            'signature',
            'stops',
            'locationHistory' => function ($query) {
                $query->where('courier_id', Auth::id())->orderBy('created_at');
            }
        ]);

        // Get current location
        $currentLocation = CourierLocation::where('courier_id', Auth::id())->first();

        // Calculate distance to pickup if location available
        $distanceToPickup = null;
        if ($currentLocation) {
            $distanceToPickup = $this->calculateDistance(
                $currentLocation->latitude,
                $currentLocation->longitude,
                $specimenRequest->pickup_latitude,
                $specimenRequest->pickup_longitude
            );
        }

        return view('courier.requests.show', compact('specimenRequest', 'currentLocation', 'distanceToPickup'));
    }

    /**
     * Start pickup process
     */
    public function startPickup(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'accepted_by_courier') {
            return redirect()->back()->with('error', 'Cannot start pickup from current status.');
        }

        $specimenRequest->update([
            'status' => 'at_stop',
            'pickup_started_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Pickup Started',
            'message' => "Courier has arrived at pickup location for request #{$specimenRequest->request_number}",
            'type' => 'pickup_started',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'start_pickup',
            'description' => "Started pickup for request #{$specimenRequest->request_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pickup process started. Please upload proof of pickup.');
    }

    /**
     * Submit pickup proof with photo
     */
    public function submitPickupProof(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (!in_array($specimenRequest->status, ['at_stop', 'accepted_by_courier'])) {
            return redirect()->back()->with('error', 'Cannot submit pickup proof from current status.');
        }

        $request->validate([
            'pickup_photo' => 'required|image|max:5120',
            'pickup_notes' => 'nullable|string|max:500',
            'specimen_condition' => 'required|in:good,acceptable,damaged',
            'temperature_check' => 'required|in:within_range,out_of_range,not_checked',
        ]);

        // Upload photo
        $photoPath = $request->file('pickup_photo')->store('pickup-proofs', 'public');

        // Create pickup proof
        $pickupProof = PickupProof::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => Auth::id(),
            'photo_path' => $photoPath,
            'notes' => $request->pickup_notes,
            'specimen_condition' => $request->specimen_condition,
            'temperature_check' => $request->temperature_check,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'verified' => false,
        ]);

        // Update request status
        $specimenRequest->update([
            'status' => 'picked_up',
            'pickup_completed_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Pickup Completed',
            'message' => "Specimen picked up for request #{$specimenRequest->request_number}. Proof uploaded.",
            'type' => 'pickup_completed',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submit_pickup_proof',
            'description' => "Submitted pickup proof for request #{$specimenRequest->request_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pickup proof submitted successfully! You can now start delivery.');
    }

    /**
     * Start transit to delivery location
     */
    public function startTransit(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'picked_up') {
            return redirect()->back()->with('error', 'Cannot start transit from current status.');
        }

        $specimenRequest->update([
            'status' => 'in_transit',
            'transit_started_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'In Transit',
            'message' => "Specimen #{$specimenRequest->request_number} is now in transit to delivery location.",
            'type' => 'in_transit',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'start_transit',
            'description' => "Started transit for request #{$specimenRequest->request_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Transit started. Continue to delivery location.');
    }

    /**
     * Arrive at destination
     */
    public function arriveAtDestination(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'in_transit') {
            return redirect()->back()->with('error', 'Cannot mark arrival from current status.');
        }

        $specimenRequest->update([
            'status' => 'arrived_at_destination',
            'arrived_at_destination_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Arrived at Destination',
            'message' => "Courier has arrived at delivery location for request #{$specimenRequest->request_number}",
            'type' => 'arrived_at_destination',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'arrive_destination',
            'description' => "Arrived at destination for request #{$specimenRequest->request_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Arrival recorded. Please complete delivery with signature.');
    }

    /**
     * Submit delivery with signature
     */
    public function submitDelivery(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (!in_array($specimenRequest->status, ['arrived_at_destination', 'in_transit'])) {
            return redirect()->back()->with('error', 'Cannot submit delivery from current status.');
        }

        $request->validate([
            'signature' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'recipient_relationship' => 'required|string|max:100',
            'delivery_photo' => 'nullable|image|max:5120',
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        // Upload delivery photo if provided
        $photoPath = null;
        if ($request->hasFile('delivery_photo')) {
            $photoPath = $request->file('delivery_photo')->store('delivery-proofs', 'public');
        }

        // Create signature record
        $signature = Signature::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => Auth::id(),
            'recipient_name' => $request->recipient_name,
            'recipient_relationship' => $request->recipient_relationship,
            'signature_data' => $request->signature,
            'photo_path' => $photoPath,
            'notes' => $request->delivery_notes,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'signed_at' => now(),
        ]);

        // Update request status
        $specimenRequest->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Delivery Completed',
            'message' => "Specimen #{$specimenRequest->request_number} has been delivered. Signature captured.",
            'type' => 'delivery_completed',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submit_delivery',
            'description' => "Submitted delivery for request #{$specimenRequest->request_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Delivery submitted successfully! Please complete the request.');
    }

    /**
     * Complete request
     */
    public function completeRequest(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'delivered') {
            return redirect()->back()->with('error', 'Cannot complete request from current status.');
        }

        $specimenRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Stop location tracking for this request
        cache()->forget("tracking_start_{$specimenRequest->id}");

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Request Completed',
            'message' => "Request #{$specimenRequest->request_number} has been marked as completed.",
            'type' => 'request_completed',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'complete_request',
            'description' => "Completed request #{$specimenRequest->request_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courier.assignments.index')
            ->with('success', 'Request completed successfully!');
    }

    /**
     * Get active request for API
     */
    public function getActiveRequest()
    {
        $activeRequest = Auth::user()->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit', 'arrived_at_destination'])
            ->with(['client', 'pickupProof', 'signature'])
            ->first();

        if (!$activeRequest) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'request' => [
                'id' => $activeRequest->id,
                'request_number' => $activeRequest->request_number,
                'status' => $activeRequest->status,
                'pickup_address' => $activeRequest->pickup_address,
                'delivery_address' => $activeRequest->delivery_address,
                'priority_level' => $activeRequest->priority_level,
            ]
        ]);
    }

    /**
     * Get navigation data
     */
    public function getNavigation(SpecimenRequest $specimenRequest)
    {
        // Verify assignment
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $currentLocation = CourierLocation::where('courier_id', Auth::id())->first();

        if (!$currentLocation) {
            return response()->json(['error' => 'Location not available'], 400);
        }

        // Calculate distance and estimated time
        $distance = $this->calculateDistance(
            $currentLocation->latitude,
            $currentLocation->longitude,
            $specimenRequest->pickup_latitude,
            $specimenRequest->pickup_longitude
        );

        $estimatedTime = $this->calculateEstimatedTime($distance);

        return response()->json([
            'from_lat' => $currentLocation->latitude,
            'from_lng' => $currentLocation->longitude,
            'to_lat' => $specimenRequest->pickup_latitude,
            'to_lng' => $specimenRequest->pickup_longitude,
            'distance_km' => round($distance, 2),
            'estimated_minutes' => $estimatedTime,
            'google_maps_url' => "https://www.google.com/maps/dir/{$currentLocation->latitude},{$currentLocation->longitude}/{$specimenRequest->pickup_latitude},{$specimenRequest->pickup_longitude}",
        ]);
    }

    /**
     * Get location history for a request
     */
    public function getLocationHistory(SpecimenRequest $specimenRequest)
    {
        // Verify assignment
        if ($specimenRequest->assigned_to != Auth::id()) { // CHANGED: courier_id -> assigned_to
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $history = LocationHistory::where('request_id', $specimenRequest->id)
            ->where('courier_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get(['latitude', 'longitude', 'created_at', 'speed', 'heading']);

        return response()->json($history);
    }

    /**
     * Calculate distance between two coordinates in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
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
     * Calculate estimated travel time in minutes
     */
    private function calculateEstimatedTime($distanceKm)
    {
        // Average speed: 40 km/h in city traffic
        $averageSpeed = 40;
        $timeHours = $distanceKm / $averageSpeed;
        return round($timeHours * 60); // Convert to minutes
    }

    /**
     * Show active pickups
     */
    public function activePickups()
    {
        $activePickups = Auth::user()->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'at_stop'])
            ->with(['client', 'pickupProof'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_pickup_time')
            ->paginate(10);

        return view('courier.assignments.active-pickups', compact('activePickups'));
    }

    /**
     * Show active deliveries
     */
    public function activeDeliveries()
    {
        $activeDeliveries = Auth::user()->assignedRequests()
            ->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination'])
            ->with(['client', 'pickupProof', 'signature'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_delivery_time')
            ->paginate(10);

        return view('courier.assignments.active-deliveries', compact('activeDeliveries'));
    }

    /**
     * Show delivery history
     */
    public function history()
    {
        $history = Auth::user()->assignedRequests()
            ->where('status', 'completed')
            ->with(['client', 'pickupProof', 'signature'])
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('courier.assignments.history', compact('history'));
    }

    /**
     * Show proofs
     */
    public function proofs()
    {
        $pickupProofs = PickupProof::where('courier_id', Auth::id())
            ->with(['request'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $signatures = Signature::where('courier_id', Auth::id())
            ->with(['request'])
            ->orderBy('signed_at', 'desc')
            ->paginate(10);

        return view('courier.assignments.proofs', compact('pickupProofs', 'signatures'));
    }

    /**
     * Show specific proof
     */
    public function viewProof($id, $type)
    {
        if ($type === 'pickup') {
            $proof = PickupProof::where('courier_id', Auth::id())
                ->with(['request'])
                ->findOrFail($id);
        } else {
            $proof = Signature::where('courier_id', Auth::id())
                ->with(['request'])
                ->findOrFail($id);
        }

        return view('courier.assignments.proof-detail', compact('proof', 'type'));
    }

    /**
     * Show courier profile
     */
    public function profile()
    {
        $user = Auth::user();
        $stats = [
            'total_deliveries' => $user->assignedRequests()->where('status', 'completed')->count(),
            'on_time_rate' => $this->calculateOnTimeRate($user),
            'avg_rating' => 4.8, // This would come from a ratings table
        ];

        return view('courier.profile', compact('user', 'stats'));
    }

    /**
     * Update courier profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show notifications
     */
    public function notifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('courier.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Notification $notification)
    {
        if ($notification->user_id != Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Calculate on-time delivery rate
     */
    private function calculateOnTimeRate($user)
    {
        $completedRequests = $user->assignedRequests()
            ->where('status', 'completed')
            ->whereNotNull('scheduled_delivery_time')
            ->whereNotNull('delivered_at')
            ->get();

        if ($completedRequests->isEmpty()) {
            return 100;
        }

        $onTime = 0;
        foreach ($completedRequests as $request) {
            if ($request->delivered_at <= $request->scheduled_delivery_time) {
                $onTime++;
            }
        }

        return round(($onTime / $completedRequests->count()) * 100, 1);
    }
}
