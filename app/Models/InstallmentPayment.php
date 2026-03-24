<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'paid_date' => 'date',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
