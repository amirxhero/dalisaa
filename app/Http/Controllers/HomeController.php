<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Story;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Base path for the demo banner imagery (marketing-only content that has
     * no database representation, e.g. hero slides / stories / blog posts).
     */
    private const CDN08 = 'https://kaveh.moeinwp.com/1/wp-content/uploads/2022/08/';

    public function index()
    {
        $bestSellers = $this->bestSellers();

        return view('home', [
            'stories' => $this->stories(),
            'heroSlides' => $this->heroSlides(),
            'bannerGrid' => $this->bannerGrid(),
            'categoryShowcase' => $this->categoryShowcase(),
            'amazingOffers' => $this->amazingOffers(),
            'promoDuo' => $this->promoDuo(),
            'bestSellers' => $bestSellers,
            'compareProducts' => $this->compareProducts($bestSellers),
            'compareFilters' => Category::orderBy('sort_order')->pluck('name')->all(),
            'blogPosts' => $this->blogPosts(),
            'trustBadges' => $this->trustBadges(),
        ]);
    }

    /** Admin-managed story reel, shown Instagram-style at the top of the homepage. */
    private function stories(): array
    {
        return Story::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Story $story) => [
                'id' => $story->id,
                'title' => $story->title,
                'badge' => $story->badge,
                'link' => $story->link,
                'image' => $story->cover_url,
                'slides' => $story->slide_urls,
            ])->all();
    }

    private function heroSlides(): array
    {
        $banners = $this->banners('hero');

        if ($banners->isNotEmpty()) {
            return $banners->map(fn (Banner $b) => [
                'image' => $b->desktop_url,
                'imageMobile' => $b->mobile_url,
                'title' => $b->title,
                'href' => $b->link ?: '#',
            ])->all();
        }

        return [
            [
                'image' => self::CDN08.'slide-01.png',
                'imageMobile' => self::CDN08.'slide-01.png',
                'eyebrow' => 'تبلت های اپل',
                'title' => 'iPad Pro ۱۲.۹ ۲۰۲۲',
                'cta' => 'مشاهده محصول',
                'href' => '#',
            ],
            [
                'image' => 'https://kaveh.moeinwp.com/1/wp-content/uploads/2023/02/Baner-2-1.jpg',
                'imageMobile' => 'https://kaveh.moeinwp.com/1/wp-content/uploads/2023/02/Baner-2-1.jpg',
                'eyebrow' => 'گوشی سامسونگ',
                'title' => 'گلکسی نوت ۱۰',
                'cta' => 'مشاهده محصول',
                'href' => '#',
            ],
            [
                'image' => 'https://kaveh.moeinwp.com/1/wp-content/uploads/2023/02/baner-3.jpg',
                'imageMobile' => 'https://kaveh.moeinwp.com/1/wp-content/uploads/2023/02/baner-3.jpg',
                'eyebrow' => 'ساعت هوشمند',
                'title' => 'گلکسی واچ سری جدید',
                'cta' => 'مشاهده محصول',
                'href' => '#',
            ],
        ];
    }

    /**
     * A flat list of categories to display as grid tiles on the homepage.
     * Leaf (child) categories are shown first; root categories that have no
     * children are appended at the end. Only categories with ≥1 active product
     * are included.
     */
    private function categoryShowcase(): Collection
    {
        $roots = Category::roots()
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $items = collect();

        foreach ($roots as $root) {
            if ($root->children->isNotEmpty()) {
                // Show each subcategory as an individual tile
                foreach ($root->children as $child) {
                    $childIds = $child->getAllCategoryIds();
                    $count    = Product::whereIn('category_id', $childIds)->where('is_active', true)->count();
                    if ($count === 0) continue;

                    $image = $child->image_url;

                    if (!$image) {
                        $image = Product::whereIn('category_id', $childIds)
                            ->where('is_active', true)
                            ->with('media')
                            ->latest('id')
                            ->first()
                            ?->main_thumb ?? asset('images/product-placeholder.svg');
                    }

                    $items->push([
                        'name'  => $child->name,
                        'href'  => route('category.show', $child),
                        'image' => $image,
                        'count' => $this->faDigits($count),
                    ]);
                }
            } else {
                // Root has no children — show it directly as a tile
                $rootIds = $root->getAllCategoryIds();
                $count   = Product::whereIn('category_id', $rootIds)->where('is_active', true)->count();
                if ($count === 0) continue;

                $image = $root->image_url;

                if (!$image) {
                    $image = Product::whereIn('category_id', $rootIds)
                        ->where('is_active', true)
                        ->with('media')
                        ->latest('id')
                        ->first()
                        ?->main_thumb ?? asset('images/product-placeholder.svg');
                }

                $items->push([
                    'name'  => $root->name,
                    'href'  => route('category.show', $root),
                    'image' => $image,
                    'count' => $this->faDigits($count),
                ]);
            }
        }

        return $items;
    }

    /** Convert Latin digits to Persian for display. */
    private function faDigits(int|string $value): string
    {
        return strtr((string) $value, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    /** Active banners for a homepage slot, ordered for display. */
    private function banners(string $position): Collection
    {
        return Banner::query()
            ->active()
            ->position($position)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function bannerGrid(): array
    {
        $demo = [
            'right' => ['image' => self::CDN08.'banner-02.png', 'imageMobile' => self::CDN08.'banner-02.png', 'href' => '#'],
            'left' => ['image' => self::CDN08.'banner-03.png', 'imageMobile' => self::CDN08.'banner-03.png', 'href' => '#'],
            'wide' => ['image' => self::CDN08.'banner-04.png', 'imageMobile' => self::CDN08.'banner-04.png', 'href' => '#', 'title' => 'گوشی موبایل', 'subtitle' => 'جدیدترین های بازار'],
        ];

        $banners = $this->banners('middle')->values();

        if ($banners->isEmpty()) {
            return $demo;
        }

        // Three fixed slots filled in display order (right, left, wide); any
        // slot without a corresponding banner falls back to the demo image so
        // the grid layout is always complete.
        $grid = $demo;
        foreach (['right', 'left', 'wide'] as $i => $slot) {
            if ($banner = $banners->get($i)) {
                $grid[$slot] = [
                    'image' => $banner->desktop_url,
                    'imageMobile' => $banner->mobile_url,
                    'href' => $banner->link ?: '#',
                    'title' => $banner->title,
                ];
            }
        }

        return $grid;
    }

    /** Manually curated "amazing offer" products set from the admin panel. */
    private function amazingOffers(): Collection
    {
        return Product::query()
            ->active()
            ->special()
            ->with(['category', 'variants'])
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    private function promoDuo(): array
    {
        $banners = $this->banners('promo');

        if ($banners->isNotEmpty()) {
            return $banners->map(fn (Banner $b) => [
                'image' => $b->desktop_url,
                'imageMobile' => $b->mobile_url,
                'href' => $b->link ?: '#',
            ])->all();
        }

        return [
            ['image' => self::CDN08.'banner-04.png', 'imageMobile' => self::CDN08.'banner-04.png', 'href' => '#'],
            ['image' => self::CDN08.'banner-05.png', 'imageMobile' => self::CDN08.'banner-05.png', 'href' => '#'],
        ];
    }

    /** Products grouped by category name, ordered by rating (a stand-in for "best selling"). */
    private function bestSellers(): array
    {
        return Category::orderBy('sort_order')
            ->get()
            ->mapWithKeys(function (Category $category) {
                $catIds = $category->getAllCategoryIds();
                $products = Product::query()
                    ->active()
                    ->with(['category', 'variants'])
                    ->whereIn('category_id', $catIds)
                    ->orderByDesc('reviews_count_cache')
                    ->orderByDesc('rating_cache')
                    ->limit(8)
                    ->get();

                return [$category->name => $products];
            })
            ->filter(fn (Collection $products) => $products->isNotEmpty())
            ->all();
    }

    private function compareProducts(array $bestSellers): Collection
    {
        return collect($bestSellers)->flatten(1)->shuffle()->take(15)->values();
    }

    private function blogPosts(): Collection
    {
        return Post::published()
            ->latest('published_at')
            ->limit(2)
            ->get();
    }

    private function trustBadges(): array
    {
        return [
            ['icon' => 'truck', 'title' => 'تحویل اکسپرس'],
            ['icon' => 'cash', 'title' => 'پرداخت در محل'],
            ['icon' => 'return', 'title' => 'هفت روز ضمانت بازگشت'],
            ['icon' => 'shield', 'title' => 'ضمانت کالا'],
            ['icon' => 'headset', 'title' => 'پشتیبانی آنلاین'],
        ];
    }
}
