@extends('layouts.app')

@section('title', $order->order_number.' – فروشگاه سالیکا')

@section('content')
    @php
        $isPaid   = $order->isPaid();
        $isFailed = $order->status === \App\Models\Order::STATUS_FAILED;
        $isPending = $order->status === \App\Models\Order::STATUS_PENDING;

        $statusColors = [
            'pending'    => ['dot' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fde68a'],
            'processing' => ['dot' => '#0ea5e9', 'bg' => '#f0f9ff', 'border' => '#bae6fd'],
            'paid'       => ['dot' => '#10b981', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
            'shipped'    => ['dot' => '#8b5cf6', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
            'delivered'  => ['dot' => '#10b981', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
            'cancelled'  => ['dot' => '#9ca3af', 'bg' => '#f9fafb', 'border' => '#e5e7eb'],
            'failed'     => ['dot' => '#ef4444', 'bg' => '#fef2f2', 'border' => '#fecaca'],
        ];
        $sc = $statusColors[$order->status] ?? $statusColors['pending'];
    @endphp

    <x-panel-page active="orders">

        {{-- Page header --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <div class="mb-3 flex items-center gap-1.5 text-xs text-ink-400">
                <a href="{{ route('panel.orders') }}" class="transition-colors hover:text-brand-500">سفارش‌های من</a>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="rotate-180"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="text-ink-600">{{ $order->order_number }}</span>
            </div>

            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-lg font-extrabold text-ink-900 sm:text-xl">{{ $order->order_number }}</h1>
                        <x-order-status-badge :status="$order->status" />
                    </div>
                    <p class="mt-1 text-xs text-ink-400">
                        ثبت شده در {{ jdate($order->created_at)->format('%d %B %Y ساعت H:i') }}
                    </p>
                </div>
                @if ($isPending)
                    <a
                        href="{{ route('payment.pay', $order) }}"
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-bold text-white shadow-sm shadow-brand-200 transition-colors hover:bg-brand-600"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        ادامه پرداخت
                    </a>
                @endif
            </div>
        </div>

        {{-- Status timeline strip --}}
        @php
            $steps = [
                ['key' => 'pending',    'label' => 'ثبت سفارش'],
                ['key' => 'processing', 'label' => 'در حال پردازش'],
                ['key' => 'shipped',    'label' => 'ارسال شده'],
                ['key' => 'delivered',  'label' => 'تحویل داده شد'],
            ];
            $stepOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3];
            $currentStep = $isFailed ? -1 : ($stepOrder[$order->status] ?? 0);
        @endphp

        @if (!$isFailed)
            <div class="mb-6 overflow-hidden rounded-2xl border border-ink-100 bg-white px-4 py-5 sm:px-6">
                <div class="flex items-start">
                    @foreach ($steps as $i => $step)
                        @php $done = $i <= $currentStep; $active = $i === $currentStep; @endphp
                        <div class="flex flex-1 flex-col items-center">
                            {{-- Connector line before (except first) --}}
                            <div class="flex w-full items-center">
                                <div class="h-0.5 flex-1 {{ $i === 0 ? 'invisible' : ($done ? 'bg-brand-500' : 'bg-ink-100') }}"></div>
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[10px] font-bold transition-all
                                    {{ $active ? 'bg-brand-500 text-white shadow-md shadow-brand-200 ring-4 ring-brand-100' : ($done ? 'bg-brand-500 text-white' : 'bg-ink-100 text-ink-400') }}">
                                    @if ($done && !$active)
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @elseif ($active)
                                        {{ $i + 1 }}
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                <div class="h-0.5 flex-1 {{ $i === count($steps)-1 ? 'invisible' : ($i < $currentStep ? 'bg-brand-500' : 'bg-ink-100') }}"></div>
                            </div>
                            <span class="mt-2 text-center text-[10px] font-medium leading-tight {{ $active ? 'text-brand-600' : ($done ? 'text-ink-600' : 'text-ink-300') }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid gap-5 lg:grid-cols-3 lg:items-start">

            {{-- Left: order items --}}
            <div class="space-y-3 lg:col-span-2">
                <h2 class="text-sm font-bold text-ink-900">کالاهای سفارش ({{ $order->items->count() }})</h2>
                <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-4 border-b border-ink-50 p-4 last:border-0">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-[#f7f7f9]">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-full w-full object-contain p-2">
                            </div>
                            <div class="min-w-0 flex-1">
                                @if ($item->product)
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="line-clamp-2 text-sm font-medium text-ink-800 transition-colors hover:text-brand-500">
                                        {{ $item->title }}
                                    </a>
                                @else
                                    <p class="line-clamp-2 text-sm font-medium text-ink-800">{{ $item->title }}</p>
                                @endif
                                <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1">
                                    @if ($item->color_name)
                                        <span class="flex items-center gap-1 text-[11px] text-ink-400">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                                            {{ $item->color_name }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-ink-400">{{ $item->quantity }} عدد</span>
                                    <span class="text-[11px] text-ink-400">هر عدد {{ number_format($item->unit_price) }} تومان</span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-sm font-extrabold text-ink-900">{{ number_format($item->line_total) }}</span>
                                <span class="block text-[10px] text-ink-400">تومان</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Notes --}}
                @if ($order->notes)
                    <div class="rounded-2xl border border-ink-100 bg-white p-4">
                        <p class="mb-1.5 text-xs font-medium text-ink-400">توضیحات سفارش</p>
                        <p class="text-sm leading-6 text-ink-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Right: summary + payment + address --}}
            <div class="space-y-4">

                {{-- Payment summary --}}
                <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white">
                    <div class="border-b border-ink-50 px-5 py-3.5">
                        <h2 class="text-sm font-bold text-ink-900">خلاصه پرداخت</h2>
                    </div>
                    <div class="space-y-2.5 px-5 py-4 text-sm text-ink-600">
                        <div class="flex items-center justify-between">
                            <span>جمع کالاها</span>
                            <span>{{ number_format($order->subtotal) }} تومان</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>هزینه ارسال</span>
                            <span class="{{ $order->shipping_cost == 0 ? 'font-medium text-emerald-600' : '' }}">
                                {{ $order->shipping_cost > 0 ? number_format($order->shipping_cost).' تومان' : 'رایگان' }}
                            </span>
                        </div>
                        @if ($order->discount_total > 0)
                            <div class="flex items-center justify-between font-medium text-emerald-600">
                                <span>تخفیف</span>
                                <span>-{{ number_format($order->discount_total) }} تومان</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between bg-ink-50/60 px-5 py-3.5">
                        <span class="text-sm font-bold text-ink-900">مبلغ نهایی</span>
                        <span class="text-base font-extrabold text-brand-500">{{ number_format($order->total) }} <span class="text-xs font-medium">تومان</span></span>
                    </div>

                    @if ($order->payment)
                        <div class="space-y-2 border-t border-dashed border-ink-100 px-5 py-4 text-xs text-ink-500">
                            <div class="flex items-center justify-between">
                                <span>درگاه پرداخت</span>
                                <span class="font-medium text-ink-700">{{ ucfirst($order->payment->gateway) }}</span>
                            </div>
                            @if ($order->payment->ref_id)
                                <div class="flex items-center justify-between">
                                    <span>کد پیگیری</span>
                                    <span class="font-mono font-semibold text-ink-800">{{ $order->payment->ref_id }}</span>
                                </div>
                            @endif
                            @if ($order->paid_at)
                                <div class="flex items-center justify-between">
                                    <span>تاریخ پرداخت</span>
                                    <span>{{ jdate($order->paid_at)->format('%d %B %Y') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Delivery address --}}
                <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white">
                    <div class="border-b border-ink-50 px-5 py-3.5">
                        <h2 class="flex items-center gap-2 text-sm font-bold text-ink-900">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e5272d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            آدرس تحویل
                        </h2>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex items-start gap-3">
                            <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e5272d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-ink-900">{{ $order->receiver_name }}</p>
                                <p class="mt-0.5 text-xs text-ink-400" dir="ltr">{{ $order->receiver_mobile }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl bg-ink-50/60 p-3 text-xs leading-6 text-ink-600">
                            {{ $order->province }}، {{ $order->city }}، {{ $order->address_line }}
                            <span class="mt-1 block text-ink-400">کد پستی: <span dir="ltr" class="inline-block">{{ $order->postal_code }}</span></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </x-panel-page>
@endsection
