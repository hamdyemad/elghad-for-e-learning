<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Resources\NotificationResource;
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
        $perPage = (int) ($request->input('per_page') ?? 15);
        $notifications = $this->notificationService->getNotificationsForUser(
            auth()->id(),
            $perPage
        );

        return $this->successResponse([
            'notifications' => NotificationResource::collection($notifications->items()),
            'pagination' => $this->formatPagination($notifications),
        ], __('notifications.retrieved_successfully'));
    }

    public function unreadCount()
    {
        $count = $this->notificationService->getUnreadCount(auth()->id());

        return $this->successResponse(['count' => $count], __('notifications.unread_count_retrieved'));
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead((int) $id);

        return $this->successResponse(null, __('notifications.marked_as_read'));
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->id());

        return $this->successResponse(null, __('notifications.all_marked_as_read'));
    }
}
