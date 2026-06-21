<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreExamRequest;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    protected $examService;
    protected $courseService;

    public function __construct(ExamService $examService, CourseService $courseService)
    {
        $this->examService = $examService;
        $this->courseService = $courseService;
    }

    public function allExams(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 15);
        $filters = [];
        if ($request->filled('course_id')) {
            $filters['course_id'] = (int) $request->input('course_id');
        }
        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }
        $exams = $this->examService->getPaginatedExams($perPage, $filters);
        $courses = $this->courseService->getAllCourses();

        return view('dashboard.exams.all', compact('exams', 'courses'));
    }

    public function index(Request $request, $course)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $perPage = (int) ($request->input('per_page') ?? 10);
        $exams = $this->examService->getPaginatedExams($perPage, ['course_id' => $course->id]);

        return view('dashboard.exams.index', compact('exams', 'course'));
    }

    public function create($course)
    {
        $course = $this->courseService->getCourseById((int) $course);

        return view('dashboard.exams.form', [
            'exam' => null,
            'course' => $course,
        ]);
    }

    public function store(StoreExamRequest $request, $course)
    {
        $data = $request->validated();
        $data['course_id'] = (int) $course;

        $this->examService->createExam($data);

        return redirect()->route('dashboard.courses.exams.index', $course)
            ->with('success', __('exams.created_successfully'));
    }

    public function show($course, $exam)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $exam = $this->examService->getExamById((int) $exam);

        return view('dashboard.exams.show', compact('exam', 'course'));
    }

    public function edit($course, $exam)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $exam = $this->examService->getExamById((int) $exam);

        return view('dashboard.exams.form', [
            'exam' => $exam,
            'course' => $course,
        ]);
    }

    public function update(StoreExamRequest $request, $course, $exam)
    {
        $this->examService->updateExam((int) $exam, $request->validated());

        return redirect()->route('dashboard.courses.exams.index', $course)
            ->with('success', __('exams.updated_successfully'));
    }

    public function destroy($course, $exam)
    {
        $this->examService->deleteExam((int) $exam);

        return redirect()->route('dashboard.courses.exams.index', $course)
            ->with('success', __('exams.deleted_successfully'));
    }
}
