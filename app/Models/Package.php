<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'category_id',
        'image',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'package_course')->withTimestamps();
    }

    /**
     * Students subscribed to this package
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'package_student')
            ->withPivot(['wallet_transaction_id', 'subscribed_at', 'expires_at', 'deleted_at'])
            ->withTimestamps()
            ->wherePivot('deleted_at', null)
            ->orderBy('package_student.subscribed_at', 'desc');
    }

    /**
     * Active subscriptions
     */
    public function activeStudents()
    {
        return $this->students()->whereNull('package_student.expires_at')
            ->orWhere('package_student.expires_at', '>', now());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = self::generateUniqueSlug($package->title);
            }
        });

        static::updating(function ($package) {
            if (isset($package->title) && (empty($package->slug) || $package->slug !== Str::slug($package->title))) {
                $package->slug = self::generateUniqueSlug($package->title, $package->id);
            }
        });
    }

    protected static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        $query = static::withTrashed()->where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->count() > 0) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = static::withTrashed()->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }
}
