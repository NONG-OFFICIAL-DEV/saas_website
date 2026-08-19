<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolutionRequest;
use App\Http\Requests\UpdateSolutionRequest;
use App\Http\Resources\SolutionResource;
use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    /** List every solution (including drafts) for the admin dashboard. */
    public function index(Request $request)
    {
        $solutions = Solution::with(['translations', 'products.translations'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => SolutionResource::collection($solutions),
        ]);
    }

    public function store(StoreSolutionRequest $request)
    {
        $data = $request->validated();

        $solution = Solution::create([
            'slug' => $data['slug'],
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => $data['is_published'] ?? false,
        ]);

        $solution->translations()->create([
            'locale' => $data['locale'] ?? 'en',
            'name' => $data['name'],
            'tagline' => $data['tagline'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        if (array_key_exists('product_ids', $data)) {
            $solution->products()->sync(array_values(array_unique($data['product_ids'])));
        }

        $solution->load(['translations', 'products.translations']);

        return response()->json([
            'success' => true,
            'data' => new SolutionResource($solution),
        ], 201);
    }

    /** Full detail for the admin editor: every translation + linked products. */
    public function show(Solution $solution)
    {
        $solution->load(['translations', 'products.translations']);

        return response()->json([
            'success' => true,
            'data' => new SolutionResource($solution),
        ]);
    }

    public function update(UpdateSolutionRequest $request, Solution $solution)
    {
        $data = $request->validated();

        $solution->update(array_intersect_key($data, array_flip([
            'slug', 'icon', 'sort_order', 'is_published',
        ])));

        if (array_key_exists('name', $data)) {
            $solution->translations()->updateOrCreate(
                ['locale' => $data['locale'] ?? 'en'],
                array_intersect_key($data, array_flip(['name', 'tagline', 'description']))
            );
        }

        if (array_key_exists('product_ids', $data)) {
            $solution->products()->sync(array_values(array_unique($data['product_ids'])));
        }

        $solution->load(['translations', 'products.translations']);

        return response()->json([
            'success' => true,
            'data' => new SolutionResource($solution),
        ]);
    }

    public function destroy(Solution $solution)
    {
        $solution->delete();

        return response()->json(['success' => true, 'message' => 'Solution deleted.']);
    }
}
