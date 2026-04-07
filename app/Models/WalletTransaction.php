<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class WalletTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get reference model (polymorphic relationship)
     */
    public function reference()
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * Get formatted reference label for display
     */
    public function getReferenceLabelAttribute(): string
    {
        if (!$this->reference_type || !$this->reference_id) {
            return '-';
        }

        $class = class_basename($this->reference_type);

        try {
            $model = $this->reference;
            if ($model) {
                $title = $model->title ?? $model->name ?? $model->topic ?? $model->description ?? $model->id;
                if ($model->description && strlen($model->description) > 50) {
                    $title = substr($model->description, 0, 47) . '...';
                }
                return $class . ': ' . $title;
            }
        } catch (\Exception $e) {
            // ignore
        }

        return $class . ' #' . $this->reference_id;
    }

    /**
     * Scope to filter by search term (description, reference_id, reference_type)
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('reference_id', 'like', "%{$search}%")
              ->orWhere('reference_type', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by transaction type
     */
    public function scopeType(Builder $query, ?string $type): Builder
    {
        if (!$type) {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * Scope to filter by date from (inclusive)
     */
    public function scopeDateFrom(Builder $query, ?string $date): Builder
    {
        if (!$date) {
            return $query;
        }

        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * Scope to filter by date to (inclusive)
     */
    public function scopeDateTo(Builder $query, ?string $date): Builder
    {
        if (!$date) {
            return $query;
        }

        return $query->whereDate('created_at', '<=', $date);
    }
}
