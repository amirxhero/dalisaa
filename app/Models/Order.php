<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'در انتظار پرداخت',
        self::STATUS_PROCESSING => 'در حال پردازش',
        self::STATUS_PAID => 'پرداخت شده',
        self::STATUS_SHIPPED => 'ارسال شده',
        self::STATUS_DELIVERED => 'تحویل داده شده',
        self::STATUS_CANCELLED => 'لغو شده',
        self::STATUS_FAILED => 'ناموفق',
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'receiver_name',
        'receiver_mobile',
        'province',
        'city',
        'address_line',
        'postal_code',
        'subtotal',
        'discount_total',
        'shipping_cost',
        'total',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isPaid(): bool
    {
        return !is_null($this->paid_at);
    }

    public static function generateOrderNumber(): string
    {
        return 'SLK-'.now()->format('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    }
}
