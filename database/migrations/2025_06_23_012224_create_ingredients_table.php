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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_category_id')->constrained();
            $table->string('name')->unique();
            $table->string('image')->nullable();
            $table->enum('unit_type', ['weight', 'quantity']);
            $table->decimal('stock', 8, 3)->nullable()->default('0');
            $table->decimal('min_stock', 8, 3);
            $table->decimal('weight_unit', 8, 3);
            $table->decimal('price', 13, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
