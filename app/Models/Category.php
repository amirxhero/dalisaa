<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'parent_id',
        'name',
        'name_en',
        'slug',
        'icon',
        'sort_order',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getDescendantsIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantsIds());
        }
        return $ids;
    }

    public function getAllCategoryIds(): array
    {
        return array_merge([$this->id], $this->getDescendantsIds());
    }

    /**
     * Get a flattened collection of categories ordered hierarchically with a 'depth' attribute.
     *
     * @param int|null $excludeId Category ID (and its descendants) to exclude (e.g. for edit parent select)
     */
    public static function getTree(?int $excludeId = null)
    {
        $excludedIds = [];
        if ($excludeId) {
            $excludedIds[] = $excludeId;
            $cat = static::with('children')->find($excludeId);
            if ($cat) {
                $excludedIds = array_merge($excludedIds, $cat->getDescendantsIds());
            }
        }

        $all = static::with(['children', 'parent'])->withCount('products')->orderBy('sort_order')->orderBy('name')->get();

        $flattened = collect();
        $processed = [];

        $traverse = function ($categories, $depth = 0) use (&$traverse, &$flattened, &$processed, $excludedIds) {
            foreach ($categories as $category) {
                if (in_array($category->id, $excludedIds) || in_array($category->id, $processed)) {
                    continue;
                }
                $category->depth = $depth;
                $processed[] = $category->id;
                $flattened->push($category);

                if ($category->children && $category->children->isNotEmpty()) {
                    $traverse($category->children, $depth + 1);
                }
            }
        };

        $roots = $all->whereNull('parent_id');
        $traverse($roots);

        // Fallback for orphan categories whose parent_id might be invalid or non-existent
        $remaining = $all->reject(fn ($c) => in_array($c->id, $processed) || in_array($c->id, $excludedIds));
        if ($remaining->isNotEmpty()) {
            $traverse($remaining, 0);
        }

        return $flattened;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->quality(85)
            ->width(200)
            ->height(200)
            ->nonQueued();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function getImageThumbAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        if ($media && $media->hasGeneratedConversion('thumb')) {
            return $media->getUrl('thumb');
        }

        return $this->image_url;
    }
}
