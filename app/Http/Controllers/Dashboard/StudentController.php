<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Http\Requests\Dashboard\StoreStudentRequest;
use App\Http\Requests\Dashboard\UpdateStudentRequest;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedStudents = $this->studentService->getPaginatedStudents($perPage, $filters);

        // Get counts for dashboard stats from the paginated query's total
        $totalStudents = $this->studentService->getAllStudents()->count();
        $activeStudents = $this->studentService->getActiveStudents()->count();
        $inactiveStudents = $this->studentService->getInactiveStudents()->count();

        return view('dashboard.students.index', [
            'students' => $paginatedStudents,
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'inactiveStudents' => $inactiveStudents,
        ]);
    }

    public function show($id)
    {
        $student = $this->studentService->getStudentById($id);
        return view('dashboard.students.show', compact('student'));
    }

    public function create()
    {
        return view('dashboard.students.form', ['student' => null]);
    }

    public function store(StoreStudentRequest $request)
    {
        $this->studentService->createStudent($request->validated());

        return redirect()->route('dashboard.students.index')
            ->with('success', __('تم إنشاء الطالب بنجاح'));
    }

    public function edit($id)
    {
        $student = $this->studentService->getStudentById($id);
        return view('dashboard.students.form', [
            'student' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, $id)
    {
        $this->studentService->updateStudent($id, $request->validated());

        return redirect()->route('dashboard.students.index')
            ->with('success', __('تم تحديث الطالب بنجاح'));
    }

    public function destroy($id)
    {
        $this->studentService->deleteStudent($id);

        return redirect()->route('dashboard.students.index')
            ->with('success', __('تم حذف الطالب بنجاح'));
    }

    public function active()
    {
        $students = $this->studentService->getActiveStudents();
        return view('dashboard.students.index', [
            'students' => $students,
            'activeFilter' => true,
        ]);
    }

    public function inactive()
    {
        $students = $this->studentService->getInactiveStudents();
        return view('dashboard.students.index', [
            'students' => $students,
            'inactiveFilter' => true,
        ]);
    }

    public function upgradeToInstructor($id)
    {
        try {
            $this->studentService->upgradeToInstructor($id);

            return redirect()->route('dashboard.students.index')
                ->with('success', __('تم ترقية الطالب إلى مدرب بنجاح'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard.students.index')
                ->with('error', __('فشلت عملية الترقية: ') . $e->getMessage());
        }
    }
}
