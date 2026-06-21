<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ExamRepositoryInterface;
use App\Models\Exam;

class ExamRepository implements ExamRepositoryInterface
{
    protected $model;

    public function __construct(Exam $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('course')->latest()->get();
    }

    public function findById(int $id)
    {
        return $this->model->with(['course', 'questions.options'])->findOrFail($id);
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
        $exam = $this->findById($id);
        $exam->update($data);
        return $exam->fresh();
    }

    public function delete(int $id)
    {
        $exam = $this->findById($id);
        return $exam->delete();
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

        return $query->latest()->paginate($perPage);
    }
}
