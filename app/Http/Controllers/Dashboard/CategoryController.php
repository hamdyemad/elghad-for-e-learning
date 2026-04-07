<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'parent_id']);
        
        // Clean empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });
        
        if (!empty($filters)) {
            $categories = $this->categoryService->filterCategories($filters);
        } else {
            $categories = $this->categoryService->getAllCategories();
        }

        $parentCategories = $this->categoryService->getParentCategories();

        return view('dashboard.categories.index', compact('categories', 'parentCategories'));
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return view('dashboard.categories.show', compact('category'));
    }

    public function create()
    {
        $parentCategories = $this->categoryService->getParentCategories();
        return view('dashboard.categories.form', [
            'category' => null,
            'parentCategories' => $parentCategories
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('dashboard.categories.index')
            ->with('success', __('categories.created_successfully'));
    }

    public function edit($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        $parentCategories = $this->categoryService->getParentCategories();
        
        return view('dashboard.categories.form', [
            'category' => $category,
            'parentCategories' => $parentCategories
        ]);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $this->categoryService->updateCategory($id, $request->validated());

        return redirect()->route('dashboard.categories.index')
            ->with('success', __('categories.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);

        return redirect()->route('dashboard.categories.index')
            ->with('success', __('categories.deleted_successfully'));
    }
}
