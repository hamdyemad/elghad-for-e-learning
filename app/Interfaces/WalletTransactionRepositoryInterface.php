<?php

namespace App\Interfaces;

use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;

interface WalletTransactionRepositoryInterface
{
    /**
     * Get transactions for a user with filters
     */
    public function getForUser(int $userId, array $filters = []);

    /**
     * Get transactions query for a user (for further chaining)
     */
    public function queryForUser(int $userId): Builder;

    /**
     * Create a new transaction
     */
    public function create(array $data): WalletTransaction;

    /**
     * Get total deposits for a user
     */
    public function getTotalDeposits(int $userId): float;

    /**
     * Get total withdrawals for a user
     */
    public function getTotalWithdrawals(int $userId): float;

    /**
     * Get the underlying model instance
     */
    public function getModel(): \Illuminate\Database\Eloquent\Builder;
}
