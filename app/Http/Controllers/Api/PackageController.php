<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PackageService;
use App\Http\Resources\PackageResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponseTrait;

    protected $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * Display a listing of packages
     * GET /api/packages
     * Query params:
     *   - status: all|published (default: published for public)
     *   - category_id: filter by category
     *   - search: search term
     * Requires: auth:sanctum (optional)
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category_id']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        $perPage = $request->input('per_page', 15);
        $paginatedPackages = $this->packageService->getPackages($filters, true, $perPage);

        return $this->successResponse([
            'packages' => PackageResource::collection($paginatedPackages->items()),
            'pagination' => $this->formatPagination($paginatedPackages)
        ], __('packages.retrieved_successfully'));
    }

    /**
     * Display the specified package
     * GET /api/packages/{id}
     * Requires: auth:sanctum (optional)
     */
    public function show($id)
    {
        $package = $this->packageService->getPackageById((int) $id);

        return $this->successResponse(
            new PackageResource($package),
            __('packages.single_retrieved_successfully')
        );
    }
}
