<?php

namespace App\Services;

use App\Interfaces\PackageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PackageService
{
    protected $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    public function getPackageById(int $id)
    {
        return $this->packageRepository->findById($id);
    }

    public function getAllPackages()
    {
        return $this->packageRepository->getAll();
    }

    public function getPublishedPackages()
    {
        return $this->getPackages(['status' => 'published']);
    }

    public function getDraftPackages()
    {
        return $this->getPackages(['status' => 'draft']);
    }

    public function getPackages(array $filters = [], bool $paginate = false, int $perPage = 15)
    {
        if ($paginate) {
            return $this->packageRepository->paginate($perPage, $filters);
        }

        return $this->packageRepository->applyFilters($filters);
    }

    public function createPackage(array $data)
    {
        // Handle image upload if present
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Set defaults for fields with database defaults
        if (!isset($data['price']) || $data['price'] === null) {
            $data['price'] = 0;
        }
        if (!isset($data['status']) || $data['status'] === null) {
            $data['status'] = 'draft';
        }

        return $this->packageRepository->create($data);
    }

    public function updatePackage(int $id, array $data)
    {
        $package = $this->packageRepository->findById($id);

        // Handle image update
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->packageRepository->update($id, $data);
    }

    public function deletePackage(int $id)
    {
        return $this->packageRepository->delete($id);
    }

    public function searchPackages(string $term)
    {
        return $this->packageRepository->search($term);
    }

    public function filterPackages(array $filters)
    {
        return $this->packageRepository->applyFilters($filters);
    }

    public function getPaginatedPackages(int $perPage, array $filters = [])
    {
        return $this->packageRepository->paginate($perPage, $filters);
    }

    public function attachCourseToPackage(int $packageId, int $courseId)
    {
        return $this->packageRepository->attachCourse($packageId, $courseId);
    }

    public function detachCourseFromPackage(int $packageId, int $courseId)
    {
        return $this->packageRepository->detachCourse($packageId, $courseId);
    }

    public function syncPackageCourses(int $packageId, array $courseIds)
    {
        return $this->packageRepository->syncCourses($packageId, $courseIds);
    }

    protected function uploadImage($image)
    {
        // If it's an UploadedFile object, store it normally
        if ($image instanceof UploadedFile) {
            return $image->store('packages', 'public');
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
                $filename = 'packages/' . uniqid() . '.' . $extension;

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
