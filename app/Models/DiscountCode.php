<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    const TYPE_PERCENT = 'percent';
    const TYPE_FIXED   = 'fixed';

    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'max_uses', 'uses_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'starts_at'  => 'datetime',
            'expires_at' => 'datetime',
            'value'      => 'float',
        ];
    }

    public function isValid(int $orderTotal = 0): bool
    {
        if (! $this->is_active) return false;
        if ($this->max_uses && $this->uses_count >= $this->max_uses) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->min_order && $orderTotal < $this->min_order) return false;

        return true;
    }

    public function calculateDiscount(int $orderTotal): int
    {
        if ($this->type === self::TYPE_PERCENT) {
            return (int) round($orderTotal * $this->value / 100);
        }

        return (int) min($this->value, $orderTotal);
    }

    public function incrementUses(): void
    {
        $this->increment('uses_count');
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) return 'غیرفعال';
        if ($this->max_uses && $this->uses_count >= $this->max_uses) return 'تمام‌شده';
        if ($this->expires_at && $this->expires_at->isPast()) return 'منقضی';

        return 'فعال';
    }

    public function getValueDisplayAttribute(): string
    {
        if ($this->type === self::TYPE_PERCENT) {
            return $this->value.'٪';
        }

        return number_format($this->value).' تومان';
    }
}
