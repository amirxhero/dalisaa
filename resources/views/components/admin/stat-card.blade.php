@props([
    'icon' => 'tabler:chart-bar',
    'label',
    'value',
    'tone' => 'indigo',
    'trend' => null,
    'trendUp' => true,
])

@php
    $toneMap = [
        'indigo'  => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
        'amber'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'sky'     => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600'],
        'rose'    => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
    ];
    $t = $toneMap[$tone] ?? $toneMap['indigo'];
@endphp

<div class="admin-card p-5 transition-shadow hover:shadow-[0_4px_20px_-4px_rgba(15,0,43,0.08)]">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-2 truncate text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if($trend)
            <p class="mt-2 flex items-center gap-1 text-xs font-medium {{ $trendUp ? 'text-emerald-600' : 'text-rose-600' }}">
                <iconify-icon icon="{{ $trendUp ? 'tabler:trending-up' : 'tabler:trending-down' }}" class="text-sm"></iconify-icon>
                {{ $trend }}
            </p>
            @endif
        </div>
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $t['bg'] }} {{ $t['text'] }}">
            <iconify-icon icon="{{ $icon }}" class="text-xl"></iconify-icon>
        </span>
    </div>
</div>
