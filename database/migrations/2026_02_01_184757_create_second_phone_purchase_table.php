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
        Schema::create('second_phone_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phone_model_id')->constrained()->cascadeOnDelete();
            $table->string('imei')->unique();
            $table->foreignId('ram_option_id')->nullable()->constrained('ram_options')->nullOnDelete();
            $table->foreignId('storage_option_id')->nullable()->constrained('storage_options')->nullOnDelete();
            $table->foreignId('color_option_id')->nullable()->constrained('color_options')->nullOnDelete();
            $table->json('image')->nullable();
            $table->string('condition_grade');
            $table->integer('battery_percentage');
            $table->decimal('buy_price', 10, 2);
            $table->dateTime('purchase_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('second_phone_purchase');
    }
};
