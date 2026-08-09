<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductFeatureResource;
use App\Models\Product;
use App\Models\ProductFeature;
use Illuminate\Http\Request;

class ProductFeatureController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $feature = $product->features()->create([
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $feature->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        $feature->load('translations');

        return response()->json(['success' => true, 'data' => new ProductFeatureResource($feature)], 201);
    }

    public function update(Request $request, ProductFeature $feature)
    {
        $data = $request->validate([
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'title' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $feature->update(array_intersect_key($data, array_flip(['icon', 'sort_order'])));

        if (array_key_exists('title', $data)) {
            $feature->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['title', 'description']))
            );
        }

        $feature->load('translations');

        return response()->json(['success' => true, 'data' => new ProductFeatureResource($feature)]);
    }

    public function destroy(ProductFeature $feature)
    {
        $feature->delete();

        return response()->json(['success' => true, 'message' => 'Feature deleted.']);
    }
}
