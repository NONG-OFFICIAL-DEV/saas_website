<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** List every product (including drafts) for the admin dashboard. */
    public function index(Request $request)
    {
        $products = Product::with('translations')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create([
            'slug' => $data['slug'],
            'status' => $data['status'] ?? 'coming_soon',
            'cta_type' => $data['cta_type'] ?? 'waitlist',
            'cta_url' => $data['cta_url'] ?? null,
            'accent_color' => $data['accent_color'] ?? '#6366F1',
            'logo_url' => $data['logo_url'] ?? null,
            'hero_image_url' => $data['hero_image_url'] ?? null,
            'lead_source' => $data['lead_source'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => $data['is_published'] ?? false,
        ]);

        $product->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'name' => $data['name'],
            'tagline' => $data['tagline'] ?? null,
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        $product->load('translations');

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ], 201);
    }

    /** Full detail for the admin editor: every translation + nested content. */
    public function show(Product $product)
    {
        $product->load([
            'translations',
            'features.translations',
            'screenshots.translations',
            'faqs.translations',
        ]);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->update(array_intersect_key($data, array_flip([
            'slug', 'status', 'cta_type', 'cta_url', 'accent_color',
            'logo_url', 'hero_image_url', 'lead_source', 'sort_order', 'is_published',
        ])));

        if (array_key_exists('name', $data)) {
            $product->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip([
                    'name', 'tagline', 'summary', 'description', 'cta_label', 'seo_title', 'seo_description',
                ]))
            );
        }

        $product->load([
            'translations',
            'features.translations',
            'screenshots.translations',
            'faqs.translations',
        ]);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }
}
