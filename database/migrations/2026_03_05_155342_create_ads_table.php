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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();

            $table->foreignId('subcategory_id')
                  ->constrained('subcategories')
                  ->restrictOnDelete();

            $table->string('title', 150);
            $table->decimal('price', 12, 2);
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 30); // enum/varchar → oddiy string
            $table->enum('status', ['active', 'sold', 'deleted']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
