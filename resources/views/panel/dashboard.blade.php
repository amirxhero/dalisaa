@extends('layouts.app')

@section('title', 'داشبورد کاربری')

@section('content')
    <x-panel-page active="dashboard" title="داشبورد کاربری">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

            {{-- سفارش‌ها --}}
            <div style="border-radius:1rem;border:1px solid #e7e8ec;padding:1.1rem;background:#fff;display:flex;flex-direction:column;gap:0.6rem;">
                <div style="display:flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;border-radius:0.75rem;background-color:#fdeced;color:#ee273a;flex-shrink:0;">
                    <x-icon name="box" class="h-5 w-5" />
                </div>
                <p style="font-size:1.5rem;font-weight:800;color:#0f002b;line-height:1;">{{ number_format($stats['orders_count']) }}</p>
                <p style="font-size:0.72rem;color:#6b6d78;margin-top:0.1rem;">تعداد سفارش‌ها</p>
            </div>

            {{-- مجموع خرید --}}
            <div style="border-radius:1rem;border:1px solid #e7e8ec;padding:1.1rem;background:#fff;display:flex;flex-direction:column;gap:0.6rem;">
                <div style="display:flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;border-radius:0.75rem;background-color:#d1fae5;color:#059669;flex-shrink:0;">
                    <x-icon name="cash" class="h-5 w-5" />
                </div>
                <p style="font-size:1.5rem;font-weight:800;color:#0f002b;line-height:1;">{{ number_format($stats['total_spent']) }}</p>
                <p style="font-size:0.72rem;color:#6b6d78;margin-top:0.1rem;">مجموع خرید (تومان)</p>
            </div>

            {{-- علاقه‌مندی‌ها --}}
            <div style="border-radius:1rem;border:1px solid #e7e8ec;padding:1.1rem;background:#fff;display:flex;flex-direction:column;gap:0.6rem;">
                <div style="display:flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;border-radius:0.75rem;background-color:#ffe4e6;color:#f43f5e;flex-shrink:0;">
                    <x-icon name="heart" class="h-5 w-5" />
                </div>
                <p style="font-size:1.5rem;font-weight:800;color:#0f002b;line-height:1;">{{ number_format($stats['wishlist_count']) }}</p>
                <p style="font-size:0.72rem;color:#6b6d78;margin-top:0.1rem;">علاقه‌مندی‌ها</p>
            </div>

            {{-- آدرس‌ها --}}
            <div style="border-radius:1rem;border:1px solid #e7e8ec;padding:1.1rem;background:#fff;display:flex;flex-direction:column;gap:0.6rem;">
                <div style="display:flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;border-radius:0.75rem;background-color:#e0f2fe;color:#0284c7;flex-shrink:0;">
                    <x-icon name="map-pin" class="h-5 w-5" />
                </div>
                <p style="font-size:1.5rem;font-weight:800;color:#0f002b;line-height:1;">{{ number_format($stats['addresses_count']) }}</p>
                <p style="font-size:0.72rem;color:#6b6d78;margin-top:0.1rem;">آدرس‌های ثبت شده</p>
            </div>

        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-ink-100">
            {{-- Section header --}}
            <div class="flex items-center justify-between bg-ink-50/60 px-4 py-3">
                <div class="flex items-center gap-2">
                    <x-icon name="box" class="h-4 w-4 text-ink-400" />
                    <h2 class="text-sm font-bold text-ink-900">آخرین سفارش‌ها</h2>
                </div>
                <a href="{{ route('panel.orders') }}" class="flex items-center gap-1 text-xs font-bold text-brand-500 hover:text-brand-600">
                    مشاهده همه
                    <x-icon name="chevron-left" class="h-3.5 w-3.5" />
                </a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="flex flex-col items-center gap-3 py-10 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-ink-50 text-ink-300">
                        <x-icon name="box" class="h-6 w-6" />
                    </div>
                    <p class="text-sm text-ink-400">هنوز سفارشی ثبت نکرده‌اید.</p>
                </div>
            @else
                <div class="divide-y divide-ink-100">
                    @foreach ($recentOrders as $order)
                        <a href="{{ route('panel.orders.show', $order) }}" class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-ink-50">

                            {{-- Product thumbnail --}}
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ink-50">
                                @if ($order->items->first()?->image_url)
                                    <img src="{{ $order->items->first()->image_url }}" alt="" class="h-8 w-8 object-contain">
                                @else
                                    <x-icon name="box" class="h-5 w-5 text-ink-300" />
                                @endif
                            </div>

                            {{-- Two-row info — never wraps --}}
                            <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-bold text-ink-900">{{ $order->order_number }}</p>
                                    <x-order-status-badge :status="$order->status" />
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs text-ink-400">{{ jdate($order->created_at)->format('%d %B %Y') }} · {{ $order->items->count() }} کالا</p>
                                    <span class="shrink-0 text-xs font-bold text-ink-700">{{ number_format($order->total) }} <span class="font-normal text-ink-400">تومان</span></span>
                                </div>
                            </div>

                            <x-icon name="chevron-left" class="h-4 w-4 shrink-0 text-ink-200" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </x-panel-page>
@endsection
