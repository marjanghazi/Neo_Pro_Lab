<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityController extends Controller
{
    /**
     * Display the facility profile
     */
    public function show()
    {
        // Get the authenticated user with facility relationship
        $user = Auth::user()->load('facilities');
        
        // Check if user belongs to any facility
        if ($user->facilities->isEmpty()) {
            return redirect()->route('client.dashboard')
                ->with('info', 'Your account is not associated with any facility.');
        }
        
        // Get the first facility (or you can modify this logic if users can belong to multiple facilities)
        $facility = $user->facilities->first();
        
        // Load additional relationships
        $facility->load([
            'approver',
            'users' => function ($query) {
                $query->select('users.id', 'first_name', 'last_name', 'email')
                      ->withPivot('position', 'department', 'is_primary_contact');
            },
            'specimenRequests'
        ]);
        
        return view('client.facility.show', compact('facility'));
    }
}