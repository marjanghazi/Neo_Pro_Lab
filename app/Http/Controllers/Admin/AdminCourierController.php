<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminCourierController extends Controller
{
    public function index()
    {
        $couriers = User::whereHas('role', function ($q) {
            $q->where('slug', 'courier');
        })->withCount(['assignedRequests' => function ($q) {
            $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']);
        }])->paginate(20);

        return view('admin.couriers.index', compact('couriers'));
    }

    public function show(User $courier)
    {
        $courier->load(['assignedRequests' => function ($q) {
            $q->with(['client', 'facility'])->orderBy('created_at', 'desc')->limit(10);
        }]);

        $stats = [
            'total_assignments' => $courier->assignedRequests()->count(),
            'active_assignments' => $courier->assignedRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])->count(),
            'completed' => $courier->assignedRequests()->where('status', 'completed')->count(),
            'on_time_rate' => $this->calculateOnTimeRate($courier),
        ];

        return view('admin.couriers.show', compact('courier', 'stats'));
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
    // In AdminCourierController.php
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
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier created successfully!');
    }
    // In AdminCourierController.php - add these methods:

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
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
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
}
