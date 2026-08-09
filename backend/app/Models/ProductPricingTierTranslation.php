<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductPricingTierTranslation extends Model
{
    use HasUuids;

    protected $fillable = [
        'pricing_tier_id',
        'locale',
        'name',
        'price_label',
        'description',
        'features_text',
        'cta_label',
    ];

    public function tier()
    {
        return $this->belongsTo(ProductPricingTier::class, 'pricing_tier_id');
    }
}
