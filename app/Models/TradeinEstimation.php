<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeinEstimation extends Model
{
    protected $guarded = ['id'];

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }
}
