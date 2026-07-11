<?php

namespace App\Support;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class JalaliDate
{
    public static function toGregorian(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        try {
            $normalized = str_replace('-', '/', $value);

            return Jalalian::fromFormat('Y/m/d', $normalized)->toCarbon()->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function fromCarbon(?Carbon $date, string $format = 'Y/m/d'): ?string
    {
        if (! $date) {
            return null;
        }

        return Jalalian::fromCarbon($date)->format($format);
    }
}
