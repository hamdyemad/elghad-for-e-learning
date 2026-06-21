<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LiveStreamRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LiveStreamService
{
    protected $liveStreamRepository;
    protected $notificationService;

    public function __construct(
        LiveStreamRepositoryInterface $liveStreamRepository,
        NotificationService $notificationService
    ) {
        $this->liveStreamRepository = $liveStreamRepository;
        $this->notificationService = $notificationService;
    }

    public function getAllLiveStreams()
    {
        return $this->liveStreamRepository->getAll();
    }

    public function getLiveStreamById(int $id)
    {
        return $this->liveStreamRepository->findById($id);
    }

    public function getLiveStreamsByCourse(int $courseId)
    {
        return $this->liveStreamRepository->getByCourseId($courseId);
    }

    public function getActiveLiveStream(int $courseId)
    {
        return $this->liveStreamRepository->getActiveByCourseId($courseId);
    }

    public function getPaginatedLiveStreams(int $perPage, array $filters = [])
    {
        return $this->liveStreamRepository->paginate($perPage, $filters);
    }

    public function createLiveStream(array $data)
    {
        $liveStream = $this->liveStreamRepository->create($data);

        if ($data['is_active'] ?? false) {
            $this->sendActivationNotification($liveStream);
        }

        return $liveStream;
    }

    public function updateLiveStream(int $id, array $data)
    {
        $oldStream = $this->liveStreamRepository->findById($id);
        $wasActive = $oldStream->is_active;

        $liveStream = $this->liveStreamRepository->update($id, $data);

        // Send notification if just activated
        if (!$wasActive && $liveStream->is_active) {
            $this->sendActivationNotification($liveStream);
        }

        return $liveStream;
    }

    public function deleteLiveStream(int $id)
    {
        return $this->liveStreamRepository->delete($id);
    }

    /**
     * Send notification to course students when live stream is activated
     */
    protected function sendActivationNotification($liveStream): void
    {
        try {
            $course = $liveStream->course;
            $students = User::where('type', 'student')
                ->where('status', 'active')
                ->whereHas('enrolledCourses', function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                      ->where(function ($q) {
                          $q->whereNull('course_student.expires_at')
                            ->orWhere('course_student.expires_at', '>', now());
                      })
                      ->whereNull('course_student.deleted_at');
                })
                ->get();

            if ($students->isEmpty()) {
                return;
            }

            $senderId = auth()->id() ?? 0;

            foreach ($students as $student) {
                $this->notificationService->sendNotification([
                    'recipient_type' => 'single_student',
                    'recipient_id' => $student->id,
                    'title' => 'بث مباشر: ' . $liveStream->title,
                    'body' => "بدأ البث المباشر في كورس {$course->title}. url: {$liveStream->url}",
                ], $senderId);
            }

            Log::info("Live stream notification sent for stream {$liveStream->id} to " . $students->count() . " students");
        } catch (\Exception $e) {
            Log::error('Failed to send live stream notification: ' . $e->getMessage());
        }
    }
}
