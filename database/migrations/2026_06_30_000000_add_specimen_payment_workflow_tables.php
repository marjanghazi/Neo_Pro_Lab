<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('specimen_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('specimen_requests', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }
            if (! Schema::hasColumn('specimen_requests', 'payment_required')) {
                $table->boolean('payment_required')->default(false)->after('payment_status');
            }
            if (! Schema::hasColumn('specimen_requests', 'payment_due_at')) {
                $table->timestamp('payment_due_at')->nullable()->after('payment_required');
            }
            if (! Schema::hasColumn('specimen_requests', 'payment_reminder_sent_at')) {
                $table->timestamp('payment_reminder_sent_at')->nullable()->after('payment_due_at');
            }
            if (! Schema::hasColumn('specimen_requests', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('notes');
            }
        });

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('specimen_request_id')->constrained('specimen_requests')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('payment_id')->nullable()->index();
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('currency', 3)->default('USD');
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('pending')->index();
                $table->string('payment_gateway')->nullable();
                $table->json('gateway_response')->nullable();
                $table->string('billing_name')->nullable();
                $table->string('billing_email')->nullable();
                $table->string('billing_phone')->nullable();
                $table->text('billing_address')->nullable();
                $table->string('card_last_four', 4)->nullable();
                $table->string('card_brand')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->string('receipt_url')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['specimen_request_id', 'payment_status']);
                $table->index(['user_id', 'payment_status']);
            });
        }

        if (! Schema::hasTable('payment_logs')) {
            Schema::create('payment_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
                $table->foreignId('request_id')->nullable()->constrained('specimen_requests')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->string('status_from')->nullable();
                $table->string('status_to')->nullable();
                $table->json('gateway_response')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['payment_id', 'action']);
            });
        }
    }

    public function down(): void
    {
        // Payment tables may already exist in deployed environments; keep rollback non-destructive.

        Schema::table('specimen_requests', function (Blueprint $table) {
            foreach (['payment_reminder_sent_at', 'payment_due_at', 'payment_required', 'payment_status', 'delivery_notes'] as $column) {
                if (Schema::hasColumn('specimen_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
