<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ramOption(): BelongsTo
    {
        return $this->belongsTo(RamOption::class, 'ram_option_id');
    }

    public function storageOption(): BelongsTo
    {
        return $this->belongsTo(StorageOption::class, 'storage_option_id');
    }

    public function colorOption(): BelongsTo
    {
        return $this->belongsTo(ColorOption::class, 'color_option_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'product_variant_id');
    }
}
