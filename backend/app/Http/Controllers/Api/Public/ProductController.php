<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Products hub listing — published only. */
    public function index(Request $request)
    {
        $products = Product::with('translations')
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['success' => true, 'data' => ProductResource::collection($products)]);
    }

    /** Product detail page — published only, with all nested content. */
    public function show(Request $request, string $slug)
    {
        $product = Product::with([
            'translations',
            'features.translations',
            'screenshots.translations',
            'pricingPlans.translations',
            'faqs.translations',
        ])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => new ProductResource($product)]);
    }
}
