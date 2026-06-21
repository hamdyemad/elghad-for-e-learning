<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\LiveStreamRepositoryInterface;
use App\Models\LiveStream;

class LiveStreamRepository implements LiveStreamRepositoryInterface
{
    protected $model;

    public function __construct(LiveStream $model)
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

    public function getActiveByCourseId(int $courseId)
    {
        return $this->model->where('course_id', $courseId)->where('is_active', true)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $liveStream = $this->findById($id);
        $liveStream->update($data);
        return $liveStream->fresh();
    }

    public function delete(int $id)
    {
        $liveStream = $this->findById($id);
        return $liveStream->delete();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with('course');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }
}
