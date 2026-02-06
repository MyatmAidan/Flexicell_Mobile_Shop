<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItem extends Model
{
    protected $guarded = ['id'];

    public function purchase()
    {
        return $this->belongsTo(SupplierPurchase::class, 'purchase_id');
    }

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }
}
