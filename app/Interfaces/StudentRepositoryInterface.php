<?php

namespace App\Interfaces;

interface StudentRepositoryInterface
{
    public function getAll();
    public function getActive();
    public function getInactive();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function search(string $term);
    public function filterByStatus(string $status);
    public function applyFilters(array $filters);
    public function paginate(int $perPage, array $filters = []);
}
