<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CourseSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'pdf_url',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getPdfUrlAttribute(): string
    {
        if ($this->attributes['pdf_url'] && str_starts_with($this->attributes['pdf_url'], 'http')) {
            return $this->attributes['pdf_url'];
        }

        return asset('storage/' . $this->attributes['pdf_url']);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%");
        });
    }
}
