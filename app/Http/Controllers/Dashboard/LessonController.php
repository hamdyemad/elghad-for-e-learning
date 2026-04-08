<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\LessonService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreLessonRequest;
use App\Http\Requests\Dashboard\UpdateLessonRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use ApiResponseTrait;

    protected $lessonService;
    protected $courseService;

    public function __construct(LessonService $lessonService, CourseService $courseService)
    {
        $this->lessonService = $lessonService;
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of lessons (for dashboard view)
     * GET /dashboard/lessons
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'course_id']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        $perPage = (int) $request->input('per_page', 10);
        $lessons = $this->lessonService->getPaginatedLessons($perPage, $filters);

        $course = null;
        if (isset($filters['course_id'])) {
            $course = $this->courseService->getCourseById((int) $filters['course_id']);
        }

        $courses = $this->courseService->getAllCourses();

        return view('dashboard.lessons.index', [
            'lessons' => $lessons,
            'courses' => $courses,
            'course' => $course,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new lesson
     * GET /dashboard/lessons/create?course_id={id}
     */
    public function create(Request $request)
    {
        $courseId = $request->input('course_id');
        $course = null;
        if ($courseId) {
            $course = $this->courseService->getCourseById((int) $courseId);
        }
        return view('dashboard.lessons.form', [
            'lesson' => null,
            'course' => $course
        ]);
    }

    /**
     * Store a newly created lesson
     * POST /dashboard/lessons
     */
    public function store(StoreLessonRequest $request)
    {
        $lesson = $this->lessonService->createLesson($request->validated());

        return $this->successRedirect(
            route('dashboard.lessons.index', ['course_id' => $lesson->course_id]),
            __('lessons.created_successfully')
        );
    }

    /**
     * Display the specified lesson
     * GET /dashboard/lessons/{id}
     */
    public function show($id)
    {
        $lesson = $this->lessonService->getLesson((int) $id);
        return view('dashboard.lessons.show', compact('lesson'));
    }

    /**
     * Show the form for editing the specified lesson
     * GET /dashboard/lessons/{id}/edit
     */
    public function edit($id)
    {
        $lesson = $this->lessonService->getLesson((int) $id);
        $courses = $this->courseService->getAllCourses();
        return view('dashboard.lessons.form', [
            'lesson' => $lesson,
            'courses' => $courses
        ]);
    }

    /**
     * Update the specified lesson
     * PUT /dashboard/lessons/{id}
     */
    public function update(UpdateLessonRequest $request, $id)
    {
        $this->lessonService->updateLesson((int) $id, $request->validated());

        return $this->successRedirect(
            route('dashboard.lessons.index'),
            __('lessons.updated_successfully')
        );
    }

    /**
     * Remove the specified lesson
     * DELETE /dashboard/lessons/{id}
     */
    public function destroy($id)
    {
        $this->lessonService->deleteLesson((int) $id);

        return $this->successRedirect(
            route('dashboard.lessons.index'),
            __('lessons.deleted_successfully')
        );
    }

    /**
     * Show reorder form for a course's lessons
     * GET /dashboard/lessons/reorder?course_id={id}
     */
    public function reorderForm(Request $request)
    {
        $courseId = $request->input('course_id');
        if (!$courseId) {
            return $this->errorRedirect(route('dashboard.lessons.index'), __('يجب اختيار دورة أولاً'));
        }

        $course = $this->courseService->getCourseById((int) $courseId);
        $lessons = $this->lessonService->getLessonsByCourse((int) $courseId);

        return view('dashboard.lessons.reorder', compact('course', 'lessons'));
    }

    /**
     * Update lesson order
     * POST /dashboard/lessons/reorder
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'lesson_orders' => 'required|array',
            'lesson_orders.*' => 'integer|min:0',
        ]);

        $this->lessonService->reorderLessons(
            $request->input('course_id'),
            $request->input('lesson_orders')
        );

        return $this->successRedirect(
            route('dashboard.lessons.index'),
            __('lessons.reordered_successfully')
        );
    }
}
