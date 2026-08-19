<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /** Blog listing — published only, newest first. */
    public function index(Request $request)
    {
        $posts = BlogPost::with('translations')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->get();

        return response()->json(['success' => true, 'data' => BlogPostResource::collection($posts)]);
    }

    /** Blog post detail page — published only. */
    public function show(Request $request, string $slug)
    {
        $post = BlogPost::with('translations')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => new BlogPostResource($post)]);
    }
}
