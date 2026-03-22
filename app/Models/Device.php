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
}
