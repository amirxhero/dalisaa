{{-- Full product grid, encouraging comparison --}}
<section class="bg-white py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <x-section-heading title="محصولات منتخب" highlight="جدیدترین" subtitle="بر اساس بازدید و علاقه‌مندی کاربران" link-label="مشاهده فروشگاه" />

        <div class="mb-6 flex flex-wrap gap-2">
            @foreach ($compareFilters as $i => $filter)
                <button
                    type="button"
                    @class([
                        'rounded-full border px-4 py-2 text-xs font-medium transition-colors',
                        'border-brand-500 bg-brand-500 text-white' => $i === 0,
                        'border-ink-100 text-ink-600 hover:border-brand-300' => $i !== 0,
                    ])
                >
                    {{ $filter }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($compareProducts as $product)
                <x-product-card :product="$product" :show-compare="true" />
            @endforeach
        </div>
    </div>
</section>
