@props([
    'icon' => 'tabler:inbox',
    'title' => 'موردی یافت نشد',
    'description' => null,
])

<div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 text-gray-300">
        <iconify-icon icon="{{ $icon }}" class="text-2xl"></iconify-icon>
    </span>
    <div>
        <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
        @if($description)
        <p class="mt-0.5 text-xs text-gray-400">{{ $description }}</p>
        @endif
    </div>
</div>
