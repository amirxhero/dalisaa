@extends('admin.layouts.admin')
@section('title', 'دسته‌بندی‌ها')
@section('page-title', 'مدیریت دسته‌بندی‌ها')
@php
    $breadcrumbs = ['دسته‌بندی‌ها'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">دسته‌بندی‌ها</h2>
        <p class="mt-0.5 text-sm text-gray-500">{{ $categories->count() }} دسته‌بندی ثبت‌شده</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="admin-btn-primary">
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        دسته جدید
    </a>
</div>

<x-admin.section :padded="true">
    @if($categories->isEmpty())
        <x-admin.empty-state icon="tabler:folder-off" title="دسته‌بندی‌ای یافت نشد" description="اولین دسته‌بندی خود را اضافه کنید." />
    @else
    <div class="admin-index-grid">
        @foreach($categories as $cat)
        @php $depth = $cat->depth ?? 0; @endphp
        <article class="admin-list-card">
            <div class="admin-list-card-head">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $depth > 0 ? 'bg-gray-100 text-gray-500' : 'bg-indigo-50 text-indigo-600' }}">
                        <iconify-icon icon="{{ $cat->icon ?: 'tabler:folder' }}" class="text-xl"></iconify-icon>
                    </span>
                    <div class="min-w-0">
                        <h3 class="truncate text-sm font-bold text-gray-900">{{ $cat->name }}</h3>
                        <p class="truncate font-mono text-[10px] text-gray-400" dir="ltr">{{ $cat->name_en ?? $cat->slug }}</p>
                    </div>
                </div>
                <x-admin.badge :tone="$depth > 0 ? 'gray' : 'indigo'">{{ $depth > 0 ? 'زیر‌دسته' : 'دسته اصلی' }}</x-admin.badge>
            </div>
            <div class="admin-list-card-body">
                <div class="admin-meta-grid">
                    <div><span class="admin-meta-label">دسته والد</span><span class="admin-meta-value">{{ $cat->parent?->name ?? 'بدون والد' }}</span></div>
                    <div><span class="admin-meta-label">محصولات</span><x-admin.badge tone="gray">{{ $cat->products_count }} محصول</x-admin.badge></div>
                    <div><span class="admin-meta-label">ترتیب نمایش</span><span class="admin-meta-value">{{ $cat->sort_order }}</span></div>
                    <div><span class="admin-meta-label">اسلاگ</span><span class="admin-meta-value font-mono" dir="ltr">{{ $cat->slug }}</span></div>
                </div>
            </div>
            <div class="admin-list-card-footer">
                <span class="text-[10px] text-gray-400">سطح {{ $depth + 1 }}</span>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.categories.edit', $cat) }}" title="ویرایش" class="admin-icon-btn"><iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon></a>
                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('حذف دسته‌بندی؟')">
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
@endsection
