{{-- "پیشنهاد شگفت‌انگیز" special / discounted products --}}
@if ($amazingOffers->isNotEmpty())
<section class="my-6 sm:my-8">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="overflow-hidden rounded-[2rem] bg-gradient-to-l from-brand-700 via-brand-600 to-brand-500 p-4 shadow-xl shadow-brand-500/10 ring-1 ring-white/10 sm:p-6">

            {{-- Header --}}
            <div class="mb-5 flex items-center justify-between gap-3 sm:mb-6">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="relative">
                        <!-- Soft pulsing glow behind the percent icon -->
                        <span class="absolute inset-0 animate-ping rounded-full bg-white/20 opacity-30"></span>
                        <img src="{{ asset('static/media/percent.webp') }}" alt="تخفیف" class="relative h-10 w-10 sm:h-14 sm:w-14 shrink-0 object-contain drop-shadow-[0_4px_12px_rgba(255,255,255,0.2)]">
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-2xl font-extrabold text-white whitespace-nowrap">
                            <span class="text-yellow-300">پیشنهاد</span> شگفت‌انگیز
                        </h2>
                        <p class="mt-0.5 text-[10px] sm:text-sm text-brand-100/80 hidden xs:block">تخفیف‌های ویژه و محصولات منتخب</p>
                    </div>
                </div>

                <a href="{{ route('special-offers.index') }}" class="flex shrink-0 items-center gap-0.5 text-[10px] font-bold text-white bg-white/15 backdrop-blur-md border border-white/10 px-2.5 py-1 rounded-full transition-all duration-200 hover:bg-white/20 active:scale-95 sm:gap-1 sm:px-4 sm:py-1.5 sm:text-xs sm:border-white/15">
                    مشاهده همه
                    <x-icon name="chevron-left" class="h-3 w-3 sm:h-3.5 sm:w-3.5" />
                </a>
            </div>

            {{-- Products carousel --}}
            <div
                x-data="carousel({ slidesPerView: 2.1, spaceBetween: 12, breakpoints: {
                    480: { slidesPerView: 2.3, spaceBetween: 12 },
                    640: { slidesPerView: 3.2, spaceBetween: 14 },
                    1024: { slidesPerView: 5, spaceBetween: 16 },
                } })"
                x-init="init()"
                class="relative"
            >
                <div class="swiper !overflow-visible" x-ref="track">
                    <div class="swiper-wrapper">
                        @foreach ($amazingOffers as $product)
                            <div class="swiper-slide">
                                <x-product-card :product="$product" class="h-full !border-none !shadow-md transition-all duration-300 hover:!shadow-lg" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
