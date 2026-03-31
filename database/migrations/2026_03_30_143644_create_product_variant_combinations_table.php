<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('ram_option_id')->nullable()->constrained('ram_options')->nullOnDelete();
            $table->foreignId('storage_option_id')->nullable()->constrained('storage_options')->nullOnDelete();
            $table->foreignId('color_option_id')->nullable()->constrained('color_options')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('price', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'ram_option_id', 'storage_option_id', 'color_option_id'], 'pvc_unique');
        });

        // Backfill: count available (non-PENDING) devices per product+variant combo.
        DB::statement("
            INSERT INTO product_variant_combinations (product_id, ram_option_id, storage_option_id, color_option_id, stock, created_at, updated_at)
            SELECT
                d.product_id,
                d.ram_option_id,
                d.storage_option_id,
                d.color_option_id,
                COUNT(*) as stock,
                NOW(),
                NOW()
            FROM devices d
            WHERE d.order_id IS NULL
              AND d.status = 'available'
              AND d.imei NOT LIKE 'PENDING-%'
            GROUP BY d.product_id, d.ram_option_id, d.storage_option_id, d.color_option_id
            ON DUPLICATE KEY UPDATE
                stock      = VALUES(stock),
                updated_at = NOW()
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_combinations');
    }
};
