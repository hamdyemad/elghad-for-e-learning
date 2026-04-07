<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseStudent extends Pivot
{
    use SoftDeletes;

    protected $table = 'course_student';

    protected $fillable = [
        'user_id',
        'course_id',
        'wallet_transaction_id',
        'enrolled_at',
        'expires_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    public function isActive()
    {
        if ($this->expires_at) {
            return $this->expires_at->isFuture() && !$this->trashed();
        }
        return !$this->trashed();
    }
}
