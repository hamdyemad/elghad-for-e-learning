<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get all users with relationships
     */
    public function getAll(array $with = []);

    /**
     * Get active users only
     */
    public function getActive();

    /**
     * Get verified users only
     */
    public function getVerified();

    /**
     * Find user by ID
     */
    public function findById(int $id): User;

    /**
     * Find user by UUID
     */
    public function findByUuid(string $uuid): ?User;

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by email or fail
     */
    public function findByEmailOrFail(string $email): User;

    /**
     * Create a new user
     */
    public function create(array $data): User;

    /**
     * Update user
     */
    public function update(int $id, array $data): User;

    /**
     * Delete user
     */
    public function delete(int $id): bool;

    /**
     * Check if email exists
     */
    public function existsByEmail(string $email): bool;

    /**
     * Assign role to user
     */
    public function assignRole(int $userId, int $roleId): bool;

    /**
     * Remove role from user
     */
    public function removeRole(int $userId, int $roleId): bool;

    /**
     * Get user's roles
     */
    public function getRoles(int $userId);
}
