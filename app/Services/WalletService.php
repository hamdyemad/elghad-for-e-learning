<?php

namespace App\Services;

use App\Interfaces\WalletTransactionRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletService
{
    protected $transactionRepository;

    public function __construct(WalletTransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Add money to user's wallet
     *
     * @param User $user
     * @param float|int $amount
     * @param string $type
     * @param string|null $description
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return \App\Models\WalletTransaction
     * @throws \Exception
     */
    public function deposit(User $user, $amount, $type = 'deposit', ?string $description = null, ?string $referenceId = null, ?string $referenceType = null, $status = 'completed', $gateway = null)
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $referenceId, $referenceType, $status, $gateway) {
            // Update user balance ONLY if status is completed
            if ($status === 'completed') {
                $user->increment('balance', $amount);
            }

            // Create transaction record via repository
            return $this->transactionRepository->create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'status' => $status,
                'gateway' => $gateway,
            ]);
        });
    }

    /**
     * Deduct money from user's wallet
     *
     * @param User $user
     * @param float|int $amount
     * @param string $type
     * @param string|null $description
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return \App\Models\WalletTransaction
     * @throws \Exception
     */
    public function withdraw(User $user, $amount, $type = 'withdrawal', ?string $description = null, ?string $referenceId = null, ?string $referenceType = null, $status = 'completed', $gateway = null)
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than zero');
        }

        if ($user->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $referenceId, $referenceType, $status, $gateway) {
            // Update user balance ONLY if status is completed
            if ($status === 'completed') {
                $user->decrement('balance', $amount);
            }

            // Create transaction record via repository and return it
            return $this->transactionRepository->create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'status' => $status,
                'gateway' => $gateway,
            ]);
        });
    }

    /**
     * Get user's current balance
     *
     * @param User $user
     * @return float
     */
    public function getBalance(User $user): float
    {
        return (float) $user->balance;
    }

    /**
     * Get user's transaction history with filters
     *
     * @param User|int $user
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTransactions($user, array $filters = [])
    {
        $userId = $user instanceof \App\Models\User ? $user->id : $user;
        return $this->transactionRepository->getForUser($userId, $filters);
    }

    /**
     * Get paginated transactions for dashboard with applied filters
     *
     * @param User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedTransactionsWithFilters(User $user, Request $request)
    {
        $filters = $request->only(['search', 'type', 'status', 'date_from', 'date_to', 'per_page']);
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        return $this->getTransactions($user, $filters);
    }

    /**
     * Get total deposited amount for user
     *
     * @param User $user
     * @return float
     */
    public function getTotalDeposits(User $user): float
    {
        return $this->transactionRepository->getTotalDeposits($user->id);
    }

    /**
     * Get total withdrawn amount for user
     *
     * @param User $user
     * @return float
     */
    public function getTotalWithdrawals(User $user): float
    {
        return $this->transactionRepository->getTotalWithdrawals($user->id);
    }

    /**
     * Recalculate user balance from all wallet transactions
     * Deposits/refunds/bonuses add to balance, withdrawals/charges subtract
     */
    public function recalculateBalance(User $user): void
    {
        $net = $this->transactionRepository->getModel()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->selectRaw("
                SUM(
                    CASE
                        WHEN type IN ('deposit', 'refund', 'bonus') THEN ABS(amount)
                        WHEN type IN ('withdrawal', 'charge') THEN -ABS(amount)
                        ELSE 0
                    END
                ) as net_balance
            ")
            ->value('net_balance') ?? 0;

        $user->update(['balance' => $net]);
    }
}
