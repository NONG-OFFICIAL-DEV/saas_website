<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentationCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'slug' => [
                'sometimes', 'string', 'max:150',
                Rule::unique('documentation_categories', 'slug')->ignore($this->route('documentation_category')),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'product_id' => ['nullable', 'uuid', 'exists:products,id'],
            'parent_id' => [
                'nullable', 'uuid', 'exists:documentation_categories,id',
                Rule::notIn([$this->route('documentation_category')?->id]),
            ],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],

            'locale' => ['sometimes', 'string', 'max:5'],
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ];
    }
}
