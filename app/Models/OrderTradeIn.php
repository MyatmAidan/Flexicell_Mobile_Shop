<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTradeIn extends Model
{
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function secondPhonePurchase()
    {
        return $this->belongsTo(SecondPhonePurchase::class);
    }
}
