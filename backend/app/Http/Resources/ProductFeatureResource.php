<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFeatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'title' => $t?->title,
            'description' => $t?->description,
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
