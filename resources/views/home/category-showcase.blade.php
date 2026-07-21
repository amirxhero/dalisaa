{{-- Category showcase — clean grid tiles matching reference design --}}
@if ($categoryShowcase->isNotEmpty())
<section class="bg-white py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">

        {{-- Section header --}}
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-500 text-white shadow-sm">
                    <x-icon name="grid" class="h-4 w-4" />
                </span>
                <h2 class="text-base font-extrabold text-brand-600 sm:text-lg">دسته‌بندی‌های منتخب</h2>
            </div>
            <a href="#"
               class="flex items-center gap-1 text-xs font-semibold text-ink-400 transition-colors hover:text-brand-500">
                مشاهده همه
                <x-icon name="chevron-left" class="h-3 w-3" />
            </a>
        </div>

        {{-- Category grid — 4 cols desktop / 2 cols mobile --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($categoryShowcase as $cat)
                <a href="{{ $cat['href'] }}"
                   class="group flex flex-col items-center text-center transition-all duration-200 active:scale-95 hover:opacity-90">

                    {{-- Image tile --}}
                    <div class="relative w-full overflow-hidden rounded-2xl bg-ink-50 sm:rounded-3xl"
                         style="aspect-ratio: 1 / 1;">
                        <img
                            src="{{ $cat['image'] }}"
                            alt="{{ $cat['name'] }}"
                            loading="lazy"
                            class="absolute inset-0 h-full w-full object-contain p-4 transition-transform duration-500 group-hover:scale-110 sm:p-5"
                        >
                    </div>

                    {{-- Name --}}
                    <p class="mt-2.5 text-xs font-bold leading-snug text-ink-800 transition-colors group-hover:text-brand-500 sm:text-sm">
                        {{ $cat['name'] }}
                    </p>
                    <p class="mt-0.5 text-[10px] text-ink-400">{{ $cat['count'] }} محصول</p>

                </a>
            @endforeach
        </div>

    </div>
</section>
@endif
