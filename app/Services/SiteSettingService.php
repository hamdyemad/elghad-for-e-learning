<?php

namespace App\Services;

use App\Interfaces\SiteSettingRepositoryInterface;

class SiteSettingService
{
    protected $siteSettingRepository;

    public function __construct(SiteSettingRepositoryInterface $siteSettingRepository)
    {
        $this->siteSettingRepository = $siteSettingRepository;
    }

    /**
     * Get site settings (creates default if not exists)
     */
    public function getSettings(): \App\Models\SiteSetting
    {
        return $this->siteSettingRepository->getFirst();
    }

    /**
     * Update site settings
     */
    public function updateSettings(array $data): \App\Models\SiteSetting
    {
        // Validate URLs if provided
        if (isset($data['facebook']) && $data['facebook'] && !filter_var($data['facebook'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('رابط Facebook غير صالح');
        }
        if (isset($data['instagram']) && $data['instagram'] && !filter_var($data['instagram'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('رابط Instagram غير صالح');
        }
        if (isset($data['tiktok']) && $data['tiktok'] && !filter_var($data['tiktok'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('رابط TikTok غير صالح');
        }

        return $this->siteSettingRepository->updateFirst($data);
    }
}
