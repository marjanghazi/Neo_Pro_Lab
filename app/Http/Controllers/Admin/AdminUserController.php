<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_approved'] = $request->has('is_approved');

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_approved'] = $request->has('is_approved');

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully!");
    }

    // Pending approvals methods
    public function pendingApprovals()
    {
        $pendingUsers = User::where('is_approved', false)
            ->whereHas('role', function($q) {
                $q->where('slug', '!=', 'admin');
            })
            ->with('role')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.users.pending', compact('pendingUsers'));
    }

    public function approveUser(User $user)
    {
        // Only approve non-admin users
        if (!$user->isAdmin()) {
            $user->update(['is_approved' => true]);
            
            // Log the approval (you can add this to an audit log)
            \Log::info('User approved by admin', [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
                'timestamp' => now()
            ]);
            
            return redirect()->route('admin.users.pending')
                ->with('success', 'User approved successfully.');
        }
        
        return back()->with('error', 'Cannot approve admin users.');
    }

    public function rejectUser(User $user)
    {
        // Only reject non-admin, pending users
        if (!$user->isAdmin() && !$user->is_approved) {
            $userName = $user->full_name;
            $user->delete();
            
            // Log the rejection
            \Log::info('User rejected by admin', [
                'user_id' => $user->id,
                'user_name' => $userName,
                'admin_id' => auth()->id(),
                'timestamp' => now()
            ]);
            
            return redirect()->route('admin.users.pending')
                ->with('success', 'User rejected and removed successfully.');
        }
        
        return back()->with('error', 'Cannot reject this user.');
    }
}