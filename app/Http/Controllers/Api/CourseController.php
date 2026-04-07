<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Services\SubscriptionService;
use App\Http\Resources\CourseResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponseTrait;

    protected $courseService;
    protected $subscriptionService;

    public function __construct(CourseService $courseService, SubscriptionService $subscriptionService)
    {
        $this->courseService = $courseService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of courses
     * GET /api/courses
     * Query params:
     *   - status: all|published (default: published for non-admin, all for admin)
     *   - category_id: filter by category
     *   - instructor_id: filter by instructor
     *   - search: search term
     * Requires: auth:sanctum (optional - can be public or authenticated)
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'instructor_id', 'status', 'is_free']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Convert is_free to boolean if present
        if (isset($filters['is_free'])) {
            $filters['is_free'] = filter_var($filters['is_free'], FILTER_VALIDATE_BOOLEAN);
        }

        // For public API, default to published only unless explicitly asked for all
        if (!$request->has('status')) {
            $filters['status'] = 'published';
        }

        $perPage = $request->input('per_page', 15);
        $paginatedCourses = $this->courseService->getCourses($filters, true, $perPage);

        return $this->successResponse([
            'courses' => CourseResource::collection($paginatedCourses->items()),
            'pagination' => $this->formatPagination($paginatedCourses)
        ], __('courses.retrieved_successfully'));
    }

    /**
     * Display the specified course
     * GET /api/courses/{id}
     * Requires: auth:sanctum
     * Access: Only users enrolled in the course (directly or via package) or admins
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        try {
            $course = $this->courseService->getCourseById((int) $id);
        } catch (\Exception $e) {
            return $this->errorResponse('Course not found', [], 404);
        }

        // Check if user is admin (has role admin)
        if ($user && $user->hasRole('admin')) {
            return $this->successResponse(
                new CourseResource($course),
                __('courses.single_retrieved_successfully')
            );
        }

        // For non-admin users, check enrollment
        if (!$user) {
            return $this->errorResponse('You must be authenticated to view this course', [], 401);
        }

        // Check if course is free - allow access if free
        if ($course->is_free) {
            return $this->successResponse(
                new CourseResource($course),
                __('courses.single_retrieved_successfully')
            );
        }

        // Check if user has access to the course through subscription
        $accessibleCourses = $this->subscriptionService->getAccessibleCourses($user);
        $hasAccess = $accessibleCourses->contains('id', $course->id);

        if (!$hasAccess) {
            return $this->errorResponse('You are not enrolled in this course', [], 403);
        }

        return $this->successResponse(
            new CourseResource($course),
            __('courses.single_retrieved_successfully')
        );
    }

    /**
     * Get free courses only
     * GET /api/courses/free
     * Requires: auth:sanctum (optional)
     */
    public function free(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $courses = $this->courseService->getCourses([
            'is_free' => true,
            'status' => 'published'
        ], true, $perPage);

        return $this->successResponse([
            'courses' => CourseResource::collection($courses->items()),
            'pagination' => $this->formatPagination($courses)
        ], __('courses.free_retrieved_successfully'));
    }
}
