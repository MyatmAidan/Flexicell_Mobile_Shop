<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = ['id'];

    public function purchases()
    {
        return $this->hasMany(SupplierPurchase::class);
    }
}
