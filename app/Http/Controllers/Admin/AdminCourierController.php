<?php
// app/Http/Controllers/Admin/AdminCourierController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\CourierVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourierApprovedMail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Twilio\Rest\Client; // Add this for SMS

class AdminCourierController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('role', function ($q) {
            $q->where('slug', 'courier');
        })->with(['courierVerification'])->withCount([
            'assignedRequests as active_assignments_count' => function ($q) {
                $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']);
            },
            'assignedRequests as completed_deliveries_count' => function ($q) {
                $q->where('status', 'completed');
            },
            'assignedRequests as total_assignments_count'
        ]);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;

            switch ($status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'verified':
                    $query->whereHas('courierVerification', function ($q) {
                        $q->where('verification_status', 'approved');
                    });
                    break;
                case 'pending':
                    $query->whereHas('courierVerification', function ($q) {
                        $q->where('verification_status', 'pending');
                    });
                    break;
                case 'available':
                    $query->where('is_active', true)
                        ->whereDoesntHave('assignedRequests', function ($q) {
                            $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']);
                        });
                    break;
                case 'busy':
                    $query->where('is_active', true)
                        ->whereHas('assignedRequests', function ($q) {
                            $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']);
                        });
                    break;
            }
        }

        $couriers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.couriers.index', compact('couriers'));
    }

    public function downloadDocument(User $courier, $documentType)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $verification = $courier->courierVerification;

        if (!$verification || !$verification->$documentType) {
            abort(404, 'Document not found');
        }

        $allowedDocuments = ['profile_image', 'government_id', 'proof_of_residency', 'drivers_license', 'medical_transport_cert'];

        if (!in_array($documentType, $allowedDocuments)) {
            abort(404, 'Invalid document type');
        }

        $path = storage_path('app/public/' . $verification->$documentType);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        // Get the original filename
        $originalName = basename($verification->$documentType);

        // Create a descriptive filename
        $documentNames = [
            'profile_image' => 'profile_image',
            'government_id' => 'government_id',
            'proof_of_residency' => 'proof_of_residency',
            'drivers_license' => 'drivers_license',
            'medical_transport_cert' => 'medical_transport_cert'
        ];

        $descriptiveName = $courier->full_name . '_' . $documentNames[$documentType] . '_' . date('Y-m-d');

        // Get file extension
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = $descriptiveName . '.' . $extension;

        return response()->download($path, $fileName);
    }

    public function show(User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $courier->load([
            'assignedRequests' => function ($q) {
                $q->with(['client', 'facility'])->orderBy('created_at', 'desc')->limit(10);
            },
            'courierVerification',
            'courierVerification.verifier'
        ]);

        $stats = [
            'total_assignments' => $courier->assignedRequests()->count(),
            'active_assignments' => $courier->assignedRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])->count(),
            'completed' => $courier->assignedRequests()->where('status', 'completed')->count(),
            'on_time_rate' => $this->calculateOnTimeRate($courier),
        ];

        // Get weekly performance data
        $weeklyData = $this->getWeeklyPerformanceData($courier);

        return view('admin.couriers.show', compact('courier', 'stats', 'weeklyData'));
    }

    public function sendSms(Request $request, User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $request->validate([
            'message' => 'required|string|max:160'
        ]);

        // Check if courier has phone number
        if (!$courier->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Courier does not have a phone number'
            ], 400);
        }

        try {
            // Initialize Twilio client
            $twilio = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            // Send SMS
            $twilio->messages->create(
                $courier->phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $request->message
                ]
            );

            // Log the SMS
            Log::info('SMS sent to courier', [
                'courier_id' => $courier->id,
                'phone' => $courier->phone,
                'message' => $request->message
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SMS sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verification(User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $courier->load('courierVerification');

        return view('admin.couriers.verification', compact('courier'));
    }

    public function approveVerification(Request $request, User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $verification = $courier->courierVerification;

        if (!$verification) {
            return redirect()->back()->with('error', 'No verification records found for this courier.');
        }

        $verification->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'rejection_reason' => null
        ]);

        // Auto-approve the user account as well
        $courier->update([
            'is_approved' => true,
            'is_active' => true
        ]);

        // Send approval email to the courier
        try {
            Mail::to($courier->email)->send(new CourierApprovedMail($courier));

            return redirect()->route('admin.couriers.show', $courier)
                ->with('success', 'Courier verification approved successfully. An email notification has been sent to the courier.');
        } catch (\Exception $e) {
            // Log the error but don't fail the approval
            Log::error('Failed to send approval email to courier: ' . $e->getMessage());

            return redirect()->route('admin.couriers.show', $courier)
                ->with('success', 'Courier verification approved successfully. Note: Email notification could not be sent.');
        }
    }

    public function rejectVerification(Request $request, User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        $verification = $courier->courierVerification;

        if (!$verification) {
            return redirect()->back()->with('error', 'No verification records found for this courier.');
        }

        $verification->update([
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason
        ]);

        // Optionally deactivate the account or keep it pending
        $courier->update([
            'is_approved' => false
        ]);

        // Send rejection email to the courier
        try {
            Mail::to($courier->email)->send(new \App\Mail\CourierRejectedMail($courier, $request->rejection_reason));

            return redirect()->route('admin.couriers.show', $courier)
                ->with('success', 'Courier verification rejected. An email notification with the reason has been sent to the courier.');
        } catch (\Exception $e) {
            // Log the error but don't fail the rejection
            Log::error('Failed to send rejection email to courier: ' . $e->getMessage());

            return redirect()->route('admin.couriers.show', $courier)
                ->with('success', 'Courier verification rejected. Note: Email notification could not be sent.');
        }
    }
    public function viewDocument(User $courier, $documentType)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $verification = $courier->courierVerification;

        if (!$verification || !$verification->$documentType) {
            abort(404, 'Document not found');
        }

        $allowedDocuments = ['profile_image', 'government_id', 'proof_of_residency', 'drivers_license', 'medical_transport_cert'];

        if (!in_array($documentType, $allowedDocuments)) {
            abort(404, 'Invalid document type');
        }

        $path = storage_path('app/public/' . $verification->$documentType);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file($path);
    }

    private function calculateOnTimeRate($courier)
    {
        $completedRequests = $courier->assignedRequests()
            ->where('status', 'completed')
            ->whereNotNull('estimated_delivery_time')
            ->whereNotNull('delivered_at')
            ->get();

        if ($completedRequests->count() === 0) {
            return 0;
        }

        $onTime = $completedRequests->filter(function ($request) {
            return $request->delivered_at->lte($request->estimated_delivery_time);
        })->count();

        return round(($onTime / $completedRequests->count()) * 100, 1);
    }

    private function getWeeklyPerformanceData($courier)
    {
        // Get the last 7 days
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Get all assignments for the courier in the last 7 days
        $assignments = $courier->assignedRequests()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        // Initialize array with all 7 days
        $dates = [];
        $deliveries = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayLabel = $date->format('D'); // Mon, Tue, etc.

            $labels[] = $dayLabel;
            $dates[] = $dateKey;

            // Count deliveries for this date
            if (isset($assignments[$dateKey])) {
                $deliveries[] = $assignments[$dateKey]->count();
            } else {
                $deliveries[] = 0;
            }
        }

        // Calculate completion times for on-time rate
        $completionData = [];
        foreach ($dates as $date) {
            $dayAssignments = $courier->assignedRequests()
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->whereNotNull('estimated_delivery_time')
                ->whereNotNull('delivered_at')
                ->get();

            if ($dayAssignments->count() > 0) {
                $onTimeCount = $dayAssignments->filter(function ($request) {
                    return $request->delivered_at->lte($request->estimated_delivery_time);
                })->count();

                $completionData[] = round(($onTimeCount / $dayAssignments->count()) * 100);
            } else {
                $completionData[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'deliveries' => $deliveries,
            'completion_rates' => $completionData,
            'total_deliveries' => array_sum($deliveries),
            'average_per_day' => count($deliveries) > 0 ? round(array_sum($deliveries) / count($deliveries), 1) : 0,
            'peak_day' => !empty($deliveries) ? max($deliveries) : 0,
        ];
    }

    public function create()
    {
        return view('admin.couriers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',

            // Document validation
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'government_id' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'proof_of_residency' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'drivers_license' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'medical_transport_cert' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Get courier role
        $courierRole = Role::where('slug', 'courier')->firstOrFail();

        // Create user
        $courier = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $courierRole->id,
            'is_active' => $validated['is_active'] ?? true,
            'is_approved' => true, // Admin created couriers are auto-approved
        ]);

        // Handle document uploads and create verification record
        $verificationData = [
            'user_id' => $courier->id,
            'verification_status' => 'pending', // Start as pending, admin can review
            'submitted_at' => now(),
        ];

        // Upload documents if provided
        $documentFields = [
            'profile_image',
            'government_id',
            'proof_of_residency',
            'drivers_license',
            'medical_transport_cert'
        ];

        foreach ($documentFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('courier-documents/' . str_replace('_', '-', $field), 'public');
                $verificationData[$field] = $path;
            }
        }

        // Create courier verification record
        CourierVerification::create($verificationData);

        // Optional: Send welcome email with document status
        try {
            // You can create a WelcomeCourierMail if needed
            // Mail::to($courier->email)->send(new WelcomeCourierMail($courier));
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to courier: ' . $e->getMessage());
        }

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier created successfully with uploaded documents!');
    }

    public function edit(User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        return view('admin.couriers.edit', compact('courier'));
    }

    public function update(Request $request, User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $courier->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $courier->id,
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Update basic information
        $courier->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $courier->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        return redirect()->route('admin.couriers.show', $courier)
            ->with('success', 'Courier updated successfully!');
    }

    public function toggleActive(User $courier)
    {
        // Verify the user is a courier
        if ($courier->role->slug !== 'courier') {
            abort(404, 'User is not a courier');
        }

        $courier->update([
            'is_active' => !$courier->is_active
        ]);

        $status = $courier->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Courier account {$status} successfully.");
    }
}
