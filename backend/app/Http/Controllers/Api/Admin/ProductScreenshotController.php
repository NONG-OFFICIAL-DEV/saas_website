<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductScreenshotResource;
use App\Models\Product;
use App\Models\ProductScreenshot;
use Illuminate\Http\Request;

class ProductScreenshotController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $screenshot = $product->screenshots()->create([
            'url' => $data['url'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $screenshot->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'alt_text' => $data['alt_text'] ?? null,
            'caption' => $data['caption'] ?? null,
        ]);

        $screenshot->load('translations');

        return response()->json(['success' => true, 'data' => new ProductScreenshotResource($screenshot)], 201);
    }

    public function update(Request $request, ProductScreenshot $screenshot)
    {
        $data = $request->validate([
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $screenshot->update(array_intersect_key($data, array_flip(['sort_order'])));

        if (array_key_exists('alt_text', $data) || array_key_exists('caption', $data)) {
            $screenshot->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['alt_text', 'caption']))
            );
        }

        $screenshot->load('translations');

        return response()->json(['success' => true, 'data' => new ProductScreenshotResource($screenshot)]);
    }

    public function destroy(ProductScreenshot $screenshot)
    {
        $screenshot->delete();

        return response()->json(['success' => true, 'message' => 'Screenshot deleted.']);
    }
}
