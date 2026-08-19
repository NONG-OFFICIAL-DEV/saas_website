<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'author_name' => $this->author_name,
            'author_title' => $this->author_title,
            'author_avatar_url' => $this->author_avatar_url,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn () => $this->product ? new ProductResource($this->product) : null),
            'rating' => $this->rating,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
            'quote' => $t?->quote,
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
