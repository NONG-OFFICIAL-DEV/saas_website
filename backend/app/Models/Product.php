<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'slug',
        'status',
        'cta_type',
        'cta_url',
        'pricing_mode',
        'accent_color',
        'logo_url',
        'hero_image_url',
        'lead_source',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function features()
    {
        return $this->hasMany(ProductFeature::class)->orderBy('sort_order');
    }

    public function screenshots()
    {
        return $this->hasMany(ProductScreenshot::class)->orderBy('sort_order');
    }

    /** Marketing-only "starting at" pricing cards — only meaningful when pricing_mode is 'cms'.
     *  Products with pricing_mode 'live' (nexstack-pos, studio-management) fetch real plans
     *  from their own backend instead; see PriceSection.vue / StudioPriceSection.vue. */
    public function pricingPlans()
    {
        return $this->hasMany(PricingPlan::class)->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(ProductFaq::class)->orderBy('sort_order');
    }

    /** Get the translation row for a given locale, falling back to English. */
    public function translation(string $locale = 'en')
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en');
    }
}
