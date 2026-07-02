<?php

namespace App\Console\Commands;

use App\Services\FacilityBillingService;
use Illuminate\Console\Command;

class GenerateFacilityInvoices extends Command
{
    protected $signature = 'billing:generate-facility-invoices';
    protected $description = 'Generate combined facility invoices for completed unbilled deliveries whose billing cycle has ended.';

    public function handle(FacilityBillingService $billing): int
    {
        $count = $billing->generateDueInvoices();
        $this->info("Generated {$count} facility invoice(s).");
        return self::SUCCESS;
    }
}
