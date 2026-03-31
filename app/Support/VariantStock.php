<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class VariantStock
{
    /**
     * Find or create a row in an option table by its value string.
     * Returns null for empty / "TBD" values.
     */
    public static function upsertOption(string $table, ?string $value): ?int
    {
        if (empty($value)) return null;
        $v = trim($value);
        if ($v === '' || strtoupper($v) === 'TBD') return null;

        $record = DB::table($table)->where('value', $v)->first();
        if ($record) return (int) $record->id;

        return DB::table($table)->insertGetId([
            'name'       => $v,
            'value'      => $v,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Find or create a color option by name and hex value.
     */
    public static function getOrCreateColorOption(string $name, ?string $hexValue = null): int
    {
        $name = trim($name);
        $hexValue = $hexValue ? trim($hexValue) : $name;

        $record = DB::table('color_options')
            ->where('name', $name)
            ->first();

        if ($record) return (int) $record->id;

        return DB::table('color_options')->insertGetId([
            'name'       => $name,
            'value'      => $hexValue,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Ensure a product_variants row exists for this combination; return its id.
     */
    public static function findOrCreateVariantId(int $productId, ?int $ramId, ?int $storageId, ?int $colorId): int
    {
        $existing = DB::table('product_variants')
            ->where('product_id', $productId)
            ->whereRaw('ram_option_id <=> ?', [$ramId])
            ->whereRaw('storage_option_id <=> ?', [$storageId])
            ->whereRaw('color_option_id <=> ?', [$colorId])
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        return (int) DB::table('product_variants')->insertGetId([
            'product_id'          => $productId,
            'ram_option_id'       => $ramId,
            'storage_option_id'   => $storageId,
            'color_option_id'     => $colorId,
            'sku'                 => null,
            'stock'               => 0,
            'price'               => null,
            'low_stock_threshold' => 10,
            'is_active'           => true,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    /**
     * Cached stock count on product_variants for available, serialized devices.
     * Source of truth is devices; this is updated by DeviceObserver and bulk inserts.
     */
    public static function syncSingleVariantStock(int $productVariantId): void
    {
        $cnt = DB::table('devices')
            ->where('product_variant_id', $productVariantId)
            ->whereNull('order_id')
            ->where('status', 'available')
            ->where('imei', 'not like', 'PENDING-%')
            ->count();

        DB::table('product_variants')->where('id', $productVariantId)->update([
            'stock'      => $cnt,
            'updated_at' => now(),
        ]);
    }

    /**
     * Recalculate cached stock for every variant of a product.
     */
    public static function syncProductVariantStock(int $productId): void
    {
        $variantIds = DB::table('product_variants')->where('product_id', $productId)->pluck('id');
        foreach ($variantIds as $vid) {
            static::syncSingleVariantStock((int) $vid);
        }
    }
}
