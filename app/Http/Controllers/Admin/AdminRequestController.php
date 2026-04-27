<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Facility;
use App\Models\User;
use App\Models\Notification;
use App\Models\CourierQuote;
use App\Models\CourierLocation;
use App\Models\AuditLog;
use App\Models\RequestDocument;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;



class AdminRequestController extends Controller
{
    use Notifiable;

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = SpecimenRequest::with(['client', 'facility', 'courier']);

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

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(SpecimenRequest $request)
    {
        // Eager-load documents with their stop and uploader so the blade
        // can group by stop and display uploader name without extra queries.
        $request->load(['client', 'facility', 'courier', 'stops', 'documents.stop', 'documents.uploader', 'pickupProofs', 'signatures']);

        $activeQuote = CourierQuote::where('request_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $couriers = User::whereHas('role', function ($q) {
            $q->where('slug', 'courier');
        })->where('is_active', true)->get();

        return view('admin.requests.show', [
            'request'     => $request,
            'couriers'    => $couriers,
            'activeQuote' => $activeQuote,
        ]);
    }

    // ─── Download a client-uploaded RequestDocument (admin only) ─────────────

    public function downloadRequestDocument(RequestDocument $document)
    {
        // Admins can download any RequestDocument — no client_id check needed.
        // We just verify the file actually exists on disk before streaming.
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    // ─── Assign Courier (direct, no quote) ────────────────────────────────────

    public function assignCourier(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $courier = User::find($validated['courier_id']);
        $admin = auth()->user();

        $request->update([
            'assigned_to' => $validated['courier_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status'      => 'assigned',
        ]);

        // ============================================
        // SEND EMAIL TO COURIER WHEN ASSIGNED
        // ============================================
        try {
            $emailData = [
                'request' => $request,
                'courier' => $courier,
                'admin' => $admin,
                'client' => $request->client,
                'assigned_at' => now(),
                'dashboard_url' => route('courier.requests.show', $request->id),
                'pickup_address' => $request->pickup_address,
                'delivery_address' => $request->delivery_address,
                'scheduled_pickup' => $request->scheduled_pickup_time,
                'priority_level' => $request->priority_level,
                'specimen_type' => $request->specimen_type,
                'estimated_price' => $request->estimated_price,
                'special_instructions' => $request->special_instructions,
            ];

            Mail::to($courier->email)->send(new \App\Mail\CourierAssignedMail($emailData));

            Log::info('Courier assignment email sent', [
                'request_id' => $request->id,
                'courier_id' => $validated['courier_id'],
                'courier_email' => $courier->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send courier assignment email: ' . $e->getMessage(), [
                'request_id' => $request->id,
                'courier_id' => $validated['courier_id']
            ]);
        }

        $this->notifyCourier(
            $validated['courier_id'],
            'request_assigned',
            'New Assignment',
            "You have been assigned to request #{$request->request_number}",
            $request->id,
            [
                'request_id'     => $request->id,
                'request_number' => $request->request_number,
                'assigned_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'assigned_at'    => now()->toDateTimeString(),
            ]
        );

        $this->notifyAdmins(
            'request_assigned',
            'Request Assigned',
            "Request #{$request->request_number} has been assigned to {$courier->first_name} {$courier->last_name}",
            $request->id,
            [
                'request_id'     => $request->id,
                'request_number' => $request->request_number,
                'courier_id'     => $validated['courier_id'],
                'courier_name'   => $courier->first_name . ' ' . $courier->last_name,
                'assigned_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            ]
        );

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Courier assigned successfully and notified via email.',
                'redirect' => route('admin.requests.show', $request),
            ]);
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', 'Courier assigned successfully and notified via email.');
    }

    // ─── Update Status (approve / reject / cancel) ────────────────────────────

    public function updateStatus(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === 'cancelled') {
            $updates['cancelled_at'] = now();
            $updates['cancelled_by'] = Auth::id();
        }

        if ($validated['status'] === 'rejected') {
            $updates['rejection_reason'] = $validated['rejection_reason'] ?? null;
            $updates['rejected_at'] = now();
            $updates['rejected_by'] = Auth::id();
        }

        $request->update($updates);

        if ($validated['status'] === 'cancelled' && $request->courier_quote_id) {
            CourierQuote::where('id', $request->courier_quote_id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        }

        if (in_array($validated['status'], ['approved', 'rejected']) && $request->client_id) {
            try {
                $emailData = [
                    'request' => $request,
                    'client' => $request->client,
                    'status' => $validated['status'],
                    'rejection_reason' => $validated['rejection_reason'] ?? null,
                    'admin_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'approved_at' => now(),
                    'dashboard_url' => route('client.requests.show', $request->id),
                ];

                if ($validated['status'] === 'approved') {
                    Mail::to($request->client->email)->send(new \App\Mail\RequestApprovedMail($emailData));
                } else {
                    Mail::to($request->client->email)->send(new \App\Mail\RequestRejectedMail($emailData));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send status update email to client: ' . $e->getMessage(), [
                    'request_id' => $request->id,
                    'status' => $validated['status']
                ]);
            }

            $this->notifyClient(
                $request->client_id,
                'status_update',
                'Request ' . ucfirst($validated['status']),
                "Your request #{$request->request_number} has been {$validated['status']}." .
                    ($validated['status'] === 'rejected' && !empty($validated['rejection_reason']) ? " Reason: {$validated['rejection_reason']}" : ""),
                $request->id,
                [
                    'request_id'       => $request->id,
                    'request_number'   => $request->request_number,
                    'status'           => $validated['status'],
                    'updated_by'       => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'updated_at'       => now()->toDateTimeString(),
                    'rejection_reason' => $validated['rejection_reason'] ?? null,
                ]
            );
        }

        $this->notifyAdmins(
            'status_update',
            'Request Status Updated',
            "Request #{$request->request_number} has been {$validated['status']}",
            $request->id,
            [
                'request_id'     => $request->id,
                'request_number' => $request->request_number,
                'status'         => $validated['status'],
                'updated_by'     => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            ]
        );

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Request {$validated['status']} successfully!",
                'redirect' => route('admin.requests.show', $request),
            ]);
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', "Request {$validated['status']} successfully!");
    }

    // ─── Calculate Price ──────────────────────────────────────────────────────

    public function calculatePrice(Request $httpRequest, SpecimenRequest $request)
    {
        try {
            $pricing = $this->buildPricing($request);
            $request->update($pricing['fields']);

            $this->notifyAdmins(
                'price_calculated',
                'Price Calculated',
                "Price calculated for request #{$request->request_number}: $" . number_format($pricing['fields']['total_price'], 2),
                $request->id,
                ['request_id' => $request->id, 'request_number' => $request->request_number, 'total_price' => $pricing['fields']['total_price']]
            );

            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Price calculated successfully! Total: $" . number_format($pricing['fields']['total_price'], 2),
                    'data'    => $pricing['fields'],
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', "Price calculated! Total: $" . number_format($pricing['fields']['total_price'], 2));
        } catch (\Exception $e) {
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.requests.show', $request)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Send Quote Only (no assignment yet) ──────────────────────────────────

    public function createQuote(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id'  => 'required|exists:users,id',
            'courier_fee' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'valid_hours' => 'nullable|integer|min:1|max:72',
            'validity_mode' => 'nullable|in:preset,custom',
            'custom_valid_hours' => 'nullable|integer|min:0|max:720',
            'custom_valid_minutes' => 'nullable|integer|min:0|max:59',
        ]);

        try {
            CourierQuote::where('request_id', $request->id)
                ->where('courier_id', $validated['courier_id'])
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            if (! $request->is_price_quoted) {
                $pricing = $this->buildPricing($request);
                $request->update($pricing['fields']);
                $request->refresh();
            }

            $customValidHours   = (int) ($validated['custom_valid_hours'] ?? 0);
            $customValidMinutes = (int) ($validated['custom_valid_minutes'] ?? 0);
            $validityMode       = $validated['validity_mode'] ?? (($customValidHours > 0 || $customValidMinutes > 0) ? 'custom' : 'preset');

            if ($validityMode === 'custom') {
                if ($customValidHours === 0 && $customValidMinutes === 0) {
                    throw ValidationException::withMessages([
                        'custom_valid_minutes' => 'Please enter custom hours or minutes greater than 0.',
                    ]);
                }
                $quoteValidMinutes = ($customValidHours * 60) + $customValidMinutes;
            } else {
                $quoteValidMinutes = ((int) ($validated['valid_hours'] ?? 24) * 60);
            }

            $quote = CourierQuote::create([
                'request_id'  => $request->id,
                'courier_id'  => $validated['courier_id'],
                'courier_fee' => $validated['courier_fee'],
                'total_price' => $validated['total_price'],
                'breakdown'   => $this->buildBreakdown($request),
                'valid_until' => now()->addMinutes($quoteValidMinutes),
                'status'      => 'pending',
            ]);

            $request->update([
                'courier_quote_id'    => $quote->id,
                'courier_can_accept'  => true,
                'acceptance_deadline' => $quote->valid_until,
                'status'              => 'quote_sent',
            ]);

            $this->notifyCourier(
                $validated['courier_id'],
                'quote_received',
                'New Price Quote',
                "You have a new price quote for request #{$request->request_number}",
                $request->id,
                [
                    'request_id'     => $request->id,
                    'request_number' => $request->request_number,
                    'quote_id'       => $quote->id,
                    'courier_fee'    => $quote->courier_fee,
                    'valid_until'    => $quote->valid_until->toDateTimeString(),
                    'created_by'     => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'quote_url'      => route('courier.requests.quote', $request->id),
                ]
            );

            $this->notifyAdmins(
                'quote_created',
                'New Quote Sent',
                "Quote sent to courier for request #{$request->request_number}",
                $request->id,
                [
                    'request_id'     => $request->id,
                    'request_number' => $request->request_number,
                    'quote_id'       => $quote->id,
                    'courier_id'     => $validated['courier_id'],
                    'total_price'    => $quote->total_price,
                    'created_by'     => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]
            );

            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Price quote sent to courier. Awaiting response.',
                    'data'    => [
                        'quote_id'    => $quote->id,
                        'courier_fee' => $quote->courier_fee,
                        'total_price' => $quote->total_price,
                        'valid_until' => $quote->valid_until->format('Y-m-d H:i:s'),
                    ],
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', 'Price quote sent to courier. Awaiting response.');
        } catch (\Exception $e) {
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.requests.show', $request)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Assign With Quote ────────────────────────────────────────────────────

    public function assignWithQuote(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id'         => 'required|exists:users,id',
            'valid_hours'        => 'nullable|integer|min:1|max:72',
            'validity_mode'      => 'nullable|in:preset,custom',
            'custom_valid_hours' => 'nullable|integer|min:0|max:720',
            'custom_valid_minutes' => 'nullable|integer|min:0|max:59',
            'override_price'     => 'nullable|boolean',
            'custom_total_price' => 'required_if:override_price,1|nullable|numeric|min:0',
            'custom_courier_fee' => 'nullable|numeric|min:0',
            'price_note'         => 'nullable|string|max:200',
        ]);

        try {
            CourierQuote::where('request_id', $request->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            if (! $request->is_price_quoted) {
                $pricing = $this->buildPricing($request);
                $request->update($pricing['fields']);
                $request->refresh();
            }

            $useOverride = $httpRequest->boolean('override_price');

            if ($useOverride) {
                $totalPrice = (float) $validated['custom_total_price'];
                $courierFee = (isset($validated['custom_courier_fee']) && $validated['custom_courier_fee'] !== null && $validated['custom_courier_fee'] !== '')
                    ? (float) $validated['custom_courier_fee']
                    : round($totalPrice * 0.70, 2);
            } else {
                $totalPrice = $request->total_price;
                $courierFee = $request->courier_fee;
            }

            $breakdown = $this->buildBreakdown($request);
            if ($useOverride) {
                $breakdown['price_override']   = true;
                $breakdown['original_total']   = $request->total_price;
                $breakdown['original_courier'] = $request->courier_fee;
                $breakdown['price_note']       = $validated['price_note'] ?? null;
                $breakdown['overridden_by']    = auth()->user()->first_name . ' ' . auth()->user()->last_name;
                $breakdown['overridden_at']    = now()->toDateTimeString();
            }

            $customValidHours   = (int) ($validated['custom_valid_hours'] ?? 0);
            $customValidMinutes = (int) ($validated['custom_valid_minutes'] ?? 0);
            $validityMode       = $validated['validity_mode'] ?? (($customValidHours > 0 || $customValidMinutes > 0) ? 'custom' : 'preset');

            if ($validityMode === 'custom') {
                if ($customValidHours === 0 && $customValidMinutes === 0) {
                    throw ValidationException::withMessages([
                        'custom_valid_minutes' => 'Please enter custom hours or minutes greater than 0.',
                    ]);
                }
                $quoteValidMinutes = ($customValidHours * 60) + $customValidMinutes;
            } else {
                $quoteValidMinutes = ((int) ($validated['valid_hours'] ?? 24) * 60);
            }

            $quote = CourierQuote::create([
                'request_id'  => $request->id,
                'courier_id'  => $validated['courier_id'],
                'courier_fee' => $courierFee,
                'total_price' => $totalPrice,
                'breakdown'   => $breakdown,
                'valid_until' => now()->addMinutes($quoteValidMinutes),
                'status'      => 'pending',
            ]);

            $request->update([
                'courier_quote_id'    => $quote->id,
                'courier_can_accept'  => true,
                'acceptance_deadline' => $quote->valid_until,
                'status'              => 'quote_sent',
            ]);

            $courier = User::find($validated['courier_id']);
            $admin   = auth()->user();

            try {
                $emailData = [
                    'request'              => $request,
                    'courier'              => $courier,
                    'admin'                => $admin,
                    'client'               => $request->client,
                    'quote'                => $quote,
                    'assigned_at'          => now(),
                    'deadline'             => $quote->valid_until,
                    'dashboard_url'        => route('courier.requests.quote', $request->id),
                    'pickup_address'       => $request->pickup_address,
                    'delivery_address'     => $request->delivery_address,
                    'scheduled_pickup'     => $request->scheduled_pickup_time,
                    'priority_level'       => $request->priority_level,
                    'specimen_type'        => $request->specimen_type,
                    'estimated_price'      => $quote->total_price,
                    'courier_fee'          => $quote->courier_fee,
                    'special_instructions' => $request->special_instructions,
                ];

                Mail::to($courier->email)->send(new \App\Mail\CourierQuoteMail($emailData));
            } catch (\Exception $e) {
                Log::error('Failed to send courier quote email: ' . $e->getMessage(), [
                    'request_id' => $request->id,
                    'courier_id' => $validated['courier_id']
                ]);
            }

            $this->notifyCourier(
                $validated['courier_id'],
                'request_assigned_with_quote',
                'New Price Quote',
                "You have a new price quote for request #{$request->request_number}. Please review and accept or decline.",
                $request->id,
                [
                    'request_id'     => $request->id,
                    'request_number' => $request->request_number,
                    'quote_id'       => $quote->id,
                    'courier_fee'    => number_format($quote->courier_fee, 2),
                    'deadline'       => $quote->valid_until->format('M d, Y h:i A'),
                    'assigned_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'quote_url'      => route('courier.requests.quote', $request->id),
                ]
            );

            $this->notifyAdmins(
                'request_assigned_with_quote',
                'Quote Sent to Courier',
                "Quote sent to {$courier->first_name} {$courier->last_name} for request #{$request->request_number} — awaiting acceptance.",
                $request->id,
                [
                    'request_id'       => $request->id,
                    'request_number'   => $request->request_number,
                    'courier_id'       => $validated['courier_id'],
                    'quote_id'         => $quote->id,
                    'total_price'      => $quote->total_price,
                    'price_overridden' => $useOverride,
                    'assigned_by'      => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]
            );

            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Price quote sent to courier. Waiting for acceptance.',
                    'data'    => [
                        'quote_id'         => $quote->id,
                        'courier_fee'      => $quote->courier_fee,
                        'total_price'      => $quote->total_price,
                        'valid_until'      => $quote->valid_until->format('Y-m-d H:i:s'),
                        'status'           => 'quote_sent',
                        'price_overridden' => $useOverride,
                    ],
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', 'Price quote sent to courier. Waiting for acceptance.');
        } catch (\Exception $e) {
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.requests.show', $request)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Cancel Quote ─────────────────────────────────────────────────────────

    public function cancelQuote(Request $httpRequest, SpecimenRequest $request)
    {
        CourierQuote::where('request_id', $request->id)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        $request->update([
            'status'              => 'approved',
            'assigned_to'         => null,
            'assigned_by'         => null,
            'assigned_at'         => null,
            'courier_can_accept'  => false,
            'courier_quote_id'    => null,
            'acceptance_deadline' => null,
        ]);

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Quote cancelled. Request is back to Approved status.']);
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', 'Quote cancelled. You can now assign a different courier.');
    }

    // ─── Get Price Data (AJAX) ────────────────────────────────────────────────

    public function getPriceData(Request $httpRequest, SpecimenRequest $request)
    {
        $data = [
            'base_price'             => $request->base_price ?? 0,
            'distance_miles'         => $request->distance_miles ?? 0,
            'distance_charge'        => $request->distance_charge ?? 0,
            'stat_urgent_charge'     => $request->stat_urgent_charge ?? 0,
            'night_hours_charge'     => $request->night_hours_charge ?? 0,
            'weekend_charge'         => $request->weekend_charge ?? 0,
            'cold_chain_charge'      => $request->cold_chain_charge ?? 0,
            'additional_stop_charge' => $request->additional_stop_charge ?? 0,
            'total_price'            => $request->total_price ?? 0,
            'courier_fee'            => $request->courier_fee ?? 0,
            'admin_fee'              => $request->admin_fee ?? 0,
            'profit_margin'          => $request->profit_margin ?? 0,
            'is_price_quoted'        => $request->is_price_quoted ?? false,
        ];

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json(['success' => true, 'data' => $data]);
        }

        return $data;
    }

    // ─── Admin Courier Location API (by courier User model) ──────────────────

    public function getCourierLocation(Request $httpRequest, User $courier)
    {
        $location = cache()->get('courier_location_' . $courier->id);

        if (! $location) {
            if (class_exists(CourierLocation::class)) {
                $dbLoc = CourierLocation::where('courier_id', $courier->id)->first();
                if ($dbLoc) {
                    $location = [
                        'latitude'    => $dbLoc->latitude,
                        'longitude'   => $dbLoc->longitude,
                        'accuracy'    => $dbLoc->accuracy,
                        'speed'       => $dbLoc->speed,
                        'heading'     => $dbLoc->heading,
                        'is_online'   => $dbLoc->is_online,
                        'last_update' => $dbLoc->last_update,
                    ];
                }
            }
        }

        return response()->json([
            'courier'  => [
                'id'    => $courier->id,
                'name'  => $courier->first_name . ' ' . $courier->last_name,
                'phone' => $courier->phone,
            ],
            'location' => $location,
            'status'   => ($location && ($location['is_online'] ?? false)) ? 'online' : 'offline',
        ]);
    }

    // ─── Tracking View ────────────────────────────────────────────────────────

    public function track(SpecimenRequest $request)
    {
        $request->load(['courier', 'client', 'facility', 'stops']);
        return view('admin.requests.track', compact('request'));
    }

    // ─── Admin Tracking API: Courier Location for a specific request ──────────

    public function getAdminCourierLocation(SpecimenRequest $request)
    {
        if (! $request->courier) {
            return response()->json([
                'error'    => 'No courier assigned to this request yet.',
                'courier'  => null,
                'location' => null,
                'status'   => 'offline',
            ]);
        }

        $courier        = $request->courier;
        $cachedLocation = Cache::get('courier_location_' . $courier->id);

        if (! $cachedLocation && class_exists(CourierLocation::class)) {
            $dbLoc = CourierLocation::where('courier_id', $courier->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($dbLoc) {
                $cachedLocation = [
                    'latitude'      => (float) $dbLoc->latitude,
                    'longitude'     => (float) $dbLoc->longitude,
                    'accuracy'      => $dbLoc->accuracy  ? (float) $dbLoc->accuracy  : null,
                    'speed'         => $dbLoc->speed     ? (float) $dbLoc->speed     : null,
                    'heading'       => $dbLoc->heading   ? (float) $dbLoc->heading   : null,
                    'altitude'      => $dbLoc->altitude  ? (float) $dbLoc->altitude  : null,
                    'battery_level' => $dbLoc->battery_level,
                    'is_online'     => (bool) $dbLoc->is_online,
                    'timestamp'     => $dbLoc->created_at->timestamp,
                    'last_update'   => $dbLoc->last_update ?? $dbLoc->created_at,
                ];
            }
        }

        if (! $cachedLocation) {
            return response()->json([
                'courier' => [
                    'id'            => $courier->id,
                    'name'          => $courier->full_name,
                    'phone'         => $courier->phone,
                    'vehicle_type'  => $courier->vehicle_type ?? null,
                    'profile_image' => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
                ],
                'location' => null,
                'status'   => 'offline',
                'message'  => 'Courier location not available yet.',
            ]);
        }

        $formattedAddress   = $this->reverseGeocode($cachedLocation['latitude'] ?? null, $cachedLocation['longitude'] ?? null);
        $distanceToPickup   = null;
        $distanceToDelivery = null;
        $etaToPickup        = null;
        $etaToDelivery      = null;

        if ($request->pickup_latitude && $request->pickup_longitude) {
            $distanceToPickup = $this->calculateDistance($cachedLocation['latitude'], $cachedLocation['longitude'], $request->pickup_latitude, $request->pickup_longitude);
            $etaToPickup = $this->calculateETA($distanceToPickup, $cachedLocation['speed'] ?? 0);
        }

        if ($request->delivery_latitude && $request->delivery_longitude) {
            $distanceToDelivery = $this->calculateDistance($cachedLocation['latitude'], $cachedLocation['longitude'], $request->delivery_latitude, $request->delivery_longitude);
            $etaToDelivery = $this->calculateETA($distanceToDelivery, $cachedLocation['speed'] ?? 0);
        }

        return response()->json([
            'courier' => [
                'id'            => $courier->id,
                'name'          => $courier->full_name,
                'phone'         => $courier->phone,
                'email'         => $courier->email,
                'vehicle_type'  => $courier->vehicle_type ?? null,
                'profile_image' => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
                'last_seen'     => isset($cachedLocation['last_update'])
                    ? Carbon::parse($cachedLocation['last_update'])->diffForHumans()
                    : (isset($cachedLocation['timestamp']) ? Carbon::createFromTimestamp($cachedLocation['timestamp'])->diffForHumans() : 'Just now'),
                'rating'        => $courier->rating ?? 4.5,
            ],
            'location' => [
                'latitude'          => (float) ($cachedLocation['latitude'] ?? 0),
                'longitude'         => (float) ($cachedLocation['longitude'] ?? 0),
                'accuracy'          => isset($cachedLocation['accuracy'])  ? (float) $cachedLocation['accuracy']  : null,
                'speed'             => isset($cachedLocation['speed'])     ? (float) $cachedLocation['speed']     : 0,
                'heading'           => isset($cachedLocation['heading'])   ? (float) $cachedLocation['heading']   : 0,
                'altitude'          => isset($cachedLocation['altitude'])  ? (float) $cachedLocation['altitude']  : null,
                'timestamp'         => $cachedLocation['timestamp'] ?? time(),
                'is_online'         => (bool) ($cachedLocation['is_online'] ?? false),
                'formatted_address' => $formattedAddress,
                'battery_level'     => $cachedLocation['battery_level'] ?? null,
                'last_update'       => $cachedLocation['last_update'] ?? now()->toDateTimeString(),
                'coordinates'       => [
                    'latitude'  => (float) ($cachedLocation['latitude'] ?? 0),
                    'longitude' => (float) ($cachedLocation['longitude'] ?? 0),
                    'formatted' => sprintf('%.6f, %.6f', (float) ($cachedLocation['latitude'] ?? 0), (float) ($cachedLocation['longitude'] ?? 0)),
                ],
            ],
            'distances' => [
                'to_pickup_km'            => $distanceToPickup   ? round($distanceToPickup, 2)   : null,
                'to_delivery_km'          => $distanceToDelivery ? round($distanceToDelivery, 2) : null,
                'eta_to_pickup_minutes'   => $etaToPickup,
                'eta_to_delivery_minutes' => $etaToDelivery,
            ],
            'status'         => ($cachedLocation['is_online'] ?? false) ? 'online' : 'offline',
            'request_status' => $request->status,
            'last_updated'   => $cachedLocation['last_update'] ?? now()->toDateTimeString(),
        ]);
    }

    // ─── Admin Tracking API: Full Tracking Details for a request ─────────────

    public function getAdminTrackingDetails(SpecimenRequest $request)
    {
        $request->load(['courier', 'stops', 'pickupProofs', 'signatures', 'payment']);

        $courierLocation = null;
        $courier         = $request->courier;

        if ($courier) {
            $cachedLocation = Cache::get('courier_location_' . $courier->id);

            if (! $cachedLocation && class_exists(CourierLocation::class)) {
                $dbLoc = CourierLocation::where('courier_id', $courier->id)->orderBy('created_at', 'desc')->first();
                if ($dbLoc) {
                    $cachedLocation = [
                        'latitude'      => (float) $dbLoc->latitude,
                        'longitude'     => (float) $dbLoc->longitude,
                        'accuracy'      => $dbLoc->accuracy  ? (float) $dbLoc->accuracy  : null,
                        'speed'         => $dbLoc->speed     ? (float) $dbLoc->speed     : null,
                        'heading'       => $dbLoc->heading   ? (float) $dbLoc->heading   : null,
                        'altitude'      => $dbLoc->altitude  ? (float) $dbLoc->altitude  : null,
                        'battery_level' => $dbLoc->battery_level,
                        'is_online'     => (bool) $dbLoc->is_online,
                        'timestamp'     => $dbLoc->created_at->timestamp,
                        'last_update'   => $dbLoc->last_update ?? $dbLoc->created_at,
                    ];
                }
            }

            if ($cachedLocation) {
                $courierLocation = $cachedLocation;
            }
        }

        $progress        = $this->calculateDeliveryProgress($request);
        $stopsWithCoords = $request->stops->map(fn($stop) => [
            'id'           => $stop->id,
            'type'         => $stop->stop_type,
            'address'      => $stop->address,
            'contact_name' => $stop->contact_name,
            'instructions' => $stop->instructions,
            'completed'    => $stop->completed,
            'completed_at' => $stop->completed_at?->format('Y-m-d H:i:s'),
            'latitude'     => $stop->latitude  ? (float) $stop->latitude  : null,
            'longitude'    => $stop->longitude ? (float) $stop->longitude : null,
        ]);

        $distances = [];
        if ($courierLocation && $courierLocation['latitude'] && $courierLocation['longitude']) {
            if ($request->pickup_latitude && $request->pickup_longitude) {
                $distances['to_pickup_km'] = round($this->calculateDistance($courierLocation['latitude'], $courierLocation['longitude'], $request->pickup_latitude, $request->pickup_longitude), 2);
            }
            if ($request->delivery_latitude && $request->delivery_longitude) {
                $distances['to_delivery_km'] = round($this->calculateDistance($courierLocation['latitude'], $courierLocation['longitude'], $request->delivery_latitude, $request->delivery_longitude), 2);
            }
        }

        return response()->json([
            'request' => [
                'id'                      => $request->id,
                'request_number'          => $request->request_number,
                'status'                  => $request->status,
                'status_display'          => str_replace('_', ' ', $request->status),
                'pickup_address'          => $request->pickup_address,
                'pickup_latitude'         => $request->pickup_latitude     ? (float) $request->pickup_latitude     : null,
                'pickup_longitude'        => $request->pickup_longitude    ? (float) $request->pickup_longitude    : null,
                'delivery_address'        => $request->delivery_address,
                'delivery_latitude'       => $request->delivery_latitude   ? (float) $request->delivery_latitude   : null,
                'delivery_longitude'      => $request->delivery_longitude  ? (float) $request->delivery_longitude  : null,
                'scheduled_pickup_time'   => $request->scheduled_pickup_time?->format('Y-m-d H:i:s'),
                'scheduled_delivery_time' => $request->scheduled_delivery_time?->format('Y-m-d H:i:s'),
                'priority_level'          => $request->priority_level,
                'specimen_type'           => $request->specimen_type,
                'temperature_requirement' => $request->temperature_requirement,
                'quantity'                => $request->quantity,
                'payment_status'          => $request->payment_status,
            ],
            'courier' => $courier ? [
                'id'             => $courier->id,
                'name'           => $courier->full_name,
                'phone'          => $courier->phone,
                'email'          => $courier->email,
                'vehicle_type'   => $courier->vehicle_type ?? null,
                'vehicle_number' => $courier->vehicle_number ?? null,
                'profile_image'  => $courier->profile_image ? asset('storage/' . $courier->profile_image) : null,
                'rating'         => $courier->rating ?? 4.5,
            ] : null,
            'courier_location' => $courierLocation ? array_merge($courierLocation, [
                'formatted_address' => $this->reverseGeocode($courierLocation['latitude'] ?? null, $courierLocation['longitude'] ?? null),
                'coordinates'       => [
                    'latitude'  => $courierLocation['latitude']  ?? null,
                    'longitude' => $courierLocation['longitude'] ?? null,
                    'formatted' => ($courierLocation['latitude'] && $courierLocation['longitude'])
                        ? sprintf('%.6f, %.6f', $courierLocation['latitude'], $courierLocation['longitude'])
                        : null,
                ],
            ]) : null,
            'stops'    => $stopsWithCoords,
            'progress' => $progress,
            'distances' => $distances,
            'timestamps' => [
                'created_at'                => $request->created_at->format('Y-m-d H:i:s'),
                'accepted_at'               => $request->accepted_at?->format('Y-m-d H:i:s'),
                'courier_accepted_at'       => $request->courier_accepted_at?->format('Y-m-d H:i:s'),
                'pickup_started_at'         => $request->pickup_started_at?->format('Y-m-d H:i:s'),
                'pickup_completed_at'       => $request->pickup_completed_at?->format('Y-m-d H:i:s'),
                'transit_started_at'        => $request->transit_started_at?->format('Y-m-d H:i:s'),
                'arrived_at_destination_at' => $request->arrived_at_destination_at?->format('Y-m-d H:i:s'),
                'delivered_at'              => $request->delivered_at?->format('Y-m-d H:i:s'),
                'completed_at'              => $request->completed_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    // ─── Payments ─────────────────────────────────────────────────────────────

    public function payments(Request $httpRequest)
    {
        $payments = SpecimenRequest::whereNotNull('total_price')
            ->where('total_price', '>', 0)
            ->with(['client', 'facility', 'courier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function viewPayment(SpecimenRequest $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    public function refundPayment(Request $httpRequest, SpecimenRequest $payment)
    {
        $payment->update(['payment_status' => 'refunded']);
        return back()->with('success', 'Payment marked as refunded.');
    }

    public function markPaymentAsPaid(Request $httpRequest, SpecimenRequest $payment)
    {
        $payment->update(['payment_status' => 'paid']);
        return back()->with('success', 'Payment marked as paid.');
    }

    public function updatePaymentStatus(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'payment_status' => 'required|in:pending,paid,overdue,refunded,waived',
        ]);

        $request->update(['payment_status' => $validated['payment_status']]);

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment status updated.']);
        }

        return back()->with('success', 'Payment status updated.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function buildPricing(SpecimenRequest $request): array
    {
        // IMPORTANT:
        // If the client already generated pricing during request creation,
        // keep those exact values to avoid admin-side recalculation drift
        // (for example from re-geocoding / different distance miles).
        $hasClientPricing = (float) ($request->total_price ?? 0) > 0
            && $request->base_price !== null;

        if ($hasClientPricing) {
            $distanceMiles        = (float) ($request->distance_miles ?? 0);
            $basePrice            = (float) ($request->base_price ?? 0);
            $distanceCharge       = (float) ($request->distance_charge ?? 0);
            $statUrgentCharge     = (float) ($request->stat_urgent_charge ?? 0);
            $nightHoursCharge     = (float) ($request->night_hours_charge ?? 0);
            $weekendCharge        = (float) ($request->weekend_charge ?? 0);
            $coldChainCharge      = (float) ($request->cold_chain_charge ?? 0);
            $additionalStopCharge = (float) ($request->additional_stop_charge ?? 0);
            $totalPrice           = (float) $request->total_price;
        } else {
            $distanceMiles        = $this->calculateDistanceMiles($request);
            $basePrice            = 50.00;
            $distanceCharge       = $distanceMiles > 15 ? ($distanceMiles - 15) * 2.00 : 0.00;
            $statUrgentCharge     = $request->priority_level === 'stat' ? 20.00 : 0.00;
            $pickupTime           = $request->scheduled_pickup_time;
            $nightHoursCharge     = ($pickupTime && $pickupTime->hour >= 18) ? 25.00 : 0.00;
            $weekendCharge        = ($pickupTime && in_array($pickupTime->dayOfWeek, [0, 6])) ? $basePrice * 0.35 : 0.00;
            $coldChainCharge      = in_array($request->temperature_requirement, ['2-8c', '-20c', '-80c']) ? 7.00 : 0.00;
            $additionalStopCharge = ($request->relationLoaded('stops') ? $request->stops->count() : 0) * 10.00;
            $totalPrice           = $basePrice + $distanceCharge + $statUrgentCharge + $nightHoursCharge + $weekendCharge + $coldChainCharge + $additionalStopCharge;
        }

        $courierFee   = round($totalPrice * 0.70, 2);
        $adminFee     = round($totalPrice * 0.20, 2);
        $profitMargin = round($totalPrice * 0.10, 2);

        return [
            'fields' => [
                'base_price'             => $basePrice,
                'distance_miles'         => $distanceMiles,
                'distance_charge'        => $distanceCharge,
                'stat_urgent_charge'     => $statUrgentCharge,
                'night_hours_charge'     => $nightHoursCharge,
                'weekend_charge'         => $weekendCharge,
                'cold_chain_charge'      => $coldChainCharge,
                'additional_stop_charge' => $additionalStopCharge,
                'total_price'            => $totalPrice,
                'courier_fee'            => $courierFee,
                'admin_fee'              => $adminFee,
                'profit_margin'          => $profitMargin,
                'has_stat_urgent'        => $request->priority_level === 'stat',
                'has_night_service'      => $nightHoursCharge > 0,
                'has_weekend_service'    => $weekendCharge > 0,
                'has_cold_chain'         => $coldChainCharge > 0,
                'additional_stops'       => $hasClientPricing
                    ? (int) ($request->additional_stops ?? 0)
                    : ($request->relationLoaded('stops') ? $request->stops->count() : 0),
                'is_price_quoted'        => true,
            ],
        ];
    }

    private function buildBreakdown(SpecimenRequest $request): array
    {
        return [
            'base_price'             => $request->base_price,
            'distance_charge'        => $request->distance_charge,
            'stat_urgent_charge'     => $request->stat_urgent_charge,
            'night_hours_charge'     => $request->night_hours_charge,
            'weekend_charge'         => $request->weekend_charge,
            'cold_chain_charge'      => $request->cold_chain_charge,
            'additional_stop_charge' => $request->additional_stop_charge,
            'admin_fee'              => $request->admin_fee,
            'profit_margin'          => $request->profit_margin,
        ];
    }

    private function calculateDistanceMiles(SpecimenRequest $request): float
    {
        if ($request->pickup_latitude && $request->pickup_longitude && $request->delivery_latitude && $request->delivery_longitude) {
            $R       = 3959;
            $latFrom = deg2rad($request->pickup_latitude);
            $lonFrom = deg2rad($request->pickup_longitude);
            $latTo   = deg2rad($request->delivery_latitude);
            $lonTo   = deg2rad($request->delivery_longitude);
            $dLat    = $latTo - $latFrom;
            $dLon    = $lonTo - $lonFrom;
            return $R * 2 * asin(sqrt(sin($dLat / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($dLon / 2) ** 2));
        }
        return 10.00;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $R       = 6371;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);
        $dLat    = $latTo - $latFrom;
        $dLon    = $lonTo - $lonFrom;
        return $R * 2 * asin(sqrt(sin($dLat / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($dLon / 2) ** 2));
    }

    private function calculateETA(float $distanceKm, $speedRaw): int
    {
        $speedKmh = $speedRaw ? (float) $speedRaw * 3.6 : 30;
        if ($speedKmh <= 0) $speedKmh = 30;
        return round(($distanceKm / $speedKmh) * 60);
    }

    private function calculateDeliveryProgress(SpecimenRequest $request): int
    {
        $map = [
            'pending_approval' => 5,
            'approved' => 15,
            'pending_courier_acceptance' => 20,
            'assigned' => 25,
            'accepted_by_courier' => 35,
            'awaiting_pickup_proof' => 45,
            'picked_up' => 55,
            'in_transit' => 70,
            'arrived_at_destination' => 85,
            'delivered' => 95,
            'completed' => 100,
            'cancelled' => 0,
        ];
        $progress = $map[$request->status] ?? 0;
        if (in_array($request->status, ['in_transit', 'picked_up', 'accepted_by_courier', 'awaiting_pickup_proof', 'arrived_at_destination']) && $request->courier) {
            $progress += 5;
        }
        return min(100, $progress);
    }

    private function reverseGeocode($latitude, $longitude): string
    {
        if (! $latitude || ! $longitude) {
            return 'Location not available';
        }
        $cacheKey      = 'reverse_geocode_eng_' . round($latitude, 6) . '_' . round($longitude, 6);
        $cachedAddress = Cache::get($cacheKey);
        if ($cachedAddress) {
            return $cachedAddress;
        }
        try {
            $response = Http::withHeaders([
                'User-Agent'      => config('app.name') . '/1.0',
                'Accept'          => 'application/json',
                'Accept-Language' => 'en',
            ])->timeout(3)->get('https://nominatim.openstreetmap.org/reverse', [
                'format'          => 'json',
                'lat'             => $latitude,
                'lon'             => $longitude,
                'zoom'            => 18,
                'addressdetails'  => 1,
                'accept-language' => 'en',
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['display_name'])) {
                    Cache::put($cacheKey, $data['display_name'], 86400);
                    return $data['display_name'];
                }
            }
        } catch (\Exception $e) {
            Log::info('Admin reverse geocoding failed: ' . $e->getMessage());
        }
        return sprintf('%.4f, %.4f', $latitude, $longitude);
    }
}
