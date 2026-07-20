@extends('admin.layouts.admin')
@section('title', 'محصولات شگفت‌انگیز')
@section('page-title', 'محصولات شگفت‌انگیز')
@php
    $breadcrumbs = ['محصولات شگفت‌انگیز'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">محصولات شگفت‌انگیز</h2>
        <p class="mt-0.5 text-sm text-gray-500">این محصولات در بخش «پیشنهاد شگفت‌انگیز» صفحه اصلی نمایش داده می‌شوند</p>
    </div>
    <div class="flex items-center gap-2 rounded-2xl bg-amber-50 border border-amber-100 px-4 py-2.5">
        <iconify-icon icon="tabler:bolt" class="text-lg text-amber-500"></iconify-icon>
        <span class="text-sm font-semibold text-amber-700">{{ $specialProducts->count() }} محصول فعال</span>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Left: current special products --}}
    <div class="lg:col-span-2 space-y-4">
        <x-admin.section title="محصولات شگفت‌انگیز فعلی" subtitle="برای حذف از لیست، دکمه حذف را بزنید">

            @if($specialProducts->isEmpty())
                <x-admin.empty-state
                    icon="tabler:bolt-off"
                    title="هنوز محصولی اضافه نشده"
                    description="از فرم سمت چپ محصولات دلخواه را جستجو کرده و به لیست اضافه کنید."
                />
            @else
            <div class="grid gap-3 md:grid-cols-2">
                @foreach($specialProducts as $product)
                <article class="admin-list-card {{ !$product->is_active ? 'bg-gray-50/70' : '' }}">
                    <div class="flex gap-3 p-3.5">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-1.5">
                            <img src="{{ $product->main_thumb }}" alt="{{ $product->title }}" loading="lazy" class="h-full w-full object-contain">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-1.5 flex flex-wrap gap-1.5">
                                @if($product->discount_percent > 0)
                                    <x-admin.badge tone="rose">{{ $product->discount_percent }}٪</x-admin.badge>
                                @endif
                                <x-admin.badge :tone="$product->stock === 0 ? 'rose' : ($product->stock < 10 ? 'amber' : 'emerald')">
                                    {{ $product->stock }} عدد
                                </x-admin.badge>
                            </div>
                            <h3 class="line-clamp-2 text-xs font-bold leading-5 text-gray-900">{{ $product->title }}</h3>
                            <p class="mt-1 truncate text-[10px] text-gray-400">{{ $product->category?->name ?? 'بدون دسته' }} · {{ $product->brand }}</p>
                            <p class="mt-2 text-sm font-black text-indigo-700">{{ number_format($product->price) }} <span class="text-[9px] text-gray-400">تومان</span></p>
                        </div>
                    </div>
                    <div class="admin-list-card-footer">
                        <span class="text-[10px] text-amber-600"><iconify-icon icon="tabler:bolt" class="align-middle"></iconify-icon> شگفت‌انگیز</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.products.edit', $product) }}" title="ویرایش" class="admin-icon-btn"><iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon></a>
                            <form action="{{ route('admin.special-products.destroy', $product) }}" method="POST" onsubmit="return confirm('این محصول از لیست شگفت‌انگیز حذف می‌شود. ادامه می‌دهید؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف از لیست" class="admin-icon-btn-danger"><iconify-icon icon="tabler:bolt-off" class="text-sm"></iconify-icon></button>
                            </form>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @endif

        </x-admin.section>
    </div>

    {{-- Right: search & add --}}
    <div class="space-y-4">

        {{-- Search form --}}
        <x-admin.section title="افزودن محصول" subtitle="نام، برند یا کد محصول را جستجو کنید">
            <form method="GET" action="{{ route('admin.special-products.index') }}" class="px-5 pt-4 pb-2">
                <div class="relative">
                    <iconify-icon icon="tabler:search" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="جستجوی محصول..."
                        class="admin-input pr-10 w-full"
                        autofocus
                    >
                </div>
                <button type="submit" class="mt-2 admin-btn-secondary w-full justify-center">
                    <iconify-icon icon="tabler:search" class="text-base"></iconify-icon>
                    جستجو
                </button>
            </form>

            <p class="px-5 pb-2 text-[11px] font-medium text-gray-400">
                {{ $search ? 'نتایج جستجو' : 'آخرین محصولات' }}
            </p>

            @if($searchResults->isEmpty())
                <div class="px-5 pb-5 text-center text-sm text-gray-400">
                    <iconify-icon icon="tabler:mood-empty" class="mb-1 text-2xl"></iconify-icon>
                    <p>محصولی یافت نشد</p>
                </div>
            @else
            <div class="max-h-96 divide-y divide-gray-50 overflow-y-auto px-5 pb-4">
                @foreach($searchResults as $product)
                <div class="flex items-center gap-3 py-3">
                    <img src="{{ $product->main_thumb }}" alt="" class="h-9 w-9 rounded-lg bg-gray-100 object-cover shrink-0">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-medium text-gray-800">{{ $product->title }}</p>
                        <p class="text-[11px] text-gray-400">{{ number_format($product->price) }} ت</p>
                    </div>
                    <form action="{{ route('admin.special-products.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" title="افزودن به لیست"
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition-colors hover:bg-indigo-100">
                            <iconify-icon icon="tabler:plus" class="text-sm"></iconify-icon>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </x-admin.section>

        {{-- Info box --}}
        <div class="flex gap-3 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4">
            <iconify-icon icon="tabler:bulb" class="mt-0.5 shrink-0 text-lg text-amber-500"></iconify-icon>
            <div class="text-xs text-amber-800 leading-5">
                <p class="mb-1 font-semibold text-sm">راهنما</p>
                <p>محصولاتی که اینجا اضافه می‌کنید در کاروسل «پیشنهاد شگفت‌انگیز» صفحه اصلی نمایش داده می‌شوند. برای نمایش درصد تخفیف، حتماً <strong>قیمت اصلی</strong> محصول را در بخش ویرایش محصول وارد کنید.</p>
            </div>
        </div>

    </div>
</div>

@endsection
