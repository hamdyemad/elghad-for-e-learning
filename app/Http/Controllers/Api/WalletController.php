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
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;
use App\Services\TlyncService;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    use ApiResponseTrait;

    protected $walletService;
    protected $tlyncService;

    public function __construct(WalletService $walletService, TlyncService $tlyncService)
    {
        $this->walletService = $walletService;
        $this->tlyncService = $tlyncService;
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

    /**
     * Initiate Tlync top-up
     * POST /api/wallet/topup/initiate
     */
    public function initiateTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'phone' => 'nullable|string',
            'frontend_url' => 'nullable|url',
        ]);

        $user = Auth::user();
        $phone = $request->phone ?? $user->phone;

        // Simple formatting for Libyan numbers if they start with 218 or +218
        if ($phone) {
            $phone = preg_replace('/^\+?218/', '0', $phone);
            $phone = preg_replace('/^00218/', '0', $phone);
        }

        $customRef = Str::uuid()->toString();

        \Log::info('Tlync Send', [
            'amount' => $request->amount,
            'phone' => $phone ?? '0911234567', // Fallback to a valid format if none exists
            'email' => $user->email,
            'backend_url' => route('payment.tlync.webhook'),
            'frontend_url' => $request->frontend_url ?? config('app.url'),
            'custom_ref' => $customRef,
        ]);

        $data = [
            'amount' => $request->amount,
            'phone' => $phone ?? '0911234567', // Fallback to a valid format if none exists
            'email' => $user->email,
            'backend_url' => route('payment.tlync.webhook'),
            'frontend_url' => $request->frontend_url ?? config('app.url'),
            'custom_ref' => $customRef,
        ];

        $response = $this->tlyncService->initiatePayment($data);

        if (isset($response['result']) && $response['result'] === 'success') {
            // Log the initiation in wallet_transactions as pending
            $this->walletService->deposit(
                $user,
                $request->amount,
                'deposit',
                __('Tlync Top-up Initiation'),
                $customRef,
                null,
                'pending',
                'tlync'
            );

            return $this->successResponse($response, __('Payment initiated successfully'));
        }

        return $this->errorResponse($response['message'] ?? __('Failed to initiate payment'), $response, 400);
    }

    /**
     * Tlync Webhook
     * POST /api/payment/tlync/webhook
     */
    public function tlyncWebhook(Request $request)
    {
        // Tlync sends a POST request to this URL
        // Typically it contains custom_ref, amount, and status

        $data = $request->all();
        \Log::info('Tlync Webhook Received', $data);

        // Verify the payment status (based on Tlync documentation which I don't have fully, 
        // but usually there's a status field)

        if (isset($data['result']) && $data['result'] === 'success') {
            $customRef = $data['custom_ref'];

            // Find the pending transaction
            $transaction = \App\Models\WalletTransaction::where('reference_id', $customRef)
                ->where('gateway', 'tlync')
                ->where('status', 'pending')
                ->first();

            if ($transaction) {
                $user = $transaction->user;

                DB::transaction(function () use ($transaction, $user, $data) {
                    // Mark as completed and store meta data
                    $transaction->update([
                        'status' => 'completed',
                        'meta_data' => array_merge($transaction->meta_data ?? [], [
                            'tlync_ref' => $data['our_ref'] ?? null,
                            'payment_method' => $data['payment_method_en'] ?? null,
                            'charges' => $data['charges'] ?? 0,
                            'net_amount' => $data['net_amount'] ?? 0,
                            'raw_response' => $data
                        ])
                    ]);

                    // Increment user balance
                    $user->increment('balance', $transaction->amount);
                });

                \Log::info('Tlync Payment Completed', ['transaction_id' => $transaction->id, 'user_id' => $user->id]);
            }
        } elseif (isset($data['result']) && $data['result'] === 'failed') {
            $customRef = $data['custom_ref'];
            \App\Models\WalletTransaction::where('reference_id', $customRef)
                ->where('gateway', 'tlync')
                ->where('status', 'pending')
                ->update(['status' => 'failed']);
        }

        return response()->json(['status' => 'received']);
    }
}
