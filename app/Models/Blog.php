<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = ['title', 'thumbnail'];

    public function contents()
    {
        return $this->hasMany(BlogContent::class)->orderBy('order');
    }
}
