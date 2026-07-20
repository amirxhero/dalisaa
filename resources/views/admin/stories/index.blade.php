@extends('admin.layouts.admin')
@section('title', 'استوری‌ها')
@section('page-title', 'مدیریت استوری‌ها')
@php
    $breadcrumbs = ['استوری‌ها'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">استوری‌ها</h2>
        <p class="mt-0.5 text-sm text-gray-500">استوری‌های نمایش داده شده در بالای صفحه اصلی (مانند اینستاگرام)</p>
    </div>
    <a href="{{ route('admin.stories.create') }}" class="admin-btn-primary">
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        استوری جدید
    </a>
</div>

<x-admin.section :padded="true">
    @if($stories->isEmpty())
        <x-admin.empty-state icon="tabler:photo-off" title="هنوز استوری‌ای اضافه نشده" description="با دکمه بالا اولین استوری را بسازید." />
    @else
    <div class="admin-index-grid">
        @foreach($stories as $story)
        <article class="admin-list-card {{ !$story->is_active ? 'bg-gray-50/70' : '' }}">
            <div class="admin-list-card-head">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-brand-500 via-brand-400 to-accent-400 p-[2px]">
                        <img src="{{ $story->cover_url }}" alt="{{ $story->title }}" loading="lazy" class="h-full w-full rounded-full border-2 border-white object-cover">
                    </span>
                    <div class="min-w-0"><h3 class="truncate text-sm font-bold text-gray-900">{{ $story->title }}</h3><p class="text-[10px] text-gray-400">ترتیب نمایش {{ $story->sort_order }}</p></div>
                </div>
                @if($story->badge)<x-admin.badge tone="rose">{{ $story->badge }}</x-admin.badge>@endif
            </div>
            <div class="admin-list-card-body">
                <div class="admin-meta-grid">
                    <div><span class="admin-meta-label">تعداد اسلاید</span><span class="admin-meta-value">{{ $story->getMedia('slides')->count() }} اسلاید</span></div>
                    <div><span class="admin-meta-label">ترتیب</span><span class="admin-meta-value">{{ $story->sort_order }}</span></div>
                </div>
            </div>
            <div class="admin-list-card-footer">
                        <form action="{{ route('admin.stories.toggle-active', $story) }}" method="POST">
                            @csrf
                    <button type="submit"><x-admin.badge :tone="$story->is_active ? 'emerald' : 'gray'">{{ $story->is_active ? 'فعال' : 'غیرفعال' }}</x-admin.badge></button>
                        </form>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.stories.edit', $story) }}" title="ویرایش" class="admin-icon-btn"><iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon></a>
                    <form action="{{ route('admin.stories.destroy', $story) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟ این عمل غیرقابل بازگشت است.')">
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
