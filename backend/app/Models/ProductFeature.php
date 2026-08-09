<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    use HasUuids;

    protected $fillable = ['product_id', 'icon', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductFeatureTranslation::class);
    }

    public function translation(string $locale = 'en')
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en');
    }
}
