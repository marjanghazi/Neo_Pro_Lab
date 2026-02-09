<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('license_number', 'LIKE', "%{$search}%");
            });
        }

        $facilities = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        $facilityTypes = collect([
            ['id' => 'hospital', 'name' => 'Hospital'],
            ['id' => 'clinic', 'name' => 'Clinic'],
            ['id' => 'lab', 'name' => 'Laboratory'],
            ['id' => 'research_center', 'name' => 'Research Center'],
            ['id' => 'other', 'name' => 'Other']
        ]);

        $admins = User::whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        })->get();

        return view('admin.facilities.create', compact('facilityTypes', 'admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'facility_type' => 'required|in:hospital,clinic,lab,research_center,other',
            'license_number' => 'required|string|max:100|unique:facilities',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_email' => 'required|email|max:255',
            'is_approved' => 'boolean',
            'status' => 'required|in:pending,active,suspended,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $facility = Facility::create($validated);

        return redirect()->route('admin.facilities.show', $facility)
            ->with('success', 'Facility created successfully!');
    }

    public function show(Facility $facility)
    {
        $facility->load(['users', 'approver', 'specimenRequests' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(10);
        }]);

        // Statistics
        $stats = [
            'total_users' => $facility->users()->count(),
            'total_requests' => $facility->specimenRequests()->count(),
            'active_requests' => $facility->specimenRequests()->whereIn('status', ['pending', 'assigned', 'in_transit', 'picked_up'])->count(),
            'completed_requests' => $facility->specimenRequests()->where('status', 'completed')->count(),
        ];

        return view('admin.facilities.show', compact('facility', 'stats'));
    }

    public function edit(Facility $facility)
    {
        // Convert facility types array to collection of objects
        $facilityTypes = collect([
            ['id' => 'hospital', 'name' => 'Hospital'],
            ['id' => 'clinic', 'name' => 'Clinic'],
            ['id' => 'lab', 'name' => 'Laboratory'],
            ['id' => 'research_center', 'name' => 'Research Center'],
            ['id' => 'other', 'name' => 'Other']
        ]);

        $admins = User::whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        })->get();

        return view('admin.facilities.edit', compact('facility', 'facilityTypes', 'admins'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'facility_type' => 'required|in:hospital,clinic,lab,research_center,other',
            'license_number' => 'required|string|max:100|unique:facilities,license_number,' . $facility->id,
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_email' => 'required|email|max:255',
            'is_approved' => 'boolean',
            'status' => 'required|in:pending,active,suspended,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $facility->update($validated);

        return redirect()->route('admin.facilities.show', $facility)
            ->with('success', 'Facility updated successfully!');
    }

    public function approve(Facility $facility)
    {
        $facility->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'active',
        ]);

        return back()->with('success', 'Facility approved successfully.');
    }

    public function reject(Facility $facility)
    {
        $facility->update([
            'status' => 'rejected',
            'is_approved' => false,
        ]);

        return back()->with('success', 'Facility rejected successfully.');
    }

    public function destroy(Facility $facility)
    {
        // Check if facility has any users or requests
        if ($facility->users()->count() > 0) {
            return back()->with('error', 'Cannot delete facility with assigned users. Remove users first.');
        }

        if ($facility->specimenRequests()->count() > 0) {
            return back()->with('error', 'Cannot delete facility with associated specimen requests.');
        }

        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility deleted successfully.');
    }
    public function users(Facility $facility)
    {
        $facility->load(['users' => function ($query) {
            $query->with(['role'])->orderBy('facility_users.created_at', 'desc');
        }]);

        $availableUsers = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['client', 'staff']);
        })
            ->whereDoesntHave('facilities', function ($q) use ($facility) {
                $q->where('facilities.id', $facility->id);
            })
            ->where('is_active', true)
            ->where('is_approved', true)
            ->orderBy('first_name')
            ->get();

        return view('admin.facilities.users', compact('facility', 'availableUsers'));
    }

    public function assignUsersForm(Facility $facility)
    {
        $availableUsers = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['client', 'staff']);
        })
            ->whereDoesntHave('facilities', function ($q) use ($facility) {
                $q->where('facilities.id', $facility->id);
            })
            ->where('is_active', true)
            ->where('is_approved', true)
            ->orderBy('first_name')
            ->get();

        $currentUsers = $facility->users()->pluck('users.id')->toArray();

        return view('admin.facilities.assign-users', compact('facility', 'availableUsers', 'currentUsers'));
    }

    public function assignUsers(Request $request, Facility $facility)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'positions' => 'nullable|array',
            'departments' => 'nullable|array',
        ]);

        $userData = [];
        foreach ($request->user_ids as $userId) {
            $userData[$userId] = [
                'position' => $request->positions[$userId] ?? null,
                'department' => $request->departments[$userId] ?? null,
                'is_primary_contact' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $facility->users()->syncWithoutDetaching($userData);

        return redirect()->route('admin.facilities.users.index', $facility)
            ->with('success', 'Users assigned successfully!');
    }

    public function detachUser(Facility $facility, User $user)
    {
        $facility->users()->detach($user->id);

        return back()->with('success', 'User removed from facility successfully!');
    }

    public function togglePrimaryContact(Facility $facility, User $user)
    {
        // Reset all users to non-primary first
        $facility->users()->updateExistingPivot($facility->users()->pluck('users.id'), ['is_primary_contact' => false]);

        // Set the selected user as primary
        $facility->users()->updateExistingPivot($user->id, ['is_primary_contact' => true]);

        return back()->with('success', 'Primary contact updated successfully!');
    }
}
