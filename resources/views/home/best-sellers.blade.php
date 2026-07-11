{{-- "پرفروش ترین های" tabbed carousel --}}
<section class="bg-white py-8 sm:py-10" x-data="{ tab: @js(array_key_first($bestSellers)) }">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="mb-5 flex flex-col gap-4 sm:mb-7 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-ink-900 sm:text-2xl">
                    <span class="text-brand-500">پرفروش ترین های</span>
                    <span x-text="tab"></span>
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @foreach (array_keys($bestSellers) as $key)
                    <button
                        type="button"
                        @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'bg-brand-500 text-white border-brand-500' : 'border-ink-100 text-ink-600 hover:border-brand-300'"
                        class="rounded-full border px-4 py-2 text-xs font-medium transition-colors"
                    >
                        {{ $key }}
                    </button>
                @endforeach

                <a href="#" class="hidden shrink-0 items-center gap-1.5 rounded-full border border-ink-100 px-4 py-2 text-xs font-medium text-ink-600 transition-colors hover:border-brand-500 hover:text-brand-500 sm:inline-flex">
                    مشاهده همه
                    <x-icon name="chevron-left" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>

        @foreach ($bestSellers as $key => $products)
            <div x-show="tab === '{{ $key }}'" x-cloak>
                <div
                    x-data="carousel({ slidesPerView: 2.2, spaceBetween: 12, observer: true, observeParents: true, breakpoints: {
                        480: { slidesPerView: 2.4, spaceBetween: 14 },
                        640: { slidesPerView: 3.2, spaceBetween: 16 },
                        1024: { slidesPerView: 4, spaceBetween: 20 },
                    } })"
                    x-init="init()"
                    class="relative"
                >
                    <div class="swiper" x-ref="track">
                        <div class="swiper-wrapper">
                            @foreach ($products as $product)
                                <div class="swiper-slide">
                                    <x-product-card :product="$product" :show-compare="true" class="h-full" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <x-carousel-arrows />
                </div>
            </div>
        @endforeach
    </div>
</section>
