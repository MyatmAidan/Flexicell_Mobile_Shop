<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('second_purchase_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('second_phone_purchase_id')->unique()->constrained('second_phone_purchases')->cascadeOnDelete();
            $table->foreignId('ram_option_id')->nullable()->constrained('ram_options')->nullOnDelete();
            $table->foreignId('storage_option_id')->nullable()->constrained('storage_options')->nullOnDelete();
            $table->foreignId('color_option_id')->nullable()->constrained('color_options')->nullOnDelete();
            $table->timestamps();
        });

        // Backfill from existing second_phone_purchases rows.
        DB::statement("
            INSERT INTO second_purchase_specifications (second_phone_purchase_id, ram_option_id, storage_option_id, color_option_id, created_at, updated_at)
            SELECT id, ram_option_id, storage_option_id, color_option_id, NOW(), NOW()
            FROM second_phone_purchases
            WHERE ram_option_id IS NOT NULL
               OR storage_option_id IS NOT NULL
               OR color_option_id IS NOT NULL
            ON DUPLICATE KEY UPDATE
                ram_option_id     = VALUES(ram_option_id),
                storage_option_id = VALUES(storage_option_id),
                color_option_id   = VALUES(color_option_id),
                updated_at        = NOW()
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('second_purchase_specifications');
    }
};
