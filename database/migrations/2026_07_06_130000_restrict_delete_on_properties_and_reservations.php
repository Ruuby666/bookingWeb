<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->restrictOnDelete();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->foreign('property_id')
                ->references('id')->on('properties')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->foreign('property_id')
                ->references('id')->on('properties')
                ->cascadeOnDelete();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }
};
