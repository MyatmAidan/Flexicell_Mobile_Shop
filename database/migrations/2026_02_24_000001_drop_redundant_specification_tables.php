<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('device_specifications');
        Schema::dropIfExists('second_purchase_specifications');
    }

    public function down(): void
    {
        // These tables were 100% redundant with FK columns on
        // devices and second_phone_purchases. No need to recreate.
    }
};
