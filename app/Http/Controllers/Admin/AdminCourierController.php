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
        $couriers = User::whereHas('role', function($q) {
            $q->where('slug', 'courier');
        })->withCount(['assignedRequests' => function($q) {
            $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']);
        }])->paginate(20);
        
        return view('admin.couriers.index', compact('couriers'));
    }

    public function show(User $courier)
    {
        $courier->load(['assignedRequests' => function($q) {
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
        
        $onTime = $completedRequests->filter(function($request) {
            return $request->delivered_at->lte($request->estimated_delivery_time);
        })->count();
        
        return round(($onTime / $completedRequests->count()) * 100, 1);
    }
}