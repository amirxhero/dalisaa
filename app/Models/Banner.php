<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;

    /** Homepage slots a banner can be assigned to. */
    public const POSITIONS = [
        'hero'   => 'اسلایدر اصلی',
        'middle' => 'بنرهای میانی',
        'promo'  => 'بنر تبلیغاتی',
    ];

    protected $fillable = [
        'title',
        'position',
        'link',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('desktop')->singleFile();
        $this->addMediaCollection('mobile')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(200)
            ->nonQueued();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function getPositionLabelAttribute(): string
    {
        return self::POSITIONS[$this->position] ?? $this->position;
    }

    public function getDesktopUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('desktop') ?: '';
    }

    public function getDesktopThumbAttribute(): string
    {
        return $this->getFirstMediaUrl('desktop', 'thumb') ?: $this->desktop_url;
    }

    /** Falls back to the desktop image when no dedicated mobile image is set. */
    public function getMobileUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('mobile') ?: $this->desktop_url;
    }
}
