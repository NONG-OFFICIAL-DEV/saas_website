<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /** Published testimonials, optionally filtered to one product. */
    public function index(Request $request)
    {
        $testimonials = Testimonial::with(['translations', 'product.translations'])
            ->where('is_published', true)
            ->when($request->query('product_id'), fn ($q, $productId) => $q->where('product_id', $productId))
            ->orderBy('sort_order')
            ->get();

        return response()->json(['success' => true, 'data' => TestimonialResource::collection($testimonials)]);
    }
}
