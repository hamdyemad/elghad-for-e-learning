<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Models\User;
use App\Http\Resources\WalletTransactionResource;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\FilterTransactionsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;

class WalletController extends Controller
{
    use ApiResponseTrait;

    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get authenticated user's wallet balance and summary
     * GET /api/wallet
     */
    public function index()
    {
        $user = Auth::user();

        return $this->successResponse([
            'balance' => $this->walletService->getBalance($user),
            'total_deposits' => $this->walletService->getTotalDeposits($user),
            'total_withdrawals' => $this->walletService->getTotalWithdrawals($user),
        ], __('Wallet retrieved successfully'));
    }

    /**
     * Get authenticated user's transactions with filters
     * GET /api/wallet/transactions
     */
    public function transactions(FilterTransactionsRequest $request)
    {
        $user = Auth::user();
        $filters = $request->validated();
        $transactions = $this->walletService->getTransactions($user, $filters);

        // Format response with separate pagination key
        return $this->successResponse([
            'transactions' => WalletTransactionResource::collection($transactions->items()),
            'pagination' => $this->formatPagination($transactions),
        ], __('Wallet transactions retrieved successfully'));
    }

    /**
     * Get any user's transactions (admin only)
     * GET /api/wallet/user/{user}/transactions
     */
    public function userTransactions(FilterTransactionsRequest $request, User $user)
    {
        $filters = $request->validated();
        $transactions = $this->walletService->getTransactions($user, $filters);

        // Format response with separate pagination key
        return $this->successResponse([
            'transactions' => WalletTransactionResource::collection($transactions->items()),
            'pagination' => $this->formatPagination($transactions),
        ], __('User wallet transactions retrieved successfully'));
    }

    /**
     * Deposit money to authenticated user's wallet
     * POST /api/wallet/deposit
     */
    public function deposit(StoreDepositRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        try {
            $transaction = $this->walletService->deposit(
                $user,
                $validated['amount'],
                'deposit',
                $validated['description'] ?? __('Deposit')
            );

            return $this->successResponse([
                'transaction' => new WalletTransactionResource($transaction),
                'balance' => $this->walletService->getBalance($user),
            ], __('auth.successfully_deposited', ['amount' => $validated['amount']]));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    /**
     * Withdraw money from authenticated user's wallet
     * POST /api/wallet/withdraw
     */
    public function withdraw(StoreWithdrawalRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        try {
            $this->walletService->withdraw(
                $user,
                $validated['amount'],
                'withdrawal',
                $validated['description'] ?? __('Withdrawal')
            );

            return $this->successResponse([
                'balance' => $this->walletService->getBalance($user),
            ], __('auth.successfully_withdrew', ['amount' => $validated['amount']]));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }
}
