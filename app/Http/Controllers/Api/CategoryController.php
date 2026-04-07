<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
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

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'parent_id', 'tree', 'is_parent']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Default to active categories if no status specified
        if (!isset($filters['status'])) {
            $filters['status'] = 'active';
        }

        // Convert tree to boolean if present
        if (isset($filters['tree'])) {
            $filters['tree'] = filter_var($filters['tree'], FILTER_VALIDATE_BOOLEAN);
        }

        // Convert is_parent to boolean if present
        if (isset($filters['is_parent'])) {
            $filters['is_parent'] = filter_var($filters['is_parent'], FILTER_VALIDATE_BOOLEAN);
        }

        $categories = $this->categoryService->getCategories($filters);

        return $this->successResponse(
            CategoryResource::collection($categories),
            __('categories.retrieved_successfully')
        );
    }

    public function parents()
    {
        $categories = $this->categoryService->getCategories(['is_parent' => true, 'status' => 'active']);

        return $this->successResponse(
            CategoryResource::collection($categories),
            __('categories.parents_retrieved_successfully')
        );
    }

    public function tree(Request $request)
    {
        $filters = $request->only(['search', 'status', 'tree']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Default to active categories only for public tree endpoint
        if (!isset($filters['status'])) {
            $filters['status'] = 'active';
        }

        // Enable tree flag
        $filters['tree'] = true;

        $perPage = $request->input('per_page') ?? 12;

        $paginatedTree = $this->categoryService->getCategories($filters, true, $perPage);

        return $this->successResponse([
            'categories' => CategoryResource::collection($paginatedTree->items()),
            'pagination' => $this->formatPagination($paginatedTree)
        ], __('categories.tree_retrieved_successfully'));
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById((int) $id);

        return $this->successResponse(
            new CategoryResource($category),
            __('categories.retrieved_successfully')
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());

        return $this->createdResponse(
            new CategoryResource($category),
            __('categories.created_successfully')
        );
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            __('categories.updated_successfully')
        );
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);

        return $this->noContentResponse(__('categories.deleted_successfully'));
    }
}
