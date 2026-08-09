<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductFeatureTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['product_feature_id', 'locale', 'title', 'description'];

    public function feature()
    {
        return $this->belongsTo(ProductFeature::class, 'product_feature_id');
    }
}
