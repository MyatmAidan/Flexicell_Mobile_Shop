<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeIn extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }
}
