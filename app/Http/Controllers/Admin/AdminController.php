<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\User;
use App\Models\Facility;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_requests' => SpecimenRequest::count(),
            'pending_requests' => SpecimenRequest::where('status', 'pending_approval')->count(),
            'active_couriers' => User::whereHas('role', function($q) {
                $q->where('slug', 'courier');
            })->where('is_active', true)->count(),
            'total_facilities' => Facility::count(),
            'today_requests' => SpecimenRequest::whereDate('created_at', today())->count(),
            'completed_requests' => SpecimenRequest::where('status', 'completed')->count(),
        ];

        $recentRequests = SpecimenRequest::with(['client', 'facility', 'courier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Chart data for requests by status
        $requestsByStatus = SpecimenRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('admin.dashboard', compact('stats', 'recentRequests', 'recentActivities', 'requestsByStatus'));
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}