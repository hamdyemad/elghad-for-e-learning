<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\InstructorService;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\UpdateInstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    use ApiResponseTrait;

    protected $instructorService;

    public function __construct(InstructorService $instructorService)
    {
        $this->instructorService = $instructorService;
    }

    /**
     * Display a listing of instructors (with filters)
     * GET /dashboard/instructors
     * Requires: admin role
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });
        $perPage = (int) $request->input('per_page', 10);

        $paginatedInstructors = $this->instructorService->getPaginatedInstructors($perPage, $filters);

        return $this->successResponse([
            'instructors' => InstructorResource::collection($paginatedInstructors->items()),
            'pagination' => $this->formatPagination($paginatedInstructors)
        ], __('instructors.retrieved_successfully'));
    }

    /**
     * Store a newly created instructor
     * POST /dashboard/instructors
     * Requires: admin role
     */
    public function store(StoreInstructorRequest $request)
    {
        $instructor = $this->instructorService->createInstructor($request->validated());

        return $this->createdResponse(
            new InstructorResource($instructor),
            __('instructors.created_successfully')
        );
    }

    /**
     * Display the specified instructor
     * GET /dashboard/instructors/{id}
     * Requires: admin role
     */
    public function show($id)
    {
        $instructor = $this->instructorService->getInstructorById((int) $id);

        return $this->successResponse(
            new InstructorResource($instructor),
            __('instructors.single_retrieved_successfully')
        );
    }

    /**
     * Update the specified instructor
     * PUT /dashboard/instructors/{id}
     * Requires: admin role
     */
    public function update(UpdateInstructorRequest $request, $id)
    {
        $instructor = $this->instructorService->updateInstructor((int) $id, $request->validated());

        return $this->successResponse(
            new InstructorResource($instructor),
            __('instructors.updated_successfully')
        );
    }

    /**
     * Remove the specified instructor
     * DELETE /dashboard/instructors/{id}
     * Requires: admin role
     */
    public function destroy($id)
    {
        $this->instructorService->deleteInstructor((int) $id);

        return $this->successResponse(
            null,
            __('instructors.deleted_successfully')
        );
    }

    /**
     * Get active instructors only
     * GET /dashboard/instructors/active
     * Requires: admin role
     */
    public function active()
    {
        $instructors = $this->instructorService->getActiveInstructors();

        return $this->successResponse(
            InstructorResource::collection($instructors),
            __('instructors.active_retrieved_successfully')
        );
    }

    /**
     * Get inactive instructors only
     * GET /dashboard/instructors/inactive
     * Requires: admin role
     */
    public function inactive()
    {
        $instructors = $this->instructorService->getInactiveInstructors();

        return $this->successResponse(
            InstructorResource::collection($instructors),
            __('instructors.inactive_retrieved_successfully')
        );
    }
}
