<?php

namespace App\Interfaces;

interface ExamRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function getByCourseId(int $courseId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function paginate(int $perPage, array $filters = []);
}
