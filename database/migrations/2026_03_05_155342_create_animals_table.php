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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_id')->unique();
            $table->foreign('ad_id')->references('id')->on('ads')->cascadeOnDelete();
            $table->smallInteger('age_months')->nullable();
            $table->decimal('weight_kg', 6, 1)->nullable();
            $table->string('breed', 80)->nullable();
            $table->enum('purpose', ['meat', 'milk', 'breeding', 'work']);
            $table->enum('health', ['healthy', 'vaccinated', 'treating']);
            $table->decimal('milk_per_day_l', 5, 1)->nullable();
            $table->boolean('is_pregnant')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
