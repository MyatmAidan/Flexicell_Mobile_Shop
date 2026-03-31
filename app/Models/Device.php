<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Device extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'image' => 'array',
    ];

    protected $dates = ['purchase_at'];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id',
            'id',
            'product_variant_id',
            'product_id'
        );
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function warrantyDetails()
    {
        return $this->hasMany(WarrantyDetail::class);
    }
}
