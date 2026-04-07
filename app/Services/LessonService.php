<?php

namespace App\Services;

use App\Interfaces\LessonRepositoryInterface;
use Illuminate\Http\UploadedFile;

class LessonService
{
    protected $lessonRepository;

    public function __construct(LessonRepositoryInterface $lessonRepository)
    {
        $this->lessonRepository = $lessonRepository;
    }

    public function getLesson(int $id)
    {
        return $this->lessonRepository->findById($id);
    }

    public function getAllLessons()
    {
        return $this->lessonRepository->getAll();
    }

    public function getLessonsByCourse(int $courseId)
    {
        return $this->lessonRepository->getByCourse($courseId);
    }

    public function getFreeLessons()
    {
        return $this->lessonRepository->getFreeLessons();
    }

    public function getLessons(array $filters = [], bool $paginate = false, int $perPage = 15)
    {
        // If pagination requested, use paginate
        if ($paginate) {
            return $this->lessonRepository->paginate($perPage, $filters);
        }

        // Use applyFilters which handles all filters including search, course_id, is_free
        return $this->lessonRepository->applyFilters($filters);
    }

    public function createLesson(array $data)
    {
        // Validate that either outsource_link or file_pdf or both are provided
        if (empty($data['outsource_link']) && empty($data['file_pdf'])) {
            throw new \InvalidArgumentException('يجب توفير رابط خارجي أو ملف فيديو');
        }

        // Validate outsource_type if outsource_link is provided
        if (!empty($data['outsource_link']) && empty($data['outsource_type'])) {
            throw new \InvalidArgumentException('يجب تحديد نوع الرابط الخارجي (vimeo, firebase, vdocipher)');
        }

        // If outsource_type is provided, ensure it's valid
        if (!empty($data['outsource_type'])) {
            $validTypes = ['vimeo', 'firebase', 'vdocipher', 'youtube', 'other'];
            if (!in_array($data['outsource_type'], $validTypes)) {
                throw new \InvalidArgumentException('نوع الرابط الخارجي غير صالح');
            }
        }

        return $this->lessonRepository->create($data);
    }

    public function updateLesson(int $id, array $data)
    {
        // If outsource_link or outsource_type is being updated, validate
        if (isset($data['outsource_link']) && empty($data['outsource_link']) && empty($data['file_pdf'])) {
            throw new \InvalidArgumentException('يجب توفير رابط خارجي أو ملف فيديو');
        }

        if (isset($data['outsource_link']) && !empty($data['outsource_link']) && empty($data['outsource_type'])) {
            throw new \InvalidArgumentException('يجب تحديد نوع الرابط الخارجي');
        }

        return $this->lessonRepository->update($id, $data);
    }

    public function deleteLesson(int $id)
    {
        return $this->lessonRepository->delete($id);
    }

    public function reorderLessons(int $courseId, array $lessonOrders): bool
    {
        // Verify that all lesson IDs belong to the specified course
        $lessonIds = array_keys($lessonOrders);
        if (!empty($lessonIds)) {
            $lessons = $this->lessonRepository->getByIds($lessonIds);
            foreach ($lessons as $lesson) {
                if ($lesson->course_id !== $courseId) {
                    throw new \InvalidArgumentException('جميع الدروس يجب أن تنتمي لنفس الدورة');
                }
            }
        }

        return $this->lessonRepository->reorder($lessonOrders);
    }

    public function searchLessons(string $term, int $courseId = null)
    {
        return $this->lessonRepository->search($term, $courseId);
    }

    public function getPaginatedLessons(int $perPage, array $filters = [])
    {
        return $this->lessonRepository->paginate($perPage, $filters);
    }
}
