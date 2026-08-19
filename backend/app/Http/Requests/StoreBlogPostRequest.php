<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:150', 'unique:blog_posts,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'cover_image_url' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['sometimes', 'boolean'],

            'locale' => ['sometimes', 'string', 'max:5'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
