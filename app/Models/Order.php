<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $guarded = ['id'];

    protected $dates = ['order_date', 'delivered_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function installment()
    {
        return $this->hasOne(Installment::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
