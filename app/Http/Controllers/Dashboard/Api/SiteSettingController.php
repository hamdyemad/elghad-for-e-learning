<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingService;
use App\Http\Requests\UpdateSiteSettingsRequest;
use App\Http\Resources\SiteSettingResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    use ApiResponseTrait;

    protected $siteSettingService;

    public function __construct(SiteSettingService $siteSettingService)
    {
        $this->siteSettingService = $siteSettingService;
    }

    /**
     * Get site settings
     * GET /dashboard/settings
     * Requires: admin role
     */
    public function show()
    {
        $settings = $this->siteSettingService->getSettings();

        return $this->successResponse(
            new SiteSettingResource($settings),
            __('site_settings.retrieved_successfully')
        );
    }

    /**
     * Update site settings
     * PUT /dashboard/settings
     * Requires: admin role
     */
    public function update(UpdateSiteSettingsRequest $request)
    {
        $settings = $this->siteSettingService->updateSettings($request->validated());

        return $this->successResponse(
            new SiteSettingResource($settings),
            __('site_settings.updated_successfully')
        );
    }
}
