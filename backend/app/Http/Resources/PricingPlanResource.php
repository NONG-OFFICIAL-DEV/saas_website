<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'monthly_price' => $this->monthly_price,
            'yearly_price' => $this->yearly_price,
            'is_popular' => $this->is_popular,
            'sort_order' => $this->sort_order,
            'name' => $t?->name,
            'tagline' => $t?->tagline,
            'cta_label' => $t?->cta_label,
            'features' => $t?->features ?? [],
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
