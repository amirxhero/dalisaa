@extends('admin.layouts.admin')
@section('title', 'بنرها')
@section('page-title', 'مدیریت بنرها')
@php
    $breadcrumbs = ['بنرها'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">بنرهای صفحه اصلی</h2>
        <p class="mt-0.5 text-sm text-gray-500">مدیریت بنرهای اسلایدر و تبلیغاتی صفحه اصلی (تصویر دسکتاپ و موبایل)</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="admin-btn-primary">
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        بنر جدید
    </a>
</div>

@php
    $hints = [
        'middle' => 'به ترتیب: بنر ۱ = ستون راست، بنر ۲ = ستون چپ، بنر ۳ = بنر عریض پایین. ترتیب با فیلد «ترتیب نمایش» کنترل می‌شود.',
    ];
@endphp
@foreach($positions as $key => $label)
    <div class="mb-6">
        <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700">
            <iconify-icon icon="tabler:layout-board" class="text-base text-indigo-500"></iconify-icon>
            {{ $label }}
        </h3>
        @if(!empty($hints[$key]))
            <p class="mb-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">{{ $hints[$key] }}</p>
        @endif
        <x-admin.section :padded="true">
            @php $items = $grouped[$key] ?? collect(); @endphp
            @if($items->isEmpty())
                <x-admin.empty-state icon="tabler:photo-off" title="بنری برای این بخش ثبت نشده" description="با دکمه «بنر جدید» اضافه کنید." />
            @else
            <div class="admin-index-grid">
                @foreach($items as $banner)
                <article class="group admin-list-card {{ !$banner->is_active ? 'bg-gray-50/70' : '' }}">
                    <div class="relative h-28 overflow-hidden bg-gray-100">
                                @if($banner->desktop_url)
                            <img src="{{ $banner->desktop_thumb }}" alt="{{ $banner->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                            <div class="flex h-full items-center justify-center text-gray-300"><iconify-icon icon="tabler:photo" class="text-3xl"></iconify-icon></div>
                                @endif
                        <div class="absolute left-2 top-2"><x-admin.badge tone="gray">ترتیب {{ $banner->sort_order }}</x-admin.badge></div>
                    </div>
                    <div class="admin-list-card-body">
                        <h4 class="truncate text-sm font-bold text-gray-900">{{ $banner->title ?: 'بدون عنوان' }}</h4>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-admin.badge :tone="$banner->getFirstMediaUrl('mobile') ? 'emerald' : 'gray'">
                                <iconify-icon icon="tabler:device-mobile" class="text-xs"></iconify-icon>
                                {{ $banner->getFirstMediaUrl('mobile') ? 'نسخه موبایل' : 'بدون نسخه موبایل' }}
                            </x-admin.badge>
                        </div>
                    </div>
                    <div class="admin-list-card-footer">
                        <form action="{{ route('admin.banners.toggle-active', $banner) }}" method="POST">
                            @csrf
                            <button type="submit"><x-admin.badge :tone="$banner->is_active ? 'emerald' : 'gray'">{{ $banner->is_active ? 'فعال' : 'غیرفعال' }}</x-admin.badge></button>
                        </form>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.banners.edit', $banner) }}" title="ویرایش" class="admin-icon-btn"><iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon></a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟ این عمل غیرقابل بازگشت است.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف" class="admin-icon-btn-danger"><iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon></button>
                            </form>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @endif
        </x-admin.section>
    </div>
@endforeach
@endsection
