<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PickupRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Pickup Request Confirmation - NeoProLab Couriers')
                    ->markdown('emails.pickup-confirmation')
                    ->with(['data' => $this->data]);
    }
}