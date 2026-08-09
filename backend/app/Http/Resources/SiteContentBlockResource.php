<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteContentBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'key' => $this->key,
            'data' => $this->data,
            'content' => $t?->content,
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
