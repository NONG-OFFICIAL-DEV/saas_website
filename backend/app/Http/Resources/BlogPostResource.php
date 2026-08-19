<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'author_name' => $this->author_name,
            'cover_image_url' => $this->cover_image_url,
            'published_at' => $this->published_at?->toIso8601String(),
            'is_published' => $this->is_published,

            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
            'content' => $t?->content,
            'seo_title' => $t?->seo_title,
            'seo_description' => $t?->seo_description,

            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
