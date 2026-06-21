<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\CourseSummaryRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CourseSummaryService
{
    protected $courseSummaryRepository;

    public function __construct(CourseSummaryRepositoryInterface $courseSummaryRepository)
    {
        $this->courseSummaryRepository = $courseSummaryRepository;
    }

    public function getAllSummaries()
    {
        return $this->courseSummaryRepository->getAll();
    }

    public function getSummaryById(int $id)
    {
        return $this->courseSummaryRepository->findById($id);
    }

    public function getSummariesByCourse(int $courseId)
    {
        return $this->courseSummaryRepository->getByCourseId($courseId);
    }

    public function getPaginatedSummaries(int $perPage, array $filters = [])
    {
        return $this->courseSummaryRepository->paginate($perPage, $filters);
    }

    public function createSummary(array $data)
    {
        if (isset($data['pdf']) && $data['pdf']) {
            $data['pdf_url'] = $data['pdf']->store('course-summaries', 'public');
            unset($data['pdf']);
        }

        return $this->courseSummaryRepository->create($data);
    }

    public function updateSummary(int $id, array $data)
    {
        $summary = $this->courseSummaryRepository->findById($id);

        if (isset($data['pdf']) && $data['pdf']) {
            // Delete old PDF
            if ($summary->pdf_url && !str_starts_with($summary->pdf_url, 'http')) {
                Storage::disk('public')->delete($summary->pdf_url);
            }
            $data['pdf_url'] = $data['pdf']->store('course-summaries', 'public');
            unset($data['pdf']);
        }

        return $this->courseSummaryRepository->update($id, $data);
    }

    public function deleteSummary(int $id)
    {
        $summary = $this->courseSummaryRepository->findById($id);

        // Delete PDF file
        if ($summary->pdf_url && !str_starts_with($summary->pdf_url, 'http')) {
            Storage::disk('public')->delete($summary->pdf_url);
        }

        return $this->courseSummaryRepository->delete($id);
    }

    /**
     * Get summaries for a course (only if user is subscribed)
     */
    public function getCourseSummariesForUser(int $courseId, User $user, int $perPage = 15)
    {
        // Check if user is subscribed to the course
        $isSubscribed = $user->enrolledCourses()
            ->where('course_id', $courseId)
            ->where(function ($q) {
                $q->whereNull('course_student.expires_at')
                  ->orWhere('course_student.expires_at', '>', now());
            })
            ->whereNull('course_student.deleted_at')
            ->exists();

        if (!$isSubscribed) {
            return null;
        }

        return $this->courseSummaryRepository->paginate($perPage, ['course_id' => $courseId]);
    }
}
