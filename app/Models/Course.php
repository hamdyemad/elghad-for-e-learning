<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'instructor_id',
        'level',
        'duration',
        'thumbnail',
        'status',
        'price',
        'is_free'
    ];

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        return asset('images/default-course.png');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Students enrolled in this course
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student')
            ->withPivot(['wallet_transaction_id', 'enrolled_at', 'expires_at', 'deleted_at'])
            ->withTimestamps()
            ->wherePivot('deleted_at', null)
            ->orderBy('course_student.enrolled_at', 'desc');
    }

    /**
     * Active student enrollments
     */
    public function activeStudents()
    {
        return $this->students()->whereNull('course_student.expires_at')
            ->orWhere('course_student.expires_at', '>', now());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = self::generateUniqueSlug($course->title);
            }
        });

        static::updating(function ($course) {
            if (isset($course->title) && (empty($course->slug) || $course->slug !== Str::slug($course->title))) {
                $course->slug = self::generateUniqueSlug($course->title, $course->id);
            }
        });
    }

    protected static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        // Build query to check for existing slugs
        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->count() > 0) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }
}
