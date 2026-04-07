<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponseTrait;

    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of courses (with filters)
     * GET /dashboard/courses
     * Requires: admin role
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category_id', 'instructor_id']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedCourses = $this->courseService->getPaginatedCourses($perPage, $filters);

        return $this->successResponse([
            'courses' => CourseResource::collection($paginatedCourses->items()),
            'pagination' => $this->formatPagination($paginatedCourses)
        ], __('courses.retrieved_successfully'));
    }

    /**
     * Store a newly created course
     * POST /dashboard/courses
     * Requires: admin role
     */
    public function store(StoreCourseRequest $request)
    {
        $course = $this->courseService->createCourse($request->validated());

        return $this->createdResponse(
            new CourseResource($course),
            __('courses.created_successfully')
        );
    }

    /**
     * Display the specified course
     * GET /dashboard/courses/{id}
     * Requires: admin role
     */
    public function show($id)
    {
        $course = $this->courseService->getCourseById((int) $id);

        return $this->successResponse(
            new CourseResource($course),
            __('courses.single_retrieved_successfully')
        );
    }

    /**
     * Update the specified course
     * PUT /dashboard/courses/{id}
     * Requires: admin role
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        $course = $this->courseService->updateCourse((int) $id, $request->validated());

        return $this->successResponse(
            new CourseResource($course),
            __('courses.updated_successfully')
        );
    }

    /**
     * Remove the specified course
     * DELETE /dashboard/courses/{id}
     * Requires: admin role
     */
    public function destroy($id)
    {
        $this->courseService->deleteCourse((int) $id);

        return $this->successResponse(
            null,
            __('courses.deleted_successfully')
        );
    }

    /**
     * Get published courses only
     * GET /dashboard/courses/published
     * Requires: admin role
     */
    public function published()
    {
        $courses = $this->courseService->getPublishedCourses();

        return $this->successResponse(
            CourseResource::collection($courses),
            __('courses.published_retrieved_successfully')
        );
    }

    /**
     * Get draft courses only
     * GET /dashboard/courses/draft
     * Requires: admin role
     */
    public function draft()
    {
        $courses = $this->courseService->getDraftCourses();

        return $this->successResponse(
            CourseResource::collection($courses),
            __('courses.draft_retrieved_successfully')
        );
    }
}
