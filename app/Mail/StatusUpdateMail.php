<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $statusDisplay = str_replace('_', ' ', $this->data['status']);
        
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Request #' . $this->data['request']->request_number . ' - ' . ucfirst($statusDisplay))
                    ->view('emails.status-update')
                    ->with('data', $this->data);
    }
}