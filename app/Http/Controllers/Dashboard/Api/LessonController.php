<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\LessonService;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use ApiResponseTrait;

    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    /**
     * Display a listing of lessons (with optional course filter)
     * GET /dashboard/lessons
     * Requires: admin role
     */
    public function index(Request $request)
    {
        $courseId = $request->input('course_id');

        if ($courseId) {
            $lessons = $this->lessonService->getLessonsByCourse((int) $courseId);
        } else {
            $lessons = $this->lessonService->getAllLessons();
        }

        return $this->successResponse(
            LessonResource::collection($lessons),
            __('lessons.retrieved_successfully')
        );
    }

    /**
     * Get lessons for a specific course
     * GET /dashboard/lessons/course/{courseId}
     * Requires: admin role
     */
    public function byCourse(int $courseId)
    {
        $lessons = $this->lessonService->getLessonsByCourse($courseId);

        return $this->successResponse(
            LessonResource::collection($lessons),
            __('lessons.retrieved_successfully')
        );
    }

    /**
     * Store a newly created lesson
     * POST /dashboard/lessons
     * Requires: admin role
     */
    public function store(StoreLessonRequest $request)
    {
        $lesson = $this->lessonService->createLesson($request->validated());

        return $this->createdResponse(
            new LessonResource($lesson),
            __('lessons.created_successfully')
        );
    }

    /**
     * Display the specified lesson
     * GET /dashboard/lessons/{id}
     * Requires: admin role
     */
    public function show($id)
    {
        $lesson = $this->lessonService->getLesson((int) $id);

        return $this->successResponse(
            new LessonResource($lesson),
            __('lessons.single_retrieved_successfully')
        );
    }

    /**
     * Update the specified lesson
     * PUT /dashboard/lessons/{id}
     * Requires: admin role
     */
    public function update(UpdateLessonRequest $request, $id)
    {
        $lesson = $this->lessonService->updateLesson((int) $id, $request->validated());

        return $this->successResponse(
            new LessonResource($lesson),
            __('lessons.updated_successfully')
        );
    }

    /**
     * Remove the specified lesson
     * DELETE /dashboard/lessons/{id}
     * Requires: admin role
     */
    public function destroy($id)
    {
        $this->lessonService->deleteLesson((int) $id);

        return $this->successResponse(
            null,
            __('lessons.deleted_successfully')
        );
    }

    /**
     * Reorder lessons within a course
     * POST /dashboard/lessons/reorder
     * Requires: admin role
     * Body: { "course_id": 5, "lesson_orders": { "1": 0, "2": 1, "3": 2 } }
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'lesson_orders' => 'required|array',
            'lesson_orders.*' => 'integer|min:0',
        ]);

        $success = $this->lessonService->reorderLessons(
            $request->input('course_id'),
            $request->input('lesson_orders')
        );

        return $this->successResponse(
            ['reordered' => $success],
            __('lessons.reordered_successfully')
        );
    }

    /**
     * Search lessons
     * GET /dashboard/lessons/search?q=term&course_id=1
     * Requires: admin role
     */
    public function search(Request $request)
    {
        $term = $request->input('q');
        $courseId = $request->input('course_id');

        if (!$term) {
            return $this->errorResponse(__('lessons.search_term_required'));
        }

        $lessons = $this->lessonService->searchLessons($term, $courseId ? (int) $courseId : null);

        return $this->successResponse(
            LessonResource::collection($lessons),
            __('lessons.search_results')
        );
    }

    /**
     * Get free lessons only
     * GET /dashboard/lessons/free
     * Requires: admin role
     */
    public function free()
    {
        $lessons = $this->lessonService->getFreeLessons();

        return $this->successResponse(
            LessonResource::collection($lessons),
            __('lessons.free_retrieved_successfully')
        );
    }
}
