<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistrationPendingMail extends Mailable
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
        
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Welcome to ' . config('app.name') . ' - Registration Received')
                    ->view('emails.user-registration-pending')
                    ->with([
                        'user' => $this->user,
                        'role' => $this->role,
                        'roleName' => $roleName,
                        'appName' => config('app.name'),
                    ]);
    }
}