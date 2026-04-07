<?php

namespace App\Repositories;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Course;

class CourseRepository implements CourseRepositoryInterface
{
    protected $model;

    public function __construct(Course $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function getPublished()
    {
        return $this->model->where('status', 'published')->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function getDraft()
    {
        return $this->model->where('status', 'draft')->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id)
    {
        return $this->model->with(['category', 'instructor', 'lessons'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $course = $this->findById($id);
        $course->update($data);
        return $course->fresh();
    }

    public function delete(int $id)
    {
        $course = $this->findById($id);
        return $course->delete();
    }

    public function search(string $term)
    {
        return $this->model->where(function($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
        })->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function filterByStatus(string $status)
    {
        return $this->model->where('status', $status)->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function filterByCategory(int $categoryId)
    {
        return $this->model->where('category_id', $categoryId)->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function filterByInstructor(int $instructorId)
    {
        return $this->model->where('instructor_id', $instructorId)->with(['category', 'instructor'])->orderBy('created_at', 'desc')->get();
    }

    public function applyFilters(array $filters)
    {
        $query = $this->model->with(['category', 'instructor']);

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

        if (isset($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        if (isset($filters['is_free'])) {
            $query->where('is_free', $filters['is_free']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with(['category', 'instructor']);

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

        if (isset($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
