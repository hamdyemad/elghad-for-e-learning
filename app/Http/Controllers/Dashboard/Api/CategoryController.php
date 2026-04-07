<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of categories (with filters)
     * GET /api/dashboard/categories
     * Requires: admin role
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'parent_id', 'is_parent']);
        $perPage = $request->input('per_page') ?? 10;
        // Clean empty filters
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Always use pagination for dashboard API
        $paginatedCategories = $this->categoryService->getPaginatedCategories($perPage, $filters);

        return $this->successResponse([
            'categories' => CategoryResource::collection($paginatedCategories->items()),
            'pagination' => $this->formatPagination($paginatedCategories)
        ], __('categories.retrieved_successfully'));
    }

    /**
     * Store a newly created category
     * POST /api/dashboard/categories
     * Requires: admin role
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());

        return $this->createdResponse(
            new CategoryResource($category),
            __('categories.created_successfully')
        );
    }

    /**
     * Display the specified category
     * GET /api/dashboard/categories/{id}
     * Requires: admin role
     */
    public function show($id)
    {
        $category = $this->categoryService->getCategoryById((int) $id);

        return $this->successResponse(
            new CategoryResource($category),
            __('categories.single_retrieved_successfully')
        );
    }

    /**
     * Update the specified category
     * PUT /api/dashboard/categories/{id}
     * Requires: admin role
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->updateCategory((int) $id, $request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            __('categories.updated_successfully')
        );
    }

    /**
     * Remove the specified category
     * DELETE /api/dashboard/categories/{id}
     * Requires: admin role
     */
    public function destroy($id)
    {
        $this->categoryService->deleteCategory((int) $id);

        return $this->successResponse(
            null,
            __('categories.deleted_successfully')
        );
    }

    /**
     * Get parent categories only
     * GET /api/dashboard/categories/parents
     * Requires: admin role
     */
    public function parents()
    {
        $parents = $this->categoryService->getParentCategories();

        return $this->successResponse(
            CategoryResource::collection($parents),
            __('categories.parents_retrieved_successfully')
        );
    }

    /**
     * Get category tree (parents with children)
     * GET /api/dashboard/categories/tree
     * Requires: admin role
     */
    public function tree(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = $request->input('per_page') ?? 12;

        $paginatedTree = $this->categoryService->getCategoryTree($perPage, $filters);

        return $this->successResponse([
            'categories' => CategoryResource::collection($paginatedTree->items()),
            'pagination' => $this->formatPagination($paginatedTree)
        ], __('categories.tree_retrieved_successfully'));
    }

    /**
     * Reorder categories
     * POST /api/dashboard/categories/reorder
     * Requires: admin role
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:categories,id',
            'order.*.order' => 'required|integer'
        ]);

        $this->categoryService->reorderCategories($request->order);

        return $this->successResponse(
            null,
            __('categories.reordered_successfully')
        );
    }
}
