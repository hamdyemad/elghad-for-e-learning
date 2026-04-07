@extends('layouts.master')

@section('title', __('auth.deposit_funds'))

@section('content')
<x-breadcrumb
    title="{{ __('auth.deposit_funds') }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => __('auth.wallet'), 'url' => route('wallet.index')],
        ['label' => __('auth.deposit_funds')]
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h5 class="card-title mb-0">{{ __('auth.add_money_to_wallet') }}</h5>
                    </div>
                    <div class="col-md-4 text-start">
                        <a href="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.show', $user->id) : route('wallet.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> {{ __('auth.back_to_wallet') }}
                        </a>
                    </div>
                </div>

                <x-alert type="success" />

                @if($user && $user->id !== Auth::user()->id)
                    <div class="alert alert-info mb-3">
                        <i class="mdi mdi-account-multiple me-2"></i>
                        {{ __('auth.managing_wallet_for') }}: <strong>{{ $user->name }}</strong> ({{ $user->email }})
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle me-2"></i>
                                {{ __('auth.please_fix_errors_below') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.deposit', $user->id) : route('wallet.deposit') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="amount" class="form-label">{{ __('auth.amount_usd') }} <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       step="0.01"
                                       min="0.01"
                                       value="{{ old('amount') }}"
                                       required
                                       autofocus>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('auth.minimum_amount') }}: {{ format_currency(0.01) }}</small>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('auth.description') }}</label>
                                <textarea name="description"
                                          id="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    {{ __('auth.deposited_amount_added') }}
                                </div>
                            </div>

                            <div class="d-flex">
                                <button type="submit" class="btn btn-success mx-1">
                                    <i class="mdi mdi-cash-plus me-2"></i>
                                    {{ __('auth.confirm_deposit') }}
                                </button>
                                <a href="{{ $user && $user->id !== Auth::user()->id ? route('wallet.user.show', $user->id) : route('wallet.index') }}" class="btn btn-secondary mx-1">
                                    <i class="mdi mdi-close me-2"></i>
                                    {{ __('auth.cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">{{ __('auth.current_balance') }}</h6>
                                <h3 class="text-success">{{ format_currency($user && $user->id !== Auth::user()->id ? $user->balance : Auth::user()->balance) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
