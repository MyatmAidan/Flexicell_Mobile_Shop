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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_model_id')->constrained()->cascadeOnDelete();
            $table->string('product_type'); // new / second hand
            $table->decimal('selling_price', 10, 2);
            $table->integer('warranty_month')->nullable();
            $table->json('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
