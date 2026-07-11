@props([
    'rating' => 0,
    'count' => null,
    'size' => 'h-4 w-4',
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1.5']) }}>
    <div class="flex items-center gap-0.5">
        @for ($i = 1; $i <= 5; $i++)
            <x-icon
                name="star"
                class="{{ $size }} {{ $i <= round($rating) ? 'fill-amber-400 text-amber-400' : 'fill-none text-ink-100' }}"
            />
        @endfor
    </div>
    <span class="text-xs font-bold text-ink-800">{{ number_format($rating, 1) }}</span>
    @if (!is_null($count))
        <span class="text-xs text-ink-400">({{ number_format($count) }} نظر)</span>
    @endif
</div>
