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
use App\Models\CourierQuote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CourierController extends Controller
{
    /**
     * Show courier dashboard - Main dashboard view for couriers
     * Route: GET /courier/dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Statistics for dashboard cards
        $stats = [
            'total_assignments' => $user->assignedRequests()->count(),
            'pending' => $user->assignedRequests()->where('status', 'assigned')->count(),
            'pending_acceptance' => $user->assignedRequests()->where('status', 'pending_courier_acceptance')->count(),
            'in_progress' => $user->assignedRequests()->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit'])->count(),
            'completed' => $user->assignedRequests()->where('status', 'completed')->count(),
            'today_pickups' => $user->assignedRequests()
                ->whereDate('scheduled_pickup_time', Carbon::today())
                ->whereIn('status', ['assigned', 'accepted_by_courier', 'pending_courier_acceptance'])
                ->count(),
            'today_deliveries' => $user->assignedRequests()
                ->whereDate('scheduled_delivery_time', Carbon::today())
                ->whereIn('status', ['picked_up', 'in_transit'])
                ->count(),
            'awaiting_proofs' => $user->assignedRequests()->where('requires_proof', true)->count(),
        ];

        // Active requests - currently in progress assignments
        $activeRequests = $user->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit', 'awaiting_pickup_proof', 'awaiting_delivery_proof', 'pending_courier_acceptance'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_delivery_time')
            ->limit(5)
            ->get();

        // Today's schedule - pickups and deliveries scheduled for today
        $todaysSchedule = $user->assignedRequests()
            ->whereDate('scheduled_pickup_time', Carbon::today())
            ->orWhereDate('scheduled_delivery_time', Carbon::today())
            ->orderBy('scheduled_pickup_time')
            ->get();

        // Recent completions - last 5 completed requests
        $recentCompletions = $user->assignedRequests()
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Pending quotes to accept - quotes waiting for courier acceptance
        $pendingQuotes = CourierQuote::where('courier_id', $user->id)
            ->where('status', 'pending')
            ->where('valid_until', '>', now())
            ->with(['request'])
            ->orderBy('valid_until')
            ->limit(3)
            ->get();

        return view('courier.dashboard', compact('stats', 'activeRequests', 'todaysSchedule', 'recentCompletions', 'pendingQuotes'));
    }

    /**
     * Show assignments list - Main assignments page with filtering
     * Route: GET /courier/assignments
     * Linked from: "Back to Assignments" button in request details view
     */
    public function assignments(Request $request)
    {
        $user = Auth::user();
        $query = $user->assignedRequests()->with(['client', 'pickupProof', 'signature']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'awaiting_proof') {
                $query->where('requires_proof', true);
            } elseif ($request->status == 'pending_acceptance') {
                $query->where('status', 'pending_courier_acceptance')
                    ->where('courier_can_accept', true);
            } else {
                $query->where('status', $request->status);
            }
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

        // Get status counts for filter tabs
        $statusCounts = [
            'total' => $user->assignedRequests()->count(),
            'pending_acceptance' => $user->assignedRequests()->where('status', 'pending_courier_acceptance')->where('courier_can_accept', true)->count(),
            'assigned' => $user->assignedRequests()->where('status', 'assigned')->count(),
            'accepted_by_courier' => $user->assignedRequests()->where('status', 'accepted_by_courier')->count(),
            'awaiting_pickup_proof' => $user->assignedRequests()->where('status', 'awaiting_pickup_proof')->count(),
            'picked_up' => $user->assignedRequests()->where('status', 'picked_up')->count(),
            'in_transit' => $user->assignedRequests()->where('status', 'in_transit')->count(),
            'awaiting_delivery_proof' => $user->assignedRequests()->where('status', 'awaiting_delivery_proof')->count(),
            'delivered' => $user->assignedRequests()->where('status', 'delivered')->count(),
            'completed' => $user->assignedRequests()->where('status', 'completed')->count(),
            'requires_proof' => $user->assignedRequests()->where('requires_proof', true)->count(),
        ];

        return view('courier.assignments.index', compact('assignments', 'statusCounts'));
    }

    /**
     * Accept an assignment - Changes status from 'assigned' to 'accepted_by_courier'
     * Route: POST /courier/assignments/{request}/accept
     * Linked from: "Accept Assignment" button in request details view when status is 'assigned'
     */
    public function acceptAssignment(Request $request, $requestId)
    {
        // Get the request by ID instead of using route model binding
        $specimenRequest = SpecimenRequest::find($requestId);

        if (!$specimenRequest) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        // Verify this request is assigned to the current courier
        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'This assignment is not assigned to you.');
        }

        if ($specimenRequest->status != 'assigned') {
            return redirect()->back()->with('error', 'This assignment cannot be accepted. Current status: ' . $specimenRequest->status);
        }

        // Update request status
        $specimenRequest->update([
            'status' => 'accepted_by_courier',
            'accepted_at' => now(),
            'requires_proof' => false,
            'proof_uploaded' => false,
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
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'assigned to accepted_by_courier',
                'accepted_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Start automatic location tracking for this request
        $this->startLocationTracking($specimenRequest);

        return redirect()->route('courier.requests.show', $specimenRequest->id)
            ->with('success', 'Assignment accepted successfully! Location tracking has been enabled.');
    }

    /**
     * Accept price quote for assignment - Accepts quoted price for assignment
     * Route: POST /courier/assignments/{request}/accept-quote
     */
    public function acceptQuote(Request $httpRequest, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        // Check if courier can accept this request
        if ($specimenRequest->assigned_to !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (!$specimenRequest->courier_can_accept) {
            return redirect()->back()->with('error', 'This request cannot be accepted.');
        }

        if ($specimenRequest->acceptance_deadline && now()->gt($specimenRequest->acceptance_deadline)) {
            return redirect()->back()->with('error', 'The acceptance deadline has passed.');
        }

        // Get the quote
        $quote = CourierQuote::where('request_id', $requestId)
            ->where('courier_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        if ($quote->isExpired()) {
            return redirect()->back()->with('error', 'The quote has expired.');
        }

        // Accept the quote
        $quote->accept();

        // Update request
        $specimenRequest->update([
            'courier_accepted_at' => now(),
            'courier_can_accept' => false,
            'status' => 'assigned',
        ]);

        // Create notification for admin
        Notification::create([
            'user_id' => $specimenRequest->assigned_by,
            'request_id' => $specimenRequest->id,
            'type' => 'quote_accepted',
            'title' => 'Quote Accepted',
            'message' => "Courier {$specimenRequest->courier->full_name} accepted the quote for request #{$specimenRequest->request_number}",
            'data' => json_encode([
                'request_id' => $specimenRequest->id,
                'request_number' => $specimenRequest->request_number,
                'courier_id' => $specimenRequest->assigned_to,
                'quote_id' => $quote->id,
            ]),
        ]);

        // Also notify client
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'request_id' => $specimenRequest->id,
            'type' => 'quote_accepted',
            'title' => 'Courier Accepted Assignment',
            'message' => "Courier has accepted the assignment for your request #{$specimenRequest->request_number}",
            'data' => json_encode([
                'request_id' => $specimenRequest->id,
                'request_number' => $specimenRequest->request_number,
            ]),
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'accepted_quote',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'pending_courier_acceptance to assigned',
                'courier_accepted_at' => now()->toDateTimeString(),
                'quote_id' => $quote->id,
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $httpRequest->ip(),
            'user_agent' => $httpRequest->userAgent(),
        ]);

        return redirect()->back()->with('success', 'You have accepted the price quote and assignment.');
    }

    /**
     * Decline price quote - Declines quoted price with reason
     * Route: POST /courier/assignments/{request}/decline-quote
     */
    public function declineQuote(Request $httpRequest, $requestId)
    {
        $validated = $httpRequest->validate([
            'reason' => 'required|string|max:500',
        ]);

        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        // Check if courier can decline this request
        if ($specimenRequest->assigned_to !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (!$specimenRequest->courier_can_accept) {
            return redirect()->back()->with('error', 'This request cannot be declined.');
        }

        // Get the quote
        $quote = CourierQuote::where('request_id', $requestId)
            ->where('courier_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        // Decline the quote
        $quote->decline($validated['reason']);

        // Update request
        $specimenRequest->update([
            'courier_declined_at' => now(),
            'courier_decline_reason' => $validated['reason'],
            'courier_can_accept' => false,
            'status' => 'approved', // Go back to approved status
        ]);

        // Create notification for admin
        Notification::create([
            'user_id' => $specimenRequest->assigned_by,
            'request_id' => $specimenRequest->id,
            'type' => 'quote_declined',
            'title' => 'Quote Declined',
            'message' => "Courier {$specimenRequest->courier->full_name} declined the quote for request #{$specimenRequest->request_number}",
            'data' => json_encode([
                'request_id' => $specimenRequest->id,
                'request_number' => $specimenRequest->request_number,
                'courier_id' => $specimenRequest->assigned_to,
                'quote_id' => $quote->id,
                'reason' => $validated['reason'],
            ]),
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'declined_quote',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'pending_courier_acceptance to approved',
                'courier_declined_at' => now()->toDateTimeString(),
                'decline_reason' => $validated['reason'],
                'quote_id' => $quote->id,
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $httpRequest->ip(),
            'user_agent' => $httpRequest->userAgent(),
        ]);

        return redirect()->route('courier.assignments.index')->with('success', 'You have declined the price quote and assignment.');
    }

    /**
     * View quote details - Shows quote acceptance page
     * Route: GET /courier/assignments/{request}/quote
     */
    public function viewQuote($requestId)
    {
        $specimenRequest = SpecimenRequest::with(['quote', 'client', 'facility'])->findOrFail($requestId);

        // Check if courier can view this quote
        if ($specimenRequest->assigned_to !== Auth::id()) {
            abort(403, 'You are not assigned to this request.');
        }

        $quote = CourierQuote::where('request_id', $requestId)
            ->where('courier_id', Auth::id())
            ->firstOrFail();

        return view('courier.quote-acceptance', [
            'request' => $specimenRequest,
            'quote' => $quote,
        ]);
    }

    /**
     * Start location tracking for a request - Internal method called after accepting assignment
     */
    private function startLocationTracking(SpecimenRequest $request)
    {
        // Store tracking start time in cache
        cache()->put("tracking_start_{$request->id}", now(), now()->addHours(24));

        // Set courier as online
        cache()->put("courier_online_{$request->assigned_to}", true, now()->addHours(24));
    }

    /**
     * Update courier location - FIXED VERSION - Called by JavaScript location tracking
     * Route: POST /courier/location/update
     * Linked from: "Update Location Now" button and automatic tracking
     */
    public function updateLocation(Request $request)
    {
        try {
            // Basic validation
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

            // Log the incoming data for debugging
            \Log::info('Location update received', [
                'courier_id' => $user->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'request_id' => $validated['request_id'] ?? 'not provided'
            ]);

            // Check if CourierLocation model exists
            if (!class_exists(CourierLocation::class)) {
                // If model doesn't exist, just cache the location
                $locationData = [
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'accuracy' => $validated['accuracy'] ?? 0,
                    'speed' => $validated['speed'] ?? 0,
                    'heading' => $validated['heading'] ?? 0,
                    'altitude' => $validated['altitude'] ?? 0,
                    'timestamp' => now(),
                    'last_update' => now(),
                    'courier_id' => $user->id,
                    'courier_name' => $user->full_name,
                ];

                // Cache location for real-time tracking
                cache()->put('courier_location_' . $user->id, $locationData, 35);

                return response()->json([
                    'success' => true,
                    'message' => 'Location received (cached only)',
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }

            // Prepare location data for database
            $locationData = [
                'courier_id' => $user->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? 0,
                'speed' => $validated['speed'] ?? 0,
                'heading' => $validated['heading'] ?? 0,
                'altitude' => $validated['altitude'] ?? 0,
                'is_online' => true,
                'last_update' => now(),
                'battery_level' => $request->battery_level ?? null,
            ];

            // Add request_id if provided (handle nullable)
            if (!empty($validated['request_id'])) {
                $locationData['request_id'] = $validated['request_id'];
            }

            \Log::info('Attempting to save location to database', $locationData);

            // Update or create current location in database
            $location = CourierLocation::updateOrCreate(
                ['courier_id' => $user->id],
                $locationData
            );

            \Log::info('Location saved successfully', [
                'location_id' => $location->id,
                'courier_id' => $location->courier_id,
                'request_id' => $location->request_id
            ]);

            // Add to location history if request_id is provided AND model exists
            if (!empty($validated['request_id']) && class_exists(LocationHistory::class)) {
                try {
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
                    \Log::info('Location history saved');
                } catch (\Exception $historyError) {
                    \Log::warning('Failed to save location history: ' . $historyError->getMessage());
                }
            }

            // Cache location for real-time tracking
            cache()->put('courier_location_' . $user->id, [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? 0,
                'timestamp' => now(),
                'last_update' => now(),
                'courier_id' => $user->id,
                'courier_name' => $user->full_name,
                'speed' => $validated['speed'] ?? 0,
                'heading' => $validated['heading'] ?? 0,
                'altitude' => $validated['altitude'] ?? 0,
                'is_online' => true,
            ], 35);

            return response()->json([
                'success' => true,
                'message' => 'Location updated',
                'timestamp' => now()->toDateTimeString(),
                'location_id' => $location->id,
                'database_saved' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Location update error: ' . $e->getMessage());
            \Log::error('Error trace: ', ['trace' => $e->getTraceAsString()]);

            // Fallback - cache the location even if database fails
            if (isset($user) && isset($validated)) {
                $locationData = [
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'accuracy' => $validated['accuracy'] ?? 0,
                    'speed' => $validated['speed'] ?? 0,
                    'heading' => $validated['heading'] ?? 0,
                    'altitude' => $validated['altitude'] ?? 0,
                    'timestamp' => now(),
                    'last_update' => now(),
                    'courier_id' => $user->id,
                    'courier_name' => $user->full_name,
                    'is_online' => true,
                ];

                cache()->put('courier_location_' . $user->id, $locationData, 35);
            }

            return response()->json([
                'success' => true,
                'message' => 'Location received',
                'timestamp' => now()->toDateTimeString(),
                'debug' => 'Database error occurred but location was cached',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get location status - Checks if courier is online/offline
     * Route: GET /courier/location/status
     */
    public function locationStatus()
    {
        $user = Auth::user();

        if (class_exists(CourierLocation::class)) {
            $location = CourierLocation::where('courier_id', $user->id)->first();
            $lastUpdate = $location ? $location->last_update : null;
            $isOnline = $location ? $location->is_online : false;
        } else {
            $lastUpdate = null;
            $isOnline = false;
        }

        return response()->json([
            'is_online' => $isOnline,
            'last_update' => $lastUpdate,
            'tracking_active' => cache()->has("courier_online_{$user->id}"),
        ]);
    }

    /**
     * Toggle location tracking - Enable/disable location tracking
     * Route: POST /courier/location/toggle
     */
    public function toggleLocation(Request $request)
    {
        $user = Auth::user();
        $isActive = $request->active;

        if ($isActive) {
            cache()->put("courier_online_{$user->id}", true, now()->addHours(24));

            if (class_exists(CourierLocation::class)) {
                CourierLocation::where('courier_id', $user->id)->update(['is_online' => true]);
            }
        } else {
            cache()->forget("courier_online_{$user->id}");

            if (class_exists(CourierLocation::class)) {
                CourierLocation::where('courier_id', $user->id)->update(['is_online' => false]);
            }
        }

        return response()->json([
            'success' => true,
            'tracking_active' => $isActive,
        ]);
    }

    /**
     * Show request details (HIPAA compliant view) - Main request details page
     * Route: GET /courier/requests/{request}
     * Linked from: Assignment list, notifications, dashboard
     */
    public function viewRequest($requestId)
    {
        // Get the request by ID instead of using route model binding
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        // Verify this request is assigned to the current courier
        if ($specimenRequest->assigned_to != Auth::id()) {
            abort(403, 'You are not assigned to this request.');
        }

        // Load necessary relationships
        $specimenRequest->load([
            'client',
            'pickupProof',
            'signature',
            'stops',
            'quote',
        ]);

        // Try to load location history if model exists
        if (class_exists(LocationHistory::class)) {
            $specimenRequest->load([
                'locationHistory' => function ($query) {
                    $query->where('courier_id', Auth::id())->orderBy('created_at');
                }
            ]);
        }

        // Get current location from cache first, then database
        $currentLocation = cache()->get('courier_location_' . Auth::id());

        // If not in cache, try database
        if (!$currentLocation && class_exists(CourierLocation::class)) {
            $dbLocation = CourierLocation::where('courier_id', Auth::id())->first();
            if ($dbLocation) {
                $currentLocation = [
                    'latitude' => $dbLocation->latitude,
                    'longitude' => $dbLocation->longitude,
                    'accuracy' => $dbLocation->accuracy,
                    'timestamp' => $dbLocation->last_update,
                    'last_update' => $dbLocation->last_update,
                    'speed' => $dbLocation->speed,
                    'heading' => $dbLocation->heading,
                    'altitude' => $dbLocation->altitude,
                    'is_online' => $dbLocation->is_online,
                ];
            }
        }

        // Calculate distance to pickup if location available and request has pickup coordinates
        $distanceToPickup = null;
        if ($currentLocation && $specimenRequest->pickup_latitude && $specimenRequest->pickup_longitude) {
            $distanceToPickup = $this->calculateDistance(
                $currentLocation['latitude'] ?? $currentLocation->latitude ?? 0,
                $currentLocation['longitude'] ?? $currentLocation->longitude ?? 0,
                $specimenRequest->pickup_latitude,
                $specimenRequest->pickup_longitude
            );
        }

        // If we have a location, prepare it for the view
        if ($currentLocation) {
            // Convert to object if it's an array
            if (is_array($currentLocation)) {
                $currentLocation = (object) $currentLocation;
            }

            // Ensure last_update is available as a Carbon instance
            if (isset($currentLocation->last_update)) {
                try {
                    $currentLocation->last_update = Carbon::parse($currentLocation->last_update);
                } catch (\Exception $e) {
                    $currentLocation->last_update = Carbon::now();
                }
            } elseif (isset($currentLocation->timestamp)) {
                try {
                    $currentLocation->last_update = Carbon::parse($currentLocation->timestamp);
                } catch (\Exception $e) {
                    $currentLocation->last_update = Carbon::now();
                }
            } else {
                $currentLocation->last_update = Carbon::now();
            }
        }

        return view('courier.requests.show', compact('specimenRequest', 'currentLocation', 'distanceToPickup'));
    }

    /**
     * Start pickup process - NOW REQUIRES PROOF - First step after accepting assignment
     * Route: POST /courier/requests/{request}/start-pickup
     * Linked from: "Start Pickup" button when status is 'accepted_by_courier'
     */
    public function startPickup(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'accepted_by_courier') {
            return redirect()->back()->with('error', 'Cannot start pickup from current status.');
        }

        // Mark that proof is required for pickup
        $specimenRequest->update([
            'status' => 'awaiting_pickup_proof',
            'requires_proof' => true,
            'proof_uploaded' => false,
            'proof_required_at_status' => 'picked_up',
            'pickup_started_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Pickup Started - Proof Required',
            'message' => "Courier is ready to pickup for request #{$specimenRequest->request_number}. Proof upload required.",
            'type' => 'pickup_started',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'start_pickup_requires_proof',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'accepted_by_courier to awaiting_pickup_proof',
                'requires_proof' => true,
                'pickup_started_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Please upload pickup proof to continue to next status.');
    }

    /**
     * Submit pickup proof with photo - REQUIRED BEFORE STATUS UPDATE
     * Route: POST /courier/requests/{request}/pickup-proof
     * Linked from: Pickup Proof Modal form (when proofType = 'pickup')
     */
    public function submitPickupProof(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        // Check if proof is required
        if (!$specimenRequest->requires_proof) {
            return redirect()->back()->with('error', 'Proof not required.');
        }

        if ($specimenRequest->proof_uploaded) {
            return redirect()->back()->with('error', 'Proof already uploaded.');
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
        PickupProof::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => Auth::id(),
            'photo_path' => $photoPath,
            'notes' => $request->pickup_notes,
            'specimen_condition' => $request->specimen_condition,
            'temperature_check' => $request->temperature_check,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'accuracy' => $request->accuracy ?? null,
            'verified' => false,
        ]);

        // Now update to the actual status since proof is uploaded
        $nextStatus = $specimenRequest->proof_required_at_status ?? 'picked_up';

        $specimenRequest->update([
            'status' => $nextStatus,
            'requires_proof' => false,
            'proof_uploaded' => true,
            'proof_required_at_status' => null,
            'pickup_completed_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Pickup Completed',
            'message' => "Specimen picked up for request #{$specimenRequest->request_number}. Proof uploaded and status updated.",
            'type' => 'pickup_completed',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submit_pickup_proof',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'awaiting_pickup_proof to ' . $nextStatus,
                'requires_proof' => 'false',
                'proof_uploaded' => 'true',
                'pickup_completed_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pickup proof submitted successfully! Status updated to ' . str_replace('_', ' ', $nextStatus) . '.');
    }

    /**
     * Start transit to delivery location - NOW REQUIRES PROOF - After pickup is complete
     * Route: POST /courier/requests/{request}/start-transit
     * Linked from: "Start Transit" button when status is 'picked_up'
     */
    public function startTransit(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'picked_up') {
            return redirect()->back()->with('error', 'Cannot start transit from current status.');
        }

        // Mark that proof is required for transit confirmation
        $specimenRequest->update([
            'status' => 'awaiting_transit_proof',
            'requires_proof' => true,
            'proof_uploaded' => false,
            'proof_required_at_status' => 'in_transit',
            'transit_started_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Transit Started - Proof Required',
            'message' => "Transit started for request #{$specimenRequest->request_number}. Proof upload required.",
            'type' => 'transit_started',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'start_transit_requires_proof',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'picked_up to awaiting_transit_proof',
                'requires_proof' => true,
                'transit_started_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Please upload transit proof to continue to next status.');
    }

    /**
     * Submit transit proof - Photo proof that specimen is in transit
     * Route: POST /courier/requests/{request}/transit-proof
     * Linked from: Transit Proof Modal form (when proofType = 'transit')
     */
    public function submitTransitProof(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        // Check if proof is required
        if (!$specimenRequest->requires_proof || $specimenRequest->proof_uploaded) {
            return redirect()->back()->with('error', 'Proof not required or already uploaded.');
        }

        $request->validate([
            'transit_photo' => 'required|image|max:5120',
            'temperature_check' => 'required|in:within_range,out_of_range,not_checked',
            'transit_notes' => 'nullable|string|max:500',
        ]);

        // Upload photo
        $photoPath = $request->file('transit_photo')->store('transit-proofs', 'public');

        // Create transit proof record (you might need to create this model)
        // For now, using pickup_proofs table with a type column
        PickupProof::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => Auth::id(),
            'photo_path' => $photoPath,
            'notes' => $request->transit_notes,
            'specimen_condition' => 'in_transit',
            'temperature_check' => $request->temperature_check,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'accuracy' => $request->accuracy ?? null,
            'verified' => false,
            'proof_type' => 'transit',
        ]);

        // Now update to the actual status since proof is uploaded
        $nextStatus = $specimenRequest->proof_required_at_status ?? 'in_transit';

        $specimenRequest->update([
            'status' => $nextStatus,
            'requires_proof' => false,
            'proof_uploaded' => true,
            'proof_required_at_status' => null,
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'In Transit',
            'message' => "Specimen #{$specimenRequest->request_number} is now in transit to delivery location. Proof uploaded.",
            'type' => 'in_transit',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submit_transit_proof',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'awaiting_transit_proof to ' . $nextStatus,
                'requires_proof' => 'false',
                'proof_uploaded' => 'true',
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Transit proof submitted successfully! Status updated to ' . str_replace('_', ' ', $nextStatus) . '.');
    }

    /**
     * Arrive at destination - NOW REQUIRES PROOF - When courier reaches delivery location
     * Route: POST /courier/requests/{request}/arrive-destination
     * Linked from: "Mark Arrival" button when status is 'in_transit'
     */
    public function arriveAtDestination(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status != 'in_transit') {
            return redirect()->back()->with('error', 'Cannot mark arrival from current status.');
        }

        // Mark that proof is required for arrival
        $specimenRequest->update([
            'status' => 'awaiting_arrival_proof',
            'requires_proof' => true,
            'proof_uploaded' => false,
            'proof_required_at_status' => 'arrived_at_destination',
            'arrived_at_destination_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Arrived at Destination - Proof Required',
            'message' => "Courier has arrived at delivery location for request #{$specimenRequest->request_number}. Proof upload required.",
            'type' => 'arrived_at_destination',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'arrive_destination_requires_proof',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'in_transit to awaiting_arrival_proof',
                'requires_proof' => true,
                'arrived_at_destination_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Arrival recorded. Please upload arrival proof to continue.');
    }

    /**
     * Submit delivery with signature - REQUIRED PROOF - Final delivery with recipient signature
     * Route: POST /courier/requests/{request}/submit-delivery
     * Linked from: Signature Modal form (delivery completion)
     */
    public function submitDelivery(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        // Check if proof is required (for arrival or direct delivery)
        if ($specimenRequest->requires_proof && !$specimenRequest->proof_uploaded) {
            // If still requiring proof, validate and upload
            $request->validate([
                'arrival_photo' => 'required|image|max:5120',
                'arrival_notes' => 'nullable|string|max:500',
            ]);

            // Upload arrival photo
            $photoPath = $request->file('arrival_photo')->store('arrival-proofs', 'public');

            // Create arrival proof
            PickupProof::create([
                'request_id' => $specimenRequest->id,
                'courier_id' => Auth::id(),
                'photo_path' => $photoPath,
                'notes' => $request->arrival_notes,
                'specimen_condition' => 'arrived',
                'temperature_check' => 'not_checked',
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
                'accuracy' => $request->accuracy ?? null,
                'verified' => false,
                'proof_type' => 'arrival',
            ]);

            // Update proof status
            $specimenRequest->update([
                'requires_proof' => false,
                'proof_uploaded' => true,
                'status' => 'arrived_at_destination',
                'proof_required_at_status' => null,
            ]);
        }

        // Now handle delivery signature
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
        Signature::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => Auth::id(),
            'recipient_name' => $request->recipient_name,
            'recipient_relationship' => $request->recipient_relationship,
            'signature_data' => $request->signature,
            'photo_path' => $photoPath,
            'notes' => $request->delivery_notes,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'accuracy' => $request->accuracy ?? null,
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
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => $specimenRequest->status . ' to delivered',
                'delivered_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Delivery submitted successfully! Please complete the request.');
    }

    /**
     * Complete request - Final step to mark request as completed
     * Route: POST /courier/requests/{request}/complete
     * Linked from: "Mark as Completed" button when status is 'delivered'
     */
    public function completeRequest(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
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
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'delivered to completed',
                'completed_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courier.assignments.index')
            ->with('success', 'Request completed successfully!');
    }

    /**
     * Get active request for API - Used by JavaScript for real-time updates
     * Route: GET /courier/active-request
     */
    public function getActiveRequest()
    {
        $activeRequest = Auth::user()->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit', 'arrived_at_destination', 'awaiting_pickup_proof', 'awaiting_transit_proof', 'awaiting_arrival_proof'])
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
                'requires_proof' => $activeRequest->requires_proof,
                'pickup_address' => $activeRequest->pickup_address,
                'delivery_address' => $activeRequest->delivery_address,
                'priority_level' => $activeRequest->priority_level,
            ]
        ]);
    }

    /**
     * Get navigation data - Provides coordinates for Google Maps navigation
     * Route: GET /courier/requests/{request}/navigation
     */
    public function getNavigation($requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        // Verify assignment
        if ($specimenRequest->assigned_to != Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        // Get current location from cache or database
        $currentLocation = cache()->get('courier_location_' . Auth::id());

        if (!$currentLocation && class_exists(CourierLocation::class)) {
            $dbLocation = CourierLocation::where('courier_id', Auth::id())->first();
            if ($dbLocation) {
                $currentLocation = [
                    'latitude' => $dbLocation->latitude,
                    'longitude' => $dbLocation->longitude,
                ];
            }
        }

        if (!$currentLocation) {
            return response()->json(['error' => 'Location not available'], 400);
        }

        // Use coordinates from either cache or database format
        $fromLat = $currentLocation['latitude'] ?? $currentLocation->latitude ?? 0;
        $fromLng = $currentLocation['longitude'] ?? $currentLocation->longitude ?? 0;

        // Calculate distance and estimated time
        $distance = $this->calculateDistance(
            $fromLat,
            $fromLng,
            $specimenRequest->pickup_latitude,
            $specimenRequest->pickup_longitude
        );

        $estimatedTime = $this->calculateEstimatedTime($distance);

        return response()->json([
            'from_lat' => $fromLat,
            'from_lng' => $fromLng,
            'to_lat' => $specimenRequest->pickup_latitude,
            'to_lng' => $specimenRequest->pickup_longitude,
            'distance_km' => round($distance, 2),
            'estimated_minutes' => $estimatedTime,
            'google_maps_url' => "https://www.google.com/maps/dir/{$fromLat},{$fromLng}/{$specimenRequest->pickup_latitude},{$specimenRequest->pickup_longitude}",
        ]);
    }

    /**
     * Get location history for a request - GPS tracking history
     * Route: GET /courier/requests/{request}/location-history
     */
    public function getLocationHistory($requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        // Verify assignment
        if ($specimenRequest->assigned_to != Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        // Check if LocationHistory model exists
        if (!class_exists(LocationHistory::class)) {
            return response()->json(['error' => 'Location history not available'], 400);
        }

        $history = LocationHistory::where('request_id', $specimenRequest->id)
            ->where('courier_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get(['latitude', 'longitude', 'created_at', 'speed', 'heading']);

        return response()->json($history);
    }

    /**
     * Calculate distance between two coordinates in kilometers - Utility function
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
     * Calculate estimated travel time in minutes - Utility function
     */
    private function calculateEstimatedTime($distanceKm)
    {
        // Average speed: 40 km/h in city traffic
        $averageSpeed = 40;
        $timeHours = $distanceKm / $averageSpeed;
        return round($timeHours * 60); // Convert to minutes
    }

    /**
     * Show active pickups - List of pickups in progress
     * Route: GET /courier/active-pickups
     */
    public function activePickups()
    {
        $activePickups = Auth::user()->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'awaiting_pickup_proof'])
            ->with(['client', 'pickupProof'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_pickup_time')
            ->paginate(10);

        return view('courier.assignments.active-pickups', compact('activePickups'));
    }

    /**
     * Show active deliveries - List of deliveries in transit
     * Route: GET /courier/active-deliveries
     */
    public function activeDeliveries()
    {
        $activeDeliveries = Auth::user()->assignedRequests()
            ->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination', 'awaiting_transit_proof', 'awaiting_arrival_proof'])
            ->with(['client', 'pickupProof', 'signature'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('scheduled_delivery_time')
            ->paginate(10);

        return view('courier.assignments.active-deliveries', compact('activeDeliveries'));
    }

    /**
     * Show delivery history - Completed deliveries
     * Route: GET /courier/history
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
     * Show proofs - All uploaded proofs gallery
     * Route: GET /courier/proofs
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
     * Show specific proof - Detailed view of a single proof
     * Route: GET /courier/proofs/{id}/{type}
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
     * Show courier profile - Personal profile page
     * Route: GET /courier/profile
     */
    public function profile()
    {
        $user = Auth::user();
        $stats = [
            'total_deliveries' => $user->assignedRequests()->where('status', 'completed')->count(),
            'on_time_rate' => $this->calculateOnTimeRate($user),
            'avg_rating' => 4.8,
            'proofs_uploaded' => PickupProof::where('courier_id', Auth::id())->count() + Signature::where('courier_id', Auth::id())->count(),
            'total_earnings' => CourierQuote::where('courier_id', Auth::id())
                ->where('status', 'accepted')
                ->sum('courier_fee'),
        ];

        return view('courier.profile', compact('user', 'stats'));
    }

    /**
     * Update courier profile - Save profile changes
     * Route: POST /courier/profile/update
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
     * Show notifications - Courier notification center
     * Route: GET /courier/notifications
     */
    public function notifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('courier.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read - Single notification read
     * Route: POST /courier/notifications/{notification}/read
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
     * Mark all notifications as read - Bulk mark as read
     * Route: POST /courier/notifications/mark-all-read
     */
    public function markAllNotificationsAsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Calculate on-time delivery rate - Statistics utility
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

    /**
     * Get courier location for API (used by client tracking) - Public API for clients
     * Route: GET /api/courier/{courierId}/location
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
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'accuracy' => $location->accuracy,
                    'speed' => $location->speed,
                    'heading' => $location->heading,
                    'altitude' => $location->altitude,
                    'battery_level' => $location->battery_level,
                    'is_online' => $location->is_online,
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

        return response()->json([
            'courier' => [
                'id' => $courier->id,
                'name' => $courier->full_name,
                'phone' => $courier->phone,
                'vehicle_type' => $courier->vehicle_type,
                'profile_image' => $courier->profile_image,
            ],
            'location' => $cachedLocation,
            'status' => ($cachedLocation['is_online'] ?? false) ? 'online' : 'offline',
        ]);
    }

    /**
     * Emergency skip proof (admin/courier override) - Bypass proof requirement
     * Route: POST /courier/requests/{request}/skip-proof
     * Linked from: "Skip Proof" button (admin/supervisor only)
     */
    public function skipProofRequirement(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (!$specimenRequest->requires_proof) {
            return redirect()->back()->with('error', 'No proof requirement to skip.');
        }

        // Get the target status
        $targetStatus = $specimenRequest->proof_required_at_status;

        if (!$targetStatus) {
            // Determine target status based on current status
            switch ($specimenRequest->status) {
                case 'awaiting_pickup_proof':
                    $targetStatus = 'picked_up';
                    break;
                case 'awaiting_transit_proof':
                    $targetStatus = 'in_transit';
                    break;
                case 'awaiting_arrival_proof':
                    $targetStatus = 'arrived_at_destination';
                    break;
                default:
                    $targetStatus = $specimenRequest->status;
            }
        }

        // Update without proof
        $specimenRequest->update([
            'status' => $targetStatus,
            'requires_proof' => false,
            'proof_uploaded' => false,
            'proof_required_at_status' => null,
        ]);

        // Log audit for skipping proof
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'skip_proof_requirement',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => $specimenRequest->status . ' to ' . $targetStatus . ' (proof skipped)',
                'requires_proof' => 'false',
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('warning', 'Proof requirement skipped. Status updated to ' . str_replace('_', ' ', $targetStatus) . '. Please note this action has been logged.');
    }

    /**
     * Submit arrival proof - Photo proof of arrival at destination
     * Route: POST /courier/requests/{request}/arrival-proof
     * Linked from: Arrival Proof Modal form (when proofType = 'arrival')
     */
    public function submitArrivalProof(Request $request, $requestId)
    {
        // Get the request by ID
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        // Check if proof is required
        if (!$specimenRequest->requires_proof) {
            return redirect()->back()->with('error', 'Proof not required.');
        }

        if ($specimenRequest->proof_uploaded) {
            return redirect()->back()->with('error', 'Proof already uploaded.');
        }

        $request->validate([
            'arrival_photo' => 'required|image|max:5120',
            'arrival_notes' => 'nullable|string|max:500',
            'temperature_check' => 'required|in:within_range,out_of_range,not_checked',
        ]);

        // Upload photo
        $photoPath = $request->file('arrival_photo')->store('arrival-proofs', 'public');

        // Create arrival proof record
        PickupProof::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => Auth::id(),
            'photo_path' => $photoPath,
            'notes' => $request->arrival_notes,
            'specimen_condition' => 'arrived',
            'temperature_check' => $request->temperature_check,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'accuracy' => $request->accuracy ?? null,
            'verified' => false,
            'proof_type' => 'arrival',
        ]);

        // Now update to the actual status since proof is uploaded
        $nextStatus = $specimenRequest->proof_required_at_status ?? 'arrived_at_destination';

        $specimenRequest->update([
            'status' => $nextStatus,
            'requires_proof' => false,
            'proof_uploaded' => true,
            'proof_required_at_status' => null,
            'arrived_at_destination_at' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'title' => 'Arrived at Destination',
            'message' => "Courier has arrived at delivery location for request #{$specimenRequest->request_number}. Proof uploaded.",
            'type' => 'arrived_at_destination',
            'is_read' => false,
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submit_arrival_proof',
            'model_type' => SpecimenRequest::class,
            'model_id' => $specimenRequest->id,
            'changes' => json_encode([
                'status' => 'awaiting_arrival_proof to ' . $nextStatus,
                'requires_proof' => 'false',
                'proof_uploaded' => 'true',
                'arrived_at_destination_at' => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Arrival proof submitted successfully! Status updated to ' . str_replace('_', ' ', $nextStatus) . '.');
    }

    /**
     * Test route for debugging location saving - Development/testing only
     * Route: GET /courier/test-location-save
     */
    public function testLocationSave(Request $request)
    {
        try {
            \DB::enableQueryLog();

            $user = Auth::user();
            $location = CourierLocation::create([
                'courier_id' => $user->id,
                'request_id' => null, // Testing with null first
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'accuracy' => 10,
                'speed' => 0,
                'heading' => 0,
                'altitude' => 0,
                'is_online' => true,
                'last_update' => now(),
                'battery_level' => 80,
            ]);

            $queries = \DB::getQueryLog();

            return response()->json([
                'success' => true,
                'location_id' => $location->id,
                'queries' => $queries,
                'table_structure' => \DB::select("DESCRIBE courier_locations")
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'queries' => \DB::getQueryLog(),
                'table_structure' => \DB::select("DESCRIBE courier_locations")
            ]);
        }
    }

    /**
     * Get courier location for specific request - API for specific request tracking
     * Route: GET /courier/requests/{request}/location
     */
    public function getCourierLocationForRequest($requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        // Verify assignment
        if ($specimenRequest->assigned_to != Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        // Get location from cache first
        $cachedLocation = cache()->get('courier_location_' . Auth::id());

        // If not in cache, check database
        if (!$cachedLocation && class_exists(CourierLocation::class)) {
            $location = CourierLocation::where('courier_id', Auth::id())->first();
            if ($location) {
                $cachedLocation = [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'accuracy' => $location->accuracy,
                    'speed' => $location->speed,
                    'heading' => $location->heading,
                    'altitude' => $location->altitude,
                    'timestamp' => $location->created_at->timestamp,
                ];
            }
        }

        if (!$cachedLocation) {
            return response()->json([
                'courier' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->full_name,
                ],
                'location' => null,
                'status' => 'offline',
            ]);
        }

        return response()->json([
            'courier' => [
                'id' => Auth::id(),
                'name' => Auth::user()->full_name,
            ],
            'location' => $cachedLocation,
            'status' => 'online',
        ]);
    }
}