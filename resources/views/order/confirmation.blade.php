@extends('layouts.app')

@section('title', 'وضعیت سفارش '.$order->order_number)

@section('content')
    @php
        $isPaid   = $order->isPaid();
        $isFailed = $order->status === \App\Models\Order::STATUS_FAILED;
    @endphp

    <div class="min-h-[80vh] bg-[#f7f7f9] py-10 sm:py-16">
        <div class="mx-auto max-w-lg px-4">

            {{-- Status hero card --}}
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm">

                {{-- Colored top band --}}
                <div class="h-2 w-full {{ $isPaid ? 'bg-emerald-400' : ($isFailed ? 'bg-red-400' : 'bg-amber-400') }}"></div>

                <div class="px-6 py-8 text-center sm:px-10">

                    {{-- Icon --}}
                    @if ($isPaid)
                        <div class="relative mx-auto mb-5 flex h-20 w-20 items-center justify-center">
                            <div class="absolute inset-0 animate-ping rounded-full bg-emerald-100 opacity-60"></div>
                            <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-200">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </div>
                        <h1 class="text-xl font-extrabold text-ink-900 sm:text-2xl">پرداخت موفق!</h1>
                        <p class="mt-2 text-sm leading-6 text-ink-500">سفارش شما با موفقیت ثبت شد.<br>به‌زودی پردازش و ارسال خواهد شد.</p>
                    @elseif ($isFailed)
                        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-500 text-white shadow-lg shadow-red-200">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </div>
                        <h1 class="text-xl font-extrabold text-ink-900 sm:text-2xl">پرداخت ناموفق</h1>
                        <p class="mt-2 text-sm leading-6 text-ink-500">متأسفانه پرداخت تکمیل نشد.<br>مبلغی از حساب شما کسر نشده است.</p>
                    @else
                        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-amber-400 text-white shadow-lg shadow-amber-200">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <h1 class="text-xl font-extrabold text-ink-900 sm:text-2xl">در انتظار تایید</h1>
                        <p class="mt-2 text-sm leading-6 text-ink-500">وضعیت پرداخت شما در حال بررسی است.<br>نتیجه را از پنل کاربری پیگیری کنید.</p>
                    @endif

                    {{-- Order details --}}
                    <div class="mt-8 divide-y divide-dashed divide-ink-100 rounded-2xl border border-ink-100 bg-[#f9f9fb] text-right">

                        <div class="flex items-center justify-between px-4 py-3">
                            <span class="text-xs text-ink-400">شماره سفارش</span>
                            <span class="font-mono text-sm font-bold tracking-wide text-ink-900">{{ $order->order_number }}</span>
                        </div>

                        <div class="flex items-center justify-between px-4 py-3">
                            <span class="text-xs text-ink-400">وضعیت</span>
                            <x-order-status-badge :status="$order->status" />
                        </div>

                        @if ($order->payment?->ref_id)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-ink-400">کد پیگیری</span>
                                <span class="font-mono text-sm font-bold text-ink-900">{{ $order->payment->ref_id }}</span>
                            </div>
                        @endif

                        @if ($order->payment?->gateway)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-ink-400">درگاه پرداخت</span>
                                <span class="text-sm font-medium text-ink-700">{{ ucfirst($order->payment->gateway) }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between px-4 py-3">
                            <span class="text-xs text-ink-400">مبلغ پرداختی</span>
                            <span class="text-base font-extrabold {{ $isPaid ? 'text-emerald-600' : 'text-brand-500' }}">
                                {{ number_format($order->total) }} <span class="text-xs font-medium">تومان</span>
                            </span>
                        </div>

                        @if ($order->items->count())
                            <div class="px-4 py-3">
                                <span class="mb-3 block text-xs text-ink-400">کالاهای سفارش</span>
                                <div class="space-y-2">
                                    @foreach ($order->items as $item)
                                        <div class="flex items-center gap-2.5">
                                            @if ($item->image_url)
                                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-xl bg-white">
                                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-full w-full object-contain p-1">
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="line-clamp-1 text-right text-xs font-medium text-ink-800">{{ $item->title }}</p>
                                                <p class="text-right text-[10px] text-ink-400">{{ $item->quantity }} عدد · {{ number_format($item->unit_price) }} تومان</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div class="mt-6 flex flex-col gap-2.5 sm:flex-row sm:justify-center">
                        @if ($isFailed)
                            <a
                                href="{{ route('payment.pay', $order) }}"
                                class="flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-600"
                            >
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                                تلاش مجدد برای پرداخت
                            </a>
                        @endif

                        <a
                            href="{{ route('panel.orders.show', $order) }}"
                            class="flex items-center justify-center gap-2 rounded-xl {{ $isPaid ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'border border-ink-200 text-ink-700 hover:border-ink-300' }} px-6 py-3 text-sm font-bold transition-colors"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            مشاهده جزئیات سفارش
                        </a>

                        <a
                            href="{{ route('home') }}"
                            class="flex items-center justify-center gap-2 rounded-xl border border-ink-200 px-6 py-3 text-sm font-bold text-ink-700 transition-colors hover:border-ink-300"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            بازگشت به فروشگاه
                        </a>
                    </div>

                </div>
            </div>

            {{-- Secure payment note --}}
            <p class="mt-5 text-center text-[11px] text-ink-400">
                <svg class="mb-0.5 me-1 inline-block" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                پرداخت شما از طریق درگاه امن انجام شده است.
            </p>

        </div>
    </div>
@endsection
