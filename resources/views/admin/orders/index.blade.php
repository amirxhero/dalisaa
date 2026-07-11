@extends('admin.layouts.admin')
@section('title', 'سفارشات')
@section('page-title', 'مدیریت سفارشات')
@php
    $breadcrumbs = ['سفارشات'];
@endphp

@section('content')

@php
    $toneMap = ['pending'=>'gray','processing'=>'amber','paid'=>'sky','shipped'=>'indigo','delivered'=>'emerald','cancelled'=>'rose','failed'=>'rose'];
    $statusLabels = \App\Models\Order::STATUS_LABELS;
@endphp

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-900">سفارشات</h2>
    <p class="mt-0.5 text-sm text-gray-500">{{ array_sum($statusCounts) }} سفارش ثبت‌شده</p>
</div>

{{-- Status pills --}}
<div class="mb-5 flex flex-wrap gap-2">
    <a href="{{ route('admin.orders.index') }}"
       class="rounded-full px-3.5 py-1.5 text-xs font-medium transition-colors {{ !request('status') ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-gray-300' }}">
        همه ({{ array_sum($statusCounts) }})
    </a>
    @foreach($statusLabels as $key => $label)
    <a href="{{ route('admin.orders.index', ['status' => $key, 'search' => request('search')]) }}"
       class="rounded-full px-3.5 py-1.5 text-xs font-medium transition-colors {{ request('status') === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-gray-300' }}">
        {{ $label }} ({{ $statusCounts[$key] ?? 0 }})
    </a>
    @endforeach
</div>

<form method="GET" class="admin-card mb-5 flex items-center gap-3 p-4">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <div class="relative flex-1 max-w-sm">
        <iconify-icon icon="tabler:search" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="شماره سفارش، نام یا موبایل..." class="admin-input pr-10">
    </div>
    <button type="submit" class="admin-btn-secondary">
        <iconify-icon icon="tabler:search" class="text-base"></iconify-icon>
        جستجو
    </button>
</form>

<x-admin.section>
    @if($orders->isEmpty())
        <x-admin.empty-state icon="tabler:shopping-cart-off" title="سفارشی یافت نشد" />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">شماره سفارش</th>
                    <th class="admin-th">مشتری</th>
                    <th class="admin-th">مبلغ</th>
                    <th class="admin-th">کد تخفیف</th>
                    <th class="admin-th">تاریخ</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($orders as $order)
                <tr class="transition-colors hover:bg-gray-50/60">
                    <td class="admin-td">
                        <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs font-semibold text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                    </td>
                    <td class="admin-td">
                        <p class="font-medium text-gray-800">{{ $order->receiver_name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->receiver_mobile }}</p>
                    </td>
                    <td class="admin-td font-semibold text-gray-900">{{ number_format($order->total) }} ت</td>
                    <td class="admin-td">
                        @if($order->discount_code)
                        <span class="rounded-lg bg-amber-50 px-2 py-0.5 font-mono text-xs font-semibold text-amber-700">{{ $order->discount_code }}</span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="admin-td text-xs text-gray-500">{{ $order->created_at->format('Y/m/d') }}</td>
                    <td class="admin-td">
                        <x-admin.badge :tone="$toneMap[$order->status] ?? 'gray'">{{ $statusLabels[$order->status] ?? $order->status }}</x-admin.badge>
                    </td>
                    <td class="admin-td">
                        <a href="{{ route('admin.orders.show', $order) }}" title="جزئیات"
                           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                            <iconify-icon icon="tabler:eye" class="text-sm"></iconify-icon>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="border-t border-gray-100 px-5 py-4">
        {{ $orders->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
