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
use App\Models\User;
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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:client');
    }

    public function dashboard()
    {
        $user     = Auth::user();
        $facility = $user->facilities()->first();

        $stats = [
            'total_requests'   => $user->createdRequests()->count(),
            'pending_requests' => $user->createdRequests()->where('status', 'pending_approval')->count(),
            'in_progress'      => $user->createdRequests()->whereIn('status', ['pending_courier_acceptance', 'accepted_by_courier', 'awaiting_pickup_proof', 'picked_up', 'in_transit', 'arrived_at_destination'])->count(),
            'completed'        => $user->createdRequests()->where('status', 'completed')->count(),
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
        $user   = Auth::user();

        $query = $user->createdRequests()->with(['courier', 'facility']);

        if ($status) {
            if ($status === 'in_transit') {
                $query->whereIn('status', ['pending_courier_acceptance', 'accepted_by_courier', 'awaiting_pickup_proof', 'picked_up', 'in_transit', 'arrived_at_destination']);
            } else {
                $query->where('status', $status);
            }
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('client.requests.index', compact('requests', 'status'));
    }

    public function createRequestWithData()
    {
        $user     = Auth::user();
        $facility = $user->facilities()->first();

        $pendingData = Session::get('pending_pickup_request');

        if (!$pendingData) {
            return redirect()->route('client.requests.create');
        }

        $prefilledData = [
            'recipient_name'          => $pendingData['name'] ?? '',
            'contact_phone'           => $pendingData['phone'] ?? $user->phone,
            'pickup_address'          => $pendingData['pickupAddress'] ?? '',
            'pickup_date'             => $pendingData['pickupDate'] ?? date('Y-m-d'),
            'pickup_time'             => $this->convertPickupTime($pendingData['pickupTime'] ?? ''),
            'delivery_address'        => $pendingData['dropoffAddress'] ?? '',
            'specimen_type'           => $this->convertSpecimenType($pendingData['specimenType'] ?? ''),
            'temperature_requirement' => $this->convertTemperatureRequirement($pendingData['temperature'] ?? ''),
            'quantity'                => 1,
            'priority_level'          => $pendingData['pickupTime'] === 'stat' ? 'stat' : 'routine',
            'special_instructions'    => $pendingData['description'] ?? '',
            'delivery_instructions'   => $pendingData['notes'] ?? '',
        ];

        Session::forget('pending_pickup_request');
        Session::flash('prefilled_request_data', $prefilledData);

        return view('client.requests.create', compact('facility'));
    }

    private function convertPickupTime($publicTime)
    {
        return [
            '800-900' => '8-10',
            '900-1000' => '10-12',
            '1000-1100' => '10-12',
            '1100-1200' => '10-12',
            '1200-100' => '12-14',
            '100-200' => '14-16',
            '200-300' => '14-16',
            '300-400' => '16-18',
            '400-500' => '16-18',
            'stat' => 'stat',
        ][$publicTime] ?? '8-10';
    }

    private function convertSpecimenType($publicType)
    {
        return [
            'blood' => 'blood',
            'urine' => 'urine',
            'biopsy' => 'biopsy',
            'lab' => 'other',
            'document' => 'other',
            'medication' => 'other',
            'vaccine' => 'other',
            'supply' => 'other',
            'other' => 'other',
        ][$publicType] ?? 'other';
    }

    private function convertTemperatureRequirement($publicTemp)
    {
        return ['room' => 'ambient', 'cool' => '2-8c', 'frozen' => '-20c', 'other' => 'ambient'][$publicTemp] ?? 'ambient';
    }

    public function createRequest()
    {
        $user     = Auth::user();
        $facility = $user->facilities()->first();
        return view('client.requests.create', compact('facility'));
    }

    // ====================================================================
    // PREVIEW REQUEST
    // ====================================================================
    public function previewRequest(Request $request)
    {
        $validated = $request->validate([
            'recipient_name'          => 'required|string|max:200',
            'contact_phone'           => 'required|string|max:20',
            'pickup_address'          => 'required|string',
            'pickup_latitude'         => 'nullable|numeric',
            'pickup_longitude'        => 'nullable|numeric',
            'pickup_date'             => 'required|date',
            'pickup_time'             => 'required|string',
            'delivery_address'        => 'required|string',
            'delivery_latitude'       => 'nullable|numeric',
            'delivery_longitude'      => 'nullable|numeric',
            'delivery_instructions'   => 'nullable|string',
            'specimen_type'           => 'required|string',
            'temperature_requirement' => 'required|string',
            'quantity'                => 'required|integer|min:1',
            'priority_level'          => 'required|string',
            'scheduled_specific_time' => 'required_if:priority_level,scheduled|nullable|date_format:H:i',
            'special_instructions'    => 'nullable|string',
            'stops'                   => 'nullable|array',
            'stops.*.type'            => 'required_with:stops|string',
            'stops.*.contact_name'    => 'nullable|string',
            'stops.*.address'         => 'required_with:stops|string',
            'stops.*.latitude'        => 'nullable|numeric',
            'stops.*.longitude'       => 'nullable|numeric',
            'stops.*.instructions'    => 'nullable|string',
            'documents'               => 'nullable|array',
            'documents.*'             => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            // FIX: use a flattened key pattern that Laravel can actually match for nested file arrays
            'stop_documents'          => 'nullable',
        ]);

        $user     = Auth::user();
        $facility = $user->facilities()->first();

        // Store main documents in temp location
        $sessionDocs = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if (!$file || !$file->isValid()) continue;
                $tmpPath = $file->store('tmp_uploads', 'local');
                $sessionDocs[] = [
                    'tmp_path'      => $tmpPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ];
            }
        }

        // FIX: Store stop documents using $request->file() directly (not $validated)
        // This avoids the issue where nested file arrays get lost during validation
        $sessionStopDocs = [];
        $rawStopDocs = $request->file('stop_documents');
        if (!empty($rawStopDocs) && is_array($rawStopDocs)) {
            foreach ($rawStopDocs as $stopIndex => $stopFiles) {
                // stopFiles could be an array of files (from stop_documents[0][])
                if (!is_array($stopFiles)) {
                    $stopFiles = [$stopFiles];
                }
                foreach ($stopFiles as $file) {
                    if (!$file || !$file->isValid()) continue;
                    $tmpPath = $file->store('tmp_uploads', 'local');
                    $sessionStopDocs[$stopIndex][] = [
                        'tmp_path'      => $tmpPath,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'file_size'     => $file->getSize(),
                    ];
                }
            }
        }

        unset($validated['documents'], $validated['stop_documents']);

        // Merge scheduled specific time into pickup_time field
        if (($validated['priority_level'] ?? '') === 'scheduled' && !empty($validated['scheduled_specific_time'])) {
            $validated['pickup_time'] = 'scheduled:' . $validated['scheduled_specific_time'];
        }

        $priceData = null;
        try {
            $priceResult = $this->calculateRequestPrice(new Request([
                'pickup_address'          => $validated['pickup_address'],
                'delivery_address'        => $validated['delivery_address'],
                'pickup_date'             => $validated['pickup_date'],
                'pickup_time'             => $validated['pickup_time'],
                'priority_level'          => $validated['priority_level'],
                'specimen_type'           => $validated['specimen_type'],
                'temperature_requirement' => $validated['temperature_requirement'],
                'stops'                   => $validated['stops'] ?? [],
            ]));
            if ($priceResult->getStatusCode() === 200) {
                $decoded = json_decode($priceResult->getContent(), true);
                if (!empty($decoded['success'])) $priceData = $decoded['data'];
            }
        } catch (\Exception $e) {
            Log::warning('Price calculation in preview failed: ' . $e->getMessage());
        }

        if (!$priceData) {
            $priceData = [
                'base_price' => 50.00,
                'distance_miles' => 0,
                'distance_charge' => 0.00,
                'priority_charge' => 0.00,
                'night_charge' => 0.00,
                'weekend_charge' => 0.00,
                'temperature_charge' => 0.00,
                'additional_stops' => count($validated['stops'] ?? []),
                'additional_stops_charge' => 0.00,
                'subtotal' => 50.00,
                'tax_rate' => 8.5,
                'tax_amount' => 4.25,
                'total_price' => 54.25,
                'estimated_total' => 54.25,
            ];
        }

        $request->session()->put('request_preview_data', [
            'form_data'      => $validated,
            'price_data'     => $priceData,
            'documents'      => $sessionDocs,
            'stop_documents' => $sessionStopDocs,
        ]);

        $priceBreakdown = array_map(fn($v) => is_numeric($v) ? (float) $v : $v, $priceData);

        return view('client.requests.preview', compact('validated', 'priceBreakdown', 'facility'));
    }

    // ====================================================================
    // CALCULATE PRICE  (AJAX)
    // ====================================================================
    public function calculateRequestPrice(Request $httpRequest)
    {
        try {
            $validated = $httpRequest->validate([
                'pickup_address'          => 'required|string',
                'delivery_address'        => 'required|string',
                'pickup_date'             => 'required|date',
                'pickup_time'             => 'required|string',
                'priority_level'          => 'required|string',
                'specimen_type'           => 'required|string',
                'temperature_requirement' => 'required|string',
                'stops'                   => 'nullable|array',
                'stops.*.type'            => 'nullable|string',
                'stops.*.address'         => 'nullable|string',
            ]);

            $distanceMiles  = $this->calculateDistanceWithGoogleMaps($validated['pickup_address'], $validated['delivery_address']);
            $basePrice      = 50.00;
            $distCharge     = $distanceMiles > 15 ? ($distanceMiles - 15) * 2.00 : 0.00;
            $prioCharge     = strtolower($validated['priority_level']) === 'stat' ? 20.00 : 0.00;
            $dt             = $this->parsePickupDateTime($validated['pickup_date'], $validated['pickup_time']);
            $nightCharge    = $dt->hour >= 18 ? 25.00 : 0.00;
            $weekendCharge  = $this->isWeekendOrHoliday($dt) ? $basePrice * 0.35 : 0.00;
            $tempCharge     = in_array(strtolower($validated['temperature_requirement']), ['2-8c', '-20c', '-80c', 'cold_chain', 'refrigerated']) ? 7.00 : 0.00;
            $addStops       = isset($validated['stops']) ? count($validated['stops']) : 0;
            $addStopsCharge = $addStops * 10.00;
            $taxRate        = 0.085;
            $subtotal       = $basePrice + $distCharge + $prioCharge + $nightCharge + $weekendCharge + $tempCharge + $addStopsCharge;
            $taxAmount      = $subtotal * $taxRate;
            $total          = $subtotal + $taxAmount;

            return response()->json(['success' => true, 'data' => [
                'base_price'               => $basePrice,
                'distance_miles'           => round($distanceMiles, 1),
                'distance_charge'          => $distCharge,
                'distance_calculation_note' => 'One-way distance calculated via Google Maps',
                'priority_charge'          => $prioCharge,
                'priority_note'            => $prioCharge > 0 ? 'STAT / Urgent Delivery Surcharge' : 'Standard Priority',
                'night_charge'             => $nightCharge,
                'night_note'               => $nightCharge > 0 ? 'Night After-Hours Service (After 6PM)' : 'Daytime Service',
                'weekend_charge'           => $weekendCharge,
                'weekend_note'             => $weekendCharge > 0 ? 'Weekend/Holiday Surcharge (35% of base rate)' : 'Weekday Service',
                'temperature_charge'       => $tempCharge,
                'temperature_note'         => $tempCharge > 0 ? 'Cold-Chain Handling' : 'No Temperature Control Required',
                'additional_stops'         => $addStops,
                'additional_stops_charge'  => $addStopsCharge,
                'additional_stops_note'    => $addStops > 0 ? "{$addStops} additional stop(s) @ \$10.00 each" : 'No additional stops',
                'subtotal'                 => $subtotal,
                'tax_rate'                 => $taxRate * 100,
                'tax_amount'               => $taxAmount,
                'total_price'              => $total,
                'estimated_total'          => $total,
                'currency'                 => 'USD',
                'calculation_time'         => now()->toDateTimeString(),
            ]]);
        } catch (\Exception $e) {
            Log::error('Price calculation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ====================================================================
    // STORE REQUEST
    // ====================================================================
    public function storeRequest(Request $request)
    {
        $previewData = $request->session()->get('request_preview_data');

        if ($previewData) {
            $validated       = $previewData['form_data'];
            $priceData       = $previewData['price_data'];
            $sessionDocs     = $previewData['documents'] ?? [];
            $sessionStopDocs = $previewData['stop_documents'] ?? [];
            $request->session()->forget('request_preview_data');
        } else {
            $validated = $request->validate([
                'recipient_name'          => 'required|string|max:200',
                'contact_phone'           => 'required|string|max:20',
                'pickup_address'          => 'required|string',
                'pickup_date'             => 'required|date',
                'pickup_time'             => 'required|string',
                'delivery_address'        => 'required|string',
                'delivery_instructions'   => 'nullable|string',
                'specimen_type'           => 'required|string',
                'temperature_requirement' => 'required|string',
                'quantity'                => 'required|integer|min:1',
                'priority_level'          => 'required|string',
                'scheduled_specific_time' => 'required_if:priority_level,scheduled|nullable|date_format:H:i',
                'special_instructions'    => 'nullable|string',
                'stops'                   => 'nullable|array',
                'stops.*.type'            => 'required|string',
                'stops.*.contact_name'    => 'nullable|string',
                'stops.*.address'         => 'required|string',
                'stops.*.instructions'    => 'nullable|string',
            ]);

            $priceData = null;
            try {
                $priceResult = $this->calculateRequestPrice(new Request($validated));
                if ($priceResult->getStatusCode() === 200) {
                    $decoded = json_decode($priceResult->getContent(), true);
                    $priceData = $decoded['success'] ? $decoded['data'] : null;
                }
            } catch (\Exception $e) { /* non-fatal */
            }

            $sessionDocs     = [];
            $sessionStopDocs = [];

            if (($validated['priority_level'] ?? '') === 'scheduled' && !empty($validated['scheduled_specific_time'])) {
                $validated['pickup_time'] = 'scheduled:' . $validated['scheduled_specific_time'];
            }
        }

        $user     = Auth::user();
        $facility = $user->facilities()->first();

        $pickupCoords    = $this->geocodeAddress($validated['pickup_address']);
        $deliveryCoords  = $this->geocodeAddress($validated['delivery_address']);
        $scheduledPickup = $this->parsePickupDateTime($validated['pickup_date'], $validated['pickup_time']);

        $specimenRequest = SpecimenRequest::create([
            'facility_id'             => $facility ? $facility->id : null,
            'client_id'               => $user->id,
            'recipient_name'          => $validated['recipient_name'],
            'pickup_address'          => $validated['pickup_address'],
            'pickup_latitude'         => $pickupCoords['latitude'] ?? null,
            'pickup_longitude'        => $pickupCoords['longitude'] ?? null,
            'delivery_address'        => $validated['delivery_address'],
            'delivery_latitude'       => $deliveryCoords['latitude'] ?? null,
            'delivery_longitude'      => $deliveryCoords['longitude'] ?? null,
            'delivery_instructions'   => $validated['delivery_instructions'] ?? null,
            'specimen_type'           => $validated['specimen_type'],
            'specimen_type_other'     => $validated['specimen_type'] === 'other' ? ($validated['specimen_type_other'] ?? null) : null,
            'temperature_requirement' => $validated['temperature_requirement'],
            'quantity'                => $validated['quantity'],
            'priority_level'          => $validated['priority_level'],
            'special_instructions'    => $validated['special_instructions'] ?? null,
            'scheduled_pickup_time'   => $scheduledPickup,
            'status'                  => 'pending_approval',
            'payment_status'          => 'pending',
            'payment_required'        => true,
            'total_price'             => $priceData ? $priceData['estimated_total'] : 0,
            'base_price'              => $priceData ? $priceData['base_price'] : 50.00,
            'distance_charge'         => $priceData ? $priceData['distance_charge'] : 0,
            'stat_urgent_charge'      => $priceData ? $priceData['priority_charge'] : 0,
            'night_hours_charge'      => $priceData ? $priceData['night_charge'] : 0,
            'weekend_charge'          => $priceData ? $priceData['weekend_charge'] : 0,
            'cold_chain_charge'       => $priceData ? $priceData['temperature_charge'] : 0,
            'additional_stop_charge'  => $priceData ? $priceData['additional_stops_charge'] : 0,
            'distance_miles'          => $priceData ? $priceData['distance_miles'] : 0,
            'additional_stops'        => $priceData ? $priceData['additional_stops'] : 0,
            'has_stat_urgent'         => ($validated['priority_level'] ?? '') === 'stat',
            'has_cold_chain'          => in_array($validated['temperature_requirement'] ?? '', ['2-8c', '-20c', '-80c']),
        ]);

        // Save stops and build index → stop_id map
        $savedStopIds = [];
        if (!empty($validated['stops'])) {
            $order = 1;
            foreach ($validated['stops'] as $index => $stop) {
                $coords    = $this->geocodeAddress($stop['address']);
                $savedStop = $specimenRequest->stops()->create([
                    'stop_type'    => $stop['type'],
                    'stop_order'   => $order++,
                    'contact_name' => $stop['contact_name'] ?? null,
                    'address'      => $stop['address'],
                    'instructions' => $stop['instructions'] ?? null,
                    'latitude'     => $coords['latitude'] ?? null,
                    'longitude'    => $coords['longitude'] ?? null,
                ]);
                $savedStopIds[$index] = $savedStop->id;
            }
        }

        // Save main documents from session temp files
        foreach ($sessionDocs as $docInfo) {
            if (!Storage::disk('local')->exists($docInfo['tmp_path'])) {
                Log::warning('Main doc temp file missing: ' . $docInfo['tmp_path']);
                continue;
            }
            $finalPath = 'request_documents/' . Str::uuid() . '_' . $docInfo['original_name'];
            Storage::disk('public')->put($finalPath, Storage::disk('local')->get($docInfo['tmp_path']));
            Storage::disk('local')->delete($docInfo['tmp_path']);
            $specimenRequest->documents()->create([
                'stop_id'       => null,
                'title'         => $docInfo['original_name'],
                'document_type' => 'other',
                'file_name'     => $docInfo['original_name'],
                'file_path'     => $finalPath,
                'file_size'     => $docInfo['file_size'],
                'mime_type'     => $docInfo['mime_type'],
                'uploaded_by'   => $user->id,
            ]);
        }

        // Save main documents uploaded directly (non-preview flow)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('request_documents', 'public');
                $specimenRequest->documents()->create([
                    'stop_id'       => null,
                    'title'         => $file->getClientOriginalName(),
                    'document_type' => 'other',
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getMimeType(),
                    'uploaded_by'   => $user->id,
                ]);
            }
        }

        // FIX: Save stop documents from session temp files
        // $sessionStopDocs is keyed by the form's stop index (e.g. 0, 1, 2)
        // $savedStopIds maps the same form index to the newly created stop's DB id
        foreach ($sessionStopDocs as $stopIndex => $docInfos) {
            $stopId = $savedStopIds[$stopIndex] ?? null;
            foreach ($docInfos as $docInfo) {
                if (!Storage::disk('local')->exists($docInfo['tmp_path'])) {
                    Log::warning('Stop doc temp file missing: ' . $docInfo['tmp_path'] . ' for stop index ' . $stopIndex);
                    continue;
                }
                // Use a unique filename to avoid collisions between stops
                $finalPath = 'request_documents/' . Str::uuid() . '_' . $docInfo['original_name'];
                Storage::disk('public')->put($finalPath, Storage::disk('local')->get($docInfo['tmp_path']));
                Storage::disk('local')->delete($docInfo['tmp_path']);
                $specimenRequest->documents()->create([
                    'stop_id'       => $stopId,
                    'title'         => $docInfo['original_name'],
                    'document_type' => 'other',
                    'file_name'     => $docInfo['original_name'],
                    'file_path'     => $finalPath,
                    'file_size'     => $docInfo['file_size'],
                    'mime_type'     => $docInfo['mime_type'],
                    'uploaded_by'   => $user->id,
                ]);
            }
        }

        if ($specimenRequest->estimated_price > 0) {
            (new PaymentService())->createPayment($specimenRequest, $user);
        }

        try {
            Mail::to('admin@neoprolab.com')->send(new \App\Mail\NewOrderNotification([
                'request'        => $specimenRequest,
                'client'         => $user,
                'facility'       => $facility,
                'price_data'     => $priceData,
                'validated_data' => $validated,
                'request_url'    => route('admin.requests.show', $specimenRequest->id),
                'dashboard_url'  => route('admin.dashboard'),
            ]));
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }

        return redirect()->route('client.requests.index')
            ->with('success', 'Specimen request submitted successfully! It is now pending approval. Please complete payment to schedule pickup.');
    }

    // ====================================================================
    // REPORTS
    // ====================================================================
    public function reports(Request $httpRequest)
    {
        $user = Auth::user();

        $startDate = $httpRequest->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate   = $httpRequest->get('end_date',   now()->format('Y-m-d'));

        $baseQuery = $user->createdRequests()
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'completed'   => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled'   => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'pending'     => (clone $baseQuery)->where('status', 'pending_approval')->count(),
            'in_progress' => (clone $baseQuery)->whereIn('status', [
                'approved',
                'assigned',
                'accepted_by_courier',
                'awaiting_pickup_proof',
                'picked_up',
                'in_transit',
                'arrived_at_destination',
            ])->count(),
        ];

        $requests = (clone $baseQuery)
            ->with(['courier', 'facility'])
            ->orderBy('created_at', 'desc')
            ->get();

        $specimenTypes = (clone $baseQuery)
            ->selectRaw('specimen_type, count(*) as count')
            ->groupBy('specimen_type')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'specimen_type');

        $priorities = (clone $baseQuery)
            ->selectRaw('priority_level, count(*) as count')
            ->groupBy('priority_level')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'priority_level');

        $monthlyTrend = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        return view('client.reports.index', compact(
            'startDate',
            'endDate',
            'stats',
            'requests',
            'specimenTypes',
            'priorities',
            'monthlyTrend'
        ));
    }

    public function downloadReport(Request $httpRequest)
    {
        $user      = Auth::user();
        $format    = $httpRequest->input('format', 'csv');
        $startDate = $httpRequest->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate   = $httpRequest->input('end_date',   now()->format('Y-m-d'));

        $requests = $user->createdRequests()
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->with(['courier', 'facility'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers  = ['Request #', 'Status', 'Specimen', 'Priority', 'Pickup Address', 'Delivery Address', 'Created'];
        $filename = 'requests-report-' . now()->format('Y-m-d') . '.csv';

        $callback = function () use ($headers, $requests) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($requests as $r) {
                fputcsv($handle, [
                    $r->request_number,
                    $r->status,
                    $r->specimen_type,
                    $r->priority_level,
                    $r->pickup_address,
                    $r->delivery_address,
                    $r->created_at->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    // ====================================================================
    // PAYMENTS
    // ====================================================================

    public function showPayment(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $payment = $request->payment;
        return view('client.payment', compact('request', 'payment'));
    }

    public function processPayment(Request $httpRequest, SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        try {
            $result = (new PaymentService())->processPayment($request, $httpRequest->all());
            if ($result['success'] ?? false) {
                return redirect()->route('client.payments.success', $result['payment'] ?? $request->id)
                    ->with('success', 'Payment processed successfully!');
            }
            return back()->with('error', $result['message'] ?? 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function paymentSuccess(Request $httpRequest, $payment)
    {
        $request = SpecimenRequest::where('client_id', Auth::id())->findOrFail($payment);
        return view('client.payments.success', compact('request'));
    }

    public function paymentCallback(Request $httpRequest, $payment)
    {
        try {
            (new PaymentService())->handleCallback($payment, $httpRequest->all());
        } catch (\Exception $e) {
            Log::error('Payment callback failed: ' . $e->getMessage());
        }
        return redirect()->route('client.requests.index');
    }

    public function downloadReceipt(Request $httpRequest, $payment)
    {
        $request = SpecimenRequest::where('client_id', Auth::id())->findOrFail($payment);
        $content = "RECEIPT\nRequest #: {$request->request_number}\nDate: {$request->created_at->format('Y-m-d')}\nStatus: {$request->status}\nAmount: \${$request->estimated_price}\n";
        return response($content, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"receipt-{$request->request_number}.txt\"",
        ]);
    }

    public function paymentHistory()
    {
        $payments = Auth::user()->createdRequests()
            ->with(['facility'])
            ->whereNotNull('payment_status')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('client.payments.history', compact('payments'));
    }

    public function viewPayment(Request $httpRequest, $payment)
    {
        $request = SpecimenRequest::where('client_id', Auth::id())->findOrFail($payment);
        return view('client.payments.view', compact('request'));
    }

    // ====================================================================
    // TRACKING API ENDPOINTS
    // ====================================================================

    public function getTrackingDetails(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $request->load(['courier', 'stops', 'pickupProofs']);

        $courierLocation = null;
        if ($request->courier_id) {
            $courierLocation = Cache::get('courier_location_' . $request->courier_id);
            if (!$courierLocation) {
                $dbLoc = CourierLocation::where('courier_id', $request->courier_id)->latest()->first();
                if ($dbLoc) {
                    $courierLocation = [
                        'latitude'  => (float) $dbLoc->latitude,
                        'longitude' => (float) $dbLoc->longitude,
                        'is_online' => (bool) $dbLoc->is_online,
                        'timestamp' => $dbLoc->created_at->timestamp,
                    ];
                }
            }
        }

        return response()->json([
            'request_status'     => $request->status,
            'pickup_latitude'    => $request->pickup_latitude    ? (float) $request->pickup_latitude    : null,
            'pickup_longitude'   => $request->pickup_longitude   ? (float) $request->pickup_longitude   : null,
            'delivery_latitude'  => $request->delivery_latitude  ? (float) $request->delivery_latitude  : null,
            'delivery_longitude' => $request->delivery_longitude ? (float) $request->delivery_longitude : null,
            'courier_location'   => $courierLocation,
            'stops'              => $request->stops->map(fn($s) => [
                'id'        => $s->id,
                'address'   => $s->address,
                'type'      => $s->stop_type,
                'completed' => $s->completed,
                'latitude'  => $s->latitude  ? (float) $s->latitude  : null,
                'longitude' => $s->longitude ? (float) $s->longitude : null,
            ]),
        ]);
    }

    public function getCourierLocationApi(Request $httpRequest, User $courier)
    {
        $hasRequest = SpecimenRequest::where('client_id', Auth::id())
            ->where('courier_id', $courier->id)
            ->whereIn('status', ['assigned', 'accepted_by_courier', 'picked_up', 'in_transit', 'arrived_at_destination'])
            ->exists();

        if (!$hasRequest) abort(403);

        $location = Cache::get('courier_location_' . $courier->id);
        if (!$location) {
            $dbLoc = CourierLocation::where('courier_id', $courier->id)->latest()->first();
            if ($dbLoc) {
                $location = [
                    'latitude'  => (float) $dbLoc->latitude,
                    'longitude' => (float) $dbLoc->longitude,
                    'is_online' => (bool) $dbLoc->is_online,
                    'timestamp' => $dbLoc->created_at->timestamp,
                ];
            }
        }

        return response()->json([
            'courier'  => ['id' => $courier->id, 'name' => $courier->full_name],
            'location' => $location,
            'status'   => ($location && ($location['is_online'] ?? false)) ? 'online' : 'offline',
        ]);
    }

    // ====================================================================
    // PRIVATE HELPERS
    // ====================================================================

    private function calculateDistanceWithGoogleMaps($origin, $destination)
    {
        $cacheKey = 'distance_' . md5($origin . $destination);
        if (($cached = Cache::get($cacheKey)) !== null) return $cached;
        try {
            $apiKey = config('services.google.maps_api_key');
            if (!empty($apiKey)) {
                $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'origins' => $origin,
                    'destinations' => $destination,
                    'units' => 'imperial',
                    'key' => $apiKey,
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                        $miles = $data['rows'][0]['elements'][0]['distance']['value'] / 1609.34;
                        Cache::put($cacheKey, $miles, 2592000);
                        return $miles;
                    }
                }
            }
            $pc = $this->geocodeAddress($origin);
            $dc = $this->geocodeAddress($destination);
            if ($pc['latitude'] && $dc['latitude']) {
                $miles = $this->haversineDistance($pc['latitude'], $pc['longitude'], $dc['latitude'], $dc['longitude']);
                Cache::put($cacheKey, $miles, 2592000);
                return $miles;
            }
        } catch (\Exception $e) {
            Log::warning('Distance calculation failed: ' . $e->getMessage());
        }
        return 15.0;
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R    = 3959;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function isWeekendOrHoliday(Carbon $date): bool
    {
        return $date->isWeekend();
    }

    private function parsePickupDateTime($date, $time): Carbon
    {
        if (str_starts_with($time, 'scheduled:')) {
            $timePart = substr($time, strlen('scheduled:'));
            [$h, $m] = explode(':', $timePart);
            return Carbon::parse($date)->setHour((int)$h)->setMinute((int)$m)->setSecond(0);
        }

        $hours = ['8-10' => 8, '10-12' => 10, '12-14' => 12, '14-16' => 14, '16-18' => 16, 'stat' => 18];
        return Carbon::parse($date)->setHour($hours[$time] ?? 8)->setMinute(0)->setSecond(0);
    }

    private function geocodeAddress($address)
    {
        if (empty($address)) return ['latitude' => null, 'longitude' => null];
        $cacheKey     = 'geocode_' . md5($address);
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult) return $cachedResult;
        try {
            $apiKey = config('services.google.maps_api_key');
            if (!empty($apiKey)) {
                $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address,
                    'key' => $apiKey,
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK' && isset($data['results'][0]['geometry']['location'])) {
                        $loc    = $data['results'][0]['geometry']['location'];
                        $result = ['latitude' => (float) $loc['lat'], 'longitude' => (float) $loc['lng']];
                        Cache::put($cacheKey, $result, 2592000);
                        return $result;
                    }
                }
            }
            $response = Http::withHeaders(['User-Agent' => config('app.name') . '/1.0'])->timeout(5)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'json',
                    'q' => $address,
                    'limit' => 1,
                    'addressdetails' => 1,
                ]);
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data[0]['lat'])) {
                    $result = ['latitude' => (float) $data[0]['lat'], 'longitude' => (float) $data[0]['lon']];
                    Cache::put($cacheKey, $result, 2592000);
                    return $result;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding failed: ' . $address . ' — ' . $e->getMessage());
        }
        return ['latitude' => null, 'longitude' => null];
    }

    private function reverseGeocode($latitude, $longitude)
    {
        if (!$latitude || !$longitude) return 'Location not available';
        $cacheKey     = 'reverse_geocode_' . md5($latitude . ',' . $longitude);
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult) return $cachedResult;
        try {
            $apiKey = config('services.google.maps_api_key');
            if (!empty($apiKey)) {
                $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => $latitude . ',' . $longitude,
                    'key' => $apiKey,
                    'language' => 'en',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK' && isset($data['results'][0]['formatted_address'])) {
                        $address = $data['results'][0]['formatted_address'];
                        Cache::put($cacheKey, $address, 86400);
                        return $address;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Reverse geocoding failed: ' . $e->getMessage());
        }
        return "Location: {$latitude}, {$longitude}";
    }

    // ====================================================================
    // REQUESTS — SHOW / TRACK / CANCEL / CONFIRM
    // ====================================================================

    public function trackRequest(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $request->load(['courier', 'stops', 'documents', 'pickupProofs', 'signatures', 'payment']);
        return view('client.requests.track', compact('request'));
    }

    public function showInvoice(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $request->load(['facility', 'stops', 'payment']);
        return view('client.requests.invoice', compact('request'));
    }

    public function showRequest(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $request->load(['courier', 'stops', 'documents.stop', 'pickupProofs', 'signatures', 'payment']);
        return view('client.requests.show', compact('request'));
    }

    public function cancelRequest(Request $request, SpecimenRequest $specimenRequest)
    {
        if ($specimenRequest->client_id != Auth::id()) abort(403);
        if (!in_array($specimenRequest->status, ['pending_approval', 'approved'])) {
            return back()->with('error', 'Request cannot be cancelled at this stage.');
        }
        $validated = $request->validate(['cancellation_reason' => 'required|string|min:10|max:500']);
        if ($specimenRequest->payment && $specimenRequest->payment->isPaid()) {
            (new PaymentService())->refundPayment($specimenRequest->payment, null, 'Request cancelled by client: ' . $validated['cancellation_reason']);
        }
        $specimenRequest->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at'        => now(),
        ]);
        return redirect()->route('client.requests.index')->with('success', 'Request has been cancelled successfully.');
    }

    public function confirmDelivery(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        return view('client.requests.confirm', compact('request'));
    }

    public function submitConfirmation(Request $httpRequest, SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $validated = $httpRequest->validate([
            'recipient_name' => 'required|string|max:200',
            'notes'          => 'nullable|string',
        ]);
        $request->update([
            'status'         => 'completed',
            'completed_at'   => now(),
            'recipient_name' => $validated['recipient_name'],
            'delivery_notes' => $validated['notes'],
        ]);
        return redirect()->route('client.requests.show', $request)->with('success', 'Delivery confirmed successfully! Thank you.');
    }

    // ====================================================================
    // DOCUMENTS
    // ====================================================================

    public function documents(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $documents = $request->documents()->with('stop')->orderBy('created_at', 'desc')->get();
        return view('client.requests.documents', compact('request', 'documents'));
    }

    public function downloadDocument(RequestDocument $document)
    {
        if ($document->request->client_id != Auth::id()) abort(403);
        if (!Storage::disk('public')->exists($document->file_path)) abort(404);
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function proofs(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $proofs = $request->pickupProofs()->orderBy('created_at', 'desc')->get();
        return view('client.requests.proofs', compact('request', 'proofs'));
    }

    // ====================================================================
    // TRACKING
    // ====================================================================

    public function tracking()
    {
        $activeRequests = Auth::user()->createdRequests()
            ->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])
            ->with(['courier', 'stops'])
            ->get();
        return view('client.tracking.index', compact('activeRequests'));
    }

    public function getActiveTracking()
    {
        $requests = Auth::user()->createdRequests()
            ->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])
            ->with(['courier'])
            ->get();
        return response()->json(['requests' => $requests]);
    }

    public function getCourierLocation(SpecimenRequest $request)
    {
        if ($request->client_id != Auth::id()) abort(403);
        $location = CourierLocation::where('courier_id', $request->courier_id)->latest()->first();
        return response()->json(['location' => $location]);
    }

    // ====================================================================
    // PROFILE
    // ====================================================================

    public function profile()
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();
        return view('client.profile.index', compact('user', 'facility'));
    }

    public function updateProfile(Request $request)
    {
        $user      = Auth::user();
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }
        $user->update($validated);
        return back()->with('success', 'Profile updated successfully.');
    }
}
