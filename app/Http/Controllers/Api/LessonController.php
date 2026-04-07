<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LessonService;
use App\Services\SubscriptionService;
use App\Http\Resources\LessonResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use ApiResponseTrait;

    protected $lessonService;
    protected $subscriptionService;

    public function __construct(LessonService $lessonService, SubscriptionService $subscriptionService)
    {
        $this->lessonService = $lessonService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of lessons
     * GET /api/lessons
     * Query params:
     *   - course_id: filter by course
     *   - is_free: boolean (filter free lessons)
     *   - search: search term
     * Requires: auth:sanctum (optional)
     */
    public function index(Request $request)
    {
        $filters = $request->only(['course_id', 'is_free', 'search']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Convert is_free to boolean if present
        if (isset($filters['is_free'])) {
            $filters['is_free'] = filter_var($filters['is_free'], FILTER_VALIDATE_BOOLEAN);
        }

        $perPage = $request->input('per_page', 15);
        $paginatedLessons = $this->lessonService->getLessons($filters, true, $perPage);

        return $this->successResponse([
            'lessons' => LessonResource::collection($paginatedLessons->items()),
            'pagination' => $this->formatPagination($paginatedLessons)
        ], __('lessons.retrieved_successfully'));
    }

    /**
     * Display the specified lesson
     * GET /api/lessons/{id}
     * Requires: auth:sanctum
     * Access: Only users with access to the course (or lesson is free) or admins
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $lesson = $this->lessonService->getLesson((int) $id);

        // Check if user is admin
        if ($user && $user->hasRole('admin')) {
            return $this->successResponse(
                new LessonResource($lesson),
                __('lessons.single_retrieved_successfully')
            );
        }

        // For non-admin users, check enrollment
        if (!$user) {
            return $this->errorResponse('You must be authenticated to view this lesson', [], 401);
        }

        // Check if lesson is free - allow access if free
        if ($lesson->is_free) {
            return $this->successResponse(
                new LessonResource($lesson),
                __('lessons.single_retrieved_successfully')
            );
        }

        // Check if user has access to the course that this lesson belongs to
        $accessibleCourses = $this->subscriptionService->getAccessibleCourses($user);
        $hasAccess = $accessibleCourses->contains('id', $lesson->course_id);

        if (!$hasAccess) {
            return $this->errorResponse('You are not enrolled in the course for this lesson', [], 403);
        }

        return $this->successResponse(
            new LessonResource($lesson),
            __('lessons.single_retrieved_successfully')
        );
    }

    /**
     * Get free lessons only
     * GET /api/lessons/free
     * Requires: auth:sanctum (optional)
     */
    public function free(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $lessons = $this->lessonService->getLessons(['is_free' => true], true, $perPage);

        return $this->successResponse([
            'lessons' => LessonResource::collection($lessons->items()),
            'pagination' => $this->formatPagination($lessons)
        ], __('lessons.free_retrieved_successfully'));
    }

    /**
     * Get lessons by course
     * GET /api/lessons/course/{courseId}
     * Requires: auth:sanctum
     * Access: Only users with access to the course or admins
     */
    public function byCourse($courseId, Request $request)
    {
        $user = $request->user();

        // Get course to check if it's free
        try {
            $course = $this->courseService->getCourseById((int) $courseId);
        } catch (\Exception $e) {
            return $this->errorResponse('Course not found', [], 404);
        }

        // Check if user is admin
        if ($user && $user->hasRole('admin')) {
            // Admin can see all lessons
        } else {
            // For non-admin users, check authentication and course access
            if (!$user) {
                return $this->errorResponse('You must be authenticated to view course lessons', [], 401);
            }

            // If course is free, allow access
            if ($course->is_free) {
                // Continue to fetch lessons
            } else {
                // Check if user has access to this course through subscription
                $accessibleCourses = $this->subscriptionService->getAccessibleCourses($user);
                $hasAccess = $accessibleCourses->contains('id', (int) $courseId);

                if (!$hasAccess) {
                    return $this->errorResponse('You are not enrolled in this course', [], 403);
                }
            }
        }

        $filters = ['course_id' => (int) $courseId];
        $perPage = $request->input('per_page', 15);
        $lessons = $this->lessonService->getLessons($filters, true, $perPage);

        return $this->successResponse([
            'lessons' => LessonResource::collection($lessons->items()),
            'pagination' => $this->formatPagination($lessons)
        ], __('lessons.by_course_retrieved_successfully'));
    }
}
