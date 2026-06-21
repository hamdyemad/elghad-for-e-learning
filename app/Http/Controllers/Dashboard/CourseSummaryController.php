<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\CourseSummaryService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreCourseSummaryRequest;
use Illuminate\Http\Request;

class CourseSummaryController extends Controller
{
    protected $courseSummaryService;
    protected $courseService;

    public function __construct(CourseSummaryService $courseSummaryService, CourseService $courseService)
    {
        $this->courseSummaryService = $courseSummaryService;
        $this->courseService = $courseService;
    }

    public function index(Request $request, $course)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $perPage = (int) ($request->input('per_page') ?? 10);
        $summaries = $this->courseSummaryService->getPaginatedSummaries($perPage, ['course_id' => $course->id]);

        return view('dashboard.course-summaries.index', compact('summaries', 'course'));
    }

    public function create($course)
    {
        $course = $this->courseService->getCourseById((int) $course);

        return view('dashboard.course-summaries.form', [
            'summary' => null,
            'course' => $course,
        ]);
    }

    public function store(StoreCourseSummaryRequest $request, $course)
    {
        $data = $request->validated();
        $data['course_id'] = (int) $course;

        $this->courseSummaryService->createSummary($data);

        return redirect()->route('dashboard.courses.summaries.index', $course)
            ->with('success', __('course_summaries.created_successfully'));
    }

    public function show($course, $summary)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $summary = $this->courseSummaryService->getSummaryById((int) $summary);

        return view('dashboard.course-summaries.show', compact('summary', 'course'));
    }

    public function edit($course, $summary)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $summary = $this->courseSummaryService->getSummaryById((int) $summary);

        return view('dashboard.course-summaries.form', [
            'summary' => $summary,
            'course' => $course,
        ]);
    }

    public function update(StoreCourseSummaryRequest $request, $course, $summary)
    {
        $this->courseSummaryService->updateSummary((int) $summary, $request->validated());

        return redirect()->route('dashboard.courses.summaries.index', $course)
            ->with('success', __('course_summaries.updated_successfully'));
    }

    public function destroy($course, $summary)
    {
        $this->courseSummaryService->deleteSummary((int) $summary);

        return redirect()->route('dashboard.courses.summaries.index', $course)
            ->with('success', __('course_summaries.deleted_successfully'));
    }
}
