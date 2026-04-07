<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\UpgradeStudentToInstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\StudentResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    use ApiResponseTrait;

    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of students (with filters)
     * GET /dashboard/students
     * Requires: admin role
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedStudents = $this->studentService->getPaginatedStudents($perPage, $filters);

        return $this->successResponse([
            'students' => StudentResource::collection($paginatedStudents->items()),
            'pagination' => $this->formatPagination($paginatedStudents)
        ], __('students.retrieved_successfully'));
    }

    /**
     * Store a newly created student
     * POST /dashboard/students
     * Requires: admin role
     */
    public function store(StoreStudentRequest $request)
    {
        $student = $this->studentService->createStudent($request->validated());

        return $this->createdResponse(
            new StudentResource($student),
            __('students.created_successfully')
        );
    }

    /**
     * Display the specified student
     * GET /dashboard/students/{id}
     * Requires: admin role
     */
    public function show($id)
    {
        $student = $this->studentService->getStudentById((int) $id);

        return $this->successResponse(
            new StudentResource($student),
            __('students.single_retrieved_successfully')
        );
    }

    /**
     * Update the specified student
     * PUT /dashboard/students/{id}
     * Requires: admin role
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        $student = $this->studentService->updateStudent((int) $id, $request->validated());

        return $this->successResponse(
            new StudentResource($student),
            __('students.updated_successfully')
        );
    }

    /**
     * Remove the specified student
     * DELETE /dashboard/students/{id}
     * Requires: admin role
     */
    public function destroy($id)
    {
        $this->studentService->deleteStudent((int) $id);

        return $this->successResponse(
            null,
            __('students.deleted_successfully')
        );
    }

    /**
     * Get active students only
     * GET /dashboard/students/active
     * Requires: admin role
     */
    public function active()
    {
        $students = $this->studentService->getActiveStudents();

        return $this->successResponse(
            StudentResource::collection($students),
            __('students.active_retrieved_successfully')
        );
    }

    /**
     * Get inactive students only
     * GET /dashboard/students/inactive
     * Requires: admin role
     */
    public function inactive()
    {
        $students = $this->studentService->getInactiveStudents();

        return $this->successResponse(
            StudentResource::collection($students),
            __('students.inactive_retrieved_successfully')
        );
    }

    /**
     * Upgrade a student to instructor
     * POST /dashboard/students/{id}/upgrade-to-instructor
     * Requires: admin role
     */
    public function upgradeToInstructor(UpgradeStudentToInstructorRequest $request, $id)
    {
        // Check if user is a student before upgrade
        $student = $this->studentService->getStudentById((int) $id);

        if ($student->type !== 'student') {
            return $this->errorResponse(
                __('students.not_a_student'),
                [__('students.not_a_student')],
                400
            );
        }

        $instructor = $this->studentService->upgradeToInstructor((int) $id, $request->validated());

        return $this->successResponse(
            new \App\Http\Resources\InstructorResource($instructor),
            __('students.upgraded_to_instructor_successfully')
        );
    }
}
