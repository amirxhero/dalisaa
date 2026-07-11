{{-- Main hero banner slider --}}
<section class="bg-white py-4 sm:py-6">
    <div
        x-data="carousel({ loop: true, autoplay: { delay: 5000, disableOnInteraction: false }, effect: 'fade', fadeEffect: { crossFade: true } })"
        x-init="init()"
        class="relative mx-auto max-w-7xl px-4 lg:px-6"
    >
        <div class="swiper overflow-hidden rounded-2xl" x-ref="track">
            <div class="swiper-wrapper">
                @foreach ($heroSlides as $slide)
                    <div class="swiper-slide">
                        <a href="{{ $slide['href'] }}" class="block aspect-[16/9] w-full overflow-hidden rounded-2xl bg-ink-900 sm:aspect-[21/8]">
                            <picture class="block h-full w-full">
                                <source media="(max-width: 640px)" srcset="{{ $slide['imageMobile'] ?? $slide['image'] }}">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="h-full w-full object-cover">
                            </picture>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination !bottom-4" x-ref="pagination"></div>
        </div>

        <x-carousel-arrows />
    </div>
</section>
