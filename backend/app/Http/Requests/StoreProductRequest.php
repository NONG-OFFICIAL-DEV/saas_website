<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:150', 'unique:products,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'status' => ['sometimes', 'in:live,beta,coming_soon'],
            'cta_type' => ['sometimes', 'in:register,external_link,waitlist'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'pricing_mode' => ['sometimes', 'in:live,cms'],
            'accent_color' => ['sometimes', 'string', 'max:20'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'hero_image_url' => ['nullable', 'string', 'max:500'],
            'lead_source' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer'],
            'is_published' => ['sometimes', 'boolean'],

            'locale' => ['sometimes', 'string', 'max:5'],
            'name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:100'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
