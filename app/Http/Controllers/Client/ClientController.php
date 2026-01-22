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

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:client');
    }

    // Existing methods...
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

        // Facility is optional, so we don't redirect if not found
        // We'll just pass null to the view
        return view('client.requests.create', compact('facility'));
    }

    public function storeRequest(Request $request)
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();

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

        // Create specimen request
        $specimenRequest = SpecimenRequest::create([
            'facility_id' => $facility ? $facility->id : null,
            'client_id' => $user->id,
            'recipient_name' => $validated['recipient_name'],
            'pickup_address' => $validated['pickup_address'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_instructions' => $validated['delivery_instructions'],
            'specimen_type' => $validated['specimen_type'],
            'temperature_requirement' => $validated['temperature_requirement'],
            'quantity' => $validated['quantity'],
            'priority_level' => $validated['priority_level'],
            'special_instructions' => $validated['special_instructions'],
            'scheduled_pickup_time' => $validated['pickup_date'] . ' ' . $this->getTimeFromWindow($validated['pickup_time']),
            'status' => 'pending_approval',
        ]);

        // Add additional stops
        if (!empty($validated['stops'])) {
            $stopOrder = 1;
            foreach ($validated['stops'] as $stop) {
                $specimenRequest->stops()->create([
                    'stop_type' => $stop['type'],
                    'stop_order' => $stopOrder++,
                    'contact_name' => $stop['contact_name'],
                    'address' => $stop['address'],
                    'instructions' => $stop['instructions'],
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

        // CREATE NOTIFICATION FOR ADMINS - ADD THIS CODE HERE
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

        return redirect()->route('client.requests.index')
            ->with('success', 'Specimen request submitted successfully! It is now pending approval.');
    }

    private function getTimeFromWindow($window)
    {
        $times = [
            '8-10' => '09:00:00',
            '10-12' => '11:00:00',
            '12-14' => '13:00:00',
            '14-16' => '15:00:00',
            '16-18' => '17:00:00',
            'stat' => date('H:i:s'),
        ];

        return $times[$window] ?? '12:00:00';
    }

    /**
     * Track individual request - NEW METHOD
     */
    public function trackRequest(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops', 'documents', 'pickupProofs', 'signatures']);

        return view('client.requests.track', compact('request'));
    }

    public function showRequest(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops', 'documents', 'pickupProofs', 'signatures']);

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
            ->with('success', 'Request cancelled successfully.');
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
            ->with(['courier', 'facility'])
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
            ->with(['courier', 'facility'])
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
                'Pickup Address',
                'Delivery Address',
                'Courier',
                'Created At',
                'Completed At',
            ]);

            // Data
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->created_at->format('Y-m-d'),
                    ucfirst($request->specimen_type),
                    ucfirst($request->priority_level),
                    str_replace('_', ' ', $request->status),
                    $request->pickup_address,
                    $request->delivery_address,
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
        // You'll need to install a PDF library like barryvdh/laravel-dompdf
        // For now, return a placeholder response
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
     * Reverse geocode coordinates to get address
     */
    private function reverseGeocode($latitude, $longitude)
    {
        // Return coordinates if invalid
        if (!$latitude || !$longitude) {
            return "Location not available";
        }

        // Try to get from cache first (cache for 1 hour)
        $cacheKey = 'reverse_geocode_' . round($latitude, 6) . '_' . round($longitude, 6);
        $cachedAddress = Cache::get($cacheKey);
        
        if ($cachedAddress) {
            return $cachedAddress;
        }

        // Using OpenStreetMap Nominatim API (free, no API key required)
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name') . '/1.0',
                'Accept' => 'application/json',
            ])->timeout(3)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
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
            \Log::info('Reverse geocoding failed, using fallback: ' . $e->getMessage());
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
            $latAbs, $latDir,
            $lngAbs, $lngDir
        );
    }

    /**
     * Get real-time courier location for a specific request
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
            ]);
        }

        $courier = $request->courier;
        
        // Try to get location from cache first (real-time updates)
        $cachedLocation = Cache::get('courier_location_' . $courier->id);
        
        // If not in cache, check database
        if (!$cachedLocation && class_exists(CourierLocation::class)) {
            $location = CourierLocation::where('courier_id', $courier->id)
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
                    'courier_id' => $courier->id,
                    'courier_name' => $courier->full_name,
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
                    'profile_image' => $courier->profile_image,
                ],
                'location' => null,
                'status' => 'offline',
                'message' => 'Courier location not available yet.',
            ]);
        }

        // Ensure the location data has proper structure
        $locationData = is_array($cachedLocation) ? $cachedLocation : (array)$cachedLocation;

        // Get formatted address from coordinates
        $formattedAddress = $this->reverseGeocode(
            $locationData['latitude'] ?? null,
            $locationData['longitude'] ?? null
        );

        return response()->json([
            'courier' => [
                'id' => $courier->id,
                'name' => $courier->full_name,
                'phone' => $courier->phone,
                'vehicle_type' => $courier->vehicle_type,
                'profile_image' => $courier->profile_image,
                'last_seen' => isset($locationData['last_update']) 
                    ? Carbon::parse($locationData['last_update'])->diffForHumans()
                    : 'Just now',
            ],
            'location' => [
                'latitude' => $locationData['latitude'] ?? null,
                'longitude' => $locationData['longitude'] ?? null,
                'accuracy' => $locationData['accuracy'] ?? null,
                'speed' => $locationData['speed'] ?? 0,
                'heading' => $locationData['heading'] ?? 0,
                'timestamp' => $locationData['timestamp'] ?? time(),
                'formatted_time' => isset($locationData['timestamp']) 
                    ? date('Y-m-d H:i:s', $locationData['timestamp'])
                    : date('Y-m-d H:i:s'),
                'is_online' => $locationData['is_online'] ?? false,
                'formatted_address' => $formattedAddress,
            ],
            'status' => ($locationData['is_online'] ?? false) ? 'online' : 'offline',
            'request_status' => $request->status,
        ]);
    }

    /**
     * Get detailed tracking information for a request
     */
    public function getTrackingDetails(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops', 'pickupProofs', 'signatures']);

        // Get courier location
        $courierLocation = null;
        $courier = $request->courier;
        
        if ($courier) {
            $cachedLocation = Cache::get('courier_location_' . $courier->id);
            if ($cachedLocation) {
                $courierLocation = is_array($cachedLocation) ? $cachedLocation : (array)$cachedLocation;
            } elseif (class_exists(CourierLocation::class)) {
                $location = CourierLocation::where('courier_id', $courier->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                if ($location) {
                    $courierLocation = $location->toArray();
                }
            }
        }

        // Calculate progress based on status
        $progress = $this->calculateDeliveryProgress($request);

        return response()->json([
            'request' => [
                'id' => $request->id,
                'request_number' => $request->request_number,
                'status' => $request->status,
                'status_display' => str_replace('_', ' ', $request->status),
                'pickup_address' => $request->pickup_address,
                'delivery_address' => $request->delivery_address,
                'scheduled_pickup_time' => $request->scheduled_pickup_time?->format('Y-m-d H:i:s'),
                'scheduled_delivery_time' => $request->scheduled_delivery_time?->format('Y-m-d H:i:s'),
                'priority_level' => $request->priority_level,
                'specimen_type' => $request->specimen_type,
                'temperature_requirement' => $request->temperature_requirement,
                'quantity' => $request->quantity,
            ],
            'courier' => $courier ? [
                'id' => $courier->id,
                'name' => $courier->full_name,
                'phone' => $courier->phone,
                'email' => $courier->email,
                'vehicle_type' => $courier->vehicle_type,
                'vehicle_number' => $courier->vehicle_number,
                'profile_image' => $courier->profile_image,
                'rating' => $courier->rating ?? 4.5,
            ] : null,
            'courier_location' => $courierLocation ? array_merge($courierLocation, [
                'formatted_address' => $this->reverseGeocode(
                    $courierLocation['latitude'] ?? null,
                    $courierLocation['longitude'] ?? null
                )
            ]) : null,
            'stops' => $request->stops->map(function($stop) {
                return [
                    'id' => $stop->id,
                    'type' => $stop->stop_type,
                    'address' => $stop->address,
                    'contact_name' => $stop->contact_name,
                    'instructions' => $stop->instructions,
                    'completed' => $stop->completed,
                    'completed_at' => $stop->completed_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'progress' => $progress,
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
            ->whereHas('role', function($q) {
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

        // Get formatted address
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
                'profile_image' => $courier->profile_image,
            ],
            'location' => array_merge($cachedLocation, ['formatted_address' => $formattedAddress]),
            'status' => ($cachedLocation['is_online'] ?? false) ? 'online' : 'offline',
        ]);
    }
}