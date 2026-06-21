<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ExamRepositoryInterface;
use App\Models\User;

class ExamService
{
    protected $examRepository;

    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getAllExams()
    {
        return $this->examRepository->getAll();
    }

    public function getExamById(int $id)
    {
        return $this->examRepository->findById($id);
    }

    public function getExamsByCourse(int $courseId)
    {
        return $this->examRepository->getByCourseId($courseId);
    }

    public function getPaginatedExams(int $perPage, array $filters = [])
    {
        return $this->examRepository->paginate($perPage, $filters);
    }

    public function createExam(array $data)
    {
        $questions = $data['questions'] ?? [];
        unset($data['questions']);

        $exam = $this->examRepository->create($data);

        foreach ($questions as $order => $questionData) {
            $question = $exam->questions()->create([
                'question' => $questionData['question'],
                'order' => $order + 1,
            ]);

            foreach ($questionData['options'] as $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $optionData['is_correct'] ?? false,
                ]);
            }
        }

        return $exam->load('questions.options');
    }

    public function updateExam(int $id, array $data)
    {
        $exam = $this->examRepository->findById($id);

        $questions = $data['questions'] ?? null;
        unset($data['questions']);

        $exam->update($data);

        if ($questions !== null) {
            // Delete old questions (cascade will delete options)
            $exam->questions()->delete();

            foreach ($questions as $order => $questionData) {
                $question = $exam->questions()->create([
                    'question' => $questionData['question'],
                    'order' => $order + 1,
                ]);

                foreach ($questionData['options'] as $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                    ]);
                }
            }
        }

        return $exam->load('questions.options');
    }

    public function deleteExam(int $id)
    {
        return $this->examRepository->delete($id);
    }

    /**
     * Get exams for a course (subscribers only)
     */
    public function getCourseExamsForUser(int $courseId, User $user, int $perPage = 15)
    {
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

        return $this->examRepository->paginate($perPage, ['course_id' => $courseId]);
    }
}
