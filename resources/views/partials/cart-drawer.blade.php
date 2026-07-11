{{-- Slide-in shopping cart drawer --}}
<div x-show="cartOpen" x-cloak @keydown.escape.window="cartOpen = false" class="fixed inset-0 z-50" role="dialog" aria-modal="true">
    <div
        x-show="cartOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="cartOpen = false"
        class="absolute inset-0 bg-ink-900/50"
    ></div>

    <div
        x-show="cartOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-y-0 left-0 flex w-[90%] max-w-sm flex-col bg-white shadow-2xl"
    >
        <div class="flex items-center justify-between border-b-4 border-brand-500 px-5 py-4">
            <h2 class="text-base font-bold text-ink-900">سبد خرید شما</h2>
            <button type="button" @click="cartOpen = false" class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-50 text-ink-600">
                <x-icon name="close" class="h-4.5 w-4.5" />
            </button>
        </div>

        @if ($cart->items->isEmpty())
            <div class="flex flex-1 flex-col items-center justify-center gap-4 p-8 text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-ink-50 text-ink-300">
                    <x-icon name="cart" class="h-10 w-10" />
                </div>
                <h6 class="text-sm font-bold text-ink-600">هیچ محصولی در سبد خرید نیست</h6>
                <p class="text-xs text-ink-400">محصولات مورد علاقه‌ی خود را به سبد خرید اضافه کنید.</p>
            </div>
        @else
            <div class="flex-1 divide-y divide-ink-100 overflow-y-auto px-5">
                @foreach ($cart->items as $item)
                    <div class="flex items-center gap-3 py-4">
                        <a href="{{ route('product.show', $item->product->slug) }}" class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-ink-50">
                            <img src="{{ $item->product->main_thumb }}" alt="{{ $item->product->title }}" class="h-full w-full object-contain p-1.5">
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('product.show', $item->product->slug) }}" class="line-clamp-1 text-xs font-medium text-ink-800 hover:text-brand-500">
                                {{ $item->product->title }}
                            </a>
                            @if ($item->variant)
                                <span class="text-[11px] text-ink-400">رنگ: {{ $item->variant->color_name }}</span>
                            @endif
                            <div class="mt-1.5 flex items-center justify-between">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center overflow-hidden rounded-lg border border-ink-100">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="flex h-6 w-6 items-center justify-center text-ink-600 hover:bg-ink-50">−</button>
                                    <span class="w-6 text-center text-[11px] font-bold">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="flex h-6 w-6 items-center justify-center text-ink-600 hover:bg-ink-50">+</button>
                                </form>
                                <span class="text-xs font-bold text-brand-500">{{ number_format($item->line_total) }}</span>
                            </div>
                        </div>
                        <form action="{{ route('cart.destroy', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="حذف" class="flex h-7 w-7 items-center justify-center rounded-full text-ink-300 hover:bg-brand-50 hover:text-brand-500">
                                <x-icon name="close" class="h-3.5 w-3.5" />
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="border-t border-ink-100 p-5">
            <div class="mb-4 flex items-center justify-between text-sm">
                <span class="text-ink-600">جمع کل سبد خرید</span>
                <span class="font-bold text-ink-900">{{ number_format($cart->subtotal) }} تومان</span>
            </div>
            <a
                href="{{ $cart->items->isEmpty() ? '#' : (auth()->check() ? route('checkout.index') : route('login')) }}"
                class="flex w-full items-center justify-center rounded-xl bg-ink-900 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-500 {{ $cart->items->isEmpty() ? 'pointer-events-none opacity-40' : '' }}"
            >
                ادامه تسویه حساب
            </a>
        </div>
    </div>
</div>
