<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = SpecimenRequest::with(['client', 'facility', 'courier']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'LIKE', "%{$search}%")
                  ->orWhere('recipient_name', 'LIKE', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.requests.index', compact('requests'));
    }

    public function show(SpecimenRequest $request)
    {
        $request->load(['client', 'facility', 'courier', 'stops', 'documents']);
        $couriers = User::whereHas('role', function($q) {
            $q->where('slug', 'courier');
        })->where('is_active', true)->get();
        
        return view('admin.requests.show', compact('request', 'couriers'));
    }

    public function assignCourier(Request $request, SpecimenRequest $specimenRequest)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $specimenRequest->update([
            'assigned_to' => $validated['courier_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        // Create notification for courier
        // ...

        return back()->with('success', 'Courier assigned successfully.');
    }

    public function updateStatus(Request $request, SpecimenRequest $specimenRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_approval,approved,rejected,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $specimenRequest->status;
        $specimenRequest->update($validated);

        // Create audit log
        // ...

        return back()->with('success', 'Request status updated successfully.');
    }
}