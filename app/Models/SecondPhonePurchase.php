<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondPhonePurchase extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'image' => 'array',
    ];

    protected $dates = ['purchase_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }
}
