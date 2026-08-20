<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentationArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', fn () => new DocumentationCategoryResource($this->category)),
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn () => $this->product ? new ProductResource($this->product) : null),
            'cover_image_url' => $this->cover_image_url,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'published_at' => $this->published_at,

            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
            'content' => $t?->content,
            'seo_title' => $t?->seo_title,
            'seo_description' => $t?->seo_description,

            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
