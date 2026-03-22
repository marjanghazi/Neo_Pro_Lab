<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Mail\ContactAutoReply;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'contactName' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'contactPhone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        try {
            // Send email to admin - using your working Gmail address
            Mail::to('syedmarjanghazi@gmail.com')->send(new ContactMail($validated));
            
            // Send auto-reply to user
            Mail::to($validated['contactEmail'])->send(new ContactAutoReply($validated));
            
            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us. We will respond within 24 hours.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error sending your message. Please try again later.'
            ], 500);
        }
    }
}