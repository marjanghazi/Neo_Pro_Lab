<?php

namespace App\Http\Controllers\Courier; // ← Note: Courier with capital C

use App\Http\Controllers\Controller; // ← Add this
use App\Models\SpecimenRequest;
use App\Models\PickupProof;
use App\Models\Signature;
use App\Models\CourierLocation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class CourierController extends Controller
{
    /**
     * Display courier dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_assigned' => $user->assignedRequests()->count(),
            'pending_acceptance' => $user->assignedRequests()->where('status', 'assigned')->count(),
            'in_progress' => $user->assignedRequests()
                ->whereIn('status', ['accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])
                ->count(),
            'completed' => $user->assignedRequests()->where('status', 'completed')->count(),
        ];

        $activeRequests = $user->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])
            ->orderBy('priority_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('courier.dashboard', compact('stats', 'activeRequests'));
    }

    /**
     * Display assignments
     */
    public function assignments(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->assignedRequests()
            ->with('facility')
            ->orderBy('priority_level', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->paginate(10);

        return view('courier.assignments.index', compact('assignments'));
    }

    /**
     * Accept assignment
     */
    public function acceptAssignment(Request $request, SpecimenRequest $specimenRequest)
    {
        // Verify the request is assigned to this courier
        if ($specimenRequest->assigned_to !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if request can be accepted
        if ($specimenRequest->status !== 'assigned') {
            return back()->with('error', 'Request cannot be accepted.');
        }

        // Update request status
        $specimenRequest->update([
            'status' => 'accepted_by_courier',
            'accepted_at' => now(),
        ]);

        // Create notification for client
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'request_id' => $specimenRequest->id,
            'type' => 'request_accepted',
            'title' => 'Courier Accepted Request',
            'message' => 'Courier ' . auth()->user()->full_name . ' has accepted your request.',
            'data' => json_encode(['courier_name' => auth()->user()->full_name]),
        ]);

        // Create notification for admin
        $admins = \App\Models\User::whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'request_id' => $specimenRequest->id,
                'type' => 'request_accepted',
                'title' => 'Courier Accepted Request',
                'message' => 'Courier ' . auth()->user()->full_name . ' has accepted request ' . $specimenRequest->request_number,
            ]);
        }

        return back()->with('success', 'Assignment accepted successfully.');
    }

    /**
     * Update courier location
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'request_id' => 'nullable|exists:specimen_requests,id',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'altitude' => 'nullable|numeric',
            'battery_level' => 'nullable|integer|min:0|max:100',
        ]);

        $user = auth()->user();

        // Get current active request for this courier
        $activeRequest = SpecimenRequest::where('assigned_to', $user->id)
            ->whereIn('status', ['accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])
            ->first();

        // Update current location
        $location = CourierLocation::create([
            'courier_id' => $user->id,
            'request_id' => $activeRequest?->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'altitude' => $request->altitude,
            'battery_level' => $request->battery_level,
            'is_online' => true,
        ]);

        // Also store in history
        \App\Models\LocationHistory::create([
            'courier_id' => $user->id,
            'request_id' => $activeRequest?->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Mark courier as online
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully.',
            'location' => $location,
        ]);
    }

    /**
     * Start pickup process
     */
    public function startPickup(Request $request, SpecimenRequest $specimenRequest)
    {
        // Verify the request is assigned to this courier and in correct status
        if ($specimenRequest->assigned_to !== auth()->id() || 
            $specimenRequest->status !== 'accepted_by_courier') {
            return back()->with('error', 'Cannot start pickup at this time.');
        }

        $specimenRequest->update([
            'status' => 'at_stop',
            'estimated_pickup_time' => now(),
        ]);

        return back()->with('success', 'Pickup started. Proceed to the pickup location.');
    }

    /**
     * Submit pickup proof
     */
    public function submitPickupProof(Request $request, SpecimenRequest $specimenRequest)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'notes' => 'nullable|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Verify the request is assigned to this courier and in correct status
        if ($specimenRequest->assigned_to !== auth()->id() || 
            !in_array($specimenRequest->status, ['at_stop', 'accepted_by_courier'])) {
            return back()->with('error', 'Cannot submit pickup proof at this time.');
        }

        // Upload image
        $imagePath = $request->file('image')->store('pickup-proofs', 'public');

        // Create pickup proof
        PickupProof::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => auth()->id(),
            'proof_type' => 'pickup',
            'image_path' => $imagePath,
            'notes' => $request->notes,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Update request status
        $specimenRequest->update([
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);

        // Create notification for client
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'request_id' => $specimenRequest->id,
            'type' => 'pickup_completed',
            'title' => 'Pickup Completed',
            'message' => 'Specimen has been picked up and is now in transit.',
        ]);

        return back()->with('success', 'Pickup proof submitted successfully. Specimen is now in transit.');
    }

    /**
     * Start delivery/transit
     */
    public function startTransit(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to !== auth()->id() || 
            $specimenRequest->status !== 'picked_up') {
            return back()->with('error', 'Cannot start transit at this time.');
        }

        $specimenRequest->update([
            'status' => 'in_transit',
        ]);

        return back()->with('success', 'Delivery started. Proceed to the delivery location.');
    }

    /**
     * Arrive at destination
     */
    public function arriveAtDestination(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to !== auth()->id() || 
            $specimenRequest->status !== 'in_transit') {
            return back()->with('error', 'Cannot mark as arrived at this time.');
        }

        $specimenRequest->update([
            'status' => 'arrived_at_destination',
        ]);

        return back()->with('success', 'Arrived at destination. Ready for delivery.');
    }

    /**
     * Submit delivery proof and get signature
     */
    public function submitDelivery(Request $request, SpecimenRequest $specimenRequest)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'signature_data' => 'required|string',
            'recipient_name' => 'required|string|max:200',
            'notes' => 'nullable|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($specimenRequest->assigned_to !== auth()->id() || 
            !in_array($specimenRequest->status, ['arrived_at_destination', 'in_transit'])) {
            return back()->with('error', 'Cannot complete delivery at this time.');
        }

        // Upload delivery proof image
        $imagePath = $request->file('image')->store('delivery-proofs', 'public');

        // Create delivery proof
        PickupProof::create([
            'request_id' => $specimenRequest->id,
            'courier_id' => auth()->id(),
            'proof_type' => 'delivery',
            'image_path' => $imagePath,
            'notes' => $request->notes,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Save signature
        $signature = Signature::create([
            'request_id' => $specimenRequest->id,
            'signature_type' => 'delivery',
            'signed_by' => auth()->id(),
            'recipient_name' => $request->recipient_name,
            'signature_data' => $request->signature_data,
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Update request status
        $specimenRequest->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Create notification for client
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'request_id' => $specimenRequest->id,
            'type' => 'delivery_completed',
            'title' => 'Delivery Completed',
            'message' => 'Specimen has been delivered. Please verify the delivery.',
        ]);

        return back()->with('success', 'Delivery completed successfully with signature.');
    }

    /**
     * Complete request
     */
    public function completeRequest(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to !== auth()->id() || 
            $specimenRequest->status !== 'delivered') {
            return back()->with('error', 'Cannot complete request at this time.');
        }

        $specimenRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_duration' => Carbon::parse($specimenRequest->picked_up_at)->diffInMinutes(now()),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $specimenRequest->client_id,
            'request_id' => $specimenRequest->id,
            'type' => 'request_completed',
            'title' => 'Request Completed',
            'message' => 'The specimen transport request has been completed successfully.',
        ]);

        return back()->with('success', 'Request completed successfully.');
    }

    /**
     * View request details (HIPAA compliant - limited information)
     */
    public function viewRequest(SpecimenRequest $specimenRequest)
    {
        // Verify the request is assigned to this courier
        if ($specimenRequest->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        // For HIPAA compliance, limit information shown to courier
        $limitedData = [
            'request_number' => $specimenRequest->request_number,
            'pickup_address' => $specimenRequest->pickup_address,
            'delivery_address' => $specimenRequest->delivery_address,
            'specimen_type' => $specimenRequest->specimen_type,
            'temperature_requirement' => $specimenRequest->temperature_requirement,
            'priority_level' => $specimenRequest->priority_level,
            'status' => $specimenRequest->status,
            'instructions' => $specimenRequest->delivery_instructions,
            'stops' => $specimenRequest->stops,
            'container_type' => $specimenRequest->container_type,
            'quantity' => $specimenRequest->quantity,
        ];

        return view('courier.requests.show', compact('limitedData', 'specimenRequest'));
    }

    /**
     * Get current active request
     */
    public function getActiveRequest()
    {
        $activeRequest = SpecimenRequest::where('assigned_to', auth()->id())
            ->whereIn('status', ['accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery', 'arrived_at_destination'])
            ->first();

        if (!$activeRequest) {
            return response()->json(['active' => false]);
        }

        // HIPAA compliant data only
        $data = [
            'active' => true,
            'request_number' => $activeRequest->request_number,
            'pickup_address' => $activeRequest->pickup_address,
            'delivery_address' => $activeRequest->delivery_address,
            'status' => $activeRequest->status,
            'latitude' => $activeRequest->pickup_latitude,
            'longitude' => $activeRequest->pickup_longitude,
            'delivery_latitude' => $activeRequest->delivery_latitude,
            'delivery_longitude' => $activeRequest->delivery_longitude,
        ];

        return response()->json($data);
    }

    /**
     * Get navigation for request
     */
    public function getNavigation(SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $navigation = [
            'pickup' => [
                'address' => $specimenRequest->pickup_address,
                'latitude' => $specimenRequest->pickup_latitude,
                'longitude' => $specimenRequest->pickup_longitude,
            ],
            'delivery' => [
                'address' => $specimenRequest->delivery_address,
                'latitude' => $specimenRequest->delivery_latitude,
                'longitude' => $specimenRequest->delivery_longitude,
            ],
        ];

        // Add stops if any
        if ($specimenRequest->stops->count() > 0) {
            $navigation['stops'] = $specimenRequest->stops->map(function($stop) {
                return [
                    'address' => $stop->address,
                    'latitude' => $stop->latitude,
                    'longitude' => $stop->longitude,
                    'stop_type' => $stop->stop_type,
                    'instructions' => $stop->instructions,
                ];
            });
        }

        return response()->json($navigation);
    }
}