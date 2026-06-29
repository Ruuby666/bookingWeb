<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['property_id', 'status'], 'reservations_property_status_idx');
            $table->index(['check_in', 'check_out'], 'reservations_checkin_checkout_idx');
            $table->index('status', 'reservations_status_idx');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->index('owner_id', 'properties_owner_idx');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_property_status_idx');
            $table->dropIndex('reservations_checkin_checkout_idx');
            $table->dropIndex('reservations_status_idx');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('properties_owner_idx');
        });
    }
};
