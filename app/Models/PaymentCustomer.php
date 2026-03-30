<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCustomer extends Model
{
    protected $fillable = [
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
}
