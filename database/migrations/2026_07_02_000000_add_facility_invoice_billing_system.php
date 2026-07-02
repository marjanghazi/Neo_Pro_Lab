<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            if (! Schema::hasColumn('facilities', 'billing_cycle')) $table->string('billing_cycle')->default('monthly')->after('status');
            if (! Schema::hasColumn('facilities', 'custom_billing_days')) $table->unsignedInteger('custom_billing_days')->nullable()->after('billing_cycle');
            if (! Schema::hasColumn('facilities', 'payment_terms')) $table->string('payment_terms')->default('net_15')->after('custom_billing_days');
            if (! Schema::hasColumn('facilities', 'custom_payment_term_days')) $table->unsignedInteger('custom_payment_term_days')->nullable()->after('payment_terms');
            if (! Schema::hasColumn('facilities', 'tax_rate')) $table->decimal('tax_rate', 5, 2)->default(0)->after('custom_payment_term_days');
            if (! Schema::hasColumn('facilities', 'last_invoiced_at')) $table->timestamp('last_invoiced_at')->nullable()->after('tax_rate');
        });

        Schema::table('specimen_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('specimen_requests', 'invoice_id')) $table->foreignId('invoice_id')->nullable()->index()->after('payment_reminder_sent_at');
            if (! Schema::hasColumn('specimen_requests', 'billing_status')) $table->string('billing_status')->default('unbilled')->index()->after('invoice_id');
            if (! Schema::hasColumn('specimen_requests', 'wait_time_fee')) $table->decimal('wait_time_fee', 10, 2)->default(0)->after('additional_stop_charge');
            if (! Schema::hasColumn('specimen_requests', 'signature_fee')) $table->decimal('signature_fee', 10, 2)->default(0)->after('wait_time_fee');
        });

        if (! Schema::hasTable('facility_invoices')) {
            Schema::create('facility_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
                $table->date('period_start');
                $table->date('period_end');
                $table->date('invoice_date');
                $table->date('due_date');
                $table->string('payment_terms')->default('net_15');
                $table->string('status')->default('pending')->index();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('grand_total', 12, 2)->default(0);
                $table->decimal('amount_paid', 12, 2)->default(0);
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('viewed_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('last_reminder_sent_at')->nullable();
                $table->string('last_reminder_type')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['facility_id', 'period_start', 'period_end']);
            });
        }

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'facility_invoice_id')) $table->foreignId('facility_invoice_id')->nullable()->index()->after('specimen_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_invoices');
    }
};
