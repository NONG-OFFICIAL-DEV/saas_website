<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteContentBlockResource;
use App\Models\SiteContentBlock;
use Illuminate\Http\Request;

class SiteContentController extends Controller
{
    public function show(Request $request, string $key)
    {
        $block = SiteContentBlock::with('translations')->where('key', $key)->first();

        if (!$block) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => new SiteContentBlockResource($block)]);
    }
}
