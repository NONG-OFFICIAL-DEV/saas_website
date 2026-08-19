<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /** List every post (including drafts) for the admin dashboard. */
    public function index(Request $request)
    {
        $posts = BlogPost::with('translations')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BlogPostResource::collection($posts),
        ]);
    }

    public function store(StoreBlogPostRequest $request)
    {
        $data = $request->validated();

        $post = BlogPost::create([
            'slug' => $data['slug'],
            'author_name' => $data['author_name'] ?? null,
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'is_published' => $data['is_published'] ?? false,
        ]);

        $post->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'title' => $data['title'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        $post->load('translations');

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($post),
        ], 201);
    }

    /** Full detail for the admin editor: every translation. */
    public function show(BlogPost $blogPost)
    {
        $blogPost->load('translations');

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($blogPost),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
    {
        $data = $request->validated();

        $blogPost->update(array_intersect_key($data, array_flip([
            'slug', 'author_name', 'cover_image_url', 'published_at', 'is_published',
        ])));

        if (array_key_exists('title', $data) || array_key_exists('content', $data)) {
            $blogPost->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip([
                    'title', 'excerpt', 'content', 'seo_title', 'seo_description',
                ]))
            );
        }

        $blogPost->load('translations');

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($blogPost),
        ]);
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return response()->json(['success' => true, 'message' => 'Post deleted.']);
    }
}
