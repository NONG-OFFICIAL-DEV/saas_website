<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->get();

        return response()->json(['success' => true, 'data' => MediaResource::collection($media)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:8192'], // 8MB
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        $media = Media::create([
            'disk' => 'public',
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json(['success' => true, 'data' => new MediaResource($media)], 201);
    }

    public function destroy(Media $media)
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(['success' => true, 'message' => 'Media deleted.']);
    }
}
