<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageStudent extends Pivot
{
    use SoftDeletes;

    protected $table = 'package_student';

    protected $fillable = [
        'user_id',
        'package_id',
        'wallet_transaction_id',
        'subscribed_at',
        'expires_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
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
