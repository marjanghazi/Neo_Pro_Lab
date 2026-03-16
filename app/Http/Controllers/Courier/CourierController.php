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
use App\Models\CourierQuote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CourierController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_assignments'  => $user->assignedRequests()->count(),
            'pending'            => $user->assignedRequests()->where('status', 'assigned')->count(),
            'pending_acceptance' => $user->assignedRequests()->where('status', 'pending_courier_acceptance')->count(),
            'in_progress'        => $user->assignedRequests()->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit'])->count(),
            'completed'          => $user->assignedRequests()->where('status', 'completed')->count(),
            'today_pickups'      => $user->assignedRequests()
                ->whereDate('scheduled_pickup_time', Carbon::today())
                ->whereIn('status', ['assigned', 'accepted_by_courier', 'pending_courier_acceptance'])
                ->count(),
            'today_deliveries'   => $user->assignedRequests()
                ->whereDate('scheduled_delivery_time', Carbon::today())
                ->whereIn('status', ['picked_up', 'in_transit'])
                ->count(),
        ];

        $activeRequests = $user->assignedRequests()
            ->whereIn('status', [
                'accepted_by_courier', 'picked_up', 'in_transit',
                'awaiting_pickup_proof', 'awaiting_transit_proof',
                'awaiting_arrival_proof', 'arrived_at_destination',
            ])
            ->orderByRaw("FIELD(priority_level,'stat','routine','scheduled')")
            ->orderBy('scheduled_delivery_time')
            ->limit(5)
            ->get();

        $todaysSchedule = $user->assignedRequests()
            ->where(function ($q) {
                $q->whereDate('scheduled_pickup_time', Carbon::today())
                  ->orWhereDate('scheduled_delivery_time', Carbon::today());
            })
            ->orderBy('scheduled_pickup_time')
            ->get();

        $recentCompletions = $user->assignedRequests()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Pending quotes waiting for this courier's response
        $pendingQuotes = CourierQuote::where('courier_id', $user->id)
            ->where('status', 'pending')
            ->where('valid_until', '>', now())
            ->with(['request'])
            ->orderBy('valid_until')
            ->limit(5)
            ->get();

        return view('courier.dashboard', compact(
            'stats', 'activeRequests', 'todaysSchedule', 'recentCompletions', 'pendingQuotes'
        ));
    }

    // ─── Assignments List ─────────────────────────────────────────────────────

    public function assignments(Request $request)
    {
        $user  = Auth::user();
        $query = $user->assignedRequests()->with(['client', 'pickupProof', 'signature']);

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'awaiting_proof') {
                $query->where('requires_proof', true);
            } elseif ($request->status === 'pending_acceptance') {
                $query->where('status', 'pending_courier_acceptance')
                      ->where('courier_can_accept', true);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority_level', $request->priority);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('scheduled_pickup_time', $request->date);
        }

        $assignments = $query
            ->orderByRaw("FIELD(priority_level,'stat','routine','scheduled')")
            ->orderBy('scheduled_pickup_time')
            ->paginate(10);

        $statusCounts = [
            'total'                  => $user->assignedRequests()->count(),
            'pending_acceptance'     => $user->assignedRequests()->where('status', 'pending_courier_acceptance')->count(),
            'assigned'               => $user->assignedRequests()->where('status', 'assigned')->count(),
            'accepted_by_courier'    => $user->assignedRequests()->where('status', 'accepted_by_courier')->count(),
            'awaiting_pickup_proof'  => $user->assignedRequests()->where('status', 'awaiting_pickup_proof')->count(),
            'picked_up'              => $user->assignedRequests()->where('status', 'picked_up')->count(),
            'in_transit'             => $user->assignedRequests()->where('status', 'in_transit')->count(),
            'delivered'              => $user->assignedRequests()->where('status', 'delivered')->count(),
            'completed'              => $user->assignedRequests()->where('status', 'completed')->count(),
        ];

        return view('courier.assignments.index', compact('assignments', 'statusCounts'));
    }

    // ─── View Quote ───────────────────────────────────────────────────────────

    /**
     * Show the quote acceptance page for a specific request.
     * Route: GET /courier/requests/{requestId}/quote
     */
    public function viewQuote($requestId)
    {
        $specimenRequest = SpecimenRequest::with(['client', 'facility', 'stops'])->findOrFail($requestId);

        if ($specimenRequest->assigned_to !== Auth::id()) {
            abort(403, 'You are not assigned to this request.');
        }

        // Get the latest pending (or most recent) quote for this courier
        $quote = CourierQuote::where('request_id', $requestId)
            ->where('courier_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->firstOrFail();

        return view('courier.quote-acceptance', [
            'request' => $specimenRequest,
            'quote'   => $quote,
        ]);
    }

    // ─── Accept Quote ─────────────────────────────────────────────────────────

    /**
     * Courier accepts the price quote → request moves to 'assigned'.
     * Route: POST /courier/requests/{requestId}/accept-quote
     */
    public function acceptQuote(Request $httpRequest, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to !== Auth::id()) {
            return $this->errorResponse($httpRequest, 'You are not assigned to this request.');
        }

        if (! $specimenRequest->courier_can_accept) {
            return $this->errorResponse($httpRequest, 'This request is not currently awaiting your acceptance.');
        }

        if ($specimenRequest->acceptance_deadline && now()->gt($specimenRequest->acceptance_deadline)) {
            return $this->errorResponse($httpRequest, 'The acceptance deadline has passed. Please contact admin.');
        }

        if ($specimenRequest->status !== 'pending_courier_acceptance') {
            return $this->errorResponse($httpRequest, 'This request cannot be accepted from its current status: ' . $specimenRequest->status);
        }

        $quote = CourierQuote::where('request_id', $requestId)
            ->where('courier_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (! $quote) {
            return $this->errorResponse($httpRequest, 'No valid quote found for this request.');
        }

        if ($quote->isExpired()) {
            $quote->expire();
            $specimenRequest->update([
                'status'              => 'approved',
                'assigned_to'         => null,
                'assigned_by'         => null,
                'assigned_at'         => null,
                'courier_can_accept'  => false,
                'acceptance_deadline' => null,
            ]);
            return $this->errorResponse($httpRequest, 'The quote has expired. Admin has been notified. Please contact admin for reassignment.');
        }

        // Accept the quote
        $quote->accept();

        // Move request to fully 'assigned' — courier can now start work
        $specimenRequest->update([
            'status'              => 'assigned',
            'courier_accepted_at' => now(),
            'courier_can_accept'  => false,
        ]);

        // Notify admin
        $this->notifyAdminsAboutQuoteResponse($specimenRequest, 'accepted', $quote);

        // Audit
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'accepted_quote',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode([
                'status'              => 'pending_courier_acceptance → assigned',
                'courier_accepted_at' => now()->toDateTimeString(),
                'quote_id'            => $quote->id,
                'request_number'      => $specimenRequest->request_number,
            ]),
            'ip_address' => $httpRequest->ip(),
            'user_agent' => $httpRequest->userAgent(),
        ]);

        return redirect()->route('courier.requests.show', $specimenRequest->id)
            ->with('success', 'You have accepted the quote and assignment. You can now start the pickup process.');
    }

    // ─── Decline Quote ────────────────────────────────────────────────────────

    /**
     * Courier declines the price quote → request goes back to 'approved'.
     * Route: POST /courier/requests/{requestId}/decline-quote
     */
    public function declineQuote(Request $httpRequest, $requestId)
    {
        $validated = $httpRequest->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to !== Auth::id()) {
            return $this->errorResponse($httpRequest, 'You are not assigned to this request.');
        }

        if ($specimenRequest->status !== 'pending_courier_acceptance') {
            return $this->errorResponse($httpRequest, 'This request is not currently awaiting your acceptance.');
        }

        $quote = CourierQuote::where('request_id', $requestId)
            ->where('courier_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (! $quote) {
            return $this->errorResponse($httpRequest, 'No pending quote found for this request.');
        }

        // Decline the quote
        $quote->decline($validated['reason']);

        // Reset request so admin can reassign
        $specimenRequest->update([
            'status'               => 'approved',
            'assigned_to'          => null,
            'assigned_by'          => null,
            'assigned_at'          => null,
            'courier_declined_at'  => now(),
            'courier_decline_reason' => $validated['reason'],
            'courier_can_accept'   => false,
            'courier_quote_id'     => null,
            'acceptance_deadline'  => null,
        ]);

        // Notify admin so they can reassign
        $this->notifyAdminsAboutQuoteResponse($specimenRequest, 'declined', $quote, $validated['reason']);

        // Audit
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'declined_quote',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode([
                'status'               => 'pending_courier_acceptance → approved',
                'courier_declined_at'  => now()->toDateTimeString(),
                'decline_reason'       => $validated['reason'],
                'quote_id'             => $quote->id,
                'request_number'       => $specimenRequest->request_number,
            ]),
            'ip_address' => $httpRequest->ip(),
            'user_agent' => $httpRequest->userAgent(),
        ]);

        return redirect()->route('courier.assignments.index')
            ->with('info', 'You have declined the quote. Admin has been notified and will reassign the request.');
    }

    // ─── Accept Assignment (direct, no quote) ─────────────────────────────────

    public function acceptAssignment(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::find($requestId);

        if (! $specimenRequest) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'This assignment is not assigned to you.');
        }

        if ($specimenRequest->status !== 'assigned') {
            return redirect()->back()->with('error', 'This assignment cannot be accepted in its current status: ' . $specimenRequest->status);
        }

        $specimenRequest->update([
            'status'        => 'accepted_by_courier',
            'accepted_at'   => now(),
            'requires_proof'=> false,
            'proof_uploaded'=> false,
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'accepted_assignment',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode([
                'status'         => 'assigned → accepted_by_courier',
                'accepted_at'    => now()->toDateTimeString(),
                'request_number' => $specimenRequest->request_number,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->startLocationTracking($specimenRequest);

        return redirect()->route('courier.requests.show', $specimenRequest->id)
            ->with('success', 'Assignment accepted! Location tracking enabled. Proceed to pickup.');
    }

    // ─── View Request ─────────────────────────────────────────────────────────

    public function viewRequest($requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            abort(403, 'You are not assigned to this request.');
        }

        $specimenRequest->load(['client', 'pickupProof', 'signature', 'stops', 'quote']);

        if (class_exists(LocationHistory::class)) {
            $specimenRequest->load([
                'locationHistory' => fn ($q) => $q->where('courier_id', Auth::id())->orderBy('created_at'),
            ]);
        }

        $currentLocation = $this->getCurrentLocation();

        $distanceToPickup = null;
        if ($currentLocation && $specimenRequest->pickup_latitude) {
            $distanceToPickup = $this->calculateDistance(
                $currentLocation->latitude,
                $currentLocation->longitude,
                $specimenRequest->pickup_latitude,
                $specimenRequest->pickup_longitude
            );
        }

        return view('courier.requests.show', compact('specimenRequest', 'currentLocation', 'distanceToPickup'));
    }

    // ─── Start Pickup ─────────────────────────────────────────────────────────

    public function startPickup(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status !== 'accepted_by_courier') {
            return redirect()->back()->with('error', 'Cannot start pickup from current status: ' . $specimenRequest->status);
        }

        $specimenRequest->update([
            'status'                   => 'awaiting_pickup_proof',
            'requires_proof'           => true,
            'proof_uploaded'           => false,
            'proof_required_at_status' => 'picked_up',
            'pickup_started_at'        => now(),
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'start_pickup_requires_proof',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode([
                'status'            => 'accepted_by_courier → awaiting_pickup_proof',
                'requires_proof'    => true,
                'pickup_started_at' => now()->toDateTimeString(),
                'request_number'    => $specimenRequest->request_number,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pickup started! Please upload pickup proof photo to continue.');
    }

    // ─── Submit Pickup Proof ──────────────────────────────────────────────────

    public function submitPickupProof(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->route('courier.requests.show', $requestId)
                ->with('error', 'You are not assigned to this request.');
        }

        // Allow from accepted_by_courier OR awaiting_pickup_proof
        if (! in_array($specimenRequest->status, ['accepted_by_courier', 'awaiting_pickup_proof'])) {
            return redirect()->route('courier.requests.show', $requestId)
                ->with('error', 'Pickup proof cannot be submitted from current status: ' . str_replace('_', ' ', $specimenRequest->status));
        }

        // Block if already has a pickup proof
        $existingProof = PickupProof::where('request_id', $specimenRequest->id)
            ->where(function($q) {
                $q->whereNull('proof_type')->orWhere('proof_type', 'pickup');
            })->first();

        if ($existingProof) {
            return redirect()->route('courier.requests.show', $requestId)
                ->with('error', 'Pickup proof already uploaded for this request.');
        }

        $validated = $request->validate([
            'pickup_photo'       => 'required|image|max:5120',
            'pickup_notes'       => 'nullable|string|max:500',
            'specimen_condition' => 'required|in:good,acceptable,damaged',
            'temperature_check'  => 'required|in:within_range,out_of_range,not_checked',
        ]);

        $photoPath = $request->file('pickup_photo')->store('pickup-proofs', 'public');

        PickupProof::create([
            'request_id'         => $specimenRequest->id,
            'courier_id'         => Auth::id(),
            'proof_type'         => 'pickup',
            'photo_path'         => $photoPath,
            'notes'              => $request->pickup_notes,
            'specimen_condition' => $request->specimen_condition,
            'temperature_check'  => $request->temperature_check,
            'latitude'           => $request->latitude ?? null,
            'longitude'          => $request->longitude ?? null,
            'accuracy'           => $request->accuracy ?? null,
            'verified'           => false,
        ]);

        $specimenRequest->update([
            'status'                   => 'picked_up',
            'requires_proof'           => false,
            'proof_uploaded'           => true,
            'proof_required_at_status' => null,
            'pickup_completed_at'      => now(),
            'pickup_started_at'        => $specimenRequest->pickup_started_at ?? now(),
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'submit_pickup_proof',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode([
                'status'         => $specimenRequest->getOriginal('status') . ' → picked_up',
                'request_number' => $specimenRequest->request_number,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courier.requests.show', $requestId)
            ->with('success', 'Pickup proof uploaded! Status updated to Picked Up.');
    }

    // ─── Start Transit ────────────────────────────────────────────────────────

    public function startTransit(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status !== 'picked_up') {
            return redirect()->back()->with('error', 'Cannot start transit from current status: ' . $specimenRequest->status);
        }

        $specimenRequest->update([
            'status'             => 'in_transit',
            'requires_proof'     => false,
            'transit_started_at' => now(),
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'start_transit',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode(['status' => 'picked_up → in_transit', 'request_number' => $specimenRequest->request_number]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Transit started! Head to the delivery location.');
    }

    // ─── Submit Transit Proof ─────────────────────────────────────────────────

    public function submitTransitProof(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (! $specimenRequest->requires_proof || $specimenRequest->proof_uploaded) {
            return redirect()->back()->with('error', 'Proof not required or already uploaded.');
        }

        $request->validate([
            'transit_photo'    => 'required|image|max:5120',
            'temperature_check'=> 'required|in:within_range,out_of_range,not_checked',
            'transit_notes'    => 'nullable|string|max:500',
        ]);

        $photoPath = $request->file('transit_photo')->store('transit-proofs', 'public');

        PickupProof::create([
            'request_id'        => $specimenRequest->id,
            'courier_id'        => Auth::id(),
            'photo_path'        => $photoPath,
            'notes'             => $request->transit_notes,
            'specimen_condition'=> 'in_transit',
            'temperature_check' => $request->temperature_check,
            'latitude'          => $request->latitude ?? null,
            'longitude'         => $request->longitude ?? null,
            'accuracy'          => $request->accuracy ?? null,
            'verified'          => false,
            'proof_type'        => 'transit',
        ]);

        $nextStatus = $specimenRequest->proof_required_at_status ?? 'in_transit';

        $specimenRequest->update([
            'status'                   => $nextStatus,
            'requires_proof'           => false,
            'proof_uploaded'           => true,
            'proof_required_at_status' => null,
        ]);

        return redirect()->back()->with('success', 'Transit proof submitted! Status: ' . str_replace('_', ' ', $nextStatus));
    }

    // ─── Arrive At Destination ────────────────────────────────────────────────

    public function arriveAtDestination(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status !== 'in_transit') {
            return redirect()->back()->with('error', 'Cannot mark arrival from current status: ' . $specimenRequest->status);
        }

        $specimenRequest->update([
            'status'                    => 'arrived_at_destination',
            'requires_proof'            => false,
            'arrived_at_destination_at' => now(),
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'arrived_at_destination',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode(['status' => 'in_transit → arrived_at_destination', 'request_number' => $specimenRequest->request_number]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Arrival marked! Now capture the recipient signature to complete delivery.');
    }

    // ─── Submit Arrival Proof ─────────────────────────────────────────────────

    public function submitArrivalProof(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (! $specimenRequest->requires_proof || $specimenRequest->proof_uploaded) {
            return redirect()->back()->with('error', 'Proof not required or already uploaded.');
        }

        $request->validate([
            'arrival_photo'    => 'required|image|max:5120',
            'arrival_notes'    => 'nullable|string|max:500',
            'temperature_check'=> 'required|in:within_range,out_of_range,not_checked',
        ]);

        $photoPath = $request->file('arrival_photo')->store('arrival-proofs', 'public');

        PickupProof::create([
            'request_id'        => $specimenRequest->id,
            'courier_id'        => Auth::id(),
            'photo_path'        => $photoPath,
            'notes'             => $request->arrival_notes,
            'specimen_condition'=> 'arrived',
            'temperature_check' => $request->temperature_check,
            'latitude'          => $request->latitude ?? null,
            'longitude'         => $request->longitude ?? null,
            'accuracy'          => $request->accuracy ?? null,
            'verified'          => false,
            'proof_type'        => 'arrival',
        ]);

        $nextStatus = $specimenRequest->proof_required_at_status ?? 'arrived_at_destination';

        $specimenRequest->update([
            'status'                    => $nextStatus,
            'requires_proof'            => false,
            'proof_uploaded'            => true,
            'proof_required_at_status'  => null,
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'submit_arrival_proof',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode(['status' => "awaiting_arrival_proof → {$nextStatus}", 'request_number' => $specimenRequest->request_number]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Arrival proof submitted! Status: ' . str_replace('_', ' ', $nextStatus));
    }

    // ─── Submit Delivery ──────────────────────────────────────────────────────

    public function submitDelivery(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status !== 'arrived_at_destination') {
            return redirect()->back()->with('error', 'You must mark arrival before completing delivery. Current status: ' . str_replace('_', ' ', $specimenRequest->status));
        }

        $request->validate([
            'signature'              => 'required|string',
            'recipient_name'         => 'required|string|max:255',
            'recipient_relationship' => 'required|string|max:100',
            'delivery_photo'         => 'required|image|max:5120',
            'delivery_notes'         => 'nullable|string|max:500',
        ]);

        $photoPath = null;
        if ($request->hasFile('delivery_photo')) {
            $photoPath = $request->file('delivery_photo')->store('delivery-proofs', 'public');
        }

        Signature::create([
            'request_id'             => $specimenRequest->id,
            'courier_id'             => Auth::id(),
            'signature_type'         => 'delivery',
            'signed_by'              => Auth::id(),
            'recipient_name'         => $request->recipient_name,
            'signature_data'         => $request->signature,
            'signature_image_path'   => $photoPath,
            'ip_address'             => $request->ip(),
            'device_info'            => $request->userAgent(),
            'latitude'               => $request->latitude ?? null,
            'longitude'              => $request->longitude ?? null,
        ]);

        $specimenRequest->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'submit_delivery',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode(['status' => '→ delivered', 'delivered_at' => now()->toDateTimeString(), 'request_number' => $specimenRequest->request_number]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courier.requests.show', $requestId)
            ->with('success', 'Delivery completed! Waiting for client to confirm receipt.');
    }

    // ─── Complete Request ─────────────────────────────────────────────────────

    public function completeRequest(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if ($specimenRequest->status !== 'delivered') {
            return redirect()->back()->with('error', 'Cannot complete request from current status.');
        }

        $specimenRequest->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        cache()->forget("tracking_start_{$specimenRequest->id}");

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'complete_request',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode(['status' => 'delivered → completed', 'completed_at' => now()->toDateTimeString(), 'request_number' => $specimenRequest->request_number]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courier.assignments.index')
            ->with('success', 'Request completed successfully! Great work!');
    }

    // ─── Skip Proof ───────────────────────────────────────────────────────────

    public function skipProofRequirement(Request $request, $requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        if (! $specimenRequest->requires_proof) {
            return redirect()->back()->with('error', 'No proof requirement to skip.');
        }

        $targetStatus = $specimenRequest->proof_required_at_status ?? match ($specimenRequest->status) {
            'awaiting_pickup_proof'  => 'picked_up',
            'awaiting_transit_proof' => 'in_transit',
            'awaiting_arrival_proof' => 'arrived_at_destination',
            default                  => $specimenRequest->status,
        };

        $specimenRequest->update([
            'status'                   => $targetStatus,
            'requires_proof'           => false,
            'proof_uploaded'           => false,
            'proof_required_at_status' => null,
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'skip_proof_requirement',
            'model_type' => SpecimenRequest::class,
            'model_id'   => $specimenRequest->id,
            'changes'    => json_encode(['status' => "proof skipped → {$targetStatus}", 'request_number' => $specimenRequest->request_number]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('warning', 'Proof skipped. Status updated to ' . str_replace('_', ' ', $targetStatus) . '. This action has been logged.');
    }

    // ─── Location Tracking ────────────────────────────────────────────────────

    public function updateLocation(Request $request)
    {
        try {
            $validated = $request->validate([
                'latitude'   => 'required|numeric',
                'longitude'  => 'required|numeric',
                'accuracy'   => 'nullable|numeric',
                'speed'      => 'nullable|numeric',
                'heading'    => 'nullable|numeric',
                'altitude'   => 'nullable|numeric',
                'request_id' => 'nullable|exists:specimen_requests,id',
            ]);

            $user = Auth::user();
            $locationData = array_merge($validated, [
                'courier_id'  => $user->id,
                'is_online'   => true,
                'last_update' => now(),
                'accuracy'    => $validated['accuracy'] ?? 0,
                'speed'       => $validated['speed'] ?? 0,
                'heading'     => $validated['heading'] ?? 0,
                'altitude'    => $validated['altitude'] ?? 0,
            ]);

            if (empty($locationData['request_id'])) {
                unset($locationData['request_id']);
            }

            $location = CourierLocation::updateOrCreate(
                ['courier_id' => $user->id],
                $locationData
            );

            if (! empty($validated['request_id']) && class_exists(LocationHistory::class)) {
                LocationHistory::create(array_merge($locationData, ['request_id' => $validated['request_id']]));
            }

            // Cache for real-time panel
            cache()->put('courier_location_' . $user->id, array_merge($locationData, [
                'courier_name' => $user->first_name . ' ' . $user->last_name,
                'timestamp'    => now(),
            ]), 35);

            return response()->json([
                'success'     => true,
                'message'     => 'Location updated',
                'timestamp'   => now()->toDateTimeString(),
                'location_id' => $location->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Location update error: ' . $e->getMessage());
            return response()->json(['success' => true, 'message' => 'Location received', 'timestamp' => now()->toDateTimeString()]);
        }
    }

    public function locationStatus()
    {
        $user = Auth::user();
        $location = class_exists(CourierLocation::class)
            ? CourierLocation::where('courier_id', $user->id)->first()
            : null;

        return response()->json([
            'is_online'       => $location ? $location->is_online : false,
            'last_update'     => $location ? $location->last_update : null,
            'tracking_active' => cache()->has("courier_online_{$user->id}"),
        ]);
    }

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

        return response()->json(['success' => true, 'tracking_active' => $isActive]);
    }

    public function getActiveRequest()
    {
        $activeRequest = Auth::user()->assignedRequests()
            ->whereIn('status', [
                'accepted_by_courier', 'picked_up', 'in_transit',
                'arrived_at_destination', 'awaiting_pickup_proof',
                'awaiting_transit_proof', 'awaiting_arrival_proof',
            ])
            ->with(['client', 'pickupProof', 'signature'])
            ->first();

        if (! $activeRequest) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active'  => true,
            'request' => [
                'id'               => $activeRequest->id,
                'request_number'   => $activeRequest->request_number,
                'status'           => $activeRequest->status,
                'requires_proof'   => $activeRequest->requires_proof,
                'pickup_address'   => $activeRequest->pickup_address,
                'delivery_address' => $activeRequest->delivery_address,
                'priority_level'   => $activeRequest->priority_level,
            ],
        ]);
    }

    public function getNavigation($requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $loc = $this->getCurrentLocation();

        if (! $loc) {
            return response()->json(['error' => 'Location not available'], 400);
        }

        $distance = $this->calculateDistance(
            $loc->latitude, $loc->longitude,
            $specimenRequest->pickup_latitude, $specimenRequest->pickup_longitude
        );

        return response()->json([
            'from_lat'          => $loc->latitude,
            'from_lng'          => $loc->longitude,
            'to_lat'            => $specimenRequest->pickup_latitude,
            'to_lng'            => $specimenRequest->pickup_longitude,
            'distance_km'       => round($distance, 2),
            'estimated_minutes' => round(($distance / 40) * 60),
            'google_maps_url'   => "https://www.google.com/maps/dir/{$loc->latitude},{$loc->longitude}/{$specimenRequest->pickup_latitude},{$specimenRequest->pickup_longitude}",
        ]);
    }

    public function getLocationHistory($requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        if (! class_exists(LocationHistory::class)) {
            return response()->json(['error' => 'Location history not available'], 400);
        }

        $history = LocationHistory::where('request_id', $specimenRequest->id)
            ->where('courier_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get(['latitude', 'longitude', 'created_at', 'speed', 'heading']);

        return response()->json($history);
    }

    public function getCourierLocationForRequest($requestId)
    {
        $specimenRequest = SpecimenRequest::findOrFail($requestId);

        if ($specimenRequest->assigned_to != Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $loc = $this->getCurrentLocation();

        return response()->json([
            'courier'  => ['id' => Auth::id(), 'name' => Auth::user()->first_name . ' ' . Auth::user()->last_name],
            'location' => $loc ? (array) $loc : null,
            'status'   => $loc ? 'online' : 'offline',
        ]);
    }

    public function getCourierLocationApi($courierId)
    {
        $courier = \App\Models\User::whereHas('role', fn ($q) => $q->where('slug', 'courier'))->find($courierId);

        if (! $courier) {
            return response()->json(['error' => 'Courier not found'], 404);
        }

        $cachedLocation = Cache::get('courier_location_' . $courierId);

        if (! $cachedLocation && class_exists(CourierLocation::class)) {
            $dbLoc = CourierLocation::where('courier_id', $courierId)->first();
            if ($dbLoc) {
                $cachedLocation = [
                    'latitude'    => $dbLoc->latitude,
                    'longitude'   => $dbLoc->longitude,
                    'is_online'   => $dbLoc->is_online,
                    'last_update' => $dbLoc->last_update,
                ];
            }
        }

        return response()->json([
            'courier'  => ['id' => $courier->id, 'name' => $courier->first_name . ' ' . $courier->last_name, 'phone' => $courier->phone],
            'location' => $cachedLocation,
            'status'   => ($cachedLocation && ($cachedLocation['is_online'] ?? false)) ? 'online' : 'offline',
        ]);
    }

    // ─── Profile, History, Proofs, Notifications ──────────────────────────────

    public function activePickups()
    {
        $activePickups = Auth::user()->assignedRequests()
            ->whereIn('status', ['accepted_by_courier', 'awaiting_pickup_proof'])
            ->with(['client', 'pickupProof'])
            ->orderByRaw("FIELD(priority_level,'stat','routine','scheduled')")
            ->paginate(10);

        return view('courier.assignments.active-pickups', compact('activePickups'));
    }

    public function activeDeliveries()
    {
        $activeDeliveries = Auth::user()->assignedRequests()
            ->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination', 'awaiting_transit_proof', 'awaiting_arrival_proof'])
            ->with(['client', 'pickupProof', 'signature'])
            ->orderByRaw("FIELD(priority_level,'stat','routine','scheduled')")
            ->paginate(10);

        return view('courier.assignments.active-deliveries', compact('activeDeliveries'));
    }

    public function history()
    {
        $history = Auth::user()->assignedRequests()
            ->where('status', 'completed')
            ->with(['client', 'pickupProof', 'signature'])
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('courier.assignments.history', compact('history'));
    }

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

    public function viewProof($id, $type)
    {
        $proof = $type === 'pickup'
            ? PickupProof::where('courier_id', Auth::id())->with(['request'])->findOrFail($id)
            : Signature::where('courier_id', Auth::id())->with(['request'])->findOrFail($id);

        return view('courier.assignments.proof-detail', compact('proof', 'type'));
    }

    public function profile()
    {
        $user  = Auth::user();
        $stats = [
            'total_deliveries' => $user->assignedRequests()->where('status', 'completed')->count(),
            'on_time_rate'     => $this->calculateOnTimeRate($user),
            'avg_rating'       => 4.8,
            'proofs_uploaded'  => PickupProof::where('courier_id', Auth::id())->count() + Signature::where('courier_id', Auth::id())->count(),
            'total_earnings'   => CourierQuote::where('courier_id', Auth::id())->where('status', 'accepted')->sum('courier_fee'),
        ];

        return view('courier.profile', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone'          => 'required|string|max:20',
            'vehicle_type'   => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:20',
            'profile_photo'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('courier.notifications', compact('notifications'));
    }

    public function markNotificationAsRead(Notification $notification)
    {
        if ($notification->user_id != Auth::id()) abort(403);
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function getCurrentLocation(): ?object
    {
        $loc = cache()->get('courier_location_' . Auth::id());

        if (! $loc && class_exists(CourierLocation::class)) {
            $dbLoc = CourierLocation::where('courier_id', Auth::id())->first();
            if ($dbLoc) {
                return (object) [
                    'latitude'    => $dbLoc->latitude,
                    'longitude'   => $dbLoc->longitude,
                    'accuracy'    => $dbLoc->accuracy,
                    'speed'       => $dbLoc->speed,
                    'heading'     => $dbLoc->heading,
                    'is_online'   => $dbLoc->is_online,
                    'last_update' => $dbLoc->last_update ? Carbon::parse($dbLoc->last_update) : now(),
                ];
            }
            return null;
        }

        if (is_array($loc)) {
            $obj = (object) $loc;
            $obj->last_update = isset($obj->last_update) ? Carbon::parse($obj->last_update) : now();
            return $obj;
        }

        return $loc;
    }

    private function startLocationTracking(SpecimenRequest $request): void
    {
        cache()->put("tracking_start_{$request->id}", now(), now()->addHours(24));
        cache()->put("courier_online_{$request->assigned_to}", true, now()->addHours(24));
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    private function calculateOnTimeRate($user): float
    {
        $completed = $user->assignedRequests()
            ->where('status', 'completed')
            ->whereNotNull('scheduled_delivery_time')
            ->whereNotNull('delivered_at')
            ->get();

        if ($completed->isEmpty()) return 100.0;

        $onTime = $completed->filter(fn ($r) => $r->delivered_at <= $r->scheduled_delivery_time)->count();

        return round(($onTime / $completed->count()) * 100, 1);
    }

    /**
     * Notify all admins when courier responds to a quote.
     */
    private function notifyAdminsAboutQuoteResponse(SpecimenRequest $request, string $action, CourierQuote $quote, string $reason = ''): void
    {
        $courier      = Auth::user();
        $courierName  = $courier->first_name . ' ' . $courier->last_name;
        $actionLabel  = $action === 'accepted' ? 'Accepted' : 'Declined';
        $title        = "Quote {$actionLabel} by Courier";
        $body         = "{$courierName} has {$action} the price quote for request #{$request->request_number}.";
        if ($action === 'declined' && $reason) {
            $body .= " Reason: {$reason}";
        }

        $admins = \App\Models\User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->get();

        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'type'       => 'quote_' . $action,
                'user_id'    => $admin->id,
                'user_type'  => 'App\Models\User',
                'sender_id'  => $admin->id,
                'sender_type'=> 'admin',
                'request_id' => $request->id,
                'title'      => $title,
                'message'    => $body,
                'data'       => json_encode([
                    'request_id'     => $request->id,
                    'request_number' => $request->request_number,
                    'courier_id'     => $courier->id,
                    'courier_name'   => $courierName,
                    'quote_id'       => $quote->id,
                    'action'         => $action,
                    'reason'         => $reason,
                    'action_url'     => route('admin.requests.show', $request->id),
                ]),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Helper to return consistent error responses for AJAX or redirect.
     */
    private function errorResponse(Request $request, string $message, int $code = 400)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], $code);
        }
        return redirect()->back()->with('error', $message);
    }
}