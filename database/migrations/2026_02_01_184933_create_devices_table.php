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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('second_purchase_id')->nullable()->constrained('second_phone_purchases')->nullOnDelete();
            $table->string('imei')->unique();
            $table->foreignId('ram_option_id')->nullable()->constrained('ram_options')->nullOnDelete();
            $table->foreignId('storage_option_id')->nullable()->constrained('storage_options')->nullOnDelete();
            $table->foreignId('color_option_id')->nullable()->constrained('color_options')->nullOnDelete();
            $table->foreignId('warranty_id')->nullable()->constrained('warranties')->nullOnDelete();
            $table->integer('battery_percentage');
            $table->string('condition_grade');
            $table->string('status')->default('available');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->json('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
