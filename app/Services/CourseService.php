<?php

namespace App\Services;

use App\Interfaces\CourseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getCourseById(int $id)
    {
        return $this->courseRepository->findById($id);
    }

    public function getAllCourses()
    {
        return $this->courseRepository->getAll();
    }

    public function getPublishedCourses()
    {
        return $this->getCourses(['status' => 'published']);
    }

    public function getDraftCourses()
    {
        return $this->getCourses(['status' => 'draft']);
    }

    public function getCourses(array $filters = [], bool $paginate = false, int $perPage = 15)
    {
        if ($paginate) {
            return $this->courseRepository->paginate($perPage, $filters);
        }

        return $this->courseRepository->applyFilters($filters);
    }

    public function createCourse(array $data)
    {
        // Handle thumbnail upload
        if (isset($data['thumbnail']) && $data['thumbnail']) {
            $data['thumbnail'] = $this->uploadImage($data['thumbnail']);
        }

        // Set defaults for fields with database defaults (to avoid NULL issues)
        if (!isset($data['price']) || $data['price'] === null) {
            $data['price'] = 0;
        }
        if (!isset($data['professor_profit']) || $data['professor_profit'] === null) {
            $data['professor_profit'] = 0;
        }
        if (!isset($data['status']) || $data['status'] === null) {
            $data['status'] = 'draft';
        }

        // If price is 0, mark as free
        if ($data['price'] == 0) {
            $data['is_free'] = true;
        }

        return $this->courseRepository->create($data);
    }

    public function updateCourse(int $id, array $data)
    {
        $course = $this->courseRepository->findById($id);

        // Handle thumbnail update
        if (isset($data['thumbnail']) && $data['thumbnail']) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $this->uploadImage($data['thumbnail']);
        }

        // If price is 0, mark as free
        if (isset($data['price']) && $data['price'] == 0) {
            $data['is_free'] = true;
        }

        return $this->courseRepository->update($id, $data);
    }

    public function deleteCourse(int $id)
    {
        return $this->courseRepository->delete($id);
    }

    public function searchCourses(string $term)
    {
        return $this->courseRepository->search($term);
    }

    public function filterCourses(array $filters)
    {
        return $this->courseRepository->applyFilters($filters);
    }

    public function getPaginatedCourses(int $perPage, array $filters = [])
    {
        return $this->courseRepository->paginate($perPage, $filters);
    }

    protected function uploadImage($image)
    {
        // If it's an UploadedFile object, store it normally
        if ($image instanceof UploadedFile) {
            return $image->store('courses', 'public');
        }

        // If it's a base64 encoded image string
        if (is_string($image) && str_starts_with($image, 'data:image')) {
            $exploded = explode(',', $image);
            if (count($exploded) == 2) {
                $base64Data = $exploded[1];
                $imageData = base64_decode($base64Data);

                if ($imageData === false) {
                    throw new \InvalidArgumentException('Invalid base64 image data');
                }

                // Generate unique filename with correct extension
                $extension = $this->getBase64Extension($exploded[0]) ?? 'png';
                $filename = 'courses/' . uniqid() . '.' . $extension;

                Storage::disk('public')->put($filename, $imageData);
                return $filename;
            }
        }

        // If it's already a path (string), return it as is
        return $image;
    }

    protected function getBase64Extension($mimeString)
    {
        if (preg_match('/data:image\/(\w+);/', $mimeString, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
