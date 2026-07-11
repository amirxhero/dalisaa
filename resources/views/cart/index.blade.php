@extends('layouts.app')

@section('title', 'سبد خرید')

@section('content')
    <div class="bg-white py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 lg:px-6">
            <h1 class="mb-6 text-lg font-extrabold text-ink-900 sm:text-2xl">سبد خرید شما</h1>

            @if ($cart->items->isEmpty())
                <div class="flex flex-col items-center gap-4 rounded-2xl border border-ink-100 py-16 text-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-ink-50 text-ink-300">
                        <x-icon name="cart" class="h-10 w-10" />
                    </div>
                    <h6 class="text-sm font-bold text-ink-600">سبد خرید شما خالی است</h6>
                    <a href="{{ route('home') }}" class="rounded-full bg-brand-500 px-6 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-600">
                        بازگشت به فروشگاه
                    </a>
                </div>
            @else
                <div class="grid gap-6 lg:grid-cols-3 lg:items-start">
                    <div class="divide-y divide-ink-100 rounded-2xl border border-ink-100 lg:col-span-2">
                        @foreach ($cart->items as $item)
                            <div class="flex items-center gap-4 p-4">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-ink-50">
                                    <img src="{{ $item->product->main_thumb }}" alt="{{ $item->product->title }}" class="h-full w-full object-contain p-2">
                                </a>

                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="line-clamp-2 text-sm font-medium text-ink-800 hover:text-brand-500">
                                        {{ $item->product->title }}
                                    </a>
                                    @if ($item->variant)
                                        <span class="mt-1 block text-xs text-ink-400">رنگ: {{ $item->variant->color_name }}</span>
                                    @endif
                                    <span class="mt-1 block text-xs text-ink-400">قیمت واحد: {{ number_format($item->unit_price) }} تومان</span>
                                </div>

                                <div class="flex shrink-0 flex-col items-end gap-2">
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center overflow-hidden rounded-lg border border-ink-100">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="flex h-8 w-8 items-center justify-center text-ink-600 hover:bg-ink-50">−</button>
                                        <span class="w-8 text-center text-sm font-bold">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="flex h-8 w-8 items-center justify-center text-ink-600 hover:bg-ink-50">+</button>
                                    </form>
                                    <span class="text-sm font-extrabold text-brand-500">{{ number_format($item->line_total) }} تومان</span>
                                    <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] text-ink-400 hover:text-brand-500">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="rounded-2xl border border-ink-100 p-5">
                        <h2 class="mb-4 text-sm font-bold text-ink-900">خلاصه سفارش</h2>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex items-center justify-between text-ink-600">
                                <span>جمع سبد خرید ({{ $cart->items_count }} کالا)</span>
                                <span>{{ number_format($cart->subtotal) }} تومان</span>
                            </div>
                        </div>
                        <div class="my-4 border-t border-dashed border-ink-100"></div>
                        <div class="mb-5 flex items-center justify-between text-sm font-bold text-ink-900">
                            <span>جمع کل</span>
                            <span class="text-brand-500">{{ number_format($cart->subtotal) }} تومان</span>
                        </div>
                        <a
                            href="{{ auth()->check() ? route('checkout.index') : route('login') }}"
                            class="flex w-full items-center justify-center rounded-xl bg-ink-900 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-500"
                        >
                            ادامه به تسویه‌حساب
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
