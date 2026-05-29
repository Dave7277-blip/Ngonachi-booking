<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * GET /api/gallery
     * Return active gallery images (public).
     * Optional query param: category=wedding|sendoff|other
     */
    public function index(Request $request): JsonResponse
    {
        $query = GalleryImage::active()->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        return response()->json([
            'success' => true,
            'images'  => $query->get(),
        ]);
    }

    /**
     * POST /api/admin/gallery
     * Upload a new gallery image (admin).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image'      => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'title'      => ['required', 'string', 'max:255'],
            'category'   => ['required', 'in:wedding,sendoff,other'],
            'alt_text'   => ['nullable', 'string', 'max:255'],
            'is_featured'=> ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        // Store original image in the public disk
        $path = $request->file('image')->store('gallery', 'public');

        $image = GalleryImage::create([
            'title'       => $request->title,
            'file_path'   => $path,
            'category'    => $request->category,
            'alt_text'    => $request->alt_text,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active'   => true,
            'sort_order'  => (int) $request->input('sort_order', 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'image'   => $image,
        ], 201);
    }

    /**
     * DELETE /api/admin/gallery/{galleryImage}
     * Delete an image record and its file from storage (admin).
     */
    public function destroy(GalleryImage $galleryImage): JsonResponse
    {
        // Remove file from storage
        if (Storage::disk('public')->exists($galleryImage->file_path)) {
            Storage::disk('public')->delete($galleryImage->file_path);
        }

        $galleryImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }
}