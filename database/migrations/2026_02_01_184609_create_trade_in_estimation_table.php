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
        Schema::create('tradein_estimations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('phone_model_id')->constrained()->cascadeOnDelete();

            $table->enum('display_condition', ['good', 'minor', 'broken']);
            $table->enum('speaker_condition', ['good', 'bad']);
            $table->enum('front_camera_condition', ['good', 'bad']);
            $table->enum('rear_camera_condition', ['good', 'bad']);
            $table->enum('back_glass_condition', ['good', 'cracked']);

            $table->string('damage')->nullable();
            $table->string('accessories')->nullable();

            $table->boolean('warranty_valid')->default(false);

            $table->decimal('estimated_price', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_in_estimation');
    }
};
