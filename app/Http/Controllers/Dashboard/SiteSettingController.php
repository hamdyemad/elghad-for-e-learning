<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingService;
use App\Http\Requests\Dashboard\UpdateSiteSettingsRequest;
use App\Traits\ApiResponseTrait;

class SiteSettingController extends Controller
{
    use ApiResponseTrait;

    protected $siteSettingService;

    public function __construct(SiteSettingService $siteSettingService)
    {
        $this->siteSettingService = $siteSettingService;
    }

    /**
     * Show the form for editing site settings
     * GET /dashboard/settings
     */
    public function edit()
    {
        $settings = $this->siteSettingService->getSettings();
        return view('dashboard.settings.edit', compact('settings'));
    }

    /**
     * Update site settings
     * PUT /dashboard/settings
     */
    public function update(UpdateSiteSettingsRequest $request)
    {
        $settings = $this->siteSettingService->updateSettings($request->validated());

        return $this->successRedirect(
            route('dashboard.settings.edit'),
            __('site_settings.updated_successfully')
        );
    }
}
