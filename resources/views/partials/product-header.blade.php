{{-- Minimal header for the product detail page: back button + product title only --}}
<header class="sticky top-0 z-40 border-b border-ink-100 bg-white">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 lg:px-6">
        <a
            href="{{ route('home') }}"
            aria-label="بازگشت به صفحه اصلی"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-ink-50 text-ink-700 transition-colors hover:bg-brand-50 hover:text-brand-500"
        >
            <x-icon name="chevron-right" class="h-5 w-5" />
        </a>

        <h1 class="truncate text-sm font-bold text-ink-900 sm:text-base">
            {{ $product['title'] }}
        </h1>
    </div>
</header>
