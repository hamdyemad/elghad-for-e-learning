<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\PackageService;
use App\Services\CategoryService;
use App\Http\Requests\Dashboard\StorePackageRequest;
use App\Http\Requests\Dashboard\UpdatePackageRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponseTrait;

    protected $packageService;
    protected $categoryService;

    public function __construct(PackageService $packageService, CategoryService $categoryService)
    {
        $this->packageService = $packageService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of packages (for dashboard view)
     * GET /dashboard/packages
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category_id']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedPackages = $this->packageService->getPaginatedPackages($perPage, $filters);
        $categories = $this->categoryService->getAllCategories();

        return view('dashboard.packages.index', [
            'packages' => $paginatedPackages,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new package
     * GET /dashboard/packages/create
     */
    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('dashboard.packages.form', [
            'categories' => $categories,
            'package' => null
        ]);
    }

    /**
     * Store a newly created package
     * POST /dashboard/packages
     */
    public function store(StorePackageRequest $request)
    {
        $package = $this->packageService->createPackage($request->validated());

        return $this->successRedirect(route('dashboard.packages.index'), __('packages.created_successfully'));
    }

    /**
     * Display the specified package
     * GET /dashboard/packages/{id}
     */
    public function show($id)
    {
        $package = $this->packageService->getPackageById((int) $id);
        return view('dashboard.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified package
     * GET /dashboard/packages/{id}/edit
     */
    public function edit($id)
    {
        $package = $this->packageService->getPackageById((int) $id);
        $categories = $this->categoryService->getAllCategories();
        return view('dashboard.packages.form', [
            'package' => $package,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified package
     * PUT /dashboard/packages/{id}
     */
    public function update(UpdatePackageRequest $request, $id)
    {
        $package = $this->packageService->updatePackage((int) $id, $request->validated());

        return $this->successRedirect(route('dashboard.packages.index'), __('packages.updated_successfully'));
    }

    /**
     * Remove the specified package
     * DELETE /dashboard/packages/{id}
     */
    public function destroy($id)
    {
        $this->packageService->deletePackage((int) $id);

        return $this->successRedirect(route('dashboard.packages.index'), __('packages.deleted_successfully'));
    }
}
