<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_id',
        'question',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(ExamOption::class, 'question_id');
    }

    public function correctOption()
    {
        return $this->hasOne(ExamOption::class, 'question_id')->where('is_correct', true);
    }
}
