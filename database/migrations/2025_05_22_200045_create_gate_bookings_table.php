<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gate_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')->nullable()->constrained();
            $table->date('booking_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('pallets_count')->nullable();
            $table->float('weight', 6)->nullable()->default(0);
            $table->string('purpose')->nullable();
            $table->string('car_number')->nullable();
            $table->foreignId('acceptances_id')->nullable()->constrained();
            $table->boolean('gbort')->default(false);
            $table->foreignId('car_type_id')->nullable()->constrained();
            $table->foreignId('driver_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('expeditor_id')->nullable()->constrained();
            $table->boolean('is_internal')->nullable();
            $table->foreignId('car_status_id')->nullable()->constrained();
            $table->integer('rs_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['gate_id', 'booking_date']);
            $table->index(['start_time']);
            $table->index(['end_time']);
            $table->index(['is_internal']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_bookings');
    }
};
