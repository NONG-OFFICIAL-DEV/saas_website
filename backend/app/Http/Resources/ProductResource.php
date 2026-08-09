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
            // No pricing/plan data here by design — plans are owned and
            // managed entirely by the existing SaaS billing system, not
            // this CMS. The frontend fetches a product's plans from there.
            'features' => ProductFeatureResource::collection($this->whenLoaded('features')),
            'screenshots' => ProductScreenshotResource::collection($this->whenLoaded('screenshots')),

            // Raw translations — only for the admin editor (needs every locale at once)
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
