<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * GET /api/packages
     * List all active packages (public).
     * Optional query param: type=wedding|sendoff
     */
    public function index(Request $request): JsonResponse
    {
        $query = Package::active()->orderBy('sort_order');

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        return response()->json([
            'success'  => true,
            'packages' => $query->get(),
        ]);
    }

    /**
     * POST /api/admin/packages
     * Create a new package (admin).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255', 'unique:packages,name'],
            'type'                => ['required', 'in:wedding,sendoff'],
            'price'               => ['required', 'numeric', 'min:0'],
            'currency'            => ['required', 'string', 'max:10'],
            'description'         => ['required', 'string'],
            'features'            => ['required', 'array', 'min:1'],
            'features.*'          => ['required', 'string'],
            'hours_coverage'      => ['required', 'integer', 'min:0'],
            'photographers_count' => ['required', 'integer', 'min:1'],
            'is_featured'         => ['boolean'],
            'is_active'           => ['boolean'],
            'sort_order'          => ['integer', 'min:0'],
        ]);

        $package = Package::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully.',
            'package' => $package,
        ], 201);
    }

    /**
     * GET /api/admin/packages/{package}
     * Show a single package with its booking count (admin).
     */
    public function show(Package $package): JsonResponse
    {
        return response()->json([
            'success' => true,
            'package' => $package->loadCount('bookings'),
        ]);
    }

    /**
     * PUT /api/admin/packages/{package}
     * Update an existing package (admin).
     */
    public function update(Request $request, Package $package): JsonResponse
    {
        $validated = $request->validate([
            'name'                => ['sometimes', 'required', 'string', 'max:255', 'unique:packages,name,' . $package->id],
            'type'                => ['sometimes', 'required', 'in:wedding,sendoff'],
            'price'               => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency'            => ['sometimes', 'required', 'string', 'max:10'],
            'description'         => ['sometimes', 'required', 'string'],
            'features'            => ['sometimes', 'required', 'array', 'min:1'],
            'features.*'          => ['required', 'string'],
            'hours_coverage'      => ['sometimes', 'required', 'integer', 'min:0'],
            'photographers_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'is_featured'         => ['boolean'],
            'is_active'           => ['boolean'],
            'sort_order'          => ['integer', 'min:0'],
        ]);

        $package->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully.',
            'package' => $package->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/packages/{package}
     * Delete a package (admin). Blocked if it has bookings.
     */
    public function destroy(Package $package): JsonResponse
    {
        if ($package->bookings()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a package that has existing bookings.',
            ], 422);
        }

        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Package deleted successfully.',
        ]);
    }
}