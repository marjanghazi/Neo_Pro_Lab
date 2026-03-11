<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link to email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We could not find an account with that email address.'
        ]);

        // Generate a token
        $token = Str::random(64);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token), // Hash the token for security
            'created_at' => Carbon::now()
        ]);

        // Get user details
        $user = User::where('email', $request->email)->first();

        // Create reset link (using raw token, not hashed)
        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        try {
            // Send email
            Mail::send('emails.forgot-password', ['user' => $user, 'resetLink' => $resetLink], function ($message) use ($user) {
                $message->to($user->email, $user->full_name)
                    ->subject('Reset Your NeoProLab Password')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            return back()->with('status', 'We have emailed your password reset link! Please check your inbox.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Password reset email failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to send reset link. Please try again later.');
        }
    }

    /**
     * Show the reset password form
     */
    public function showResetForm($token, Request $request)
    {
        $email = $request->query('email');
        
        // Verify if token exists and is valid
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid or expired password reset link.');
        }

        // Check if token has expired (tokens valid for 60 minutes)
        $createdAt = Carbon::parse($resetRecord->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'This password reset link has expired. Please request a new one.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Reset the user's password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        // Verify token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Invalid password reset link.'])->withInput();
        }

        // Check if token has expired
        $createdAt = Carbon::parse($resetRecord->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired.'])->withInput();
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Optional: Log the user in
        // Auth::login($user);

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully! You can now log in with your new password.');
    }
}