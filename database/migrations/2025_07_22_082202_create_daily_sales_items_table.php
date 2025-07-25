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
        Schema::create('daily_sales_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_sales_id')->constrained('daily_sales');
            $table->enum('item_type', ['product', 'addon']);
            $table->unsignedBigInteger('item_id');
            $table->string('quantity');
            $table->decimal('price', 13, 2);
            $table->decimal('amount', 13, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales_items');
    }
};
