<?php

namespace App\Services;

use App\Models\Setting;

class CurrencyService
{
    private static array $rateKeys = [
        'USD'  => 'usd_rate',
        'EUR'  => 'eur_rate',
        'USDT' => 'usdt_rate',
        'GBP'  => 'gbp_rate',
        'IRR'  => null,
    ];

    public static function currencies(): array
    {
        return [
            'USD'  => 'دلار آمریکا',
            'EUR'  => 'یورو',
            'USDT' => 'تتر (USDT)',
            'GBP'  => 'پوند انگلیس',
            'IRR'  => 'تومان',
        ];
    }

    public static function symbols(): array
    {
        return ['USD' => '$', 'EUR' => '€', 'USDT' => '₮', 'GBP' => '£', 'IRR' => '﷼'];
    }

    public static function rate(string $currency): float
    {
        if ($currency === 'IRR') return 1.0;

        $key = self::$rateKeys[$currency] ?? null;
        if (! $key) return 1.0;

        return (float) Setting::get($key, 1);
    }

    public static function toToman(float $amount, string $currency): int
    {
        return (int) round($amount * self::rate($currency));
    }

    public static function formatToman(int $amount): string
    {
        return number_format($amount).' تومان';
    }

    public static function allRates(): array
    {
        $rates = [];
        foreach (array_keys(self::$rateKeys) as $currency) {
            if ($currency === 'IRR') continue;
            $rates[$currency] = self::rate($currency);
        }
        return $rates;
    }
}
