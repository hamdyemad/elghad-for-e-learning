<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'experience_years' => 'integer',
        'is_instructor' => 'boolean',
        'balance' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = \Str::uuid();
            }
        });
    }

    /**
     * Relationships
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    /**
     * Courses the user is enrolled in (as student)
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->withPivot(['wallet_transaction_id', 'enrolled_at', 'expires_at', 'deleted_at'])
            ->withTimestamps()
            ->wherePivot('deleted_at', null)
            ->orderBy('course_student.enrolled_at', 'desc');
    }

    /**
     * Active course enrollments
     */
    public function activeEnrollments()
    {
        return $this->enrolledCourses()->whereNull('course_student.expires_at')
            ->orWhere('course_student.expires_at', '>', now());
    }

    /**
     * Packages the user is subscribed to
     */
    public function subscribedPackages()
    {
        return $this->belongsToMany(Package::class, 'package_student')
            ->withPivot(['wallet_transaction_id', 'subscribed_at', 'expires_at', 'deleted_at'])
            ->withTimestamps()
            ->wherePivot('deleted_at', null)
            ->orderBy('package_student.subscribed_at', 'desc');
    }

    /**
     * Active package subscriptions
     */
    public function activeSubscriptions()
    {
        return $this->subscribedPackages()->whereNull('package_student.expires_at')
            ->orWhere('package_student.expires_at', '>', now());
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Check if user is admin
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is verified
     */
    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user's full name (if first_name and last_name exist)
     */
    public function getFullNameAttribute(): string
    {
        if (isset($this->first_name) && isset($this->last_name)) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->name;
    }

    /**
     * Get avatar URL (will use gravatar or default)
     */
    public function getAvatarUrlAttribute(): string
    {
        if (isset($this->avatar) && $this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Use Gravatar
        $emailHash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$emailHash}?s=200&d=mp";
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'admin');
        });
    }

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = \Illuminate\Support\Facades\Hash::needsRehash($value) 
                ? \Illuminate\Support\Facades\Hash::make($value) 
                : $value;
        }
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
