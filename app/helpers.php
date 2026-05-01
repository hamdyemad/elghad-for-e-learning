<?php

if (!function_exists('currency_symbol')) {
    /**
     * Get the currency symbol
     *
     * @return string
     */
    function currency_symbol(): string
    {
        return config('currency.symbol', 'د.ل');
    }
}

if (!function_exists('currency_name')) {
    /**
     * Get the currency name
     *
     * @return string
     */
    function currency_name(): string
    {
        return config('currency.name', 'ريال ليبي');
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format an amount with currency symbol
     *
     * @param float|int $amount
     * @param bool $withSymbol
     * @param bool $withSpace
     * @return string
     */
    function format_currency($amount, $withSymbol = true, $withSpace = true): string
    {
        $config = config('currency');
        $formatted = number_format(
            $amount,
            $config['decimal_places'] ?? 2,
            $config['decimal_separator'] ?? '.',
            $config['thousand_separator'] ?? ','
        );

        if ($withSymbol) {
            $symbol = currency_symbol();
            if ($withSpace) {
                return $symbol . ' ' . $formatted;
            }
            return $symbol . $formatted;
        }

        return $formatted;
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Get currency symbol by code (for future multi-currency support)
     *
     * @param string $code
     * @return string
     */
    function get_currency_symbol(string $code): string
    {
        $currencies = [
            'lyd' => 'د.ل',
            'usd' => '$',
            'eur' => '€',
            'sar' => 'ر.س',
            'egp' => 'ج.م',
        ];

        return $currencies[strtolower($code)] ?? currency_symbol();
    }
}
