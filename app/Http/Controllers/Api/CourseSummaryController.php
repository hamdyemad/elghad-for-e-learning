<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseSummaryService;
use App\Http\Resources\CourseSummaryResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CourseSummaryController extends Controller
{
    use ApiResponseTrait;

    protected $courseSummaryService;

    public function __construct(CourseSummaryService $courseSummaryService)
    {
        $this->courseSummaryService = $courseSummaryService;
    }

    /**
     * Get summaries for a course (subscribers only)
     * GET /api/courses/{id}/summaries
     * Requires: auth:sanctum
     */
    public function index(Request $request, $courseId)
    {
        $perPage = (int) ($request->input('per_page') ?? 15);
        $summaries = $this->courseSummaryService->getCourseSummariesForUser(
            (int) $courseId,
            $request->user(),
            $perPage
        );

        if ($summaries === null) {
            return $this->errorResponse('You are not subscribed to this course', [], 403);
        }

        return $this->successResponse([
            'summaries' => CourseSummaryResource::collection($summaries->items()),
            'pagination' => $this->formatPagination($summaries),
        ], __('course_summaries.retrieved_successfully'));
    }
}
