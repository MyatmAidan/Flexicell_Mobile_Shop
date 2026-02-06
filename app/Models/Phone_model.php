<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone_model extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'description' => 'array',
        'image' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
