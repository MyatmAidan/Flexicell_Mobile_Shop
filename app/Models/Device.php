<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'image' => 'array',
    ];

    protected $dates = ['purchase_at'];

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ramOption()
    {
        return $this->belongsTo(\App\Models\RamOption::class, 'ram_option_id');
    }

    public function storageOption()
    {
        return $this->belongsTo(\App\Models\StorageOption::class, 'storage_option_id');
    }

    public function colorOption()
    {
        return $this->belongsTo(\App\Models\ColorOption::class, 'color_option_id');
    }
}
