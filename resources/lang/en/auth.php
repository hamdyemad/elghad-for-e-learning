<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Registration
    'register_success' => 'Registration successful! Please check your email for the verification code.',
    'email_exists_unverified' => 'An account with this email already exists but has not been verified. Please check your email for the verification code or request a new one.',
    'email_exists_verified' => 'An account with this email already exists. Please login or use forgot password if you need to reset your password.',

    // Login
    'login_success' => 'Login successful',
    'invalid_credentials' => 'Invalid credentials',
    'email_not_verified' => 'Please verify your email first',
    'account_inactive' => 'Your account is inactive. Please contact support.',

    // Logout
    'logout_success' => 'Logout successful',

    // Profile
    'user_retrieved' => 'User retrieved successfully',

    // Email Verification
    'email_verified' => 'Email verified successfully',
    'verification_code_resent' => 'Verification code sent to your email',
    'verification_status_retrieved' => 'Verification status retrieved',
    'already_verified' => 'Email already verified',
    'invalid_or_expired_code' => 'Invalid or expired verification code',

    // Password Reset
    'reset_code_sent' => 'Password reset code sent to your email',
    'reset_success' => 'Password reset successful',
    'reset_failed' => 'Failed to reset password',
    'invalid_email' => 'Invalid email address',

    // General
    'user_not_found' => 'User not found',

    // Wallet
    'wallet' => 'Wallet',
    'balance' => 'Balance',
    'current_balance' => 'Current Balance',
    'total_deposits' => 'Total Deposits',
    'total_withdrawals' => 'Total Withdrawals',
    'transaction_history' => 'Transaction History',
    'no_transactions_found' => 'No transactions found',
    'deposit' => 'Deposit',
    'withdraw' => 'Withdraw',
    'deposit_funds' => 'Deposit Funds',
    'withdraw_funds' => 'Withdraw Funds',
    'add_money_to_wallet' => 'Add Money to Wallet',
    'withdraw_from_wallet' => 'Withdraw from Wallet',
    'back_to_wallet' => 'Back to Wallet',
    'amount_usd' => 'Amount',
    'description' => 'Description',
    'minimum_amount' => 'Minimum amount',
    'deposited_amount_added' => 'Deposited amount will be added to your wallet balance immediately.',
    'withdrawal_will_deduct' => 'Withdrawal will deduct the amount from your wallet balance immediately.',
    'confirm_deposit' => 'Confirm Deposit',
    'confirm_withdrawal' => 'Confirm Withdrawal',
    'insufficient_balance' => 'Insufficient balance',
    'maximum_available_balance' => 'Maximum available balance',
    'successfully_deposited' => 'Successfully deposited :amount to your wallet',
    'successfully_withdrew' => 'Successfully withdrew :amount',
    'minimum_amount' => 'Minimum amount',
    'please_fix_errors_below' => 'Please fix the errors below',
    'amount_must_be_greater_than_zero' => 'Amount must be greater than zero',
    'date' => 'Date',
    'type' => 'Type',
    'reference' => 'Reference',
    'deposit' => 'Deposit',
    'withdrawal' => 'Withdrawal',
    'refund' => 'Refund',
    'charge' => 'Charge',
    'bonus' => 'Bonus',
    'view_wallet' => 'View Wallet',
    'managing_wallet_for' => 'Managing wallet for',
    'cancel' => 'Cancel',
    'search' => 'Search',
    'search_placeholder' => 'Search by description or reference...',
    'all_types' => 'All Types',
    'date_from' => 'Date From',
    'date_to' => 'Date To',
    'filter' => 'Filter',
    'reset' => 'Reset',
    'no_filtered_transactions' => 'No transactions found matching your filters',

    // Subscriptions
    'already_enrolled' => 'You are already enrolled in this course',
    'already_subscribed' => 'You are already subscribed to this package',
    'not_enrolled' => 'You are not enrolled in this course',
    'not_subscribed' => 'You are not subscribed to this package',
    'insufficient_balance_subscription' => 'Insufficient wallet balance. Required: :amount ' . config('currency.symbol'),
    'course_subscription' => 'Course subscription: :title',
    'package_subscription' => 'Package subscription: :title',
];
