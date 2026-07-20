<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'brand_id',
        'brand',
        'title',
        'name_en',
        'slug',
        'sku',
        'description',
        'price',
        'price_currency',
        'price_original',
        'regular_price',
        'discount_type',
        'discount_value',
        'stock',
        'highlights',
        'specs',
        'rating_cache',
        'reviews_count_cache',
        'is_active',
        'is_special',
    ];

    protected function casts(): array
    {
        return [
            'highlights'     => 'array',
            'specs'          => 'array',
            'is_active'      => 'boolean',
            'is_special'     => 'boolean',
            'rating_cache'   => 'float',
            'price_original' => 'float',
        ];
    }

    public function getPriceCurrencyLabelAttribute(): string
    {
        return match($this->price_currency) {
            'USD'  => 'دلار',
            'EUR'  => 'یورو',
            'USDT' => 'تتر',
            'GBP'  => 'پوند',
            default => 'تومان',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(500)
            ->height(500)
            ->nonQueued();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function getBrandNameAttribute(): string
    {
        return $this->brand?->title ?? (is_string($this->attributes['brand'] ?? null) ? $this->attributes['brand'] : '—');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSpecial($query)
    {
        return $query->where('is_special', true);
    }

    public function getMainImageAttribute(): string
    {
        return $this->getFirstMediaUrl('gallery') ?: asset('images/product-placeholder.svg');
    }

    public function getMainThumbAttribute(): string
    {
        return $this->getFirstMediaUrl('gallery', 'thumb') ?: $this->main_image;
    }

    public function getGalleryUrlsAttribute(): array
    {
        $urls = $this->getMedia('gallery')->map(fn (Media $media) => $media->getUrl())->values()->all();

        return $urls ?: [$this->main_image];
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->regular_price || $this->regular_price <= $this->price) {
            return 0;
        }

        return (int) round((($this->regular_price - $this->price) / $this->regular_price) * 100);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getColorsAttribute(): array
    {
        return $this->variants->map(fn (ProductVariant $variant) => [
            'name' => $variant->color_name,
            'hex' => $variant->color_hex,
        ])->all();
    }
}
