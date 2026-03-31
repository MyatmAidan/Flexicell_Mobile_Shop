<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'image' => 'array',
    ];

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function devices(): HasManyThrough
    {
        return $this->hasManyThrough(Device::class, ProductVariant::class, 'product_id', 'product_variant_id', 'id', 'id');
    }

    /**
     * Aggregate available (in-stock) device count for storefront / legacy "stock_quantity" usage.
     */
    public function getStockQuantityAttribute($value): int
    {
        return (int) $this->devices()
            ->where('devices.status', 'available')
            ->whereNull('devices.order_id')
            ->count();
    }

    /**
     * Display price: lowest available device selling price, else lowest variant catalog price.
     */
    public function getSellingPriceAttribute($value): float
    {
        if ($value !== null && $value !== '') {
            return (float) $value;
        }

        $min = DB::table('devices')
            ->join('product_variants as pv', 'pv.id', '=', 'devices.product_variant_id')
            ->where('pv.product_id', $this->id)
            ->where('devices.status', 'available')
            ->whereNull('devices.order_id')
            ->whereNotNull('devices.selling_price')
            ->min('devices.selling_price');

        if ($min !== null) {
            return (float) $min;
        }

        $vp = DB::table('product_variants')->where('product_id', $this->id)->whereNotNull('price')->min('price');

        return $vp !== null ? (float) $vp : 0.0;
    }

    public function imageUrl()
    {
        $images = $this->image;
        if (is_array($images) && count($images) > 0) {
            return asset('storage/products/' . $images[0]);
        }
        return asset('img/logo.png');
    }
}
