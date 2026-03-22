<?php
// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\CourierVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistrationPendingMail;
use App\Mail\AdminNewUserNotificationMail;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister(Request $request)
    {
        // Pre-fill email from query parameter if coming from pickup form
        $email = $request->query('email', '');

        // Check if there's a pending pickup request
        $hasPendingPickup = Session::has('pending_pickup_request');

        return view('auth.register', compact('email', 'hasPendingPickup'));
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

            // Check if there's a pending pickup request after login
            if (Session::has('pending_pickup_request')) {
                return redirect()->route('client.requests.create-with-data')
                    ->with('info', 'Please complete your pending pickup request.');
            }

            // Redirect based on role
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        // Base validation rules
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role' => 'required|in:client,courier',
        ];

        // Add courier document validation rules
        if ($request->role === 'courier') {
            $rules = array_merge($rules, [
                'profile_image' => 'required|image|mimes:jpeg,png|max:5120',
                'government_id' => 'required|file|mimes:jpeg,png,pdf|max:5120',
                'proof_of_residency' => 'required|file|mimes:jpeg,png,pdf|max:5120',
                'drivers_license' => 'required|file|mimes:jpeg,png,pdf|max:5120',
                'medical_transport_cert' => 'nullable|file|mimes:jpeg,png,pdf|max:5120',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get role ID based on selection
        $role = Role::where('slug', $request->role)->firstOrFail();

        // Create user
        $user = User::create([
            'role_id' => $role->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'is_approved' => false, // Always false initially, admin will approve after document verification
        ]);

        // ============================================
        // SEND EMAIL TO USER (Registration Received)
        // ============================================
        try {
            Mail::to($user->email)->send(new UserRegistrationPendingMail($user, $request->role));
            Log::info('Registration pending email sent to user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role' => $request->role
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send registration pending email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
        }

        // ============================================
        // SEND EMAIL TO ADMIN (New User Notification)
        // ============================================
        try {
            // Get admin emails from settings or use default admin email
            $adminEmails = ['admin@neoprolab.com']; // You can fetch from database settings

            foreach ($adminEmails as $adminEmail) {
                Mail::to($adminEmail)->send(new AdminNewUserNotificationMail($user, $request->role));
            }

            Log::info('Admin notification email sent for new user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role' => $request->role
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
        }

        // Handle courier document uploads
        if ($request->role === 'courier') {
            $documentPaths = [];

            // Upload profile image
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('courier-documents/profile-images', 'public');
                $documentPaths['profile_image'] = $path;

                // Also update user's profile image
                $user->profile_image = $path;
                $user->save();
            }

            // Upload government ID
            if ($request->hasFile('government_id')) {
                $path = $request->file('government_id')->store('courier-documents/government-ids', 'public');
                $documentPaths['government_id'] = $path;
            }

            // Upload proof of residency
            if ($request->hasFile('proof_of_residency')) {
                $path = $request->file('proof_of_residency')->store('courier-documents/proof-of-residency', 'public');
                $documentPaths['proof_of_residency'] = $path;
            }

            // Upload driver's license
            if ($request->hasFile('drivers_license')) {
                $path = $request->file('drivers_license')->store('courier-documents/drivers-licenses', 'public');
                $documentPaths['drivers_license'] = $path;
            }

            // Upload medical transport certificate (optional)
            if ($request->hasFile('medical_transport_cert')) {
                $path = $request->file('medical_transport_cert')->store('courier-documents/medical-certs', 'public');
                $documentPaths['medical_transport_cert'] = $path;
            }

            // Create courier verification record
            CourierVerification::create([
                'user_id' => $user->id,
                'profile_image' => $documentPaths['profile_image'] ?? null,
                'government_id' => $documentPaths['government_id'] ?? null,
                'proof_of_residency' => $documentPaths['proof_of_residency'] ?? null,
                'drivers_license' => $documentPaths['drivers_license'] ?? null,
                'medical_transport_cert' => $documentPaths['medical_transport_cert'] ?? null,
                'verification_status' => 'pending',
                'submitted_at' => now(),
            ]);

            // Log the registration
            $this->notifyAdminAboutNewRegistration($user);

            return redirect()->route('login')
                ->with('info', 'Registration successful! Your documents have been submitted for verification. You will receive an email notification once your account is approved. Please wait for admin approval before logging in.');
        }

        // For regular clients
        $this->notifyAdminAboutNewRegistration($user);

        return redirect()->route('login')
            ->with('success', 'Registration successful! Your account is pending admin approval. You will receive an email notification once your account is approved. Please check back later.');
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
        // Log the registration (email already sent above)
        Log::info('New user registration pending approval', [
            'user_id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role->name,
        ]);
    }
}
