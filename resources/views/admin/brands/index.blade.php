@extends('admin.layouts.admin')
@section('title', 'برندها')
@section('page-title', 'مدیریت برندها')
@php
    $breadcrumbs = ['برندها'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">برندها</h2>
        <p class="mt-0.5 text-sm text-gray-500">{{ $brands->total() }} برند ثبت‌شده در سیستم</p>
    </div>
    <a href="{{ route('admin.brands.create') }}" class="admin-btn-primary">
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        برند جدید
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="admin-card mb-5 flex flex-wrap items-center gap-3 p-4">
    <div class="relative flex-1 min-w-[220px]">
        <iconify-icon icon="tabler:search" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو در عنوان، نام انگلیسی، اسلاگ..."
               class="admin-input pr-10">
    </div>
    <button type="submit" class="admin-btn-secondary">
        <iconify-icon icon="tabler:filter" class="text-base"></iconify-icon>
        اعمال
    </button>
    @if(request()->has('search'))
        <a href="{{ route('admin.brands.index') }}" class="text-sm text-gray-400 transition-colors hover:text-gray-600">پاک کردن</a>
    @endif
</form>

<x-admin.section>
    @if($brands->isEmpty())
        <x-admin.empty-state icon="tabler:award-off" title="برندی یافت نشد" description="برند جدیدی ایجاد کنید یا عبارت جستجو را تغییر دهید." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">لوگو / تصویر</th>
                    <th class="admin-th">عنوان برند</th>
                    <th class="admin-th">نام انگلیسی</th>
                    <th class="admin-th">اسلاگ</th>
                    <th class="admin-th">محصولات مرتبط</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($brands as $brand)
                <tr class="transition-colors hover:bg-gray-50/60 {{ !$brand->is_active ? 'opacity-60' : '' }}">
                    <td class="admin-td">
                        @if($brand->image_url)
                            <img src="{{ $brand->image_thumb }}" alt="{{ $brand->title }}" class="h-10 w-10 rounded-xl bg-gray-100 object-contain p-1 border border-gray-100">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-400 font-bold text-xs">
                                {{ mb_substr($brand->title, 0, 1) }}
                            </div>
                        @endif
                    </td>
                    <td class="admin-td font-semibold text-gray-900">
                        {{ $brand->title }}
                    </td>
                    <td class="admin-td text-gray-500 font-mono text-xs" dir="ltr">{{ $brand->title_en ?? '—' }}</td>
                    <td class="admin-td text-gray-500 font-mono text-xs" dir="ltr">{{ $brand->slug }}</td>
                    <td class="admin-td">
                        <x-admin.badge tone="indigo">{{ $brand->products_count }} محصول</x-admin.badge>
                    </td>
                    <td class="admin-td">
                        <form action="{{ route('admin.brands.toggle-active', $brand) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                                {{ $brand->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $brand->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>
                        </form>
                    </td>
                    <td class="admin-td">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.brands.edit', $brand) }}" title="ویرایش"
                               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('آیا از حذف این برند مطمئن هستید؟')">
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
    @if($brands->hasPages())
    <div class="border-t border-gray-100 px-5 py-4">
        {{ $brands->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
