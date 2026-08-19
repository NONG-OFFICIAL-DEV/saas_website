<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SolutionResource;
use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    /** Solutions hub listing — published only, with linked products. */
    public function index(Request $request)
    {
        $solutions = Solution::with(['translations', 'products.translations'])
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['success' => true, 'data' => SolutionResource::collection($solutions)]);
    }

    /** Solution detail page — published only, with linked products' nested content. */
    public function show(Request $request, string $slug)
    {
        $solution = Solution::with(['translations', 'products.translations'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$solution) {
            return response()->json(['success' => false, 'message' => 'Solution not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => new SolutionResource($solution)]);
    }
}
