<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('name');
            $table->string('facility');
            $table->string('phone');
            $table->string('email');
            $table->text('pickup_address');
            $table->text('dropoff_address');
            $table->string('specimen_type');
            $table->string('temperature');
            $table->string('pickup_time');
            $table->date('pickup_date');
            $table->text('description');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pickup_requests');
    }
}