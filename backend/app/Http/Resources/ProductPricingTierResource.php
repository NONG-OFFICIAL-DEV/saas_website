<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPricingTierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'name' => $t?->name,
            'price_label' => $t?->price_label,
            'description' => $t?->description,
            'features_text' => $t?->features_text,
            'cta_label' => $t?->cta_label,
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
