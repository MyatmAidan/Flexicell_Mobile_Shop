<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    protected $guarded = [];

    public function phone_model()
    {
        return $this->hasMany(Phone_model::class);
    }
    public function logoUrl()
    {
        if (!$this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'brands/')) {
            return asset('storage/' . $this->logo);
        }

        return asset('storage/brands/' . $this->logo);
    }
}
