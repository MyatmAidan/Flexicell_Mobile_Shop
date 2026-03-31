<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variant_combinations')) {
            return;
        }

        Schema::rename('product_variant_combinations', 'product_variants');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedInteger('low_stock_threshold')->default(10)->after('price');
            $table->boolean('is_active')->default(true)->after('low_stock_threshold');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('id');
        });

        $this->assignDeviceVariantIds();

        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['ram_option_id']);
            $table->dropForeign(['storage_option_id']);
            $table->dropForeign(['color_option_id']);
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'ram_option_id', 'storage_option_id', 'color_option_id']);
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('order_id');
        });

        $this->assignOrderItemVariantIds();
        $this->fillRemainingOrderItemVariantIds();

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'low_stock_threshold']);
        });

        $this->recalculateAllVariantStocks();
    }

    public function down(): void
    {
        throw new \RuntimeException('This migration cannot be safely reversed without a database restore.');
    }

    private function upsertVariantRow(int $productId, ?int $ramId, ?int $storageId, ?int $colorId): int
    {
        $q = DB::table('product_variants')->where('product_id', $productId);
        $q->whereRaw('ram_option_id <=> ?', [$ramId]);
        $q->whereRaw('storage_option_id <=> ?', [$storageId]);
        $q->whereRaw('color_option_id <=> ?', [$colorId]);
        $existing = $q->first();
        if ($existing) {
            return (int) $existing->id;
        }

        return (int) DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'ram_option_id' => $ramId,
            'storage_option_id' => $storageId,
            'color_option_id' => $colorId,
            'sku' => null,
            'stock' => 0,
            'price' => null,
            'low_stock_threshold' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function assignDeviceVariantIds(): void
    {
        $devices = DB::table('devices')->get(['id', 'product_id', 'ram_option_id', 'storage_option_id', 'color_option_id']);
        foreach ($devices as $d) {
            $vid = $this->upsertVariantRow(
                (int) $d->product_id,
                $d->ram_option_id !== null ? (int) $d->ram_option_id : null,
                $d->storage_option_id !== null ? (int) $d->storage_option_id : null,
                $d->color_option_id !== null ? (int) $d->color_option_id : null
            );
            DB::table('devices')->where('id', $d->id)->update(['product_variant_id' => $vid]);
        }
    }

    private function assignOrderItemVariantIds(): void
    {
        $items = DB::table('order_items')->get(['id', 'device_id', 'product_id']);

        foreach ($items as $oi) {
            $variantId = null;
            if ($oi->device_id) {
                $variantId = DB::table('devices')->where('id', $oi->device_id)->value('product_variant_id');
            }
            if (! $variantId && $oi->product_id) {
                $variantId = DB::table('product_variants')
                    ->where('product_id', $oi->product_id)
                    ->orderBy('id')
                    ->value('id');
            }
            if (! $variantId) {
                continue;
            }
            DB::table('order_items')->where('id', $oi->id)->update(['product_variant_id' => $variantId]);
        }
    }

    private function fillRemainingOrderItemVariantIds(): void
    {
        foreach (DB::table('order_items')->whereNull('product_variant_id')->get(['id', 'product_id']) as $oi) {
            if (! $oi->product_id) {
                continue;
            }
            $vid = DB::table('product_variants')
                ->where('product_id', $oi->product_id)
                ->orderBy('id')
                ->value('id');
            if (! $vid) {
                $vid = $this->upsertVariantRow((int) $oi->product_id, null, null, null);
            }
            DB::table('order_items')->where('id', $oi->id)->update(['product_variant_id' => $vid]);
        }
    }

    private function recalculateAllVariantStocks(): void
    {
        $productIds = DB::table('product_variants')->distinct()->pluck('product_id');
        foreach ($productIds as $pid) {
            DB::table('product_variants')->where('product_id', $pid)->update(['stock' => 0, 'updated_at' => now()]);

            $variantIds = DB::table('product_variants')->where('product_id', $pid)->pluck('id');
            if ($variantIds->isEmpty()) {
                continue;
            }

            $rows = DB::table('devices')
                ->select('product_variant_id', DB::raw('COUNT(*) as cnt'))
                ->whereIn('product_variant_id', $variantIds)
                ->whereNull('order_id')
                ->where('status', 'available')
                ->where('imei', 'not like', 'PENDING-%')
                ->groupBy('product_variant_id')
                ->get();

            foreach ($rows as $r) {
                DB::table('product_variants')->where('id', $r->product_variant_id)->update([
                    'stock' => (int) $r->cnt,
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
