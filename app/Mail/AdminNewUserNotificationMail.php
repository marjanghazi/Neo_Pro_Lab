<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewUserNotificationMail extends Mailable
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
                    ->subject('New User Registration Pending Approval')
                    ->view('emails.admin-new-user-notification')
                    ->with([
                        'user' => $this->user,
                        'role' => $this->role,
                        'roleName' => $roleName,
                        'adminUrl' => route('admin.users.pending'),
                        'appName' => config('app.name'),
                    ]);
    }
}