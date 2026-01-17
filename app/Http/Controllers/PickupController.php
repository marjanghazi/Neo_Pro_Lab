<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PickupRequestReceived;

class PickupController extends Controller
{
    public function create()
    {
        return view('pickup-request');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'facility' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'pickupAddress' => 'required|string|max:500',
            'dropoffAddress' => 'required|string|max:500',
            'specimenType' => 'required|string',
            'temperature' => 'required|string',
            'pickupTime' => 'required|string',
            'pickupDate' => 'required|date',
            'description' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate a request number
        $requestNumber = 'NP' . date('Ymd') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        // In a real application, you would save to database here
        // $pickupRequest = PickupRequest::create([...]);

        // Send confirmation email (optional)
        try {
            Mail::to($validated['email'])->send(new PickupRequestReceived([
                'name' => $validated['name'],
                'requestNumber' => $requestNumber,
                'pickupDate' => $validated['pickupDate'],
                'pickupTime' => $validated['pickupTime']
            ]));
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        return redirect()->route('pickup.create')
            ->with('success', 'Thank you! Your pickup request #' . $requestNumber . ' has been submitted. We will contact you within 2 hours.');
    }
}