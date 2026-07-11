<?php

namespace App\Support;

class Pricing
{
    public const FREE_SHIPPING_THRESHOLD = 3_000_000;
    public const SHIPPING_COST = 45_000;

    public static function shippingCostFor(int $subtotal): int
    {
        return $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0 : self::SHIPPING_COST;
    }
}
