{{-- Paired prev/next controls for a Swiper carousel. Must be placed inside
     an element carrying the `carousel()` Alpine component so x-ref resolves. --}}
<div class="pointer-events-none absolute inset-x-0 top-1/2 z-10 hidden -translate-y-1/2 justify-between px-1 sm:flex">
    <button x-ref="prev" type="button" aria-label="اسلاید بعدی"
        class="pointer-events-auto -mr-4 flex h-9 w-9 items-center justify-center rounded-full border border-ink-100 bg-white text-ink-600 shadow-card transition-colors hover:border-brand-500 hover:text-brand-500 disabled:opacity-30">
        <x-icon name="chevron-right" class="h-4 w-4" />
    </button>
    <button x-ref="next" type="button" aria-label="اسلاید قبلی"
        class="pointer-events-auto -ml-4 flex h-9 w-9 items-center justify-center rounded-full border border-ink-100 bg-white text-ink-600 shadow-card transition-colors hover:border-brand-500 hover:text-brand-500 disabled:opacity-30">
        <x-icon name="chevron-left" class="h-4 w-4" />
    </button>
</div>
