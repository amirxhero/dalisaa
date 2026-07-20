<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;

/**
 * Header mega-menu / mobile menu / category-sheet navigation data, built
 * dynamically from the category tree and its products. Shared across every
 * page (via a view composer) since the header, mobile menu and category
 * sheet partials are part of the base layout — memoized per request so the
 * three partials don't each re-run the query.
 */
class NavigationMenu
{
    private static ?array $memo = null;

    public static function items(): array
    {
        return self::$memo ??= self::build();
    }

    private static function build(): array
    {
        return Category::roots()
            ->orderBy('sort_order')
            ->get()
            ->map(function (Category $category) {
                $categoryIds = $category->getAllCategoryIds();

                $products = Product::whereIn('category_id', $categoryIds)
                    ->where('is_active', true)
                    ->with('media')
                    ->get();

                if ($products->isEmpty()) {
                    return null;
                }

                $popular = $products
                    ->sortByDesc('rating_cache')
                    ->sortByDesc('reviews_count_cache')
                    ->take(7)
                    ->values();

                $newest = $products->sortByDesc('id')->take(7)->values();

                $promo = $products->firstWhere('is_special', true) ?? $popular->first();

                return [
                    'id'      => $category->slug,
                    'label'   => $category->name,
                    'icon'    => $category->icon ?: 'grid',
                    'columns' => array_values(array_filter([
                        self::column('محبوب‌ترین‌ها', $popular),
                        self::column('جدیدترین‌ها', $newest),
                    ])),
                    'promo'   => [
                        'image' => $promo->main_thumb,
                        'badge' => $promo->discount_percent ? $promo->discount_percent . '%' : null,
                        'href'  => route('product.show', $promo),
                    ],
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /** @param \Illuminate\Support\Collection<int,Product> $products */
    private static function column(string $title, $products): ?array
    {
        if ($products->isEmpty()) {
            return null;
        }

        return [
            'title' => $title,
            'links' => $products->map(fn (Product $p) => [
                'label' => $p->title,
                'href'  => route('product.show', $p),
            ])->all(),
        ];
    }
}
