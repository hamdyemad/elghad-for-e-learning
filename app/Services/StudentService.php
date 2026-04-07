<?php

namespace App\Services;

use App\Interfaces\StudentRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    protected $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getAllStudents()
    {
        return $this->studentRepository->getAll();
    }

    public function getActiveStudents()
    {
        return $this->studentRepository->getActive();
    }

    public function getInactiveStudents()
    {
        return $this->studentRepository->getInactive();
    }

    public function getStudentById(int $id)
    {
        return $this->studentRepository->findById($id);
    }

    public function createStudent(array $data)
    {
        // Handle avatar upload if provided
        if (isset($data['avatar']) && $data['avatar']) {
            $data['avatar'] = $this->uploadImage($data['avatar']);
        }

        return $this->studentRepository->create($data);
    }

    public function updateStudent(int $id, array $data)
    {
        $student = $this->studentRepository->findById($id);

        // Handle avatar update
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar if exists
            if ($student->avatar) {
                Storage::disk('public')->delete($student->avatar);
            }
            $data['avatar'] = $this->uploadImage($data['avatar']);
        }

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $this->studentRepository->update($id, $data);
    }

    public function deleteStudent(int $id)
    {
        return $this->studentRepository->delete($id);
    }

    public function upgradeToInstructor(int $id, array $data = [])
    {
        $student = $this->studentRepository->findById($id);

        // Verify user is a student
        if ($student->type !== 'student') {
            throw new \Exception('User is not a student or already an instructor');
        }

        // Upgrade to instructor
        $updateData = array_merge([
            'type' => 'instructor',
            'is_instructor' => true,
        ], $data);

        $updatedStudent = $this->studentRepository->update($id, $updateData);

        // Update roles: remove 'student' role and add 'instructor' role
        $studentRole = \App\Models\Role::where('name', 'student')->first();
        $instructorRole = \App\Models\Role::where('name', 'instructor')->first();

        if ($studentRole) {
            $updatedStudent->roles()->detach($studentRole);
        }

        if ($instructorRole) {
            $updatedStudent->roles()->attach($instructorRole);
        }

        return $updatedStudent->fresh(['roles']);
    }

    public function searchStudents(string $term)
    {
        return $this->studentRepository->search($term);
    }

    public function filterStudents(array $filters)
    {
        return $this->studentRepository->applyFilters($filters);
    }

    public function getPaginatedStudents(int $perPage, array $filters = [])
    {
        return $this->studentRepository->paginate($perPage, $filters);
    }

    protected function uploadImage($image)
    {
        return $image->store('avatars', 'public');
    }
}
