<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('owner_id')->constrained();
            $table->foreignId('category_id')->constrained('vehicle_categories');
            $table->foreignId('brand_id')->constrained('vehicle_brands');
            $table->foreignId('model_id')->constrained('vehicle_models');
            $table->string('status');
            $table->unsignedSmallInteger('year');
            $table->string('plate');
            $table->string('color');
            $table->string('chassi');
            $table->string('renavam');
            $table->enum('transmission', ['manual', 'automatic'])->default('automatic');
            $table->integer('odometer');
            $table->string('engine');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
