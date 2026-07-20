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
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                {{ str_repeat('── ', $cat->depth ?? 0) }}{{ $cat->name }}
            </option>
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

<x-admin.section :padded="true">
    @if($products->isEmpty())
        <x-admin.empty-state icon="tabler:box-off" title="محصولی یافت نشد" description="فیلترها را تغییر دهید یا محصول جدیدی اضافه کنید." />
    @else
    <div class="admin-products-grid">
        @foreach($products as $product)
        <article class="group flex min-h-72 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-[0_3px_14px_-8px_rgba(15,23,42,0.25)] transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-[0_14px_30px_-16px_rgba(79,70,229,0.35)] {{ !$product->is_active ? 'bg-gray-50/70' : '' }}">
            {{-- Card badges --}}
            <div class="flex min-h-12 items-center justify-between gap-2 border-b border-gray-100 bg-gray-50/60 px-3.5 py-2">
                <form action="{{ route('admin.products.toggle-active', $product) }}" method="POST">
                    @csrf
                    <button type="submit" title="تغییر وضعیت فعال/غیرفعال"
                            class="inline-flex h-7 items-center gap-1.5 rounded-full border px-2.5 text-[11px] font-bold transition-colors
                                {{ $product->is_active
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:border-emerald-300 hover:bg-emerald-100'
                                    : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:bg-gray-100' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $product->is_active ? 'فعال' : 'غیرفعال' }}
                    </button>
                </form>

                <div class="flex items-center gap-1.5">
                    @if($product->price_currency !== 'IRR')
                        <span class="inline-flex h-7 items-center rounded-full border border-indigo-200 bg-indigo-50 px-2 font-mono text-[10px] font-bold text-indigo-700">
                            {{ $product->price_currency }}
                        </span>
                    @endif

                    @if($product->stock === 0)
                        <span class="inline-flex h-7 items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-2.5 text-[11px] font-bold text-rose-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                            ناموجود
                        </span>
                    @elseif($product->stock < 10)
                        <span class="inline-flex h-7 items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 text-[11px] font-bold text-amber-700">
                            <iconify-icon icon="tabler:alert-triangle" class="text-xs"></iconify-icon>
                            {{ $product->stock }} عدد
                        </span>
                    @else
                        <span class="inline-flex h-7 items-center gap-1.5 rounded-full border border-gray-200 bg-white px-2.5 text-[11px] font-semibold text-gray-600">
                            <iconify-icon icon="tabler:box" class="text-xs text-gray-400"></iconify-icon>
                            {{ $product->stock }} عدد
                        </span>
                    @endif
                </div>
            </div>

            {{-- Compact product summary --}}
            <div class="flex flex-1 gap-3.5 p-3.5">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-2 sm:h-28 sm:w-28">
                    <img src="{{ $product->main_thumb }}" alt="{{ $product->title }}" loading="lazy"
                         class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105">
                </div>

                <div class="min-w-0 flex-1">
                    <div class="mb-2 flex min-w-0 items-center gap-1.5 text-[10px]">
                        <span class="max-w-full truncate rounded-md bg-indigo-50 px-2 py-1 font-semibold text-indigo-700">
                            {{ $product->category?->name ?? 'بدون دسته' }}
                        </span>
                        @if($product->brand_name)
                            <span class="truncate text-gray-400" title="{{ $product->brand_name }}">{{ $product->brand_name }}</span>
                        @endif
                    </div>

                    <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-gray-800" title="{{ $product->title }}">
                        {{ $product->title }}
                    </h3>

                    @if($product->name_en)
                        <p class="mt-1 truncate text-right font-mono text-[10px] text-gray-400" dir="ltr">{{ $product->name_en }}</p>
                    @endif

                    <div class="mt-2 flex min-w-0 items-center gap-1.5 text-[10px]">
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-md border border-gray-100 bg-gray-50 px-1.5 py-1 font-mono font-medium text-gray-500">
                            <iconify-icon icon="tabler:barcode" class="text-xs text-gray-400"></iconify-icon>
                            {{ $product->sku }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Price and actions --}}
            <div class="border-t border-gray-100 px-3.5 py-3">
                <div class="mb-3 flex items-end justify-between gap-3">
                    <div class="min-w-0 text-[10px] text-gray-400">
                        @if($product->price_currency !== 'IRR' && $product->price_original > 0)
                            <p class="truncate">قیمت ارزی</p>
                            <p class="truncate font-mono font-bold text-indigo-600">{{ number_format($product->price_original) }} {{ $product->price_currency }}</p>
                        @elseif($product->regular_price && $product->regular_price > $product->price)
                            <p class="font-mono text-gray-400 line-through">{{ number_format($product->regular_price) }}</p>
                        @else
                            <p class="max-w-32 truncate font-mono text-[9px] text-gray-400" dir="ltr" title="{{ $product->slug }}">{{ $product->slug }}</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 items-baseline gap-1 text-left leading-none">
                        <span class="text-lg font-black tracking-tight text-indigo-700">{{ number_format($product->price) }}</span>
                        <span class="text-[10px] font-bold text-gray-400">تومان</span>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.products.edit', $product) }}" title="ویرایش"
                           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600">
                            <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                        </a>
                        <form action="{{ route('admin.products.duplicate', $product) }}" method="POST">
                            @csrf
                            <button type="submit" title="تکثیر"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-amber-600 transition-colors hover:border-amber-300 hover:bg-amber-50">
                                <iconify-icon icon="tabler:copy" class="text-sm"></iconify-icon>
                            </button>
                        </form>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('آیا مطمئن هستید؟ این عمل غیرقابل بازگشت است.')">
                            @csrf @method('DELETE')
                            <button type="submit" title="حذف"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-rose-500 transition-colors hover:border-rose-300 hover:bg-rose-50">
                                <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('product.show', $product) }}" target="_blank" title="مشاهده"
                       class="inline-flex h-8 items-center gap-1 rounded-lg bg-gray-100 px-2.5 text-[11px] font-semibold text-gray-600 transition-colors hover:bg-gray-200 hover:text-gray-900">
                        <iconify-icon icon="tabler:external-link" class="text-sm"></iconify-icon>
                        مشاهده
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @if($products->hasPages())
    <div class="mt-6 border-t border-gray-100 pt-4">
        {{ $products->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
