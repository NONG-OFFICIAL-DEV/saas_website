<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'status' => $this->status,
            'cta_type' => $this->cta_type,
            'cta_url' => $this->cta_url,
            'pricing_mode' => $this->pricing_mode,
            'accent_color' => $this->accent_color,
            'logo_url' => $this->logo_url,
            'hero_image_url' => $this->hero_image_url,
            'lead_source' => $this->lead_source,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,

            // Locale-resolved translatable fields
            'name' => $t?->name,
            'tagline' => $t?->tagline,
            'summary' => $t?->summary,
            'description' => $t?->description,
            'cta_label' => $t?->cta_label,
            'seo_title' => $t?->seo_title,
            'seo_description' => $t?->seo_description,

            // Nested content — only present when eager-loaded by the controller.
            // pricing_plans is CMS-authored marketing content ("starting at" cards,
            // no checkout) and only meaningful when pricing_mode is 'cms'. Products
            // with pricing_mode 'live' (nexstack-pos, studio-management) have their
            // own real billing system and fetch live plans from there instead —
            // see PriceSection.vue / StudioPriceSection.vue.
            'features' => ProductFeatureResource::collection($this->whenLoaded('features')),
            'screenshots' => ProductScreenshotResource::collection($this->whenLoaded('screenshots')),
            'pricing_plans' => PricingPlanResource::collection($this->whenLoaded('pricingPlans')),
            'faqs' => ProductFaqResource::collection($this->whenLoaded('faqs')),

            // Raw translations — only for the admin editor (needs every locale at once)
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
