<?php

namespace App\Services;

class CurrencyService
{
    /**
     * Supported currencies with display name and approximate rate from IDR.
     * Rates are indicative — in production connect to an exchange rate API.
     */
    private const CURRENCIES = [
        'IDR' => ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp',  'rate' => 1,       'decimals' => 0],
        'USD' => ['name' => 'US Dollar',          'symbol' => '$',   'rate' => 0.000062, 'decimals' => 2],
        'SGD' => ['name' => 'Singapore Dollar',   'symbol' => 'S$',  'rate' => 0.000083, 'decimals' => 2],
        'EUR' => ['name' => 'Euro',               'symbol' => '€',   'rate' => 0.000057, 'decimals' => 2],
        'AUD' => ['name' => 'Australian Dollar',  'symbol' => 'A$',  'rate' => 0.000095, 'decimals' => 2],
    ];

    public function current(): string
    {
        $code = session('currency', 'IDR');

        return array_key_exists($code, self::CURRENCIES) ? $code : 'IDR';
    }

    public function all(): array
    {
        return self::CURRENCIES;
    }

    /**
     * Format an IDR amount in the customer's preferred currency.
     */
    public function format(int|float|null $amountIdr, ?string $currencyCode = null): string
    {
        $code = $currencyCode ?? $this->current();
        $cfg  = self::CURRENCIES[$code] ?? self::CURRENCIES['IDR'];

        $safeAmount = $amountIdr ?? 0;
        $converted = $safeAmount * $cfg['rate'];

        return $cfg['symbol'] . ' ' . number_format($converted, $cfg['decimals'], '.', ',');
    }

    public function symbol(?string $currencyCode = null): string
    {
        $code = $currencyCode ?? $this->current();

        return self::CURRENCIES[$code]['symbol'] ?? 'Rp';
    }
}
