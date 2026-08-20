<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentationCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn () => $this->product ? new ProductResource($this->product) : null),
            'parent_id' => $this->parent_id,
            // whenLoaded here is self-limiting against infinite recursion —
            // callers only ever eager-load one level up (category.parent),
            // never parent.parent, so the ancestor's own 'parent' key is
            // simply omitted rather than nesting forever.
            'parent' => $this->whenLoaded('parent', fn () => $this->parent ? new DocumentationCategoryResource($this->parent) : null),
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,

            'name' => $t?->name,
            'description' => $t?->description,

            'children' => DocumentationCategoryResource::collection($this->whenLoaded('children')),
            'articles' => DocumentationArticleSummaryResource::collection($this->whenLoaded('articles')),

            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
