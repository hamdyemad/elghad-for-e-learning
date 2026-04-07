<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
    public function getAll();
    public function getAllActive();
    public function getParents();
    public function getChildren($parentId);
    public function findById(int $id);
    public function findBySlug(string $slug);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function search(string $term);
    public function existsBySlug(string $slug, ?int $excludeId = null): bool;
    public function getWithChildren();
    public function reorder(array $orderData);
    public function filterByStatus(string $status);
    public function filterByParentId($parentId);
    public function applyFilters(array $filters);
    public function paginate(int $perPage, array $filters = []);
}
