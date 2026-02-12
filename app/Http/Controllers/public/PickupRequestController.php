<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PickupRequestController extends Controller
{
    /**
     * Show the public pickup request form
     */
    public function create()
    {
        return view('pickup-request');
    }

    /**
     * Store the pickup request data in session and redirect to registration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'facility' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'pickupAddress' => 'required|string',
            'dropoffAddress' => 'required|string',
            'specimenType' => 'required|string',
            'temperature' => 'required|string',
            'pickupTime' => 'required|string',
            'pickupDate' => 'required|date',
            'description' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Store the pickup request data in session
        Session::put('pending_pickup_request', $validated);
        
        // Redirect to registration with the email pre-filled
        return redirect()->route('register', ['email' => $validated['email']])
            ->with('info', 'Please create an account to complete your pickup request.');
    }
}