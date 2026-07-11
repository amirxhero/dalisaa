@props([
    'title',
    'highlight' => null,
    'subtitle' => null,
    'href' => '#',
    'linkLabel' => 'مشاهده همه',
])

<div class="mb-5 flex items-end justify-between gap-3 sm:mb-7">
    <div>
        <h2 class="text-lg font-extrabold text-ink-900 sm:text-2xl">
            @if ($highlight)
                <span class="text-brand-500">{{ $highlight }}</span>
            @endif
            {{ $title }}
        </h2>
        @if ($subtitle)
            <p class="mt-1 text-xs text-ink-400 sm:text-sm">{{ $subtitle }}</p>
        @endif
    </div>

    <a href="{{ $href }}" class="hidden shrink-0 items-center gap-1.5 rounded-full border border-ink-100 px-4 py-2 text-xs font-medium text-ink-600 transition-colors hover:border-brand-500 hover:text-brand-500 sm:inline-flex">
        {{ $linkLabel }}
        <x-icon name="chevron-left" class="h-3.5 w-3.5" />
    </a>
</div>
