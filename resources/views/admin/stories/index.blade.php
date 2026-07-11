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

<x-admin.section>
    @if($stories->isEmpty())
        <x-admin.empty-state icon="tabler:photo-off" title="هنوز استوری‌ای اضافه نشده" description="با دکمه بالا اولین استوری را بسازید." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">استوری</th>
                    <th class="admin-th">برچسب</th>
                    <th class="admin-th">تعداد اسلاید</th>
                    <th class="admin-th">ترتیب</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($stories as $story)
                <tr class="transition-colors hover:bg-gray-50/60 {{ !$story->is_active ? 'opacity-60' : '' }}">
                    <td class="admin-td">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-brand-500 via-brand-400 to-accent-400 p-[2px]">
                                <img src="{{ $story->cover_url }}" alt="" class="h-full w-full rounded-full border-2 border-white object-cover">
                            </span>
                            <p class="font-medium text-gray-900">{{ $story->title }}</p>
                        </div>
                    </td>
                    <td class="admin-td text-gray-500">{{ $story->badge ?: '—' }}</td>
                    <td class="admin-td text-gray-500">{{ $story->getMedia('slides')->count() }}</td>
                    <td class="admin-td text-gray-500">{{ $story->sort_order }}</td>
                    <td class="admin-td">
                        <form action="{{ route('admin.stories.toggle-active', $story) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                                {{ $story->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $story->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>
                        </form>
                    </td>
                    <td class="admin-td">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.stories.edit', $story) }}" title="ویرایش"
                               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.stories.destroy', $story) }}" method="POST"
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
    @endif
</x-admin.section>
@endsection
