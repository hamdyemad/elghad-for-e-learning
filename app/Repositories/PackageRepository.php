<?php

namespace App\Repositories;

use App\Interfaces\PackageRepositoryInterface;
use App\Models\Package;

class PackageRepository implements PackageRepositoryInterface
{
    protected $model;

    public function __construct(Package $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['category', 'courses'])->orderBy('created_at', 'desc')->get();
    }

    public function getPublished()
    {
        return $this->model->where('status', 'published')->with(['category', 'courses'])->orderBy('created_at', 'desc')->get();
    }

    public function getDraft()
    {
        return $this->model->where('status', 'draft')->with(['category', 'courses'])->orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id)
    {
        return $this->model->with(['category', 'courses'])->findOrFail($id);
    }

    public function create(array $data)
    {
        // Extract courses if present
        $courseIds = $data['course_ids'] ?? [];
        unset($data['course_ids']);

        // Set defaults
        if (!isset($data['price']) || $data['price'] === null) {
            $data['price'] = 0;
        }
        if (!isset($data['status']) || $data['status'] === null) {
            $data['status'] = 'draft';
        }

        $package = $this->model->create($data);

        // Attach courses
        if (!empty($courseIds)) {
            $package->courses()->attach($courseIds);
        }

        return $package->fresh(['category', 'courses']);
    }

    public function update(int $id, array $data)
    {
        $package = $this->findById($id);

        // Extract courses if present
        $courseIds = $data['course_ids'] ?? null;
        unset($data['course_ids']);

        $package->update($data);

        // Sync courses if provided
        if ($courseIds !== null) {
            $package->courses()->sync($courseIds);
        }

        return $package->fresh(['category', 'courses']);
    }

    public function delete(int $id)
    {
        $package = $this->findById($id);
        // Detach all courses first
        $package->courses()->detach();
        return $package->delete();
    }

    public function search(string $term)
    {
        return $this->model->where(function($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
        })->with(['category', 'courses'])->orderBy('created_at', 'desc')->get();
    }

    public function filterByStatus(string $status)
    {
        return $this->model->where('status', $status)->with(['category', 'courses'])->orderBy('created_at', 'desc')->get();
    }

    public function filterByCategory(int $categoryId)
    {
        return $this->model->where('category_id', $categoryId)->with(['category', 'courses'])->orderBy('created_at', 'desc')->get();
    }

    public function applyFilters(array $filters)
    {
        $query = $this->model->with(['category', 'courses']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with(['category', 'courses']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function attachCourse(int $packageId, int $courseId)
    {
        $package = $this->findById($packageId);
        $package->courses()->attach($courseId);
        return $package->fresh(['courses']);
    }

    public function detachCourse(int $packageId, int $courseId)
    {
        $package = $this->findById($packageId);
        $package->courses()->detach($courseId);
        return $package->fresh(['courses']);
    }

    public function syncCourses(int $packageId, array $courseIds)
    {
        $package = $this->findById($packageId);
        $package->courses()->sync($courseIds);
        return $package->fresh(['courses']);
    }
}
