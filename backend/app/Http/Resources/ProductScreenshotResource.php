<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductScreenshotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'url' => $this->url,
            'sort_order' => $this->sort_order,
            'alt_text' => $t?->alt_text,
            'caption' => $t?->caption,
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
