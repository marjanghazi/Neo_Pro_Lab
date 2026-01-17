<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\SpecimenRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function performance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Courier Performance
        $courierPerformance = User::whereHas('role', function($q) {
                $q->where('slug', 'courier');
            })
            ->withCount(['assignedRequests' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['assignedRequests as completed_count' => function($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('completed_count', 'desc')
            ->paginate(10);
        
        return view('admin.reports.performance', compact('courierPerformance', 'startDate', 'endDate'));
    }

    public function facilities(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $facilityStats = Facility::withCount(['specimenRequests' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['specimenRequests as completed_count' => function($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('specimen_requests_count', 'desc')
            ->paginate(10);
        
        return view('admin.reports.facilities', compact('facilityStats', 'startDate', 'endDate'));
    }

    public function requests(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $requests = SpecimenRequest::with(['facility', 'client', 'assignedTo'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Statistics
        $stats = [
            'total' => SpecimenRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed' => SpecimenRequest::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'pending' => SpecimenRequest::whereIn('status', ['draft', 'pending_approval', 'approved'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'in_transit' => SpecimenRequest::whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];
        
        return view('admin.reports.requests', compact('requests', 'stats', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'requests');
        $format = $request->get('format', 'csv');
        
        return back()->with('info', 'Export feature will be implemented soon.');
    }
}