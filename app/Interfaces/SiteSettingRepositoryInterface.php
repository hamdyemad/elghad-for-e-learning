<?php

namespace App\Interfaces;

interface SiteSettingRepositoryInterface
{
    public function getFirst(): \App\Models\SiteSetting;
    public function updateFirst(array $data): \App\Models\SiteSetting;
    public function createIfNotExists(array $data = []): \App\Models\SiteSetting;
}
