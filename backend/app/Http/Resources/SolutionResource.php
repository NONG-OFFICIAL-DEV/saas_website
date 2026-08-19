<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
            'name' => $t?->name,
            'tagline' => $t?->tagline,
            'description' => $t?->description,
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
