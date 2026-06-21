<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Dashboard\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'recipient_type', 'is_read']);
        $filters = array_filter($filters, fn($value) => $value !== '' && $value !== null);
        $perPage = (int) ($request->input('per_page') ?? 15);

        $notifications = $this->notificationService->getPaginatedNotifications($perPage, $filters);

        return $this->successResponse([
            'notifications' => NotificationResource::collection($notifications->items()),
            'pagination' => $this->formatPagination($notifications),
        ], __('notifications.retrieved_successfully'));
    }

    public function store(StoreNotificationRequest $request)
    {
        $this->notificationService->sendNotification(
            $request->validated(),
            auth()->id()
        );

        return $this->createdResponse(null, __('notifications.sent_successfully'));
    }

    public function show($id)
    {
        $notification = $this->notificationService->getNotificationById((int) $id);

        return $this->successResponse(
            new NotificationResource($notification),
            __('notifications.single_retrieved_successfully')
        );
    }

    public function destroy($id)
    {
        $this->notificationService->deleteNotification((int) $id);

        return $this->successResponse(null, __('notifications.deleted_successfully'));
    }

    public function students()
    {
        $students = User::where('type', 'student')
            ->where('status', 'active')
            ->select('id', 'name', 'email')
            ->get();

        return $this->successResponse($students, __('notifications.students_retrieved_successfully'));
    }

    public function instructors()
    {
        $instructors = User::where('type', 'instructor')
            ->where('status', 'active')
            ->select('id', 'name', 'email')
            ->get();

        return $this->successResponse($instructors, __('notifications.instructors_retrieved_successfully'));
    }
}
