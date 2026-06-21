<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\LiveStreamService;
use App\Services\CourseService;
use App\Http\Requests\Dashboard\StoreLiveStreamRequest;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    protected $liveStreamService;
    protected $courseService;

    public function __construct(LiveStreamService $liveStreamService, CourseService $courseService)
    {
        $this->liveStreamService = $liveStreamService;
        $this->courseService = $courseService;
    }

    public function allLiveStreams(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 15);
        $filters = [];
        if ($request->filled('course_id')) {
            $filters['course_id'] = (int) $request->input('course_id');
        }
        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }
        if ($request->filled('is_active')) {
            $filters['is_active'] = $request->input('is_active') === '1';
        }
        $liveStreams = $this->liveStreamService->getPaginatedLiveStreams($perPage, $filters);
        $courses = $this->courseService->getAllCourses();

        return view('dashboard.live-streams.all', compact('liveStreams', 'courses'));
    }

    public function index(Request $request, $course)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $perPage = (int) ($request->input('per_page') ?? 10);
        $liveStreams = $this->liveStreamService->getPaginatedLiveStreams($perPage, ['course_id' => $course->id]);

        return view('dashboard.live-streams.index', compact('liveStreams', 'course'));
    }

    public function create($course)
    {
        $course = $this->courseService->getCourseById((int) $course);

        return view('dashboard.live-streams.form', [
            'liveStream' => null,
            'course' => $course,
        ]);
    }

    public function store(StoreLiveStreamRequest $request, $course)
    {
        $data = $request->validated();
        $data['course_id'] = (int) $course;

        $this->liveStreamService->createLiveStream($data);

        return redirect()->route('dashboard.courses.live-streams.index', $course)
            ->with('success', __('live_streams.created_successfully'));
    }

    public function show($course, $liveStream)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $liveStream = $this->liveStreamService->getLiveStreamById((int) $liveStream);

        return view('dashboard.live-streams.show', compact('liveStream', 'course'));
    }

    public function edit($course, $liveStream)
    {
        $course = $this->courseService->getCourseById((int) $course);
        $liveStream = $this->liveStreamService->getLiveStreamById((int) $liveStream);

        return view('dashboard.live-streams.form', [
            'liveStream' => $liveStream,
            'course' => $course,
        ]);
    }

    public function update(StoreLiveStreamRequest $request, $course, $liveStream)
    {
        $this->liveStreamService->updateLiveStream((int) $liveStream, $request->validated());

        return redirect()->route('dashboard.courses.live-streams.index', $course)
            ->with('success', __('live_streams.updated_successfully'));
    }

    public function destroy($course, $liveStream)
    {
        $this->liveStreamService->deleteLiveStream((int) $liveStream);

        return redirect()->route('dashboard.courses.live-streams.index', $course)
            ->with('success', __('live_streams.deleted_successfully'));
    }
}
