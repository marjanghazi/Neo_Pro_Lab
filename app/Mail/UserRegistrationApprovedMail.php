<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistrationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $roleName = ucfirst($this->role);
        $dashboardRoute = $this->role === 'courier' ? 'courier.dashboard' : 'client.dashboard';

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Account Approved - Welcome to ' . config('app.name'))
            ->view('emails.user-registration-approved')
            ->with([
                'user' => $this->user,
                'role' => $this->role,
                'roleName' => $roleName,
                'dashboardRoute' => $dashboardRoute,
                'appName' => config('app.name'),
                'loginUrl' => route('login'),
            ]);
    }
}
