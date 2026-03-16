<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogContent extends Model
{
    protected $fillable = ['blog_id', 'heading', 'content', 'order'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function images()
    {
        return $this->hasMany(BlogContentImage::class, 'blog_content_id');
    }
}
