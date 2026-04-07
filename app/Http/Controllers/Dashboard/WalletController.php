<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Models\User;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\StoreWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get the target user for wallet operations
     * If a User model is passed via route binding, use it; otherwise use authenticated user
     */
    private function getTargetUser(?User $routeUser = null): User
    {
        return $routeUser ?: Auth::user();
    }

    /**
     * Display wallet with balance and transactions (for self)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $targetUser = $this->getTargetUser($user);

        // Recalculate balance from transactions to ensure consistency
        $this->walletService->recalculateBalance($targetUser);

        $transactions = $this->walletService->getPaginatedTransactionsWithFilters($targetUser, $request);
        $balance = $this->walletService->getBalance($targetUser);
        $totalDeposits = $this->walletService->getTotalDeposits($targetUser);
        $totalWithdrawals = $this->walletService->getTotalWithdrawals($targetUser);

        return view('dashboard.wallet.index', compact(
            'user',
            'transactions',
            'balance',
            'totalDeposits',
            'totalWithdrawals'
        ));
    }

    /**
     * Display any user's wallet (admin only)
     */
    public function show(Request $request, User $user)
    {
        // Recalculate balance from transactions to ensure consistency
        $this->walletService->recalculateBalance($user);

        $transactions = $this->walletService->getPaginatedTransactionsWithFilters($user, $request);
        $balance = $this->walletService->getBalance($user);
        $totalDeposits = $this->walletService->getTotalDeposits($user);
        $totalWithdrawals = $this->walletService->getTotalWithdrawals($user);

        return view('dashboard.wallet.index', compact(
            'user',
            'transactions',
            'balance',
            'totalDeposits',
            'totalWithdrawals'
        ));
    }

    /**
     * Show deposit form (for self or admin)
     */
    public function createDeposit(?User $user = null)
    {
        return view('dashboard.wallet.deposit', compact('user'));
    }

    /**
     * Store deposit (for self or admin)
     */
    public function storeDeposit(StoreDepositRequest $request, ?User $user = null)
    {
        $targetUser = $this->getTargetUser($user);
        $validated = $request->validated();

        try {
            $transaction = $this->walletService->deposit(
                $targetUser,
                $validated['amount'],
                'deposit',
                $validated['description'] ?? __('auth.deposit')
            );

            $message = __('auth.successfully_deposited', ['amount' => $validated['amount']]);

            // Redirect based on context (admin vs self)
            if ($user) {
                return redirect()->route('wallet.user.show', $user->id)
                    ->with('success', $message);
            }

            return redirect()->route('wallet.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }

    /**
     * Show withdraw form (for self or admin)
     */
    public function createWithdraw(?User $user = null)
    {
        return view('dashboard.wallet.withdraw', compact('user'));
    }

    /**
     * Store withdraw (for self or admin)
     */
    public function storeWithdraw(StoreWithdrawalRequest $request, ?User $user = null)
    {
        $targetUser = $this->getTargetUser($user);
        $validated = $request->validated();

        try {
            $this->walletService->withdraw(
                $targetUser,
                $validated['amount'],
                'withdrawal',
                $validated['description'] ?? __('auth.withdrawal')
            );

            $message = __('auth.successfully_withdrew', ['amount' => $validated['amount']]);

            // Redirect based on context (admin vs self)
            if ($user) {
                return redirect()->route('wallet.user.show', $user->id)
                    ->with('success', $message);
            }

            return redirect()->route('wallet.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }
}
