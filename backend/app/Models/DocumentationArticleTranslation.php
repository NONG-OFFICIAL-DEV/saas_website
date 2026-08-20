<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DocumentationArticleTranslation extends Model
{
    use HasUuids;

    protected $fillable = [
        'article_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
    ];

    public function article()
    {
        return $this->belongsTo(DocumentationArticle::class, 'article_id');
    }
}
