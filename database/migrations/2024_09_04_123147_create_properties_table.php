<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->decimal('price_per_night', 8, 2);
            $table->integer('capacity');
            $table->integer('size');
            $table->json('bedrooms');
            $table->integer('bathrooms');
            $table->integer('min_nights');
            $table->string('images_div');
            $table->string('tv')->nullable();
            $table->boolean('entertainment')->default(false);
            $table->boolean('parking')->default(false);
            $table->boolean('pool')->default(false);
            $table->boolean('garden')->default(false);
            $table->boolean('safeBox')->default(false);
            $table->boolean('terrace')->default(false);
            $table->boolean('wifi')->default(false);
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->timestamps();

            // Indexes
            $table->index('owner_id', 'properties_owner_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
