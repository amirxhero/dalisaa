{{-- Promotional banner grid --}}
<section class="bg-white py-4 sm:py-6">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        {{-- Top row: two equal columns --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <a href="{{ $bannerGrid['right']['href'] }}" class="group aspect-267/150 overflow-hidden rounded-2xl bg-ink-50 sm:aspect-564/246">
                <picture class="block h-full w-full">
                    <source media="(max-width: 640px)" srcset="{{ $bannerGrid['right']['imageMobile'] ?? $bannerGrid['right']['image'] }}">
                    <img src="{{ $bannerGrid['right']['image'] }}" alt="بنر تبلیغاتی" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                </picture>
            </a>
            <a href="{{ $bannerGrid['left']['href'] }}" class="group aspect-267/150 overflow-hidden rounded-2xl bg-ink-50 sm:aspect-564/246">
                <picture class="block h-full w-full">
                    <source media="(max-width: 640px)" srcset="{{ $bannerGrid['left']['imageMobile'] ?? $bannerGrid['left']['image'] }}">
                    <img src="{{ $bannerGrid['left']['image'] }}" alt="بنر تبلیغاتی" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                </picture>
            </a>
        </div>

        {{-- Bottom row: single wide banner --}}
        <a href="{{ $bannerGrid['wide']['href'] }}" class="group relative mt-3 block aspect-564/200 overflow-hidden rounded-2xl bg-ink-900 sm:mt-4 lg:aspect-auto lg:h-36">
            <picture>
                <source media="(max-width: 640px)" srcset="{{ $bannerGrid['wide']['imageMobile'] ?? $bannerGrid['wide']['image'] }}">
                <img src="{{ $bannerGrid['wide']['image'] }}" alt="{{ $bannerGrid['wide']['title'] ?? 'بنر تبلیغاتی' }}" class="h-full w-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105">
            </picture>
        </a>
    </div>
</section>
