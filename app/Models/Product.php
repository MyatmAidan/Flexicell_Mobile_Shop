<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'image' => 'array',
    ];

    public function phoneModel()
    {
        return $this->belongsTo(Phone_model::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function imageUrl()
    {
        $images = $this->image;
        if (is_array($images) && count($images) > 0) {
            return asset('storage/products/' . $images[0]);
        }
        return asset('img/logo.png'); // Placeholder
    }
}
