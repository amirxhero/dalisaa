@extends('admin.layouts.admin')
@section('title', $order->order_number)
@section('page-title', 'سفارش '.$order->order_number)
@php
    $breadcrumbs = ['سفارشات', $order->order_number];
@endphp

@section('content')
@php
    $toneMap = ['pending'=>'gray','processing'=>'amber','paid'=>'sky','shipped'=>'indigo','delivered'=>'emerald','cancelled'=>'rose','failed'=>'rose'];
    $allStatuses = \App\Models\Order::STATUS_LABELS;
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-900">
            سفارش <span class="font-mono text-indigo-600">{{ $order->order_number }}</span>
        </h2>
        <p class="mt-0.5 text-sm text-gray-500">ثبت‌شده در {{ $order->created_at->format('Y/m/d H:i') }}</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="admin-btn-secondary">
        <iconify-icon icon="tabler:arrow-right" class="text-base"></iconify-icon>
        بازگشت به لیست
    </a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Main --}}
    <div class="space-y-6 lg:col-span-2">
        {{-- Items --}}
        <x-admin.section title="اقلام سفارش">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/60">
                        <th class="admin-th">محصول</th>
                        <th class="admin-th">تعداد</th>
                        <th class="admin-th">قیمت واحد</th>
                        <th class="admin-th">جمع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="admin-td">
                            <p class="font-medium text-gray-900">{{ $item->title }}</p>
                            @if($item->color_name)<p class="text-xs text-gray-400">{{ $item->color_name }}</p>@endif
                        </td>
                        <td class="admin-td text-gray-600">{{ $item->quantity }}</td>
                        <td class="admin-td text-gray-600">{{ number_format($item->unit_price) }} ت</td>
                        <td class="admin-td font-semibold text-gray-900">{{ number_format($item->line_total) }} ت</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="space-y-2 border-t border-gray-100 px-5 py-4 text-sm">
                <div class="flex justify-between text-gray-600"><span>جمع اقلام</span><span>{{ number_format($order->subtotal) }} ت</span></div>
                @if($order->discount_total > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>تخفیف {{ $order->discount_code ? "($order->discount_code)" : '' }}</span>
                    <span>− {{ number_format($order->discount_total) }} ت</span>
                </div>
                @endif
                @if($order->shipping_cost > 0)
                <div class="flex justify-between text-gray-600"><span>هزینه ارسال</span><span>{{ number_format($order->shipping_cost) }} ت</span></div>
                @endif
                <div class="flex justify-between border-t border-gray-100 pt-3 text-base font-bold text-gray-900">
                    <span>مجموع</span><span>{{ number_format($order->total) }} ت</span>
                </div>
            </div>
        </x-admin.section>

        {{-- Shipping --}}
        <x-admin.section title="اطلاعات ارسال">
            <div class="grid grid-cols-2 gap-4 p-5 text-sm">
                <div><p class="text-xs text-gray-400">گیرنده</p><p class="mt-0.5 font-medium text-gray-800">{{ $order->receiver_name }}</p></div>
                <div><p class="text-xs text-gray-400">موبایل</p><p class="mt-0.5 font-mono text-gray-800">{{ $order->receiver_mobile }}</p></div>
                <div><p class="text-xs text-gray-400">استان</p><p class="mt-0.5 text-gray-800">{{ $order->province }}</p></div>
                <div><p class="text-xs text-gray-400">شهر</p><p class="mt-0.5 text-gray-800">{{ $order->city }}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-400">آدرس</p><p class="mt-0.5 text-gray-800">{{ $order->address_line }}</p></div>
                <div><p class="text-xs text-gray-400">کد پستی</p><p class="mt-0.5 font-mono text-gray-800">{{ $order->postal_code }}</p></div>
            </div>
        </x-admin.section>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Status --}}
        <x-admin.section title="وضعیت سفارش">
            <div class="p-5">
                <div class="mb-4">
                    <x-admin.badge :tone="$toneMap[$order->status] ?? 'gray'">{{ $allStatuses[$order->status] ?? $order->status }}</x-admin.badge>
                </div>
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="admin-select">
                        @foreach($allStatuses as $key => $label)
                        <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="admin-btn-primary w-full">
                        <iconify-icon icon="tabler:refresh" class="text-base"></iconify-icon>
                        به‌روزرسانی وضعیت
                    </button>
                </form>
            </div>
        </x-admin.section>

        {{-- Order Info --}}
        <x-admin.section title="اطلاعات سفارش">
            <div class="space-y-3 p-5 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">شماره:</span><span class="font-mono text-xs">{{ $order->order_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">تاریخ:</span><span>{{ $order->created_at->format('Y/m/d H:i') }}</span></div>
                @if($order->paid_at)
                <div class="flex justify-between"><span class="text-gray-500">پرداخت:</span><span class="text-emerald-600">{{ $order->paid_at->format('Y/m/d H:i') }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">روش پرداخت:</span><span>{{ $order->payment_method ?? '—' }}</span></div>
                @if($order->user)
                <div class="flex justify-between"><span class="text-gray-500">کاربر:</span><span>{{ $order->user->name }}</span></div>
                @endif
            </div>
        </x-admin.section>
    </div>
</div>
@endsection
