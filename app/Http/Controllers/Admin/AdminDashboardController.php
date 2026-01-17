<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\SpecimenRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $stats = [
            'total_requests' => SpecimenRequest::count(),
            'pending_requests' => SpecimenRequest::where('status', 'pending_approval')->count(),
            'active_couriers' => User::whereHas('role', function($q) {
                $q->where('slug', 'courier');
            })->where('is_active', true)->count(),
            'total_facilities' => Facility::count(),
            'active_requests' => SpecimenRequest::whereIn('status', ['assigned', 'in_transit', 'picked_up'])->count(),
            'completed_today' => SpecimenRequest::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
        ];

        // Recent requests
        $recentRequests = SpecimenRequest::with(['client', 'facility', 'courier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Requests by status
        $requestsByStatus = SpecimenRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent activities (simplified)
        $recentActivities = [
            (object)[
                'user' => auth()->user(),
                'action' => 'created',
                'model_type' => 'facility',
                'created_at' => now()->subMinutes(30)
            ],
            // Add more activities as needed
        ];

        return view('admin.dashboard.index', compact('stats', 'recentRequests', 'requestsByStatus', 'recentActivities'));
    }
}