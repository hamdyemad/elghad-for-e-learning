<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'topic',
        'title',
        'outsource_link',
        'outsource_type',
        'is_free',
        'duration',
        'file_pdf',
        'course_id',
        'order',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
