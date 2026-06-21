<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CourseSummaryRepositoryInterface;
use App\Models\CourseSummary;

class CourseSummaryRepository implements CourseSummaryRepositoryInterface
{
    protected $model;

    public function __construct(CourseSummary $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('course')->latest()->get();
    }

    public function findById(int $id)
    {
        return $this->model->with('course')->findOrFail($id);
    }

    public function getByCourseId(int $courseId)
    {
        return $this->model->where('course_id', $courseId)->latest()->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $summary = $this->findById($id);
        $summary->update($data);
        return $summary->fresh();
    }

    public function delete(int $id)
    {
        $summary = $this->findById($id);
        return $summary->delete();
    }

    public function search(string $term)
    {
        return $this->model->search($term)->with('course')->latest()->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with('course');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        return $query->latest()->paginate($perPage);
    }
}
