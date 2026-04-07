<?php

namespace App\Repositories;

use App\Interfaces\StudentRepositoryInterface;
use App\Models\User;

class StudentRepository implements StudentRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    protected function baseQuery()
    {
        return $this->model->where('type', 'student');
    }

    public function getAll()
    {
        return $this->baseQuery()->orderBy('created_at', 'desc')->get();
    }

    public function getActive()
    {
        return $this->baseQuery()->active()->orderBy('name')->get();
    }

    public function getInactive()
    {
        return $this->baseQuery()->inactive()->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return $this->baseQuery()->findOrFail($id);
    }

    public function create(array $data)
    {
        // Ensure type is set to 'student'
        $data['type'] = 'student';
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $student = $this->findById($id);
        $student->update($data);
        return $student->fresh();
    }

    public function delete(int $id)
    {
        $student = $this->findById($id);
        return $student->delete();
    }

    public function search(string $term)
    {
        return $this->baseQuery()->where(function($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
        })->orderBy('created_at', 'desc')->get();
    }

    public function filterByStatus(string $status)
    {
        return $this->baseQuery()->where('status', $status)->orderBy('name')->get();
    }

    public function applyFilters(array $filters)
    {
        $query = $this->baseQuery();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->baseQuery();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
