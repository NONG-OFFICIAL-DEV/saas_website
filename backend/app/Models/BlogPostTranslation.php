<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BlogPostTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['blog_post_id', 'locale', 'title', 'excerpt', 'content', 'seo_title', 'seo_description'];

    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
