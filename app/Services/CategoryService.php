<?php

namespace App\Services;

use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAll();
    }

    public function getActiveCategories()
    {
        return $this->categoryRepository->getAllActive();
    }

    public function getParentCategories()
    {
        return $this->categoryRepository->getParents();
    }

    public function getSubCategories($parentId)
    {
        return $this->categoryRepository->getChildren($parentId);
    }

    public function getCategories(array $filters = [], bool $paginate = false, int $perPage = 15)
    {
        // If tree flag is set, get hierarchical tree
        if (isset($filters['tree']) && $filters['tree']) {
            unset($filters['tree']);
            return $this->categoryRepository->getWithChildren($perPage, $filters);
        }

        // Otherwise get flat list with filters
        if ($paginate) {
            return $this->categoryRepository->paginate($perPage, $filters);
        }

        return $this->categoryRepository->applyFilters($filters);
    }

    public function getCategoryTree(int $perPage = 15, array $filters = [])
    {
        // Set tree flag to trigger hierarchical retrieval
        $filters['tree'] = true;
        return $this->getCategories($filters, true, $perPage);
    }

    public function getCategoryById(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function getCategoryBySlug(string $slug)
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function createCategory(array $data)
    {
        // Set default order if not provided
        if (!isset($data['order']) || $data['order'] === null) {
            $data['order'] = $this->getNextOrder($data['parent_id'] ?? null);
        }

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->categoryRepository->create($data);
    }

    /**
     * Get the next order value for a category (optionally within a parent)
     */
    protected function getNextOrder(?int $parentId): int
    {
        $query = $this->categoryRepository->getModel()->where('parent_id', $parentId);
        $maxOrder = $query->max('order');
        return ($maxOrder ?? 0) + 1;
    }

    public function updateCategory(int $id, array $data)
    {
        $category = $this->categoryRepository->findById($id);

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        // If order is provided but empty/null, remove it to keep existing order
        if (array_key_exists('order', $data) && ($data['order'] === null || $data['order'] === '')) {
            unset($data['order']);
        }

        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id)
    {
        $category = $this->categoryRepository->findById($id);

        // Delete children first (cascade)
        if ($category->children()->count() > 0) {
            foreach ($category->children as $child) {
                // Recursively delete child
                $this->deleteCategory($child->id);
            }
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return $this->categoryRepository->delete($id);
    }

    public function searchCategories(string $term)
    {
        return $this->categoryRepository->search($term);
    }

    public function reorderCategories(array $orderData)
    {
        return $this->categoryRepository->reorder($orderData);
    }

    public function filterCategories(array $filters)
    {
        return $this->categoryRepository->applyFilters($filters);
    }

    public function getPaginatedCategories(int $perPage, array $filters = [])
    {
        return $this->categoryRepository->paginate($perPage, $filters);
    }

    protected function uploadImage($image)
    {
        return $image->store('categories', 'public');
    }
}
