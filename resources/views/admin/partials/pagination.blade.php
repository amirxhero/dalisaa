@if ($paginator->hasPages())
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-xs text-gray-500">
        نمایش <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>
        تا <span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
        از <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span> مورد
    </p>

    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-300">
                <iconify-icon icon="tabler:chevron-right" class="text-sm"></iconify-icon>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100">
                <iconify-icon icon="tabler:chevron-right" class="text-sm"></iconify-icon>
            </a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="flex h-8 w-8 items-center justify-center text-xs text-gray-400">...</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-xs font-semibold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-medium text-gray-600 transition-colors hover:bg-gray-100">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100">
                <iconify-icon icon="tabler:chevron-left" class="text-sm"></iconify-icon>
            </a>
        @else
            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-300">
                <iconify-icon icon="tabler:chevron-left" class="text-sm"></iconify-icon>
            </span>
        @endif
    </div>
</div>
@endif
