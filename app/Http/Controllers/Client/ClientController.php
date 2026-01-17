<?php

namespace App\Http\Controllers;

use App\Models\SpecimenRequest;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:client');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $facility = $user->facilities()->first();
        
        $stats = [
            'total_requests' => $user->createdRequests()->count(),
            'pending_requests' => $user->createdRequests()->where('status', 'pending_approval')->count(),
            'in_progress' => $user->createdRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit'])->count(),
            'completed' => $user->createdRequests()->where('status', 'completed')->count(),
        ];

        $recentRequests = $user->createdRequests()
            ->with(['courier', 'facility'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('client.dashboard', compact('stats', 'recentRequests', 'facility'));
    }

    public function requests()
    {
        $requests = Auth::user()->createdRequests()
            ->with(['courier', 'facility'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.requests.index', compact('requests'));
    }

    public function createRequest()
    {
        $facility = Auth::user()->facilities()->first();
        
        if (!$facility) {
            return redirect()->route('client.dashboard')
                ->with('error', 'You need to be associated with a facility to create requests.');
        }

        return view('client.requests.create', compact('facility'));
    }

    public function trackRequest(SpecimenRequest $request)
    {
        if ($request->client_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['courier', 'stops']);
        
        return view('client.requests.track', compact('request'));
    }
}