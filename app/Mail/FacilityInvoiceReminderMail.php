<?php

namespace App\Mail;

use App\Models\FacilityInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacilityInvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public FacilityInvoice $invoice, public string $reminderType) {}
    public function build()
    {
        return $this->subject('NeoProLab invoice reminder '.$this->invoice->invoice_number)
            ->view('emails.facility-invoice-reminder');
    }
}
