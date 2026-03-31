<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondPhonePurchase extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'image' => 'array',
        'purchase_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }

    public function ramOption()
    {
        return $this->belongsTo(RamOption::class, 'ram_option_id');
    }

    public function storageOption()
    {
        return $this->belongsTo(StorageOption::class, 'storage_option_id');
    }

    public function colorOption()
    {
        return $this->belongsTo(ColorOption::class, 'color_option_id');
    }
}
