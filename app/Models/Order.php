<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

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

    /**
     * Installment checkout details (NRC, attachments) from direct sale.
     */
    public function paymentCustomer()
    {
        return $this->hasOne(PaymentCustomer::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
