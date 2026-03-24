<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function rate()
    {
        return $this->belongsTo(InstallmentRate::class, 'installment_rate_id');
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class);
    }
}
