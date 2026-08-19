<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductFaqResource;
use App\Models\Product;
use App\Models\ProductFaq;
use Illuminate\Http\Request;

class ProductFaqController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
        ]);

        $faq = $product->faqs()->create([
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $faq->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'question' => $data['question'],
            'answer' => $data['answer'],
        ]);

        $faq->load('translations');

        return response()->json(['success' => true, 'data' => new ProductFaqResource($faq)], 201);
    }

    public function update(Request $request, ProductFaq $faq)
    {
        $data = $request->validate([
            'sort_order' => ['sometimes', 'integer'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'question' => ['sometimes', 'string', 'max:255'],
            'answer' => ['sometimes', 'string'],
        ]);

        $faq->update(array_intersect_key($data, array_flip(['sort_order'])));

        if (array_key_exists('question', $data) || array_key_exists('answer', $data)) {
            $faq->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['question', 'answer']))
            );
        }

        $faq->load('translations');

        return response()->json(['success' => true, 'data' => new ProductFaqResource($faq)]);
    }

    public function destroy(ProductFaq $faq)
    {
        $faq->delete();

        return response()->json(['success' => true, 'message' => 'FAQ deleted.']);
    }
}
