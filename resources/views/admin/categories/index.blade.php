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

<x-admin.section>
    @if($categories->isEmpty())
        <x-admin.empty-state icon="tabler:folder-off" title="دسته‌بندی‌ای یافت نشد" description="اولین دسته‌بندی خود را اضافه کنید." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">نام</th>
                    <th class="admin-th">نام انگلیسی</th>
                    <th class="admin-th">اسلاگ</th>
                    <th class="admin-th">والد</th>
                    <th class="admin-th">آیکون</th>
                    <th class="admin-th">ترتیب</th>
                    <th class="admin-th">محصولات</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($categories as $cat)
                @php $depth = $cat->depth ?? 0; @endphp
                <tr class="transition-colors hover:bg-gray-50/60">
                    <td class="admin-td font-medium {{ $depth > 0 ? 'text-gray-600' : 'text-gray-900' }}" style="padding-right: {{ $depth * 1.5 + 0.75 }}rem;">
                        @if($depth > 0)
                            <span class="text-gray-400 font-normal">└ </span>
                        @endif
                        {{ $cat->name }}
                    </td>
                    <td class="admin-td text-gray-500 font-mono text-xs" dir="ltr">{{ $cat->name_en ?? '—' }}</td>
                    <td class="admin-td text-gray-500 font-mono text-xs" dir="ltr">{{ $cat->slug }}</td>
                    <td class="admin-td text-gray-500">{{ $cat->parent?->name ?? '—' }}</td>
                    <td class="admin-td text-gray-500">{{ $cat->icon ?? '—' }}</td>
                    <td class="admin-td text-gray-500">{{ $cat->sort_order }}</td>
                    <td class="admin-td">
                        <x-admin.badge tone="gray">{{ $cat->products_count }}</x-admin.badge>
                    </td>
                    <td class="admin-td">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.categories.edit', $cat) }}" title="ویرایش"
                               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('حذف دسته‌بندی؟')">
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
