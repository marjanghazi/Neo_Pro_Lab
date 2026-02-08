<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserApproval
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Skip middleware for admin users and already approved users
            if (!$user->isAdmin() && !$user->is_approved) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your account is pending approval. Please wait for admin approval.');
            }
        }

        return $next($request);
    }
}