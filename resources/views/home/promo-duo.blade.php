{{-- Secondary promotional duo banner --}}
<section class="bg-white py-4 sm:py-6">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-3 px-4 sm:grid-cols-2 sm:gap-4 lg:px-6">
        @foreach ($promoDuo as $banner)
            <a href="{{ $banner['href'] }}" class="group aspect-564/246 overflow-hidden rounded-2xl bg-ink-50">
                <picture class="block h-full w-full">
                    <source media="(max-width: 640px)" srcset="{{ $banner['imageMobile'] ?? $banner['image'] }}">
                    <img src="{{ $banner['image'] }}" alt="بنر تبلیغاتی" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                </picture>
            </a>
        @endforeach
    </div>
</section>
