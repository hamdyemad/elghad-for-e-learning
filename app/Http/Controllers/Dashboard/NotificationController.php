<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Dashboard\StoreNotificationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'recipient_type']);
        $filters = array_filter($filters, fn($value) => $value !== '' && $value !== null);

        $notifications = !empty($filters)
            ? $this->notificationService->getPaginatedNotifications(15, $filters)
            : $this->notificationService->getPaginatedNotifications(15);

        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $students = User::where('type', 'student')->where('status', 'active')->get();
        $instructors = User::where('type', 'instructor')->where('status', 'active')->get();

        return view('dashboard.notifications.form', [
            'notification' => null,
            'students' => $students,
            'instructors' => $instructors,
        ]);
    }

    public function store(StoreNotificationRequest $request)
    {
        $this->notificationService->sendNotification(
            $request->validated(),
            auth()->id()
        );

        return redirect()->route('dashboard.notifications.index')
            ->with('success', __('notifications.sent_successfully'));
    }

    public function show($id)
    {
        $notification = $this->notificationService->getNotificationById((int) $id);

        return view('dashboard.notifications.show', compact('notification'));
    }

    public function destroy($id)
    {
        $this->notificationService->deleteNotification((int) $id);

        return redirect()->route('dashboard.notifications.index')
            ->with('success', __('notifications.deleted_successfully'));
    }
}
