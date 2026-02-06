<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
