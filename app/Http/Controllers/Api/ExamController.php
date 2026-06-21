<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use App\Http\Resources\ExamResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    use ApiResponseTrait;

    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Get exams for a course (subscribers only)
     * GET /api/courses/{id}/exams
     */
    public function index(Request $request, $courseId)
    {
        $perPage = (int) ($request->input('per_page') ?? 15);
        $exams = $this->examService->getCourseExamsForUser(
            (int) $courseId,
            $request->user(),
            $perPage
        );

        if ($exams === null) {
            return $this->errorResponse('You are not subscribed to this course', [], 403);
        }

        return $this->successResponse([
            'exams' => ExamResource::collection($exams->items()),
            'pagination' => $this->formatPagination($exams),
        ], __('exams.retrieved_successfully'));
    }

    /**
     * Get exam with questions + options + correct answer
     * GET /api/exams/{id}
     */
    public function show($id)
    {
        $exam = $this->examService->getExamById((int) $id);

        return $this->successResponse(
            new ExamResource($exam),
            __('exams.single_retrieved_successfully')
        );
    }
}
