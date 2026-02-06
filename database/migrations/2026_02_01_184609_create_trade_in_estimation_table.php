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
            $table->string('display_condition');
            $table->string('speaker_condition');
            $table->string('front_camera_condition');
            $table->string('rear_camera_condition');
            $table->string('back_glass_condition');
            $table->string('damage')->nullable();
            $table->string('accessories')->nullable();
            $table->boolean('warranty_valid')->default(false);
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
