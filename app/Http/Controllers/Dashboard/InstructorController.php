<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\InstructorService;
use App\Http\Requests\Dashboard\StoreInstructorRequest;
use App\Http\Requests\Dashboard\UpdateInstructorRequest;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    protected $instructorService;

    public function __construct(InstructorService $instructorService)
    {
        $this->instructorService = $instructorService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedInstructors = $this->instructorService->getPaginatedInstructors($perPage, $filters);

        // Get counts for dashboard stats
        $totalInstructors = $this->instructorService->getAllInstructors()->count();
        $activeInstructors = $this->instructorService->getActiveInstructors()->count();
        $inactiveInstructors = $this->instructorService->getInactiveInstructors()->count();

        return view('dashboard.instructors.index', [
            'instructors' => $paginatedInstructors,
            'totalInstructors' => $totalInstructors,
            'activeInstructors' => $activeInstructors,
            'inactiveInstructors' => $inactiveInstructors,
        ]);
    }

    public function show($id)
    {
        $instructor = $this->instructorService->getInstructorById($id);
        return view('dashboard.instructors.show', compact('instructor'));
    }

    public function create()
    {
        return view('dashboard.instructors.form', ['instructor' => null]);
    }

    public function store(StoreInstructorRequest $request)
    {
        $this->instructorService->createInstructor($request->validated());

        return redirect()->route('dashboard.instructors.index')
            ->with('success', __('تم إنشاء المحاضر بنجح'));
    }

    public function edit($id)
    {
        $instructor = $this->instructorService->getInstructorById($id);
        return view('dashboard.instructors.form', [
            'instructor' => $instructor,
        ]);
    }

    public function update(UpdateInstructorRequest $request, $id)
    {
        $this->instructorService->updateInstructor($id, $request->validated());

        return redirect()->route('dashboard.instructors.index')
            ->with('success', __('تم تحديث المحاضر بنجاح'));
    }

    public function destroy($id)
    {
        $this->instructorService->deleteInstructor($id);

        return redirect()->route('dashboard.instructors.index')
            ->with('success', __('تم حذف المحاضر بنجاح'));
    }

    public function active()
    {
        $instructors = $this->instructorService->getActiveInstructors();
        return view('dashboard.instructors.index', [
            'instructors' => $instructors,
            'activeFilter' => true,
        ]);
    }

    public function inactive()
    {
        $instructors = $this->instructorService->getInactiveInstructors();
        return view('dashboard.instructors.index', [
            'instructors' => $instructors,
            'inactiveFilter' => true,
        ]);
    }
}
