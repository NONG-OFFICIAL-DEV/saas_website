<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentationArticleRequest;
use App\Http\Requests\UpdateDocumentationArticleRequest;
use App\Http\Resources\DocumentationArticleResource;
use App\Models\DocumentationArticle;
use Illuminate\Http\Request;

class DocumentationArticleController extends Controller
{
    /** Every article (including drafts/archived) for the admin dashboard. */
    public function index(Request $request)
    {
        $articles = DocumentationArticle::with(['translations', 'category.translations', 'product.translations'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => DocumentationArticleResource::collection($articles),
        ]);
    }

    public function store(StoreDocumentationArticleRequest $request)
    {
        $data = $request->validated();

        $article = DocumentationArticle::create([
            'slug' => $data['slug'],
            'category_id' => $data['category_id'],
            'product_id' => $data['product_id'] ?? null,
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'sort_order' => $data['sort_order'] ?? 0,
            'published_at' => $data['published_at'] ?? (($data['status'] ?? 'draft') === 'published' ? now() : null),
        ]);

        $article->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'title' => $data['title'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        $article->load(['translations', 'category.translations', 'product.translations']);

        return response()->json([
            'success' => true,
            'data' => new DocumentationArticleResource($article),
        ], 201);
    }

    /** Full detail for the admin editor: every translation. */
    public function show(DocumentationArticle $documentation_article)
    {
        $documentation_article->load(['translations', 'category.translations', 'product.translations']);

        return response()->json([
            'success' => true,
            'data' => new DocumentationArticleResource($documentation_article),
        ]);
    }

    public function update(UpdateDocumentationArticleRequest $request, DocumentationArticle $documentation_article)
    {
        $data = $request->validated();

        // Publishing for the first time — stamp published_at automatically
        // unless the admin explicitly set one.
        if (
            ($data['status'] ?? null) === 'published'
            && $documentation_article->status !== 'published'
            && !array_key_exists('published_at', $data)
        ) {
            $data['published_at'] = now();
        }

        $documentation_article->update(array_intersect_key($data, array_flip([
            'slug', 'category_id', 'product_id', 'cover_image_url', 'status', 'sort_order', 'published_at',
        ])));

        if (array_key_exists('title', $data)) {
            $documentation_article->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['title', 'excerpt', 'content', 'seo_title', 'seo_description']))
            );
        }

        $documentation_article->load(['translations', 'category.translations', 'product.translations']);

        return response()->json([
            'success' => true,
            'data' => new DocumentationArticleResource($documentation_article),
        ]);
    }

    public function destroy(DocumentationArticle $documentation_article)
    {
        $documentation_article->delete();

        return response()->json(['success' => true, 'message' => 'Article deleted.']);
    }
}
