{{-- Product category showcase --}}
@if ($categoryShowcase->isNotEmpty())
<section class="bg-white py-6 sm:py-8">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">

        {{-- ── Category tiles (Responsive: Horizontal Scroll on Mobile, Grid on Desktop) ────────────────── --}}
        <!-- Mobile View (Horizontal Scroll of circular items) -->
        <div class="no-scrollbar -mx-4 flex gap-5 overflow-x-auto px-4 pb-6 lg:hidden">
            @foreach ($categoryShowcase as $category)
                <a href="{{ $category['href'] }}"
                   class="group flex shrink-0 flex-col items-center text-center transition-all duration-200 active:scale-95">
                    <div class="relative flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-ink-100/70 bg-ink-50/50 shadow-sm transition-all duration-300 group-hover:border-brand-500/50 group-hover:bg-brand-50/10">
                        <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}"
                             class="h-full w-full object-contain p-2.5 transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <span class="mt-2 text-xs font-bold text-ink-800 transition-colors group-hover:text-brand-500">{{ $category['name'] }}</span>
                    <span class="mt-0.5 text-[9px] font-medium text-ink-400">{{ $category['count'] }} محصول</span>
                </a>
            @endforeach
        </div>

        <!-- Desktop View (Original beautiful grid layout) -->
        <div class="hidden lg:grid mb-8 grid-cols-4 gap-4">
            @foreach ($categoryShowcase as $category)
                <a href="{{ $category['href'] }}"
                   class="group flex flex-col items-center rounded-2xl border border-ink-100 bg-gradient-to-b from-white to-ink-50/50 p-5 text-center shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-card-hover">
                    <div class="relative mb-3 flex h-28 w-full items-center justify-center overflow-hidden rounded-xl bg-ink-50">
                        <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}"
                             class="h-full w-full object-contain p-4 transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <h3 class="text-base font-bold text-ink-900 transition-colors group-hover:text-brand-500">{{ $category['name'] }}</h3>
                    <span class="mt-1 rounded-full bg-ink-50 px-3 py-0.5 text-xs text-ink-400">{{ $category['count'] }} محصول</span>
                </a>
            @endforeach
        </div>

        {{-- ── "Picked for you" per-category preview cards (Improved mobile layout) ─────────── --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach ($categoryShowcase as $category)
                <div class="group flex flex-col rounded-2xl border border-ink-100 bg-white p-3 sm:p-4 shadow-card transition-all duration-300 hover:shadow-card-hover">
                    
                    {{-- Card Header --}}
                    <div class="mb-3 flex items-start justify-between gap-1.5">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-bold text-ink-900 sm:text-base transition-colors group-hover:text-brand-500">
                                <a href="{{ $category['href'] }}">{{ $category['name'] }}</a>
                            </h3>
                            <p class="mt-0.5 text-[10px] sm:text-[11px] text-ink-400 whitespace-nowrap">بر اساس سلیقه شما</p>
                        </div>
                        @if ($category['icon'])
                            <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500 shadow-sm shadow-brand-500/5">
                                <x-icon :name="$category['icon']" class="h-4.5 w-4.5 sm:h-5 sm:w-5" />
                            </span>
                        @endif
                    </div>

                    {{-- 4 Product Thumbnails --}}
                    <div class="grid grid-cols-2 gap-2">
                        @for ($i = 0; $i < 4; $i++)
                            <a href="{{ $category['href'] }}" class="group/thumb flex aspect-square items-center justify-center overflow-hidden rounded-xl bg-ink-50/40 border border-ink-100/40 hover:border-brand-200 transition-colors">
                                @if (!empty($category['thumbs'][$i]))
                                    <img src="{{ $category['thumbs'][$i] }}" alt="" class="h-full w-full object-contain p-1.5 transition-transform duration-500 group-hover/thumb:scale-105">
                                @else
                                    <x-icon name="grid" class="h-4.5 w-4.5 text-ink-200" />
                                @endif
                            </a>
                        @endfor
                    </div>

                    {{-- Bottom Link --}}
                    <a href="{{ $category['href'] }}"
                       class="mt-3.5 flex items-center justify-center gap-1 text-[11px] font-extrabold text-brand-500 transition-colors hover:text-brand-600">
                        مشاهده همه
                        <x-icon name="chevron-left" class="h-3 w-3" />
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
