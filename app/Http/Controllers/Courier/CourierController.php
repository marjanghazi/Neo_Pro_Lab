<?php

namespace App\Http\Controllers;

use App\Models\SpecimenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:courier');
    }

    public function dashboard()
    {
        $courier = Auth::user();
        
        $stats = [
            'total_assigned' => $courier->assignedRequests()->count(),
            'pending_acceptance' => $courier->assignedRequests()->where('status', 'assigned')->count(),
            'in_progress' => $courier->assignedRequests()->whereIn('status', ['accepted_by_courier', 'in_transit', 'picked_up'])->count(),
            'completed' => $courier->assignedRequests()->where('status', 'completed')->count(),
        ];

        $activeRequests = $courier->assignedRequests()
            ->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])
            ->with(['client', 'facility'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('courier.dashboard', compact('stats', 'activeRequests'));
    }

    public function assignments()
    {
        $assignments = Auth::user()->assignedRequests()
            ->with(['client', 'facility'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('courier.assignments.index', compact('assignments'));
    }

    public function acceptAssignment(SpecimenRequest $request)
    {
        if ($request->assigned_to !== Auth::id()) {
            abort(403);
        }

        $request->update([
            'status' => 'accepted_by_courier',
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Request accepted successfully.');
    }

    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'altitude' => 'nullable|numeric',
            'battery_level' => 'nullable|integer',
        ]);

        // Save location update
        // ...

        return response()->json(['success' => true]);
    }
}