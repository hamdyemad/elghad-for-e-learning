<?php

namespace App\Interfaces;

interface LessonRepositoryInterface
{
    public function getAll();
    public function getByCourse(int $courseId);
    public function getPublishedByCourse(int $courseId);
    public function getFreeLessons();
    public function findById(int $id);
    public function getByIds(array $ids);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function reorder(array $lessonOrders): bool;
    public function search(string $term, int $courseId = null);
    public function paginate(int $perPage, array $filters = []);
}
