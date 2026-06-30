<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        if (! Schema::hasColumn('payments', 'specimen_request_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('specimen_request_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasColumn('payments', 'request_id')) {
            DB::table('payments')
                ->whereNull('specimen_request_id')
                ->whereNotNull('request_id')
                ->update(['specimen_request_id' => DB::raw('request_id')]);
        }
    }

    public function down(): void
    {
        // Keep both legacy and normalized request-link columns to avoid data loss in production.
    }
};
