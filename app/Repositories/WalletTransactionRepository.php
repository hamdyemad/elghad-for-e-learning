<?php

namespace App\Repositories;

use App\Interfaces\WalletTransactionRepositoryInterface;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionRepository implements WalletTransactionRepositoryInterface
{
    protected $model;

    public function __construct(WalletTransaction $model)
    {
        $this->model = $model;
    }

    public function getModel(): Builder
    {
        return $this->model->query();
    }

    /**
     * Get transactions for a user with filters
     */
    public function getForUser(int $userId, array $filters = [])
    {
        $query = $this->queryForUser($userId)->with('reference');

        // Apply filters using scopes
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['type'])) {
            $query->type($filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->dateFrom($filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->dateTo($filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get transactions query for a user (for further chaining)
     */
    public function queryForUser(int $userId): Builder
    {
        return $this->model->where('user_id', $userId);
    }

    /**
     * Create a new transaction
     */
    public function create(array $data): WalletTransaction
    {
        return $this->model->create($data);
    }

    /**
     * Get total deposits for a user
     */
    public function getTotalDeposits(int $userId): float
    {
        return (float) $this->model
            ->where('user_id', $userId)
            ->whereIn('type', ['deposit', 'refund', 'bonus'])
            ->sum('amount');
    }

    /**
     * Get total withdrawals for a user (including charges)
     */
    public function getTotalWithdrawals(int $userId): float
    {
        return (float) $this->model
            ->where('user_id', $userId)
            ->whereIn('type', ['withdrawal', 'charge'])
            ->sum('amount');
    }
}
