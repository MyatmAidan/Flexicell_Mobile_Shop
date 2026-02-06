<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['paid_date'];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
