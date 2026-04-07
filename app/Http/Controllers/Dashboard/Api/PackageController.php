<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\PackageService;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
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
     * Display a listing of packages (with filters)
     * GET /dashboard/packages
     * Requires: admin role
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category_id']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedPackages = $this->packageService->getPaginatedPackages($perPage, $filters);

        return $this->successResponse([
            'packages' => PackageResource::collection($paginatedPackages->items()),
            'pagination' => $this->formatPagination($paginatedPackages)
        ], __('packages.retrieved_successfully'));
    }

    /**
     * Store a newly created package
     * POST /dashboard/packages
     * Requires: admin role
     */
    public function store(StorePackageRequest $request)
    {
        $package = $this->packageService->createPackage($request->validated());

        return $this->createdResponse(
            new PackageResource($package),
            __('packages.created_successfully')
        );
    }

    /**
     * Display the specified package
     * GET /dashboard/packages/{id}
     * Requires: admin role
     */
    public function show($id)
    {
        $package = $this->packageService->getPackageById((int) $id);

        return $this->successResponse(
            new PackageResource($package),
            __('packages.single_retrieved_successfully')
        );
    }

    /**
     * Update the specified package
     * PUT /dashboard/packages/{id}
     * Requires: admin role
     */
    public function update(UpdatePackageRequest $request, $id)
    {
        $package = $this->packageService->updatePackage((int) $id, $request->validated());

        return $this->successResponse(
            new PackageResource($package),
            __('packages.updated_successfully')
        );
    }

    /**
     * Remove the specified package
     * DELETE /dashboard/packages/{id}
     * Requires: admin role
     */
    public function destroy($id)
    {
        $this->packageService->deletePackage((int) $id);

        return $this->successResponse(
            null,
            __('packages.deleted_successfully')
        );
    }

    /**
     * Get published packages only
     * GET /dashboard/packages/published
     * Requires: admin role
     */
    public function published()
    {
        $packages = $this->packageService->getPublishedPackages();

        return $this->successResponse(
            PackageResource::collection($packages),
            __('packages.published_retrieved_successfully')
        );
    }

    /**
     * Get draft packages only
     * GET /dashboard/packages/draft
     * Requires: admin role
     */
    public function draft()
    {
        $packages = $this->packageService->getDraftPackages();

        return $this->successResponse(
            PackageResource::collection($packages),
            __('packages.draft_retrieved_successfully')
        );
    }
}
