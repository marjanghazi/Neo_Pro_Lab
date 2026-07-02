<?php

namespace App\Console\Commands;

use App\Services\FacilityBillingService;
use Illuminate\Console\Command;

class SendFacilityInvoiceReminders extends Command
{
    protected $signature = 'billing:send-facility-invoice-reminders';
    protected $description = 'Send scheduled facility invoice due-date and overdue reminders.';

    public function handle(FacilityBillingService $billing): int
    {
        $count = $billing->sendDueReminders();
        $this->info("Sent {$count} facility invoice reminder(s).");
        return self::SUCCESS;
    }
}
