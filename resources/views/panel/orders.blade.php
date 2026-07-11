@extends('layouts.app')

@section('title', 'سفارش‌های من')

@section('content')
    <x-panel-page active="orders" title="سفارش‌های من">
        @if ($orders->isEmpty())
            <div class="flex flex-col items-center gap-4 rounded-2xl border border-ink-100 py-16 text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-ink-50 text-ink-300">
                    <x-icon name="box" class="h-10 w-10" />
                </div>
                <h6 class="text-sm font-bold text-ink-600">هنوز سفارشی ثبت نکرده‌اید</h6>
                <a href="{{ route('home') }}" class="rounded-full bg-brand-500 px-6 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-600">
                    شروع خرید
                </a>
            </div>
        @else
            <div class="divide-y divide-ink-100 rounded-2xl border border-ink-100">
                @foreach ($orders as $order)
                    <a href="{{ route('panel.orders.show', $order) }}" class="flex items-center gap-3 p-4 transition-colors hover:bg-ink-50">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-ink-50">
                            <img src="{{ $order->items->first()?->image_url }}" alt="" class="h-8 w-8 object-contain">
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-bold text-ink-900">{{ $order->order_number }}</p>
                                <x-order-status-badge :status="$order->status" />
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs text-ink-400">{{ jdate($order->created_at)->format('%d %B %Y') }} · {{ $order->items->count() }} کالا</p>
                                <span class="shrink-0 text-sm font-bold text-ink-900">{{ number_format($order->total) }} تومان</span>
                            </div>
                        </div>

                        <x-icon name="chevron-left" class="h-4 w-4 shrink-0 text-ink-300" />
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </x-panel-page>
@endsection
