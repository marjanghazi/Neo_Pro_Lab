<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourierRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $courier;
    public $rejectionReason;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $courier, string $rejectionReason)
    {
        $this->courier = $courier;
        $this->rejectionReason = $rejectionReason;
        $this->loginUrl = route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Courier Account Verification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.courier-rejected',
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