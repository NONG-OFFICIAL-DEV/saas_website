<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentationArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'slug' => [
                'sometimes', 'string', 'max:180',
                Rule::unique('documentation_articles', 'slug')->ignore($this->route('documentation_article')),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'category_id' => ['sometimes', 'uuid', 'exists:documentation_categories,id'],
            'product_id' => ['nullable', 'uuid', 'exists:products,id'],
            'cover_image_url' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'sort_order' => ['sometimes', 'integer'],
            'published_at' => ['nullable', 'date'],

            'locale' => ['sometimes', 'string', 'max:5'],
            'title' => ['sometimes', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
