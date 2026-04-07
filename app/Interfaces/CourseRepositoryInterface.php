<?php

namespace App\Interfaces;

interface CourseRepositoryInterface
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
    public function filterByInstructor(int $instructorId);
    public function applyFilters(array $filters);
    public function paginate(int $perPage, array $filters = []);
}
