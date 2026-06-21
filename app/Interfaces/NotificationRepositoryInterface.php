<?php

namespace App\Interfaces;

interface NotificationRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function delete(int $id);
    public function search(string $term);
    public function paginate(int $perPage, array $filters = []);
    public function getForUser(int $userId, int $perPage = 15);
    public function getUnreadCount(int $userId): int;
    public function markAsRead(int $id): bool;
    public function markAllAsRead(int $userId): bool;
    public function createBulk(array $notifications): bool;
}
