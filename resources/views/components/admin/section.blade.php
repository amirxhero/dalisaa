@props(['title' => null, 'subtitle' => null, 'padded' => false])

<div {{ $attributes->merge(['class' => 'admin-card overflow-hidden']) }}>
    @if($title)
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-800">{{ $title }}</h2>
            @if($subtitle)<p class="mt-0.5 text-xs text-gray-400">{{ $subtitle }}</p>@endif
        </div>
        @isset($actions)<div class="shrink-0">{{ $actions }}</div>@endisset
    </div>
    @endif
    <div class="{{ $padded ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</div>
