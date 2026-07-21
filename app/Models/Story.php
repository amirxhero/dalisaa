<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Story extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'badge',
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
        $this->addMediaCollection('slides');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->quality(85)
            ->width(160)
            ->height(160)
            ->nonQueued();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getCoverUrlAttribute(): string
    {
        $media = $this->getFirstMedia('slides');
        if ($media && $media->hasGeneratedConversion('thumb')) {
            return $media->getUrl('thumb');
        }

        return $this->getFirstMediaUrl('slides') ?: asset('images/product-placeholder.svg');
    }

    public function getSlideUrlsAttribute(): array
    {
        $urls = $this->getMedia('slides')->map(fn (Media $media) => $media->getUrl())->values()->all();

        return $urls ?: [asset('images/product-placeholder.svg')];
    }
}
