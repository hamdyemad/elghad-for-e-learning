<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseSummaryService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreCourseSummaryRequest;
use App\Http\Resources\CourseSummaryResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CourseSummaryController extends Controller
{
    use ApiResponseTrait;

    protected $courseSummaryService;
    protected $courseService;

    public function __construct(CourseSummaryService $courseSummaryService, CourseService $courseService)
    {
        $this->courseSummaryService = $courseSummaryService;
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'course_id']);
        $filters = array_filter($filters, fn($value) => $value !== '' && $value !== null);
        $perPage = (int) ($request->input('per_page') ?? 15);

        $summaries = $this->courseSummaryService->getPaginatedSummaries($perPage, $filters);

        return $this->successResponse([
            'summaries' => CourseSummaryResource::collection($summaries->items()),
            'pagination' => $this->formatPagination($summaries),
        ], __('course_summaries.retrieved_successfully'));
    }

    public function store(StoreCourseSummaryRequest $request)
    {
        $summary = $this->courseSummaryService->createSummary($request->validated());

        return $this->createdResponse(
            new CourseSummaryResource($summary),
            __('course_summaries.created_successfully')
        );
    }

    public function show($id)
    {
        $summary = $this->courseSummaryService->getSummaryById((int) $id);

        return $this->successResponse(
            new CourseSummaryResource($summary),
            __('course_summaries.single_retrieved_successfully')
        );
    }

    public function update(StoreCourseSummaryRequest $request, $id)
    {
        $summary = $this->courseSummaryService->updateSummary((int) $id, $request->validated());

        return $this->successResponse(
            new CourseSummaryResource($summary),
            __('course_summaries.updated_successfully')
        );
    }

    public function destroy($id)
    {
        $this->courseSummaryService->deleteSummary((int) $id);

        return $this->successResponse(null, __('course_summaries.deleted_successfully'));
    }

    public function courses()
    {
        $courses = $this->courseService->getAllCourses()
            ->map(fn($course) => ['id' => $course->id, 'title' => $course->title]);

        return $this->successResponse($courses, __('course_summaries.courses_retrieved_successfully'));
    }
}
