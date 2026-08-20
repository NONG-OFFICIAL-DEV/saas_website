<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DocumentationCategoryTranslation extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentationCategory::class, 'category_id');
    }
}
