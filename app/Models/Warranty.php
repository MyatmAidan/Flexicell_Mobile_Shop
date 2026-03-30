<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $guarded = ['id'];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function warrantyDetails()
    {
        return $this->hasMany(WarrantyDetail::class);
    }
}
