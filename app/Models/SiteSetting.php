<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'facebook',
        'instagram',
        'tiktok',
        'mobile_number',
        'terms_of_use',
        'privacy_policy',
    ];

    protected $casts = [
        'terms_of_use' => 'string',
        'privacy_policy' => 'string',
    ];

    /**
     * Get the first (or only) site settings record.
     * Creates one if it doesn't exist.
     */
    public static function getSettings(): self
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([]);
        }
        return $settings;
    }
}
