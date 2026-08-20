<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight shape (no content) for contexts that only need to list
 * articles — the category tree sidebar, search results, related articles.
 * Keeps those payloads small instead of shipping full HTML content that
 * won't be rendered.
 */
class DocumentationArticleSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'product_id' => $this->product_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
        ];
    }
}
