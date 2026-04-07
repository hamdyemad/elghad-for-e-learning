<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll(array $with = [])
    {
        $query = $this->model;

        if (!empty($with)) {
            $query = $query->with($with);
        }

        return $query->ordered()->get();
    }

    public function getActive()
    {
        return $this->model->active()->get();
    }

    public function getVerified()
    {
        return $this->model->verified()->get();
    }

    public function findById(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByEmailOrFail(string $email): User
    {
        return $this->model->where('email', $email)->firstOrFail();
    }

    public function create(array $data): User
    {
        // Generate UUID if not provided
        if (empty($data['uuid'])) {
            $data['uuid'] = \Str::uuid();
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user->fresh();
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        return $user->delete();
    }

    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', $email)->exists();
    }

    public function assignRole(int $userId, int $roleId): bool
    {
        $user = $this->findById($userId);
        return $user->roles()->attach($roleId);
    }

    public function removeRole(int $userId, int $roleId): bool
    {
        $user = $this->findById($userId);
        return $user->roles()->detach($roleId);
    }

    public function getRoles(int $userId)
    {
        return $this->model->with('roles')->find($userId)?->roles;
    }
}
