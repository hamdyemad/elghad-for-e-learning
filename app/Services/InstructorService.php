<?php

namespace App\Services;

use App\Interfaces\InstructorRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class InstructorService
{
    protected $instructorRepository;

    public function __construct(InstructorRepositoryInterface $instructorRepository)
    {
        $this->instructorRepository = $instructorRepository;
    }

    public function getAllInstructors()
    {
        return $this->instructorRepository->getAll();
    }

    public function getActiveInstructors()
    {
        return $this->instructorRepository->getActive();
    }

    public function getInactiveInstructors()
    {
        return $this->instructorRepository->getInactive();
    }

    public function getInstructorById(int $id)
    {
        return $this->instructorRepository->findById($id);
    }

    public function createInstructor(array $data)
    {
        // Handle avatar upload if provided
        if (isset($data['avatar']) && $data['avatar']) {
            $data['avatar'] = $this->uploadImage($data['avatar']);
        }

        return $this->instructorRepository->create($data);
    }

    public function updateInstructor(int $id, array $data)
    {
        $instructor = $this->instructorRepository->findById($id);

        // Handle avatar update
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar if exists
            if ($instructor->avatar) {
                Storage::disk('public')->delete($instructor->avatar);
            }
            $data['avatar'] = $this->uploadImage($data['avatar']);
        }

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $this->instructorRepository->update($id, $data);
    }

    public function deleteInstructor(int $id)
    {
        return $this->instructorRepository->delete($id);
    }

    public function searchInstructors(string $term)
    {
        return $this->instructorRepository->search($term);
    }

    public function filterInstructors(array $filters)
    {
        return $this->instructorRepository->applyFilters($filters);
    }

    public function getPaginatedInstructors(int $perPage, array $filters = [])
    {
        return $this->instructorRepository->paginate($perPage, $filters);
    }

    protected function uploadImage($image)
    {
        return $image->store('instructors', 'public');
    }
}
