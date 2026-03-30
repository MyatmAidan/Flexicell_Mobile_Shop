<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCustomer extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'payment_method',
        'amount',
        'nrc',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
