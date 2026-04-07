<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Services\CategoryService;
use App\Services\InstructorService;
use App\Http\Requests\Dashboard\StoreCourseRequest;
use App\Http\Requests\Dashboard\UpdateCourseRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponseTrait;

    protected $courseService;
    protected $categoryService;
    protected $instructorService;

    public function __construct(CourseService $courseService, CategoryService $categoryService, InstructorService $instructorService)
    {
        $this->courseService = $courseService;
        $this->categoryService = $categoryService;
        $this->instructorService = $instructorService;
    }

    /**
     * Display a listing of courses (for dashboard view)
     * GET /dashboard/courses
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'category_id', 'instructor_id']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedCourses = $this->courseService->getPaginatedCourses($perPage, $filters);
        $categories = $this->categoryService->getAllCategories();

        return view('dashboard.courses.index', [
            'courses' => $paginatedCourses,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new course
     * GET /dashboard/courses/create
     */
    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        $instructors = $this->instructorService->getAllInstructors();

        if ($instructors->count() === 0) {
            return redirect()->route('dashboard.instructors.index')->with('error', 'لا يوجد محاضرين. يرجى إضافة محاضرين أولاً.');
        }

        return view('dashboard.courses.form', [
            'categories' => $categories,
            'course' => null,
            'instructors' => $instructors
        ]);
    }

    /**
     * Store a newly created course
     * POST /dashboard/courses
     */
    public function store(StoreCourseRequest $request)
    {
        $course = $this->courseService->createCourse($request->validated());

        return $this->successRedirect(route('dashboard.courses.index'), __('courses.created_successfully'));
    }

    /**
     * Display the specified course
     * GET /dashboard/courses/{id}
     */
    public function show($id)
    {
        $course = $this->courseService->getCourseById((int) $id);
        return view('dashboard.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course
     * GET /dashboard/courses/{id}/edit
     */
    public function edit($id)
    {
        $course = $this->courseService->getCourseById((int) $id);
        $categories = $this->categoryService->getAllCategories();
        $instructors = $this->instructorService->getAllInstructors();

        // Ensure the current instructor is in the list even if they lost the instructor role
        if ($course->instructor && !$instructors->contains('id', $course->instructor_id)) {
            $instructors->push($course->instructor);
            $instructors = $instructors->sortBy('name');
        }

        if ($instructors->count() === 0) {
            return redirect()->route('dashboard.instructors.index')->with('error', 'لا يوجد محاضرين. يرجى إضافة محاضرين أولاً.');
        }

        return view('dashboard.courses.form', [
            'course' => $course,
            'categories' => $categories,
            'instructors' => $instructors
        ]);
    }

    /**
     * Update the specified course
     * PUT /dashboard/courses/{id}
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        $course = $this->courseService->updateCourse((int) $id, $request->validated());

        return $this->successRedirect(route('dashboard.courses.index'), __('courses.updated_successfully'));
    }

    /**
     * Remove the specified course
     * DELETE /dashboard/courses/{id}
     */
    public function destroy($id)
    {
        $this->courseService->deleteCourse((int) $id);

        return $this->successRedirect(route('dashboard.courses.index'), __('courses.deleted_successfully'));
    }
}
