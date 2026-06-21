<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreExamRequest;
use App\Http\Resources\ExamResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    use ApiResponseTrait;

    protected $examService;
    protected $courseService;

    public function __construct(ExamService $examService, CourseService $courseService)
    {
        $this->examService = $examService;
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'course_id']);
        $filters = array_filter($filters, fn($value) => $value !== '' && $value !== null);
        $perPage = (int) ($request->input('per_page') ?? 15);

        $exams = $this->examService->getPaginatedExams($perPage, $filters);

        return $this->successResponse([
            'exams' => ExamResource::collection($exams->items()),
            'pagination' => $this->formatPagination($exams),
        ], __('exams.retrieved_successfully'));
    }

    public function store(StoreExamRequest $request)
    {
        $exam = $this->examService->createExam($request->validated());

        return $this->createdResponse(
            new ExamResource($exam),
            __('exams.created_successfully')
        );
    }

    public function show($id)
    {
        $exam = $this->examService->getExamById((int) $id);

        return $this->successResponse(
            new ExamResource($exam),
            __('exams.single_retrieved_successfully')
        );
    }

    public function update(StoreExamRequest $request, $id)
    {
        $exam = $this->examService->updateExam((int) $id, $request->validated());

        return $this->successResponse(
            new ExamResource($exam),
            __('exams.updated_successfully')
        );
    }

    public function destroy($id)
    {
        $this->examService->deleteExam((int) $id);

        return $this->successResponse(null, __('exams.deleted_successfully'));
    }

    public function courses()
    {
        $courses = $this->courseService->getAllCourses()
            ->map(fn($course) => ['id' => $course->id, 'title' => $course->title]);

        return $this->successResponse($courses, __('exams.courses_retrieved_successfully'));
    }
}
