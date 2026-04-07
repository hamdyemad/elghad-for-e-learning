<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerificationCode extends Model
{
    protected $fillable = [
        'email',
        'code',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * Check if code is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Scope for valid (non-expired) codes
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }
}
