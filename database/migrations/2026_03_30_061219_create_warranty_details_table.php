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
        Schema::create('warranty_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->references('id')->on('warranties')->onDelete('cascade');
            $table->foreignId('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_details');
    }
};
