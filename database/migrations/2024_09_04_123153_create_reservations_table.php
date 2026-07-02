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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->dateTime('check_in');
            $table->dateTime('check_out');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('invoice')->default(false);
            $table->integer('guests');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            // Indexes
            $table->index(['property_id', 'status'], 'reservations_property_status_idx');
            $table->index(['check_in', 'check_out'], 'reservations_checkin_checkout_idx');
            $table->index('status', 'reservations_status_idx');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
