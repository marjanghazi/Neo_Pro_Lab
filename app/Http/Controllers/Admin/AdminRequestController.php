<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Facility;
use App\Models\User;
use App\Models\Notification;
use App\Models\CourierQuote;
use App\Models\AuditLog;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $request->load(['client', 'facility', 'courier', 'stops', 'documents']);

        // Load the active/latest quote if any
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

    // ─── Assign Courier (direct, no quote) ────────────────────────────────────

    public function assignCourier(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $request->update([
            'assigned_to' => $validated['courier_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status'      => 'assigned',
        ]);

        $this->notifyCourier(
            $validated['courier_id'],
            'request_assigned',
            'New Assignment',
            "You have been assigned to request #{$request->request_number}",
            $request->id,
            [
                'request_id'    => $request->id,
                'request_number'=> $request->request_number,
                'assigned_by'   => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'assigned_at'   => now()->toDateTimeString(),
            ]
        );

        $this->notifyAdmins(
            'request_assigned',
            'Request Assigned',
            "Request #{$request->request_number} has been assigned to a courier",
            $request->id,
            [
                'request_id'    => $request->id,
                'request_number'=> $request->request_number,
                'courier_id'    => $validated['courier_id'],
                'assigned_by'   => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            ]
        );

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Courier assigned successfully.',
                'redirect' => route('admin.requests.show', $request),
            ]);
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', 'Courier assigned successfully.');
    }

    // ─── Update Status (approve / reject / cancel) ────────────────────────────

    public function updateStatus(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:approved,rejected,cancelled',
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === 'cancelled') {
            $updates['cancelled_at'] = now();
            $updates['cancelled_by'] = Auth::id();
        }

        $request->update($updates);

        // If we're cancelling a pending-courier-acceptance request, expire its quote
        if ($validated['status'] === 'cancelled' && $request->courier_quote_id) {
            CourierQuote::where('id', $request->courier_quote_id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        }

        if (in_array($validated['status'], ['approved', 'rejected']) && $request->client_id) {
            $this->notifyClient(
                $request->client_id,
                'status_update',
                'Request ' . ucfirst($validated['status']),
                "Your request #{$request->request_number} has been {$validated['status']}.",
                $request->id,
                [
                    'request_id'    => $request->id,
                    'request_number'=> $request->request_number,
                    'status'        => $validated['status'],
                    'updated_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'updated_at'    => now()->toDateTimeString(),
                ]
            );
        }

        $this->notifyAdmins(
            'status_update',
            'Request Status Updated',
            "Request #{$request->request_number} has been {$validated['status']}",
            $request->id,
            [
                'request_id'    => $request->id,
                'request_number'=> $request->request_number,
                'status'        => $validated['status'],
                'updated_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
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
            'courier_id'   => 'required|exists:users,id',
            'courier_fee'  => 'required|numeric|min:0',
            'total_price'  => 'required|numeric|min:0',
            'valid_hours'  => 'nullable|integer|min:1|max:72',
        ]);

        try {
            // Expire any previous pending quotes for this request+courier
            CourierQuote::where('request_id', $request->id)
                ->where('courier_id', $validated['courier_id'])
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            // Auto-calculate pricing if not done yet
            if (! $request->is_price_quoted) {
                $pricing = $this->buildPricing($request);
                $request->update($pricing['fields']);
                $request->refresh();
            }

            $quote = CourierQuote::create([
                'request_id'  => $request->id,
                'courier_id'  => $validated['courier_id'],
                'courier_fee' => $validated['courier_fee'],
                'total_price' => $validated['total_price'],
                'breakdown'   => $this->buildBreakdown($request),
                'valid_until' => now()->addHours($validated['valid_hours'] ?? 24),
                'status'      => 'pending',
            ]);

            $request->update([
                'courier_quote_id'   => $quote->id,
                'courier_can_accept' => true,
                'acceptance_deadline'=> $quote->valid_until,
            ]);

            $this->notifyCourier(
                $validated['courier_id'],
                'quote_received',
                'New Price Quote',
                "You have a new price quote for request #{$request->request_number}",
                $request->id,
                [
                    'request_id'    => $request->id,
                    'request_number'=> $request->request_number,
                    'quote_id'      => $quote->id,
                    'courier_fee'   => $quote->courier_fee,
                    'total_price'   => $quote->total_price,
                    'valid_until'   => $quote->valid_until->toDateTimeString(),
                    'created_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]
            );

            $this->notifyAdmins(
                'quote_created',
                'New Quote Created',
                "Quote sent to courier for request #{$request->request_number}",
                $request->id,
                [
                    'request_id'    => $request->id,
                    'request_number'=> $request->request_number,
                    'quote_id'      => $quote->id,
                    'courier_id'    => $validated['courier_id'],
                    'total_price'   => $quote->total_price,
                    'created_by'    => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]
            );

            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Price quote sent to courier successfully.',
                    'data'    => [
                        'quote_id'    => $quote->id,
                        'courier_fee' => $quote->courier_fee,
                        'total_price' => $quote->total_price,
                        'valid_until' => $quote->valid_until->format('Y-m-d H:i:s'),
                    ],
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', 'Price quote sent to courier successfully. Awaiting response.');

        } catch (\Exception $e) {
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.requests.show', $request)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Assign With Quote (assignment + quote in one step) ───────────────────

    public function assignWithQuote(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id'  => 'required|exists:users,id',
            'valid_hours' => 'nullable|integer|min:1|max:72',
        ]);

        try {
            // Expire any previous pending quotes
            CourierQuote::where('request_id', $request->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            // Calculate pricing if needed
            if (! $request->is_price_quoted) {
                $pricing = $this->buildPricing($request);
                $request->update($pricing['fields']);
                $request->refresh();
            }

            $quote = CourierQuote::create([
                'request_id'  => $request->id,
                'courier_id'  => $validated['courier_id'],
                'courier_fee' => $request->courier_fee,
                'total_price' => $request->total_price,
                'breakdown'   => $this->buildBreakdown($request),
                'valid_until' => now()->addHours($validated['valid_hours'] ?? 24),
                'status'      => 'pending',
            ]);

            $request->update([
                'assigned_to'         => $validated['courier_id'],
                'assigned_by'         => auth()->id(),
                'assigned_at'         => now(),
                'courier_quote_id'    => $quote->id,
                'courier_can_accept'  => true,
                'acceptance_deadline' => $quote->valid_until,
                'status'              => 'pending_courier_acceptance',
            ]);

            $courier = User::find($validated['courier_id']);

            $this->notifyCourier(
                $validated['courier_id'],
                'request_assigned_with_quote',
                'New Assignment with Price Quote',
                "You have been assigned to request #{$request->request_number}. Please review and accept or decline the quote.",
                $request->id,
                [
                    'request_id'    => $request->id,
                    'request_number'=> $request->request_number,
                    'quote_id'      => $quote->id,
                    'courier_fee'   => number_format($quote->courier_fee, 2),
                    'total_price'   => number_format($quote->total_price, 2),
                    'deadline'      => $quote->valid_until->format('M d, Y h:i A'),
                    'assigned_by'   => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'quote_url'     => route('courier.requests.quote', $request->id),
                ]
            );

            if ($request->client_id) {
                $this->notifyClient(
                    $request->client_id,
                    'request_assigned',
                    'Request Assigned to Courier',
                    "Your request #{$request->request_number} has been assigned and is awaiting courier acceptance.",
                    $request->id,
                    [
                        'request_id'    => $request->id,
                        'request_number'=> $request->request_number,
                        'status'        => 'pending_courier_acceptance',
                        'assigned_at'   => now()->toDateTimeString(),
                    ]
                );
            }

            $this->notifyAdmins(
                'request_assigned_with_quote',
                'Request Assigned with Quote',
                "Request #{$request->request_number} assigned to {$courier->first_name} {$courier->last_name} — awaiting acceptance.",
                $request->id,
                [
                    'request_id'    => $request->id,
                    'request_number'=> $request->request_number,
                    'courier_id'    => $validated['courier_id'],
                    'quote_id'      => $quote->id,
                    'total_price'   => $quote->total_price,
                    'assigned_by'   => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]
            );

            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Courier assigned with price quote. Waiting for acceptance.',
                    'data'    => [
                        'quote_id'    => $quote->id,
                        'courier_fee' => $quote->courier_fee,
                        'total_price' => $quote->total_price,
                        'valid_until' => $quote->valid_until->format('Y-m-d H:i:s'),
                        'status'      => 'pending_courier_acceptance',
                    ],
                ]);
            }

            return redirect()->route('admin.requests.show', $request)
                ->with('success', 'Courier assigned with price quote. Waiting for courier acceptance.');

        } catch (\Exception $e) {
            if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.requests.show', $request)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ─── Resend / Cancel Quote ────────────────────────────────────────────────

    /**
     * Cancel a pending quote and reset the request back to 'approved'.
     */
    public function cancelQuote(Request $httpRequest, SpecimenRequest $request)
    {
        // Expire the active quote
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
            'base_price'            => $request->base_price ?? 0,
            'distance_miles'        => $request->distance_miles ?? 0,
            'distance_charge'       => $request->distance_charge ?? 0,
            'stat_urgent_charge'    => $request->stat_urgent_charge ?? 0,
            'night_hours_charge'    => $request->night_hours_charge ?? 0,
            'weekend_charge'        => $request->weekend_charge ?? 0,
            'cold_chain_charge'     => $request->cold_chain_charge ?? 0,
            'additional_stop_charge'=> $request->additional_stop_charge ?? 0,
            'total_price'           => $request->total_price ?? 0,
            'courier_fee'           => $request->courier_fee ?? 0,
            'admin_fee'             => $request->admin_fee ?? 0,
            'profit_margin'         => $request->profit_margin ?? 0,
            'is_price_quoted'       => $request->is_price_quoted ?? false,
        ];

        if ($httpRequest->ajax() || $httpRequest->wantsJson()) {
            return response()->json(['success' => true, 'data' => $data]);
        }

        return $data;
    }

    // ─── Courier Location API (admin tracking panel) ──────────────────────────

    public function getCourierLocation(Request $httpRequest, User $courier)
    {
        // Try cache first (real-time)
        $location = cache()->get('courier_location_' . $courier->id);

        // Fall back to DB
        if (! $location) {
            $dbLoc = \App\Models\CourierLocation::where('courier_id', $courier->id)->first();
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

        return response()->json([
            'courier' => [
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
        $request->load(['courier', 'client', 'facility']);
        return view('admin.requests.track', compact('request'));
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

    /**
     * Build pricing fields array from a SpecimenRequest.
     * Returns ['fields' => [...]] ready to pass to $request->update().
     */
    private function buildPricing(SpecimenRequest $request): array
    {
        $distanceMiles = $this->calculateDistanceMiles($request);
        $basePrice     = 50.00;

        $distanceCharge       = $distanceMiles > 15 ? ($distanceMiles - 15) * 2.00 : 0.00;
        $statUrgentCharge     = $request->priority_level === 'stat' ? 20.00 : 0.00;

        $pickupTime           = $request->scheduled_pickup_time;
        $nightHoursCharge     = ($pickupTime && $pickupTime->hour >= 18) ? 25.00 : 0.00;
        $weekendCharge        = ($pickupTime && in_array($pickupTime->dayOfWeek, [0, 6])) ? $basePrice * 0.35 : 0.00;
        $coldChainCharge      = in_array($request->temperature_requirement, ['2-8c', '-20c', '-80c']) ? 7.00 : 0.00;
        $additionalStopCharge = ($request->relationLoaded('stops') ? $request->stops->count() : 0) * 10.00;

        $totalPrice   = $basePrice + $distanceCharge + $statUrgentCharge + $nightHoursCharge
                      + $weekendCharge + $coldChainCharge + $additionalStopCharge;
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
                'additional_stops'       => ($request->relationLoaded('stops') ? $request->stops->count() : 0),
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
        if (
            $request->pickup_latitude && $request->pickup_longitude &&
            $request->delivery_latitude && $request->delivery_longitude
        ) {
            $earthRadius = 3959;
            $latFrom = deg2rad($request->pickup_latitude);
            $lonFrom = deg2rad($request->pickup_longitude);
            $latTo   = deg2rad($request->delivery_latitude);
            $lonTo   = deg2rad($request->delivery_longitude);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

            return $angle * $earthRadius;
        }

        return 10.00;
    }
}