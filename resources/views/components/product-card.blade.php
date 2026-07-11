@props([
    'product',
    'showCompare' => false,
])

@php
    $hasDiscount = $product->discount_percent > 0;
    $hasVariants = $product->relationLoaded('variants') ? $product->variants->isNotEmpty() : $product->variants()->exists();
    $productUrl = route('product.show', $product->slug);
    $isWishlisted = auth()->check() && auth()->user()->hasWishlisted($product->id);
@endphp

<div {{ $attributes->merge(['class' => 'group relative flex h-full flex-col rounded-2xl border border-ink-100 bg-white p-3 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-card-hover sm:p-4']) }}>
    <div class="relative mb-3 aspect-square overflow-hidden rounded-xl bg-ink-50">
        @if ($hasDiscount)
            <span class="absolute right-2.5 top-2.5 z-10 rounded-full bg-brand-500 px-2 py-1 text-[11px] font-bold text-white shadow-sm">
                {{ $product->discount_percent }}%
            </span>
        @endif

        <a href="{{ $productUrl }}" class="block h-full w-full">
            <img
                src="{{ $product->main_thumb }}"
                alt="{{ $product->title }}"
                loading="lazy"
                class="h-full w-full object-contain p-2.5 sm:p-4 transition-transform duration-500 group-hover:scale-105"
            >
        </a>

        <div class="absolute inset-x-0 bottom-0 flex translate-y-full items-center justify-center gap-2 bg-gradient-to-t from-white/95 to-white/0 pb-2 pt-6 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
            <a href="{{ $productUrl }}" aria-label="مشاهده سریع" class="flex h-8 w-8 items-center justify-center rounded-full border border-ink-100 bg-white text-ink-600 shadow-sm transition-colors hover:border-brand-500 hover:text-brand-500">
                <x-icon name="eye" class="h-4 w-4" />
            </a>

            <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                @csrf
                <button type="submit" aria-label="افزودن به علاقه‌مندی" class="flex h-8 w-8 items-center justify-center rounded-full border {{ $isWishlisted ? 'border-brand-500 text-brand-500' : 'border-ink-100 text-ink-600' }} bg-white shadow-sm transition-colors hover:border-brand-500 hover:text-brand-500">
                    <x-icon name="heart" class="h-4 w-4 {{ $isWishlisted ? 'fill-brand-500' : '' }}" />
                </button>
            </form>

            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" aria-label="افزودن به سبد خرید" {{ $product->in_stock ? '' : 'disabled' }} class="flex h-8 w-8 items-center justify-center rounded-full border border-ink-100 bg-white text-ink-600 shadow-sm transition-colors hover:border-brand-500 hover:text-brand-500 disabled:cursor-not-allowed disabled:opacity-40">
                    <x-icon name="cart" class="h-4 w-4" />
                </button>
            </form>
        </div>
    </div>

    @if ($hasVariants)
        <div class="mb-1.5 flex items-center gap-1">
            @foreach ($product->colors as $color)
                <span class="h-2.5 w-2.5 rounded-full border border-black/10" style="background-color: {{ $color['hex'] }}"></span>
            @endforeach
        </div>
    @endif

    <h3 class="mb-2 line-clamp-2 min-h-[2.6em] text-[13px] leading-[1.3] text-ink-800 sm:text-sm">
        <a href="{{ $productUrl }}" class="transition-colors hover:text-brand-500">{{ $product->title }}</a>
    </h3>

    <div class="mt-auto flex flex-col gap-1.5 pt-1 sm:flex-row sm:items-center sm:justify-between sm:gap-2">
        <div class="flex flex-col">
            @if ($hasDiscount)
                <span class="text-[11px] text-ink-400 line-through">{{ number_format($product->regular_price) }}</span>
            @endif
            <span class="whitespace-nowrap text-sm font-extrabold text-brand-500 sm:text-[15px]">
                @if ($hasVariants && !$hasDiscount)
                    از
                @endif
                {{ number_format($product->price) }}
                <span class="text-[11px] font-normal text-ink-400">تومان</span>
            </span>
        </div>

        <a href="{{ $productUrl }}" class="block w-full shrink-0 rounded-lg bg-ink-900 px-2.5 py-1.5 text-center text-[11px] font-medium text-white transition-colors hover:bg-brand-500 sm:w-auto sm:text-xs">
            {{ $hasVariants ? 'مشاهده تنوع' : 'مشاهده' }}
        </a>
    </div>
</div>
