<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getAll()
    {
        return $this->model->with('parent', 'children')->ordered()->get();
    }

    public function getAllActive()
    {
        return $this->model->active()->with('parent', 'children')->ordered()->get();
    }

    public function getParents()
    {
        return $this->model->parents()->active()->with('children')->ordered()->get();
    }

    public function getChildren($parentId)
    {
        return $this->model->byParent($parentId)->active()->with('parent', 'children')->ordered()->get();
    }

    public function findById(int $id)
    {
        return $this->model->with('parent', 'children')->findOrFail($id);
    }

    public function findBySlug(string $slug)
    {
        return $this->model->with('parent', 'children')->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id)
    {
        $category = $this->findById($id);
        return $category->delete();
    }

    public function search(string $term)
    {
        return $this->model->search($term)->with('parent')->ordered()->get();
    }

    public function existsBySlug(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getWithChildren(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->query();

        // Apply search filter to parent categories only (not to children)
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Get only parent categories (where parent_id is null)
        $query->whereNull('parent_id');

        return $query->ordered()->with([
            'children' => function ($childQuery) use ($filters) {
                // Only apply status filter to children (not search)
                // This preserves tree structure when searching
                if (isset($filters['status'])) {
                    $childQuery->where('status', $filters['status']);
                }
                $childQuery->ordered()->with('children');
            }
        ])->paginate($perPage);
    }

    public function reorder(array $orderData)
    {
        foreach ($orderData as $item) {
            $this->model->where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return true;
    }

    public function filterByStatus(string $status)
    {
        return $this->model->where('status', $status)->with('parent', 'children')->ordered()->get();
    }

    public function filterByParentId($parentId)
    {
        if ($parentId === '0' || $parentId === 0) {
            return $this->model->whereNull('parent_id')->with('children')->ordered()->get();
        }
        return $this->model->where('parent_id', $parentId)->with('parent', 'children')->ordered()->get();
    }

    public function applyFilters(array $filters)
    {
        $query = $this->model->with('parent', 'children');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] == '0' || $filters['parent_id'] == 0) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        if (isset($filters['is_parent'])) {
            if ($filters['is_parent']) {
                $query->whereNull('parent_id');
            } else {
                $query->whereNotNull('parent_id');
            }
        }

        return $query->ordered()->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with('parent', 'children');

        // Apply filters if provided
        if (!empty($filters)) {
            if (!empty($filters['search'])) {
                $query->search($filters['search']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['parent_id'])) {
                if ($filters['parent_id'] == '0' || $filters['parent_id'] == 0) {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $filters['parent_id']);
                }
            }

            if (isset($filters['is_parent'])) {
                if ($filters['is_parent']) {
                    $query->whereNull('parent_id');
                } else {
                    $query->whereNotNull('parent_id');
                }
            }
        }

        return $query->ordered()->paginate($perPage);
    }
}
