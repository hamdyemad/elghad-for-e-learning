<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\LiveStreamService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreLiveStreamRequest;
use App\Http\Resources\LiveStreamResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    use ApiResponseTrait;

    protected $liveStreamService;
    protected $courseService;

    public function __construct(LiveStreamService $liveStreamService, CourseService $courseService)
    {
        $this->liveStreamService = $liveStreamService;
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'course_id', 'is_active']);
        $filters = array_filter($filters, fn($value) => $value !== '' && $value !== null);
        $perPage = (int) ($request->input('per_page') ?? 15);

        $liveStreams = $this->liveStreamService->getPaginatedLiveStreams($perPage, $filters);

        return $this->successResponse([
            'live_streams' => LiveStreamResource::collection($liveStreams->items()),
            'pagination' => $this->formatPagination($liveStreams),
        ], __('live_streams.retrieved_successfully'));
    }

    public function store(StoreLiveStreamRequest $request)
    {
        $data = $request->validated();
        $data['course_id'] = (int) $request->input('course_id');

        $liveStream = $this->liveStreamService->createLiveStream($data);

        return $this->createdResponse(
            new LiveStreamResource($liveStream),
            __('live_streams.created_successfully')
        );
    }

    public function show($id)
    {
        $liveStream = $this->liveStreamService->getLiveStreamById((int) $id);

        return $this->successResponse(
            new LiveStreamResource($liveStream),
            __('live_streams.single_retrieved_successfully')
        );
    }

    public function update(StoreLiveStreamRequest $request, $id)
    {
        $liveStream = $this->liveStreamService->updateLiveStream((int) $id, $request->validated());

        return $this->successResponse(
            new LiveStreamResource($liveStream),
            __('live_streams.updated_successfully')
        );
    }

    public function destroy($id)
    {
        $this->liveStreamService->deleteLiveStream((int) $id);

        return $this->successResponse(null, __('live_streams.deleted_successfully'));
    }

    public function courses()
    {
        $courses = $this->courseService->getAllCourses()
            ->map(fn($course) => ['id' => $course->id, 'title' => $course->title]);

        return $this->successResponse($courses, __('live_streams.courses_retrieved_successfully'));
    }
}
