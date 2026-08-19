<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSolutionRequest extends FormRequest
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
                Rule::unique('solutions', 'slug')->ignore($this->route('solution')),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer'],
            'is_published' => ['sometimes', 'boolean'],
            'product_ids' => ['sometimes', 'array'],
            'product_ids.*' => ['uuid', 'exists:products,id'],

            'locale' => ['sometimes', 'string', 'max:5'],
            'name' => ['sometimes', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
