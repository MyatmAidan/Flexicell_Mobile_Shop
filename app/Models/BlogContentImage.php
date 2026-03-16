<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogContentImage extends Model
{
    protected $table = 'blog_images';

    protected $fillable = ['blog_content_id', 'image_path'];

    public function content()
    {
        return $this->belongsTo(BlogContent::class, 'blog_content_id');
    }
}
