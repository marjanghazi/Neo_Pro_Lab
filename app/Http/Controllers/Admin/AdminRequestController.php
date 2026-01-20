<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecimenRequest;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'LIKE', "%{$search}%")
                    ->orWhere('recipient_name', 'LIKE', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.requests.index', compact('requests'));
    }

    public function show(SpecimenRequest $request)
    {
        // The parameter is named $request but it's a SpecimenRequest model
        $request->load(['client', 'facility', 'courier', 'stops', 'documents']);
        $couriers = User::whereHas('role', function ($q) {
            $q->where('slug', 'courier');
        })->where('is_active', true)->get();

        return view('admin.requests.show', [
            'request' => $request,
            'couriers' => $couriers
        ]);
    }

    public function assignCourier(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $request->update([
            'assigned_to' => $validated['courier_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        // Create notification for courier
        Notification::create([
            'user_id' => $validated['courier_id'],
            'request_id' => $request->id,
            'type' => 'request_assigned',
            'title' => 'New Assignment',
            'message' => "You have been assigned to request #{$request->request_number}",
            'data' => json_encode([
                'request_id' => $request->id,
                'request_number' => $request->request_number
            ]),
        ]);

        return redirect()->route('admin.requests.show', $request)
            ->with('success', 'Courier assigned successfully.');
    }

    public function updateStatus(Request $httpRequest, SpecimenRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:approved,rejected,cancelled'
        ]);

        // Update status based on the submitted value
        $request->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] == 'approved' ? now() : null,
            'cancelled_at' => $validated['status'] == 'cancelled' ? now() : null,
            'cancelled_by' => $validated['status'] == 'cancelled' ? Auth::id() : null,
        ]);

        // Create notification - with null checks
        if (in_array($validated['status'], ['approved', 'rejected'])) {
            // Check if client_id exists
            if ($request->client_id) {
                Notification::create([
                    'user_id' => $request->client_id,
                    'request_id' => $request->id,
                    'type' => 'status_update',
                    'title' => 'Request ' . ucfirst($validated['status']),
                    'message' => "Your request " . ($request->request_number ?: '#' . $request->id) . " has been {$validated['status']}.",
                    'data' => json_encode([
                        'request_id' => $request->id,
                        'status' => $validated['status']
                    ]),
                ]);
            }
        }

        return redirect()->route('admin.requests.show', $request)
            ->with('success', "Request {$validated['status']} successfully!");
    }
}