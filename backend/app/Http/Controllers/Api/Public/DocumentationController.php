<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentationArticleResource;
use App\Http\Resources\DocumentationCategoryResource;
use App\Models\DocumentationArticle;
use App\Models\DocumentationCategory;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /** Full published category tree (2 levels) with their published articles — one call builds the whole sidebar/home page. */
    public function categories(Request $request)
    {
        $publishedArticles = fn ($q) => $q->published()->with('translations')->orderBy('sort_order');

        $categories = DocumentationCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'translations',
                'product.translations',
                'articles' => $publishedArticles,
                'children' => fn ($q) => $q->where('is_active', true)->with([
                    'translations',
                    'product.translations',
                    'articles' => $publishedArticles,
                ]),
            ])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => DocumentationCategoryResource::collection($categories),
        ]);
    }

    /** Single published article, with breadcrumb, prev/next, and related articles. */
    public function article(Request $request, string $slug)
    {
        $article = DocumentationArticle::published()
            ->where('slug', $slug)
            ->with(['translations', 'product.translations', 'category.translations', 'category.parent.translations'])
            ->first();

        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article not found.'], 404);
        }

        $siblings = DocumentationArticle::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $ordered = DocumentationArticle::published()
            ->where('category_id', $article->category_id)
            ->orderBy('sort_order')
            ->pluck('id')
            ->values();
        $position = $ordered->search($article->id);

        $prev = $position !== false && $position > 0
            ? DocumentationArticle::with('translations')->find($ordered[$position - 1])
            : null;
        $next = $position !== false && $position < $ordered->count() - 1
            ? DocumentationArticle::with('translations')->find($ordered[$position + 1])
            : null;

        $related = $siblings->take(4);

        return response()->json([
            'success' => true,
            'data' => [
                ...(new DocumentationArticleResource($article))->toArray($request),
                'prev' => $prev ? ['slug' => $prev->slug, 'title' => $prev->translation($request->get('locale', 'en'))?->title] : null,
                'next' => $next ? ['slug' => $next->slug, 'title' => $next->translation($request->get('locale', 'en'))?->title] : null,
                'related' => $related->map(fn ($a) => [
                    'slug' => $a->slug,
                    'title' => $a->translation($request->get('locale', 'en'))?->title,
                    'excerpt' => $a->translation($request->get('locale', 'en'))?->excerpt,
                ])->values(),
            ],
        ]);
    }

    /** Search across title/excerpt/content plus category and product names. */
    public function search(Request $request)
    {
        $term = trim((string) $request->get('q', ''));
        if ($term === '') {
            return response()->json(['success' => true, 'data' => []]);
        }
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        // Grouped inside one where() closure so "published" stays AND'd
        // against the whole OR set below it, rather than only the first
        // clause — otherwise a draft/archived article whose category or
        // product name happens to match would leak into public results.
        $articles = DocumentationArticle::published()
            ->with(['translations', 'category.translations', 'product.translations'])
            ->where(function ($q) use ($like) {
                $q->whereHas('translations', function ($q2) use ($like) {
                    $q2->where('locale', 'en')
                        ->where(function ($q3) use ($like) {
                            $q3->where('title', 'ilike', $like)
                                ->orWhere('excerpt', 'ilike', $like)
                                ->orWhere('content', 'ilike', $like);
                        });
                })
                    ->orWhereHas('category.translations', fn ($q4) => $q4->where('name', 'ilike', $like))
                    ->orWhereHas('product.translations', fn ($q4) => $q4->where('name', 'ilike', $like));
            })
            ->limit(20)
            ->get();

        $locale = $request->get('locale', 'en');

        return response()->json([
            'success' => true,
            'data' => $articles->map(fn ($a) => [
                'slug' => $a->slug,
                'title' => $a->translation($locale)?->title,
                'excerpt' => $a->translation($locale)?->excerpt,
                'category' => $a->category?->translation($locale)?->name,
                'product' => $a->product?->translation($locale)?->name,
            ])->values(),
        ]);
    }
}
