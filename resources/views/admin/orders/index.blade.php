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

<x-admin.section :padded="true">
    @if($orders->isEmpty())
        <x-admin.empty-state icon="tabler:shopping-cart-off" title="سفارشی یافت نشد" />
    @else
    <div class="admin-index-grid">
        @foreach($orders as $order)
        <article class="admin-list-card">
            <div class="admin-list-card-head">
                <div><span class="text-[10px] text-gray-400">شماره سفارش</span><a href="{{ route('admin.orders.show', $order) }}" class="block font-mono text-xs font-bold text-indigo-700 hover:underline">{{ $order->order_number }}</a></div>
                <x-admin.badge :tone="$toneMap[$order->status] ?? 'gray'">{{ $statusLabels[$order->status] ?? $order->status }}</x-admin.badge>
            </div>
            <div class="admin-list-card-body">
                <div class="mb-4 flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-sm font-black text-indigo-600">{{ mb_substr($order->receiver_name, 0, 1) }}</span><div class="min-w-0"><p class="truncate text-sm font-bold text-gray-900">{{ $order->receiver_name }}</p><p class="font-mono text-[10px] text-gray-400">{{ $order->receiver_mobile }}</p></div></div>
                <div class="admin-meta-grid">
                    <div><span class="admin-meta-label">مبلغ سفارش</span><span class="admin-meta-value font-bold text-indigo-700">{{ number_format($order->total) }} تومان</span></div>
                    <div><span class="admin-meta-label">تاریخ</span><span class="admin-meta-value">{{ $order->created_at->format('Y/m/d') }}</span></div>
                    <div class="col-span-2"><span class="admin-meta-label">کد تخفیف</span><span class="admin-meta-value font-mono text-amber-700">{{ $order->discount_code ?: 'بدون کد تخفیف' }}</span></div>
                </div>
            </div>
            <div class="admin-list-card-footer">
                <span class="text-[10px] text-gray-400">مشاهده اطلاعات کامل سفارش</span>
                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex h-8 items-center gap-1 rounded-lg bg-indigo-50 px-3 text-[11px] font-semibold text-indigo-700 hover:bg-indigo-100"><iconify-icon icon="tabler:eye" class="text-sm"></iconify-icon>جزئیات</a>
            </div>
        </article>
        @endforeach
    </div>
    @if($orders->hasPages())
    <div class="mt-6 border-t border-gray-100 pt-4">
        {{ $orders->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
