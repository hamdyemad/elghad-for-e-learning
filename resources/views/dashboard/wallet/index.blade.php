@extends('layouts.master')

@section('title', __('auth.wallet'))

@section('content')
<x-breadcrumb
    title="{{ __('auth.wallet') }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => __('auth.wallet')]
    ]"
/>

<div class="row">
    <div class="col-12">
        @if($user && $user->id !== Auth::user()->id)
            <div class="alert alert-info mb-3">
                <i class="mdi mdi-account-multiple me-2"></i>
                {{ __('auth.managing_wallet_for') }}: <strong>{{ $user->name }}</strong> ({{ $user->email }})
            </div>
        @endif

        <!-- Balance Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2 opacity-75">{{ __('auth.current_balance') }}</h6>
                                <h3 class="card-title mb-0">{{ format_currency($balance) }}</h3>
                            </div>
                            <i class="mdi mdi-wallet font-size-32 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2 opacity-75">{{ __('auth.total_deposits') }}</h6>
                                <h3 class="card-title mb-0">{{ format_currency($totalDeposits) }}</h3>
                            </div>
                            <i class="mdi mdi-arrow-down-circle font-size-32 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2 opacity-75">{{ __('auth.total_withdrawals') }}</h6>
                                <h3 class="card-title mb-0">{{ format_currency($totalWithdrawals) }}</h3>
                            </div>
                            <i class="mdi mdi-arrow-up-circle font-size-32 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('auth.transaction_history') }}</h5>
                    <div>
                        <a href="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.deposit.form', $user->id) : route('wallet.deposit.form') }}" class="btn btn-success mx-1">
                            <i class="mdi mdi-cash-plus"></i> {{ __('auth.deposit') }}
                        </a>
                        <a href="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.withdraw.form', $user->id) : route('wallet.withdraw.form') }}" class="btn btn-warning mx-1">
                            <i class="mdi mdi-cash-minus"></i> {{ __('auth.withdraw') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card bg-light mb-3">
            <div class="card-body">
                <form method="GET" action="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.show', $user->id) : route('wallet.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <x-form-input
                                name="search"
                                label="{{ __('auth.search') }}"
                                :value="request('search')"
                                placeholder="{{ __('auth.search_placeholder') }}"
                                :compact="true"
                            />
                        </div>
                        <div class="col-md-2">
                            <x-form-select
                                name="type"
                                label="{{ __('auth.type') }}"
                                :value="request('type')"
                                :options="['' => __('auth.all_types'), 'deposit' => __('auth.deposit'), 'withdrawal' => __('auth.withdrawal'), 'refund' => __('auth.refund'), 'charge' => __('auth.charge'), 'bonus' => __('auth.bonus')]"
                                :compact="true"
                            />
                        </div>
                        <div class="col-md-2">
                            <x-form-select
                                name="status"
                                label="{{ __('auth.status') }}"
                                :value="request('status')"
                                :options="['' => __('All Statuses'), 'pending' => __('auth.pending'), 'completed' => __('auth.completed'), 'failed' => __('auth.transaction_failed')]"
                                :compact="true"
                            />
                        </div>
                        <div class="col-md-2">
                            <x-form-input
                                name="date_from"
                                label="{{ __('auth.date_from') }}"
                                type="date"
                                :value="request('date_from')"
                                :compact="true"
                            />
                        </div>
                        <div class="col-md-2">
                            <x-form-input
                                name="date_to"
                                label="{{ __('auth.date_to') }}"
                                type="date"
                                :value="request('date_to')"
                                :compact="true"
                            />
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mx-1">
                                    <i class="mdi mdi-magnify"></i> {{ __('auth.filter') }}
                                </button>
                                <a href="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.show', $user->id) : route('wallet.index') }}" class="btn btn-secondary mx-1">
                                    <i class="mdi mdi-refresh"></i> {{ __('auth.reset') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-body">
                <x-alert type="success" />

                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('auth.date') }}</th>
                                    <th>{{ __('auth.type') }}</th>
                                    <th>{{ __('auth.status') }}</th>
                                    <th>{{ __('auth.amount') }}</th>
                                    <th>{{ __('auth.description') }}</th>
                                    <th>{{ __('auth.reference') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($transaction->type) {
                                                    'deposit', 'refund', 'bonus' => 'bg-success',
                                                    'withdrawal', 'charge' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                $typeLabel = match($transaction->type) {
                                                    'deposit' => __('auth.deposit'),
                                                    'withdrawal' => __('auth.withdrawal'),
                                                    'refund' => __('auth.refund'),
                                                    'charge' => __('auth.charge'),
                                                    'bonus' => __('auth.bonus'),
                                                    default => $transaction->type
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadgeClass = match($transaction->status) {
                                                    'completed' => 'bg-success',
                                                    'pending' => 'bg-warning',
                                                    'failed' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                $statusLabel = match($transaction->status) {
                                                    'completed' => __('auth.completed'),
                                                    'pending' => __('auth.pending'),
                                                    'failed' => __('auth.transaction_failed'),
                                                    default => $transaction->status
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $amountColorClass = 'text-muted';
                                                if ($transaction->status === 'completed') {
                                                    $amountColorClass = in_array($transaction->type, ['deposit', 'refund', 'bonus']) ? 'text-success' : 'text-danger';
                                                } elseif ($transaction->status === 'failed') {
                                                    $amountColorClass = 'text-danger text-decoration-line-through';
                                                }
                                            @endphp
                                            <strong class="{{ $amountColorClass }}">
                                                {{ in_array($transaction->type, ['deposit', 'refund', 'bonus']) ? '+' : '-' }}
                                                {{ format_currency($transaction->amount) }}
                                            </strong>
                                        </td>
                                        <td>{{ $transaction->description ?? '-' }}</td>
                                        <td>
                                            @if($transaction->gateway)
                                                <div class="mb-1">
                                                    <span class="badge badge-soft-info">{{ strtoupper($transaction->gateway) }}</span>
                                                </div>
                                            @endif
                                            <small class="text-muted">{{ $transaction->reference_label }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="mdi mdi-wallet-off font-size-48 text-muted mb-3"></i>
                        <p class="text-muted mb-0">{{ __('auth.no_filtered_transactions') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
