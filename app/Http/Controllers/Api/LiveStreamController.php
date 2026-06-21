<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LiveStreamService;
use App\Http\Resources\LiveStreamResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    use ApiResponseTrait;

    protected $liveStreamService;

    public function __construct(LiveStreamService $liveStreamService)
    {
        $this->liveStreamService = $liveStreamService;
    }

    /**
     * Get active live stream for a course (subscribers only)
     * GET /api/courses/{id}/live
     */
    public function show(Request $request, $courseId)
    {
        $liveStream = $this->liveStreamService->getActiveLiveStream((int) $courseId);

        if (!$liveStream) {
            return $this->errorResponse('No active live stream for this course', [], 404);
        }

        return $this->successResponse(
            new LiveStreamResource($liveStream),
            __('live_streams.retrieved_successfully')
        );
    }
}
