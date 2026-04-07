<?php

namespace App\Interfaces;

interface PackageRepositoryInterface
{
    public function getAll();
    public function getPublished();
    public function getDraft();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function search(string $term);
    public function filterByStatus(string $status);
    public function filterByCategory(int $categoryId);
    public function applyFilters(array $filters);
    public function paginate(int $perPage, array $filters = []);
    public function attachCourse(int $packageId, int $courseId);
    public function detachCourse(int $packageId, int $courseId);
    public function syncCourses(int $packageId, array $courseIds);
}
