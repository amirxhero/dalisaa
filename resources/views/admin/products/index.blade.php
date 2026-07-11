@extends('admin.layouts.admin')

@section('title', 'محصولات')
@section('page-title', 'مدیریت محصولات')
@php
    $breadcrumbs = ['محصولات'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">محصولات</h2>
        <p class="mt-0.5 text-sm text-gray-500">{{ $products->total() }} محصول در فروشگاه شما</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        محصول جدید
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="admin-card mb-5 flex flex-wrap items-center gap-3 p-4">
    <div class="relative flex-1 min-w-[220px]">
        <iconify-icon icon="tabler:search" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو در نام، برند، SKU..."
               class="admin-input pr-10">
    </div>
    <select name="category" class="admin-select w-48">
        <option value="">همه دسته‌ها</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="stock" class="admin-select w-44">
        <option value="">همه موجودی‌ها</option>
        <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>موجودی کم (زیر ۱۰)</option>
    </select>
    <button type="submit" class="admin-btn-secondary">
        <iconify-icon icon="tabler:filter" class="text-base"></iconify-icon>
        اعمال
    </button>
    @if(request()->hasAny(['search','category','stock']))
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-400 transition-colors hover:text-gray-600">پاک کردن</a>
    @endif
</form>

<x-admin.section>
    @if($products->isEmpty())
        <x-admin.empty-state icon="tabler:box-off" title="محصولی یافت نشد" description="فیلترها را تغییر دهید یا محصول جدیدی اضافه کنید." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">محصول</th>
                    <th class="admin-th">دسته</th>
                    <th class="admin-th">ارز</th>
                    <th class="admin-th">قیمت اصلی</th>
                    <th class="admin-th">قیمت (تومان)</th>
                    <th class="admin-th">موجودی</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($products as $product)
                <tr class="transition-colors hover:bg-gray-50/60 {{ !$product->is_active ? 'opacity-60' : '' }}">
                    <td class="admin-td">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->main_thumb }}" alt="" class="h-10 w-10 rounded-xl bg-gray-100 object-cover">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $product->title }}</p>
                                <p class="text-xs text-gray-400">{{ $product->brand }} · {{ $product->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="admin-td text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                    <td class="admin-td">
                        <x-admin.badge tone="gray">{{ $product->price_currency }}</x-admin.badge>
                    </td>
                    <td class="admin-td font-mono text-gray-600">{{ number_format($product->price_original) }}</td>
                    <td class="admin-td font-semibold text-indigo-700">{{ number_format($product->price) }} ت</td>
                    <td class="admin-td">
                        <x-admin.badge :tone="$product->stock === 0 ? 'rose' : ($product->stock < 10 ? 'amber' : 'emerald')">
                            {{ $product->stock }} عدد
                        </x-admin.badge>
                    </td>
                    <td class="admin-td">
                        <form action="{{ route('admin.products.toggle-active', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                                {{ $product->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $product->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>
                        </form>
                    </td>
                    <td class="admin-td">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.products.edit', $product) }}" title="ویرایش"
                               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('آیا مطمئن هستید؟ این عمل غیرقابل بازگشت است.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-rose-500 transition-colors hover:border-rose-300 hover:bg-rose-50">
                                    <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="border-t border-gray-100 px-5 py-4">
        {{ $products->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
