<?php

namespace App\Repositories;

use App\Interfaces\InstructorRepositoryInterface;
use App\Models\User;

class InstructorRepository implements InstructorRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    protected function baseQuery()
    {
        return $this->model->where('type', 'instructor');
    }

    public function getAll()
    {
        return $this->baseQuery()->with(['roles'])->orderBy('created_at', 'desc')->get();
    }

    public function getActive()
    {
        return $this->baseQuery()->active()->with(['roles'])->orderBy('name')->get();
    }

    public function getInactive()
    {
        return $this->baseQuery()->inactive()->with(['roles'])->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return $this->baseQuery()->with(['roles'])->findOrFail($id);
    }

    public function create(array $data)
    {
        // Ensure type is set to 'instructor'
        $data['type'] = 'instructor';
        $data['is_instructor'] = true;
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $instructor = $this->findById($id);
        $instructor->update($data);
        return $instructor->fresh();
    }

    public function delete(int $id)
    {
        $instructor = $this->findById($id);
        return $instructor->delete();
    }

    public function search(string $term)
    {
        return $this->baseQuery()->where(function($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('specialization', 'like', "%{$term}%");
        })->with(['roles'])->orderBy('created_at', 'desc')->get();
    }

    public function filterByStatus(string $status)
    {
        return $this->baseQuery()->where('status', $status)->with(['roles'])->orderBy('name')->get();
    }

    public function applyFilters(array $filters)
    {
        $query = $this->baseQuery();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%")
                  ->orWhere('specialization', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['roles'])->orderBy('created_at', 'desc')->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->baseQuery();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%")
                  ->orWhere('specialization', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['roles'])->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
