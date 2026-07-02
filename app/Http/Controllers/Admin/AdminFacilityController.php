<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminFacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::withCount(['users', 'specimenRequests']);

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
            ['id' => 'hospital',        'name' => 'Hospital'],
            ['id' => 'clinic',          'name' => 'Clinic'],
            ['id' => 'lab',             'name' => 'Laboratory'],
            ['id' => 'research_center', 'name' => 'Research Center'],
            ['id' => 'other',           'name' => 'Other'],
        ]);

        $admins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->get();

        return view('admin.facilities.create', compact('facilityTypes', 'admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'facility_type'        => 'required|in:hospital,clinic,lab,research_center,other',
            'license_number'       => 'nullable|string|max:100|unique:facilities',
            'address'              => 'required|string|max:500',
            'city'                 => 'required|string|max:100',
            'state'                => 'required|string|max:100',
            'country'              => 'required|string|max:100',
            'postal_code'          => 'nullable|string|max:20',
            'zip_code'             => 'nullable|string|max:20',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:255',
            'website'              => 'nullable|url|max:255',
            'operating_hours'      => 'nullable|string|max:255',
            'contact_person_name'  => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'is_approved'          => 'boolean',
            'status'               => 'required|in:pending,active,suspended,rejected',
            'notes'                => 'nullable|string|max:1000',
            'billing_cycle'        => 'nullable|in:daily,every_5_days,every_10_days,every_15_days,monthly,custom',
                'custom_billing_days'  => 'required_if:billing_cycle,custom|nullable|integer|min:1|max:365',
                'payment_terms'        => 'nullable|in:net_5,net_10,net_15,net_30,custom',
                'custom_payment_term_days' => 'required_if:payment_terms,custom|nullable|integer|min:0|max:365',
                'tax_rate'             => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['billing_cycle'] = $validated['billing_cycle'] ?? 'monthly';
        $validated['payment_terms'] = $validated['payment_terms'] ?? 'net_15';
        $validated['tax_rate'] = $validated['tax_rate'] ?? 0;
        $validated['is_approved'] = $request->has('is_approved');

        if ($validated['is_approved']) {
            $validated['approved_by'] = auth()->id();
            $validated['approved_at'] = now();
        }

        $facility = Facility::create($validated);

        return redirect()->route('admin.facilities.show', $facility)
            ->with('success', 'Facility created successfully!');
    }

    public function show(Facility $facility)
    {
        $facility->load([
            'users',
            'approver',
            'specimenRequests' => fn($q) => $q->orderBy('created_at', 'desc')->limit(10),
        ]);

        $stats = [
            'total_users'        => $facility->users()->count(),
            'total_requests'     => $facility->specimenRequests()->count(),
            'active_requests'    => $facility->specimenRequests()
                                        ->whereIn('status', ['pending', 'assigned', 'in_transit', 'picked_up'])
                                        ->count(),
            'completed_requests' => $facility->specimenRequests()->where('status', 'completed')->count(),
        ];

        return view('admin.facilities.show', compact('facility', 'stats'));
    }

    public function edit(Facility $facility)
    {
        $facilityTypes = collect([
            ['id' => 'hospital',        'name' => 'Hospital'],
            ['id' => 'clinic',          'name' => 'Clinic'],
            ['id' => 'lab',             'name' => 'Laboratory'],
            ['id' => 'research_center', 'name' => 'Research Center'],
            ['id' => 'other',           'name' => 'Other'],
        ]);

        $admins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->get();

        return view('admin.facilities.edit', compact('facility', 'facilityTypes', 'admins'));
    }

    public function update(Request $request, Facility $facility)
    {
        Log::info('=== FACILITY UPDATE ATTEMPT ===', [
            'facility_id'  => $facility->id,
            'facility_name'=> $facility->name,
            'request_data' => $request->except(['_token', '_method']),
        ]);

        try {
            $validated = $request->validate([
                'name'                 => 'required|string|max:255',
                'facility_type'        => 'required|in:hospital,clinic,lab,research_center,other',
                'license_number'       => 'nullable|string|max:100|unique:facilities,license_number,' . $facility->id,
                'address'              => 'required|string|max:500',
                'city'                 => 'required|string|max:100',
                'state'                => 'required|string|max:100',
                'country'              => 'required|string|max:100',
                'postal_code'          => 'nullable|string|max:20',
                'zip_code'             => 'nullable|string|max:20',
                'phone'                => 'nullable|string|max:20',
                'email'                => 'nullable|email|max:255',
                'website'              => 'nullable|url|max:255',
                'operating_hours'      => 'nullable|string|max:255',
                'contact_person_name'  => 'nullable|string|max:255',
                'contact_person_phone' => 'nullable|string|max:20',
                'contact_person_email' => 'nullable|email|max:255',
                'is_approved'          => 'sometimes|boolean',
                'status'               => 'required|in:pending,active,suspended,rejected',
                'notes'                => 'nullable|string|max:1000',
                'billing_cycle'        => 'nullable|in:daily,every_5_days,every_10_days,every_15_days,monthly,custom',
                'custom_billing_days'  => 'required_if:billing_cycle,custom|nullable|integer|min:1|max:365',
                'payment_terms'        => 'nullable|in:net_5,net_10,net_15,net_30,custom',
                'custom_payment_term_days' => 'required_if:payment_terms,custom|nullable|integer|min:0|max:365',
                'tax_rate'             => 'nullable|numeric|min:0|max:100',
            ]);

            $validated['billing_cycle'] = $validated['billing_cycle'] ?? $facility->billing_cycle ?? 'monthly';
            $validated['payment_terms'] = $validated['payment_terms'] ?? $facility->payment_terms ?? 'net_15';
            $validated['tax_rate'] = $validated['tax_rate'] ?? $facility->tax_rate ?? 0;
            $validated['is_approved'] = $request->has('is_approved');

            // Record approval details when newly approved
            if ($validated['is_approved'] && !$facility->getRawOriginal('is_approved')) {
                $validated['approved_by'] = auth()->id();
                $validated['approved_at'] = now();
            }

            $facility->update($validated);

            Log::info('Facility updated successfully', ['facility_id' => $facility->id]);

            return redirect()->route('admin.facilities.show', $facility)
                ->with('success', 'Facility updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors(), 'facility_id' => $facility->id]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error updating facility', [
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'facility_id' => $facility->id,
            ]);
            return back()->with('error', 'Failed to update facility: ' . $e->getMessage())->withInput();
        }
    }

    public function approve(Facility $facility)
    {
        $facility->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status'      => 'active',
        ]);

        return back()->with('success', 'Facility approved successfully.');
    }

    public function reject(Facility $facility)
    {
        $facility->update([
            'status'      => 'rejected',
            'is_approved' => false,
        ]);

        return back()->with('success', 'Facility rejected.');
    }

    public function suspend(Request $request, Facility $facility)
    {
        $facility->update([
            'status'      => 'suspended',
            'is_approved' => false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Facility suspended successfully.']);
        }

        return back()->with('success', 'Facility suspended successfully.');
    }

    public function activate(Facility $facility)
    {
        $facility->update([
            'status'      => 'active',
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Facility activated successfully.');
    }

    public function destroy(Facility $facility)
    {
        try {
            if ($facility->users()->count() > 0) {
                return back()->with('error', 'Cannot delete facility with assigned users. Remove users first.');
            }

            if ($facility->specimenRequests()->count() > 0) {
                return back()->with('error', 'Cannot delete facility with associated specimen requests.');
            }

            $facility->delete();

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting facility', [
                'error'       => $e->getMessage(),
                'facility_id' => $facility->id,
            ]);
            return back()->with('error', 'Failed to delete facility: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // User Management
    // -----------------------------------------------------------------------

    public function users(Facility $facility)
    {
        $facility->load(['users' => fn($q) => $q->with('role')->orderBy('facility_users.created_at', 'desc')]);

        $availableUsers = User::whereHas('role', fn($q) => $q->whereIn('slug', ['client', 'staff']))
            ->whereDoesntHave('facilities', fn($q) => $q->where('facilities.id', $facility->id))
            ->where('is_active', true)
            ->where('is_approved', true)
            ->orderBy('first_name')
            ->get();

        return view('admin.facilities.users', compact('facility', 'availableUsers'));
    }

    public function assignUsersForm(Facility $facility)
    {
        $availableUsers = User::whereHas('role', fn($q) => $q->whereIn('slug', ['client', 'staff']))
            ->whereDoesntHave('facilities', fn($q) => $q->where('facilities.id', $facility->id))
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
            'user_ids'    => 'required|array',
            'user_ids.*'  => 'exists:users,id',
            'positions'   => 'nullable|array',
            'departments' => 'nullable|array',
        ]);

        $userData = [];
        foreach ($request->user_ids as $userId) {
            $userData[$userId] = [
                'position'           => $request->positions[$userId] ?? null,
                'department'         => $request->departments[$userId] ?? null,
                'is_primary_contact' => false,
            ];
        }

        // syncWithoutDetaching handles created_at/updated_at via withTimestamps() on the relationship
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
        // Reset all to non-primary
        $allIds = $facility->users()->pluck('users.id')->toArray();
        foreach ($allIds as $id) {
            $facility->users()->updateExistingPivot($id, ['is_primary_contact' => false]);
        }

        // Set chosen user as primary
        $facility->users()->updateExistingPivot($user->id, ['is_primary_contact' => true]);

        return back()->with('success', 'Primary contact updated successfully!');
    }
}