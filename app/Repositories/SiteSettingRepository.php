<?php

namespace App\Repositories;

use App\Interfaces\SiteSettingRepositoryInterface;
use App\Models\SiteSetting;

class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    protected $model;

    public function __construct(SiteSetting $model)
    {
        $this->model = $model;
    }

    public function getFirst(): SiteSetting
    {
        return $this->model->first() ?? $this->model->create([]);
    }

    public function updateFirst(array $data): SiteSetting
    {
        $settings = $this->getFirst();
        $settings->update($data);
        return $settings->fresh();
    }

    public function createIfNotExists(array $data = []): SiteSetting
    {
        $settings = $this->model->first();
        if (!$settings) {
            $settings = $this->model->create($data);
        }
        return $settings;
    }
}
