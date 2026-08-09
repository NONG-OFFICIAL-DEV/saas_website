<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'tagline',
        'summary',
        'description',
        'cta_label',
        'seo_title',
        'seo_description',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
