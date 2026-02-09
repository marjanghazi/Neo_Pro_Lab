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
        // Get the authenticated user
        $user = Auth::user();
        
        // Load user with their facility relationship
        $user->load('facilities');
        
        // Get the first facility (assuming user belongs to one facility)
        $facility = $user->facilities->first();
        
        if (!$facility) {
            return redirect()->route('client.dashboard')
                ->with('error', 'No facility assigned to your account.');
        }
        
        // Load additional relationships if needed
        $facility->load('approver');
        
        return view('client.facility.show', compact('facility'));
    }
}