<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentationCategoryRequest;
use App\Http\Requests\UpdateDocumentationCategoryRequest;
use App\Http\Resources\DocumentationCategoryResource;
use App\Models\DocumentationCategory;
use Illuminate\Http\Request;

class DocumentationCategoryController extends Controller
{
    /** Every category (including inactive) for the admin dashboard. */
    public function index(Request $request)
    {
        $categories = DocumentationCategory::with(['translations', 'product.translations', 'parent.translations'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => DocumentationCategoryResource::collection($categories),
        ]);
    }

    public function store(StoreDocumentationCategoryRequest $request)
    {
        $data = $request->validated();

        $category = DocumentationCategory::create([
            'slug' => $data['slug'],
            'icon' => $data['icon'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $category->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $category->load(['translations', 'product.translations', 'parent.translations']);

        return response()->json([
            'success' => true,
            'data' => new DocumentationCategoryResource($category),
        ], 201);
    }

    public function show(DocumentationCategory $documentation_category)
    {
        $documentation_category->load(['translations', 'product.translations', 'parent.translations', 'articles.translations']);

        return response()->json([
            'success' => true,
            'data' => new DocumentationCategoryResource($documentation_category),
        ]);
    }

    public function update(UpdateDocumentationCategoryRequest $request, DocumentationCategory $documentation_category)
    {
        $data = $request->validated();

        $documentation_category->update(array_intersect_key($data, array_flip([
            'slug', 'icon', 'product_id', 'parent_id', 'sort_order', 'is_active',
        ])));

        if (array_key_exists('name', $data)) {
            $documentation_category->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['name', 'description']))
            );
        }

        $documentation_category->load(['translations', 'product.translations', 'parent.translations']);

        return response()->json([
            'success' => true,
            'data' => new DocumentationCategoryResource($documentation_category),
        ]);
    }

    public function destroy(DocumentationCategory $documentation_category)
    {
        $documentation_category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted.']);
    }
}
