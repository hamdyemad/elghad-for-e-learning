<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use App\Models\User;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('sender', 'recipient')->latest()->get();
    }

    public function findById(int $id)
    {
        return $this->model->with('sender', 'recipient')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete(int $id)
    {
        $notification = $this->findById($id);
        return $notification->delete();
    }

    public function search(string $term)
    {
        return $this->model->search($term)->with('sender', 'recipient')->latest()->get();
    }

    public function paginate(int $perPage, array $filters = [])
    {
        $query = $this->model->with('sender', 'recipient');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['recipient_type'])) {
            $query->where('recipient_type', $filters['recipient_type']);
        }

        if (isset($filters['is_read'])) {
            $query->where('is_read', $filters['is_read']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getForUser(int $userId, int $perPage = 15)
    {
        return $this->model
            ->where('recipient_id', $userId)
            ->with('sender')
            ->latest()
            ->paginate($perPage);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->model
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead(int $id): bool
    {
        $notification = $this->findById($id);
        $notification->markAsRead();
        return true;
    }

    public function markAllAsRead(int $userId): bool
    {
        $this->model
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return true;
    }

    public function createBulk(array $notifications): bool
    {
        return $this->model->insert($notifications);
    }
}
