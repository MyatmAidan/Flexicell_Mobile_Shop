<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_items', 'quantity')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(1)->after('product_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'device_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('device_id')->nullable()->after('quantity')->constrained('devices')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('devices', 'order_id')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->foreignId('order_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('devices', 'order_id')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->dropConstrainedForeignId('order_id');
            });
        }

        if (Schema::hasColumn('order_items', 'device_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('device_id');
            });
        }

        if (Schema::hasColumn('order_items', 'quantity')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};

