<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteContentBlockResource;
use App\Models\SiteContentBlock;
use Illuminate\Http\Request;

class SiteContentController extends Controller
{
    public function index()
    {
        $blocks = SiteContentBlock::with('translations')->get();

        return response()->json(['success' => true, 'data' => SiteContentBlockResource::collection($blocks)]);
    }

    public function show(string $key)
    {
        $block = SiteContentBlock::with('translations')->firstOrCreate(['key' => $key], ['data' => []]);

        return response()->json(['success' => true, 'data' => new SiteContentBlockResource($block)]);
    }

    public function update(Request $request, string $key)
    {
        $data = $request->validate([
            'data' => ['sometimes', 'array'],
            'locale' => ['sometimes', 'string', 'max:5'],
            'content' => ['required', 'array'],
        ]);

        $block = SiteContentBlock::firstOrCreate(['key' => $key], ['data' => []]);

        if (array_key_exists('data', $data)) {
            $block->update(['data' => $data['data']]);
        }

        $block->translations()->updateOrCreate(
            ['locale' => $data['locale'] ?? 'en'],
            ['content' => $data['content']]
        );

        $block->load('translations');

        return response()->json(['success' => true, 'data' => new SiteContentBlockResource($block)]);
    }
}
