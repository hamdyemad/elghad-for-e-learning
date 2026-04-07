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

    'failed' => 'بيانات الدخول غير صحيحة.',
    'throttle' => 'تم تجاوز الحد الأقصى لمحاولات الدخول. يرجى المحاولة مرة أخرى خلال :seconds ثانية.',

    // Registration
    'register_success' => 'تم التسجيل بنجاح! يرجى التحقق من بريدك الإلكتروني لاستلام رمز التحقق.',
    'email_exists_unverified' => 'هناك حساب مسجل بهذا البريد ولكن لم يتم تفعيله. يرجى التحقق من بريدك لاستلام رمز التفعيل أو طلب رمز جديد.',
    'email_exists_verified' => 'هناك حساب مسجل بهذا البريد الإلكتروني. يرجى تسجيل الدخول أو استخدام وظيفة استعادة كلمة المرور إذا نسيتها.',

    // Login
    'login_success' => 'تم تسجيل الدخول بنجاح',
    'invalid_credentials' => 'بيانات الدخول غير صحيحة',
    'email_not_verified' => 'يرجى التحقق من البريد الإلكتروني أولاً',
    'account_inactive' => 'حسابك غير مفعل. يرجى التواصل مع الدعم.',

    // Logout
    'logout_success' => 'تم تسجيل الخروج بنجاح',

    // Profile
    'user_retrieved' => 'تم استرجاع المستخدم بنجاح',

    // Email Verification
    'email_verified' => 'تم التحقق من البريد الإلكتروني بنجاح',
    'verification_code_resent' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني',
    'verification_status_retrieved' => 'تم استرجاع حالة التحقق',
    'already_verified' => 'البريد الإلكتروني محقق بالفعل',
    'invalid_or_expired_code' => 'رمز التحقق غير صالح أو منتهي الصلاحية',

    // Password Reset
    'reset_code_sent' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني',
    'reset_success' => 'تم إعادة تعيين كلمة المرور بنجاح',
    'reset_failed' => 'فشلت عملية إعادة تعيين كلمة المرور',
    'invalid_email' => 'عنوان بريد إلكتروني غير صالح',

    // General
    'user_not_found' => 'المستخدم غير موجود',

    // Wallet
    'wallet' => 'المحفظة',
    'balance' => 'الرصيد',
    'current_balance' => 'الرصيد الحالي',
    'total_deposits' => 'إجمالي الإيداعات',
    'total_withdrawals' => 'إجمالي السحوبات',
    'transaction_history' => 'سجل المعاملات',
    'no_transactions_found' => 'لا توجد معاملات',
    'deposit' => 'إيداع',
    'withdraw' => 'سحب',
    'deposit_funds' => 'إضافة رصيد',
    'withdraw_funds' => 'سحب رصيد',
    'add_money_to_wallet' => 'إضافة أموال إلى المحفظة',
    'withdraw_from_wallet' => 'سحب من المحفظة',
    'back_to_wallet' => 'العودة للمحفظة',
    'amount_usd' => 'المبلغ',
    'description' => 'الوصف',
    'minimum_amount' => 'الحد الأدنى للمبلغ',
    'deposited_amount_added' => 'سيتم إضافة المبلغ المودع إلى رصيد محفظتك فوراً',
    'withdrawal_will_deduct' => 'سيتم خصم المبلغ من رصيد محفظتك فوراً',
    'confirm_deposit' => 'تأكيد الإيداع',
    'confirm_withdrawal' => 'تأكيد السحب',
    'insufficient_balance' => 'رصيد غير كافي',
    'maximum_available_balance' => 'الحد الأقصى للرصيد المتاح',
    'successfully_deposited' => 'تم إيداع :amount بنجاح في محفظتك',
    'successfully_withdrew' => 'تم سحب :amount بنجاح',
    'please_fix_errors_below' => 'رجاءً اصلح الأخطاء بالأسفل',
    'amount_must_be_greater_than_zero' => 'يجب أن يكون المبلغ أكبر من صفر',
    'date' => 'التاريخ',
    'type' => 'النوع',
    'reference' => 'المرجع',
    'deposit' => 'إيداع',
    'withdrawal' => 'سحب',
    'refund' => 'استرداد',
    'charge' => 'خصم',
    'bonus' => 'مكافأة',
    'view_wallet' => 'عرض المحفظة',
    'managing_wallet_for' => 'إدارة محفظة',
    'cancel' => 'إلغاء',
    'search' => 'بحث',
    'search_placeholder' => 'ابحث بالوصف أو المرجع...',
    'all_types' => 'جميع الأنواع',
    'date_from' => 'من تاريخ',
    'date_to' => 'إلى تاريخ',
    'filter' => 'تصفية',
    'reset' => 'إعادة تعيين',
    'no_filtered_transactions' => 'لا توجد معاملات تطابق الفلاتر',

    // Subscriptions
    'already_enrolled' => 'أنت مسجل بالفعل في هذه الدورة',
    'already_subscribed' => 'أنت مشترك بالفعل في هذه الباقة',
    'not_enrolled' => 'أنت غير مسجل في هذه الدورة',
    'not_subscribed' => 'أنت غير مشترك في هذه الباقة',
    'insufficient_balance_subscription' => 'رصيد المحفظة غير كافي. المطلوب: :amount ' . config('currency.symbol'),
    'course_subscription' => 'اشتراك دورة: :title',
    'package_subscription' => 'اشتراك باقة: :title',

];
