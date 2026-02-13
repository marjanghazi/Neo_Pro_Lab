<?php
// app/Mail/CourierApprovedMail.php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourierApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $courier;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $courier)
    {
        $this->courier = $courier;
        $this->loginUrl = route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Courier Account Has Been Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.courier-approved',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}