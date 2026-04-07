<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use App\Models\Course;
use App\Models\Package;
use App\Models\CourseStudent;
use App\Models\PackageStudent;
use App\Http\Resources\CourseSubscriptionResource;
use App\Http\Resources\PackageSubscriptionResource;
use App\Http\Resources\AccessibleCourseResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    use ApiResponseTrait;

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Subscribe to a course or package using wallet balance
     * POST /api/subscribe?type=course|package&id={id}
     * Requires: auth:sanctum
     * Query: expires_at (ISO date), description
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();

        // Validate required parameters
        $validated = $request->validate([
            'type' => 'required|string|in:course,package',
            'id' => 'required|integer',
        ]);

        $type = $validated['type'];
        $id = $validated['id'];

        // Check that the id references an existing record (and is not soft deleted)
        if ($type === 'course') {
            $model = Course::where('id', $id)->whereNull('deleted_at')->first();
            if (!$model) {
                return $this->errorResponse('The selected course does not exist', [], 404);
            }
        } else {
            $model = Package::where('id', $id)->whereNull('deleted_at')->first();
            if (!$model) {
                return $this->errorResponse('The selected package does not exist', [], 404);
            }
        }

        if ($type === 'course') {
            $this->subscriptionService->subscribeToCourse($user, $model, [
                'expires_at' => $request->query('expires_at'),
                'description' => $request->query('description'),
            ]);
            return $this->successResponse(null, __('courses.subscribed_successfully'));
        } else {
            $this->subscriptionService->subscribeToPackage($user, $model, [
                'expires_at' => $request->query('expires_at'),
                'description' => $request->query('description'),
            ]);
            return $this->successResponse(null, __('packages.subscribed_successfully'));
        }
    }

    /**
     * Unsubscribe from a course or package (soft delete)
     * DELETE /api/subscribe?type=course|package&id={id}
     * Requires: auth:sanctum
     */
    public function unsubscribe(Request $request)
    {
        $user = Auth::user();

        // Validate required parameters
        $validated = $request->validate([
            'type' => 'required|string|in:course,package',
            'id' => 'required|integer',
        ]);

        $type = $validated['type'];
        $id = $validated['id'];

        if ($type === 'course') {
            $enrollment = $user->enrolledCourses()
                ->where('course_id', $id)
                ->first();

            if (!$enrollment) {
                return $this->errorResponse(__('auth.not_enrolled'), [], 404);
            }

            $enrollment->pivot->delete();
            return $this->successResponse(null, __('courses.unsubscribed_successfully'));
        } else {
            $subscription = $user->subscribedPackages()
                ->where('package_id', $id)
                ->first();

            if (!$subscription) {
                return $this->errorResponse(__('auth.not_subscribed'), [], 404);
            }

            $subscription->pivot->delete();
            return $this->successResponse(null, __('packages.unsubscribed_successfully'));
        }
    }

    /**
     * Get user's subscriptions (courses or packages)
     * GET /api/subscriptions?type=course|package
     * Requires: auth:sanctum
     * Query:
     *   - type: course|package (required)
     *   - active_only: true|false (default: false)
     *   - per_page: int (default: 15)
     *   - page: int
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type');

        if (!$type || !in_array($type, ['course', 'package'])) {
            return $this->errorResponse('The type parameter is required and must be either "course" or "package"', [], 400);
        }

        $filters = [
            'active_only' => filter_var($request->boolean('active_only', false), FILTER_VALIDATE_BOOLEAN),
        ];
        $perPage = (int) $request->input('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        if ($type === 'course') {
            // Get accessible courses (direct + from packages)
            $items = $this->subscriptionService->getAccessibleCourses($user, $filters);
            $resourceClass = AccessibleCourseResource::class;
            $responseKey = 'courses';
            $message = __('courses.enrollments_retrieved_successfully');
        } else {
            // Get package subscriptions
            $items = $this->subscriptionService->getUserSubscriptions($user, $filters);
            $resourceClass = PackageSubscriptionResource::class;
            $responseKey = 'subscriptions';
            $message = __('packages.subscriptions_retrieved_successfully');
        }

        // Manual pagination for collection
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $itemsPage = $items->slice($offset, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $itemsPage,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->successResponse([
            $responseKey => $resourceClass::collection($itemsPage),
            'pagination' => $this->formatPagination($paginated)
        ], $message);
    }
}
