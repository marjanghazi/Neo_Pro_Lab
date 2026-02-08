<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->isCourier()) {
                    return redirect()->route('courier.dashboard');
                }

                if ($user->isClient()) {
                    return redirect()->route('client.dashboard');
                }

                return redirect('/');
            }
        }

        return $next($request);
    }
}
