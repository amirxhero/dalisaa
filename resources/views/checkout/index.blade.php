@extends('layouts.app')

@section('title', 'تسویه‌حساب')

@section('content')
    <div
        class="bg-white py-8 sm:py-10"
        x-data="{
            selected: '{{ old('address_id', $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id ?? 'new') }}',
            fallback: '{{ $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id ?? '' }}',
            openNew() { this.selected = 'new'; },
            cancelNew() { this.selected = this.fallback || '{{ $addresses->first()?->id ?? '' }}'; }
        }"
    >
        <div class="mx-auto max-w-5xl px-4 lg:px-6">
            <h1 class="mb-6 text-lg font-extrabold text-ink-900 sm:text-2xl">تسویه‌حساب</h1>

            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="grid gap-6 lg:grid-cols-3 lg:items-start">
                @csrf

                <div class="space-y-4 pb-32 lg:col-span-2 lg:pb-0">
                    <div class="rounded-2xl border border-ink-100 p-5">
                        <h2 class="mb-4 flex items-center gap-2 text-sm font-bold text-ink-900">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                                <x-icon name="map-pin" class="h-4 w-4" />
                            </span>
                            آدرس تحویل سفارش
                        </h2>

                        <div class="space-y-2.5">
                            @foreach ($addresses as $address)
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-4 transition-all"
                                    :class="selected === '{{ $address->id }}'
                                        ? 'border-brand-500 bg-brand-50/40 shadow-sm'
                                        : 'border-ink-100 bg-white hover:border-ink-200'"
                                >
                                    {{-- Hidden native radio --}}
                                    <input type="radio" name="address_id" value="{{ $address->id }}" x-model="selected" class="sr-only">

                                    {{-- Address icon badge --}}
                                    <span
                                        class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition-colors"
                                        :class="selected === '{{ $address->id }}' ? 'bg-brand-500 text-white' : 'bg-ink-50 text-ink-400'"
                                    >
                                        <x-icon name="map-pin" class="h-4 w-4" />
                                    </span>

                                    {{-- Content --}}
                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-bold text-ink-900">{{ $address->title }}</span>
                                            @if ($address->is_default)
                                                <span class="rounded-full bg-accent-50 px-2.5 py-0.5 text-[10px] font-bold text-accent-600">پیش‌فرض</span>
                                            @endif
                                        </span>
                                        <span class="mt-1 block text-xs font-medium text-ink-600">{{ $address->receiver_name }} · <span dir="ltr" class="inline-block">{{ $address->receiver_mobile }}</span></span>
                                        <span class="mt-1.5 block text-xs leading-5 text-ink-400">{{ $address->full_address }}</span>
                                    </span>

                                    {{-- Custom radio indicator --}}
                                    <span
                                        class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-all"
                                        :class="selected === '{{ $address->id }}' ? 'border-brand-500 bg-brand-500' : 'border-ink-200 bg-white'"
                                    >
                                        <span
                                            x-show="selected === '{{ $address->id }}'"
                                            class="block h-2 w-2 rounded-full bg-white"
                                        ></span>
                                    </span>
                                </label>
                            @endforeach

                            {{-- Add new address option (hidden when form is open) --}}
                            <button
                                type="button"
                                x-show="selected !== 'new'"
                                @click="openNew()"
                                class="flex w-full cursor-pointer items-center gap-3 rounded-2xl border-2 border-dashed border-ink-100 p-4 transition-all hover:border-brand-300"
                            >
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-ink-50 text-ink-400 transition-colors">
                                    <x-icon name="plus" class="h-4 w-4" />
                                </span>
                                <span class="flex-1 text-right text-sm font-bold text-ink-600">افزودن آدرس جدید</span>
                            </button>

                            {{-- Hidden radio to keep form value --}}
                            <input type="radio" name="address_id" value="new" x-model="selected" class="sr-only">
                        </div>

                        {{-- New address form (expands inline) --}}
                        <div x-show="selected === 'new'" x-cloak
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-4 overflow-hidden rounded-2xl border border-dashed border-brand-200 bg-brand-50/30"
                        >
                            {{-- Form header with cancel --}}
                            <div class="flex items-center justify-between border-b border-brand-100 px-4 py-3">
                                <div class="flex items-center gap-2 text-sm font-bold text-brand-600">
                                    <x-icon name="map-pin" class="h-4 w-4" />
                                    آدرس جدید
                                </div>
                                <button
                                    type="button"
                                    @click="cancelNew()"
                                    class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-ink-500 transition-colors hover:bg-ink-100 hover:text-ink-800"
                                >
                                    <x-icon name="close" class="h-3.5 w-3.5" />
                                    لغو
                                </button>
                            </div>

                            <div class="grid gap-4 p-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">عنوان آدرس</label>
                                    <input type="text" name="new_title" placeholder="مثلاً خانه، محل کار" value="{{ old('new_title') }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                                    @error('new_title')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">نام گیرنده</label>
                                    <input type="text" name="new_receiver_name" value="{{ old('new_receiver_name') }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                                    @error('new_receiver_name')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">موبایل گیرنده</label>
                                    <input type="text" name="new_receiver_mobile" dir="ltr" value="{{ old('new_receiver_mobile') }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-left text-sm outline-none focus:border-brand-300">
                                    @error('new_receiver_mobile')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">استان</label>
                                    <input type="text" name="new_province" value="{{ old('new_province') }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                                    @error('new_province')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">شهر</label>
                                    <input type="text" name="new_city" value="{{ old('new_city') }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                                    @error('new_city')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">آدرس کامل</label>
                                    <textarea name="new_address_line" rows="2" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">{{ old('new_address_line') }}</textarea>
                                    @error('new_address_line')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-ink-600">کد پستی</label>
                                    <input type="text" name="new_postal_code" dir="ltr" value="{{ old('new_postal_code') }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-left text-sm outline-none focus:border-brand-300">
                                    @error('new_postal_code')
                                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    <div class="rounded-2xl border border-ink-100 p-5">
                        <h2 class="mb-3 text-sm font-bold text-ink-900">کالاهای سفارش ({{ $cart->items_count }})</h2>
                        <div class="divide-y divide-ink-100">
                            @foreach ($cart->items as $item)
                                <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-ink-50">
                                        <img src="{{ $item->product->main_thumb }}" alt="{{ $item->product->title }}" class="h-full w-full object-contain p-1.5">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-1 text-xs font-medium text-ink-800">{{ $item->product->title }}</p>
                                        <p class="mt-1 text-[11px] text-ink-400">{{ number_format($item->unit_price) }} تومان × {{ $item->quantity }}</p>
                                    </div>
                                    <span class="shrink-0 text-xs font-bold text-ink-900">{{ number_format($item->line_total) }} تومان</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-ink-100 p-5">
                        <label class="mb-1.5 block text-xs font-medium text-ink-600">توضیحات سفارش (اختیاری)</label>
                        <textarea name="notes" rows="2" placeholder="در صورت نیاز توضیحی برای ارسال سفارش بنویسید..." class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Desktop summary card (hidden on mobile) --}}
                <div class="sticky top-4 hidden rounded-2xl border border-ink-100 p-5 lg:block">
                    <h2 class="mb-4 text-sm font-bold text-ink-900">خلاصه سفارش</h2>
                    <div class="space-y-2.5 text-sm text-ink-600">
                        <div class="flex items-center justify-between">
                            <span>جمع سبد خرید ({{ $cart->items_count }} کالا)</span>
                            <span>{{ number_format($cart->subtotal) }} تومان</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>هزینه ارسال</span>
                            <span>{{ $shipping > 0 ? number_format($shipping).' تومان' : 'رایگان' }}</span>
                        </div>
                    </div>
                    <div class="my-4 border-t border-dashed border-ink-100"></div>
                    <div class="mb-5 flex items-center justify-between text-sm font-bold text-ink-900">
                        <span>مبلغ قابل پرداخت</span>
                        <span class="text-brand-500">{{ number_format($total) }} تومان</span>
                    </div>
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-600">
                        <x-icon name="shield" class="h-4 w-4" />
                        ثبت سفارش و پرداخت
                    </button>
                    <p class="mt-3 text-center text-[11px] text-ink-400">پرداخت شما با درگاه امن انجام می‌شود.</p>
                </div>
            </form>
        </div>
    </div>

    {{-- Mobile sticky checkout bar --}}
    <div class="fixed inset-x-0 bottom-0 z-50 lg:hidden" style="background:rgba(255,255,255,0.96);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-top:1px solid #f0f0f4;padding:12px 16px calc(12px + env(safe-area-inset-bottom));box-shadow:0 -4px 24px rgba(15,0,43,0.08);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <div style="display:flex;flex-direction:column;gap:2px;">
                <span style="font-size:10px;color:#9e9eab;">مبلغ قابل پرداخت</span>
                <span style="font-size:15px;font-weight:800;color:#e5272d;letter-spacing:-0.3px;">{{ number_format($total) }} <span style="font-size:12px;font-weight:600;">تومان</span></span>
            </div>
            @if ($shipping === 0)
                <span style="font-size:10px;font-weight:700;color:#16a34a;background:#dcfce7;border-radius:999px;padding:3px 10px;">ارسال رایگان</span>
            @else
                <span style="font-size:10px;color:#9e9eab;">ارسال: {{ number_format($shipping) }} تومان</span>
            @endif
        </div>
        <button
            type="submit"
            form="checkout-form"
            style="display:flex;width:100%;align-items:center;justify-content:center;gap:8px;background:#e5272d;color:#fff;font-size:14px;font-weight:700;border:none;border-radius:14px;padding:14px;cursor:pointer;transition:background 0.15s;"
            onmousedown="this.style.background='#c0181d'" onmouseup="this.style.background='#e5272d'" ontouchstart="this.style.background='#c0181d'" ontouchend="this.style.background='#e5272d'"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            ثبت سفارش و پرداخت
        </button>
    </div>
@endsection
