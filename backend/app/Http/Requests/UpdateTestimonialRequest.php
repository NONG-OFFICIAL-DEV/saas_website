<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'author_name' => ['sometimes', 'string', 'max:150'],
            'author_title' => ['nullable', 'string', 'max:255'],
            'author_avatar_url' => ['nullable', 'string', 'max:500'],
            'product_id' => ['nullable', 'uuid', 'exists:products,id'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['sometimes', 'integer'],
            'is_published' => ['sometimes', 'boolean'],

            'locale' => ['sometimes', 'string', 'max:5'],
            'quote' => ['sometimes', 'string'],
        ];
    }
}
