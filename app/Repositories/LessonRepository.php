<?php

namespace App\Repositories;

use App\Interfaces\LessonRepositoryInterface;
use App\Models\Lesson;

class LessonRepository implements LessonRepositoryInterface
{
    protected $model;

    public function __construct(Lesson $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['course'])->orderBy('course_id')->orderBy('order')->get();
    }

    public function getByCourse(int $courseId)
    {
        return $this->model->where('course_id', $courseId)->orderBy('order')->with(['course'])->get();
    }

    public function getPublishedByCourse(int $courseId)
    {
        // Assuming lessons don't have a status field yet, but courses might have published packages
        // For now, return all lessons for the course
        return $this->getByCourse($courseId);
    }

    public function getFreeLessons()
    {
        return $this->model->where('is_free', true)->with(['course'])->orderBy('course_id')->orderBy('order')->get();
    }

    public function findById(int $id)
    {
        return $this->model->with(['course'])->findOrFail($id);
    }

    public function getByIds(array $ids)
    {
        return $this->model->whereIn('id', $ids)->with(['course'])->get();
    }

    public function create(array $data)
    {
        // Handle file uploads if present
        if (isset($data['file_pdf']) && $data['file_pdf']) {
            $data['file_pdf'] = $this->uploadFile($data['file_pdf']);
        }

        // Set order if not provided - calculate max order for this course + 1
        if (!isset($data['order'])) {
            $maxOrder = $this->model->where('course_id', $data['course_id'])->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $lesson = $this->model->create($data);

        return $lesson->fresh(['course']);
    }

    public function update(int $id, array $data)
    {
        $lesson = $this->findById($id);

        // Handle file update
        if (isset($data['file_pdf']) && $data['file_pdf']) {
            // Delete old file if exists
            if ($lesson->file_pdf) {
                \Storage::disk('public')->delete($lesson->file_pdf);
            }
            $data['file_pdf'] = $this->uploadFile($data['file_pdf']);
        }

        $lesson->update($data);

        return $lesson->fresh(['course']);
    }

    public function delete(int $id)
    {
        $lesson = $this->findById($id);

        // Delete associated PDF file if exists
        if ($lesson->file_pdf) {
            \Storage::disk('public')->delete($lesson->file_pdf);
        }

        return $lesson->delete();
    }

    public function reorder(array $lessonOrders): bool
    {
        try {
            foreach ($lessonOrders as $lessonId => $newOrder) {
                $this->model->where('id', $lessonId)->update(['order' => $newOrder]);
            }
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function search(string $term, int $courseId = null)
    {
        $query = $this->model->with(['course']);

        $query->where(function($q) use ($term) {
            $q->where('topic', 'like', "%{$term}%")
              ->orWhere('title', 'like', "%{$term}%");
        });

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->orderBy('course_id')->orderBy('order')->get();
    }

    public function applyFilters(array $filters)
    {
        $query = $this->model->with(['course']);

        // Apply Course Filter
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        // Apply is_free filter
        if (isset($filters['is_free'])) {
            $query->where('is_free', $filters['is_free']);
        }

        // Apply Search Filter (wrapped in closure for correct scoping)
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('topic', 'like', "%{$filters['search']}%")
                  ->orWhere('title', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('course_id')->orderBy('order')->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with(['course']);

        // Apply Course Filter
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        // Apply is_free filter
        if (isset($filters['is_free'])) {
            $query->where('is_free', $filters['is_free']);
        }

        // Apply Search Filter (wrapped in closure for correct scoping)
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('topic', 'like', "%{$filters['search']}%")
                  ->orWhere('title', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('course_id')->orderBy('order')->paginate($perPage);
    }

    protected function uploadFile($file)
    {
        // If it's an UploadedFile object, store it normally
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            return $file->store('lessons', 'public');
        }

        // If it's already a path (string), return it as is
        return $file;
    }
}
