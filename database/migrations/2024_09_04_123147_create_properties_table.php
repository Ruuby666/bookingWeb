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
            $table->boolean('entertainment')->nullable();
            $table->boolean('parking');
            $table->boolean('pool');
            $table->boolean('garden');
            $table->boolean('safeBox');
            $table->boolean('terrace');
            $table->boolean('wifi');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
