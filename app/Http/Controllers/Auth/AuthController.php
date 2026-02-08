<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            // Check if user is approved (except for admin users)
            if (!$user->isAdmin() && !$user->is_approved) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your account is pending approval. Please wait for admin approval.');
            }
            
            // Update last login time
            $user->last_login_at = now();
            $user->save();

            // Redirect based on role
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role' => 'required|in:client,courier',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get role ID based on selection
        $role = Role::where('slug', $request->role)->firstOrFail();

        $user = User::create([
            'role_id' => $role->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'is_approved' => $role->slug === 'admin' ? true : false, // Auto-approve admins
        ]);

        // Send notification to admin about new registration
        $this->notifyAdminAboutNewRegistration($user);

        return redirect()->route('login')
            ->with('success', 'Registration successful! Your account is pending admin approval. You will be notified once approved.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    private function redirectToDashboard()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isCourier()) {
            return redirect()->route('courier.dashboard');
        } else {
            return redirect()->route('client.dashboard');
        }
    }

    private function notifyAdminAboutNewRegistration(User $user)
    {
        // You can implement email notification here
        // For now, we'll just log it
        \Log::info('New user registration pending approval', [
            'user_id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role->name,
        ]);
    }
}