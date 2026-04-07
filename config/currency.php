<?php

return [
    // Currency symbol (e.g., د.ل, $, €)
    'symbol' => env('CURRENCY_SYMBOL', 'د.ل'),

    // Currency name (e.g., ريال ليبي, US Dollar, Euro)
    'name' => env('CURRENCY_NAME', 'دينار ليبي'),

    // Currency code (ISO 4217) - for future multi-currency support
    'code' => env('CURRENCY_CODE', 'LYD'),

    // Number format options
    'decimal_separator' => env('CURRENCY_DECIMAL_SEP', '.'),
    'thousand_separator' => env('CURRENCY_THOUSAND_SEP', ','),
    'decimal_places' => env('CURRENCY_DECIMAL_PLACES', 2),
];
