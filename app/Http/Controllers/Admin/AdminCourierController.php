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
                    $query->whereHas('courierVerification', function($q) {
                        $q->where('verification_status', 'approved');
                    });
                    break;
                case 'pending':
                    $query->whereHas('courierVerification', function($q) {
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

        return view('admin.couriers.show', compact('courier', 'stats'));
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

        return redirect()->route('admin.couriers.show', $courier)
            ->with('success', 'Courier verification approved successfully. The courier can now accept deliveries.');
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

        return redirect()->route('admin.couriers.show', $courier)
            ->with('success', 'Courier verification rejected. The courier has been notified.');
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

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier created successfully!');
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