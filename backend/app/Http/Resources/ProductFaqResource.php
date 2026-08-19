<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', 'en');
        $t = $this->translation($locale);

        return [
            'id' => $this->id,
            'sort_order' => $this->sort_order,
            'question' => $t?->question,
            'answer' => $t?->answer,
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
