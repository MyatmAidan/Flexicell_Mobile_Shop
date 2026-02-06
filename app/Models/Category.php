<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    function Phone_models()
    {
        return $this->hasMany(Phone_model::class);
    }
}
