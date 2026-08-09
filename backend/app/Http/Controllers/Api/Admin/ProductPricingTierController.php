<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductPricingTierResource;
use App\Models\Product;
use App\Models\ProductPricingTier;
use Illuminate\Http\Request;

class ProductPricingTierController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'name' => ['required', 'string', 'max:100'],
            'price_label' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'features_text' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:100'],
        ]);

        $tier = $product->pricingTiers()->create([
            'is_featured' => $data['is_featured'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $tier->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'name' => $data['name'],
            'price_label' => $data['price_label'] ?? null,
            'description' => $data['description'] ?? null,
            'features_text' => $data['features_text'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
        ]);

        $tier->load('translations');

        return response()->json(['success' => true, 'data' => new ProductPricingTierResource($tier)], 201);
    }

    public function update(Request $request, ProductPricingTier $pricingTier)
    {
        $data = $request->validate([
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'name' => ['sometimes', 'string', 'max:100'],
            'price_label' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'features_text' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:100'],
        ]);

        $pricingTier->update(array_intersect_key($data, array_flip(['is_featured', 'sort_order'])));

        if (array_key_exists('name', $data)) {
            $pricingTier->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['name', 'price_label', 'description', 'features_text', 'cta_label']))
            );
        }

        $pricingTier->load('translations');

        return response()->json(['success' => true, 'data' => new ProductPricingTierResource($pricingTier)]);
    }

    public function destroy(ProductPricingTier $pricingTier)
    {
        $pricingTier->delete();

        return response()->json(['success' => true, 'message' => 'Pricing tier deleted.']);
    }
}
