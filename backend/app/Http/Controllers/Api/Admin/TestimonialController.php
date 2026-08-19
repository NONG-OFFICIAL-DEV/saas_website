<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /** List every testimonial (including drafts) for the admin dashboard. */
    public function index(Request $request)
    {
        $testimonials = Testimonial::with(['translations', 'product.translations'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => TestimonialResource::collection($testimonials),
        ]);
    }

    public function store(StoreTestimonialRequest $request)
    {
        $data = $request->validated();

        $testimonial = Testimonial::create([
            'author_name' => $data['author_name'],
            'author_title' => $data['author_title'] ?? null,
            'author_avatar_url' => $data['author_avatar_url'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'rating' => $data['rating'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => $data['is_published'] ?? false,
        ]);

        $testimonial->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'quote' => $data['quote'],
        ]);

        $testimonial->load(['translations', 'product.translations']);

        return response()->json([
            'success' => true,
            'data' => new TestimonialResource($testimonial),
        ], 201);
    }

    /** Full detail for the admin editor: every translation. */
    public function show(Testimonial $testimonial)
    {
        $testimonial->load(['translations', 'product.translations']);

        return response()->json([
            'success' => true,
            'data' => new TestimonialResource($testimonial),
        ]);
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial)
    {
        $data = $request->validated();

        $testimonial->update(array_intersect_key($data, array_flip([
            'author_name', 'author_title', 'author_avatar_url', 'product_id', 'rating', 'sort_order', 'is_published',
        ])));

        if (array_key_exists('quote', $data)) {
            $testimonial->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                ['quote' => $data['quote']]
            );
        }

        $testimonial->load(['translations', 'product.translations']);

        return response()->json([
            'success' => true,
            'data' => new TestimonialResource($testimonial),
        ]);
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return response()->json(['success' => true, 'message' => 'Testimonial deleted.']);
    }
}
