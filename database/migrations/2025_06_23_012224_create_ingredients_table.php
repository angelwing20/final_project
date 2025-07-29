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
            $table->string('name');
            $table->string('image')->nullable();
            $table->decimal('stock_weight', 8, 2)->nullable()->default('0');
            $table->decimal('alarm_weight', 8, 2);
            $table->decimal('weight_unit', 8, 2);
            $table->decimal('price_per_weight_unit', 13, 2);
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
