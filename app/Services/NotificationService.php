<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\User;
use App\Models\UserFcmToken;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $notificationRepository;
    protected $messaging;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;

        try {
            $this->messaging = app('firebase.messaging');
        } catch (\Exception $e) {
            Log::warning('Firebase not configured: ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    public function getAllNotifications()
    {
        return $this->notificationRepository->getAll();
    }

    public function getNotificationById(int $id)
    {
        return $this->notificationRepository->findById($id);
    }

    public function getPaginatedNotifications(int $perPage, array $filters = [])
    {
        return $this->notificationRepository->paginate($perPage, $filters);
    }

    public function deleteNotification(int $id)
    {
        return $this->notificationRepository->delete($id);
    }

    public function searchNotifications(string $term)
    {
        return $this->notificationRepository->search($term);
    }

    public function getNotificationsForUser(int $userId, int $perPage = 15)
    {
        return $this->notificationRepository->getForUser($userId, $perPage);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }

    public function markAsRead(int $id): bool
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    /**
     * Send notification to users based on recipient type
     */
    public function sendNotification(array $data, int $senderId): \Illuminate\Support\Collection
    {
        $recipientType = $data['recipient_type'];
        $title = $data['title'];
        $body = $data['body'];
        $recipientId = !empty($data['recipient_id']) ? (int) $data['recipient_id'] : null;

        $users = $this->getRecipientUsers($recipientType, $recipientId);

        $notifications = [];
        foreach ($users as $user) {
            $notifications[] = [
                'sender_id' => $senderId,
                'recipient_id' => $user->id,
                'recipient_type' => $recipientType,
                'title' => $title,
                'body' => $body,
                'is_read' => false,
                'sent_via_firebase' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($notifications)) {
            $this->notificationRepository->createBulk($notifications);
        }

        // Send Firebase push notifications
        $this->sendFirebaseNotifications($users, $title, $body);

        return collect($notifications);
    }

    /**
     * Get users based on recipient type
     */
    protected function getRecipientUsers(string $recipientType, ?int $recipientId = null)
    {
        return match ($recipientType) {
            'all_students' => User::where('type', 'student')->where('status', 'active')->get(),
            'all_instructors' => User::where('type', 'instructor')->where('status', 'active')->get(),
            'single_student' => User::where('id', $recipientId)->where('type', 'student')->get(),
            'single_instructor' => User::where('id', $recipientId)->where('type', 'instructor')->get(),
            default => collect(),
        };
    }

    /**
     * Send Firebase Cloud Messaging push notifications
     */
    protected function sendFirebaseNotifications($users, string $title, string $body): void
    {
        if (!$this->messaging) {
            Log::warning('Firebase Messaging not configured. Skipping push notification.');
            return;
        }

        $notification = FirebaseNotification::create($title, $body);

        foreach ($users as $user) {
            $fcmTokens = UserFcmToken::where('user_id', $user->id)->get();

            foreach ($fcmTokens as $tokenRecord) {
                try {
                    $message = CloudMessage::new()
                        ->withToken($tokenRecord->fcm_token)
                        ->withNotification($notification)
                        ->withData([
                            'type' => 'notification',
                            'title' => $title,
                            'body' => $body,
                        ]);

                    $this->messaging->send($message);

                    $tokenRecord->update(['last_used_at' => now()]);
                } catch (\Exception $e) {
                    Log::error('Firebase push notification failed for user ' . $user->id . ': ' . $e->getMessage());

                    // Remove invalid token
                    if (str_contains($e->getMessage(), 'NOT_FOUND') || str_contains($e->getMessage(), 'Invalid token')) {
                        $tokenRecord->delete();
                    }
                }
            }
        }
    }
}
