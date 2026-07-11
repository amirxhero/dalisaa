{{-- Modern bottom-sheet for browsing product categories & sub-categories (mobile) --}}
<div
    x-data="{ activeCat: 0 }"
    x-show="categorySheetOpen"
    x-cloak
    @keydown.escape.window="categorySheetOpen = false"
    class="fixed inset-0 z-50 lg:hidden"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="categorySheetOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="categorySheetOpen = false"
        class="absolute inset-0 bg-ink-900/50 backdrop-blur-[2px]"
    ></div>

    <div
        x-show="categorySheetOpen"
        x-transition:enter="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="absolute inset-x-0 bottom-0 flex max-h-[86vh] flex-col overflow-hidden rounded-t-[28px] bg-white shadow-2xl"
    >
        {{-- Drag handle --}}
        <div class="flex shrink-0 justify-center pb-1 pt-2.5">
            <span class="h-1.5 w-10 rounded-full bg-ink-100"></span>
        </div>

        {{-- Header --}}
        <div class="flex shrink-0 items-center justify-between px-5 pb-3">
            <h2 class="text-base font-extrabold text-ink-900">دسته‌بندی محصولات</h2>
            <button type="button" @click="categorySheetOpen = false" class="flex h-8 w-8 items-center justify-center rounded-full bg-ink-50 text-ink-600 transition-colors hover:bg-ink-100">
                <x-icon name="close" class="h-4 w-4" />
            </button>
        </div>

        {{-- Top-level category chips --}}
        <div class="no-scrollbar flex shrink-0 gap-2 overflow-x-auto border-b border-ink-100 px-5 pb-4">
            @foreach ($megaMenu as $i => $section)
                <button
                    type="button"
                    @click="activeCat = {{ $i }}"
                    class="flex shrink-0 flex-col items-center gap-1.5"
                >
                    <span
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border transition-all duration-200"
                        :class="activeCat === {{ $i }} ? 'border-brand-500 bg-brand-500 text-white shadow-lg shadow-brand-500/25 scale-105' : 'border-ink-100 bg-ink-50 text-ink-500'"
                    >
                        <x-icon :name="$section['icon']" class="h-6 w-6" />
                    </span>
                    <span
                        class="max-w-[68px] truncate text-[11px] font-medium transition-colors"
                        :class="activeCat === {{ $i }} ? 'text-brand-500' : 'text-ink-500'"
                    >
                        {{ $section['label'] }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Active category content --}}
        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4">
            @foreach ($megaMenu as $i => $section)
                <div x-show="activeCat === {{ $i }}" x-cloak class="space-y-5">
                    @if (!empty($section['promo']['image']))
                        <a href="{{ $section['promo']['href'] ?? '#' }}" class="group relative block overflow-hidden rounded-2xl bg-ink-900">
                            <img src="{{ $section['promo']['image'] }}" alt="{{ $section['label'] }}" class="h-28 w-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105">
                            <span class="absolute inset-0 bg-linear-to-l from-black/10 via-transparent to-transparent"></span>
                            <span class="absolute bottom-3 right-4 text-sm font-bold text-white drop-shadow">{{ $section['label'] }}</span>
                            @if (!empty($section['promo']['badge']))
                                <span class="absolute left-3 top-3 rounded-full bg-brand-500 px-2.5 py-1 text-[11px] font-bold text-white shadow-sm">
                                    {{ $section['promo']['badge'] }} تخفیف
                                </span>
                            @endif
                        </a>
                    @endif

                    <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                        @foreach ($section['columns'] as $column)
                            <div>
                                <div class="mb-2 flex items-center gap-1.5">
                                    <span class="h-3.5 w-1 rounded-full bg-brand-500"></span>
                                    <h3 class="text-[13px] font-bold text-ink-900">{{ $column['title'] }}</h3>
                                    @if (!empty($column['badge']))
                                        <span class="rounded-full bg-accent-50 px-1.5 py-0.5 text-[10px] font-bold text-accent-600">{{ $column['badge'] }}</span>
                                    @endif
                                </div>
                                <ul class="space-y-2.5">
                                    @foreach ($column['links'] as $link)
                                        <li>
                                            <a href="{{ $link['href'] }}" class="flex items-center justify-between gap-1 text-[12.5px] text-ink-600 transition-colors hover:text-brand-500">
                                                <span class="truncate">{{ $link['label'] }}</span>
                                                <x-icon name="chevron-left" class="h-3 w-3 shrink-0 text-ink-300" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer CTA --}}
        <div class="shrink-0 border-t border-ink-100 p-4 pb-[calc(env(safe-area-inset-bottom)+1rem)]">
            <a href="#" @click="categorySheetOpen = false" class="flex w-full items-center justify-center gap-2 rounded-xl bg-ink-900 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-500">
                مشاهده همه محصولات
                <x-icon name="chevron-left" class="h-4 w-4" />
            </a>
        </div>
    </div>
</div>
