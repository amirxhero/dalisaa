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
        <x-admin.section>
            @php $items = $grouped[$key] ?? collect(); @endphp
            @if($items->isEmpty())
                <x-admin.empty-state icon="tabler:photo-off" title="بنری برای این بخش ثبت نشده" description="با دکمه «بنر جدید» اضافه کنید." />
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="admin-th">پیش‌نمایش</th>
                            <th class="admin-th">عنوان</th>
                            <th class="admin-th">موبایل</th>
                            <th class="admin-th">ترتیب</th>
                            <th class="admin-th">وضعیت</th>
                            <th class="admin-th">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($items as $banner)
                        <tr class="transition-colors hover:bg-gray-50/60 {{ !$banner->is_active ? 'opacity-60' : '' }}">
                            <td class="admin-td">
                                @if($banner->desktop_url)
                                    <img src="{{ $banner->desktop_thumb }}" alt="" class="h-12 w-24 rounded-lg border border-gray-100 object-cover">
                                @else
                                    <div class="flex h-12 w-24 items-center justify-center rounded-lg bg-gray-100 text-gray-300">
                                        <iconify-icon icon="tabler:photo" class="text-lg"></iconify-icon>
                                    </div>
                                @endif
                            </td>
                            <td class="admin-td font-medium text-gray-900">{{ $banner->title ?: '—' }}</td>
                            <td class="admin-td">
                                @if($banner->getFirstMediaUrl('mobile'))
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-600">دارد</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-400">ندارد</span>
                                @endif
                            </td>
                            <td class="admin-td text-gray-500">{{ $banner->sort_order }}</td>
                            <td class="admin-td">
                                <form action="{{ route('admin.banners.toggle-active', $banner) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                                        {{ $banner->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        {{ $banner->is_active ? 'فعال' : 'غیرفعال' }}
                                    </button>
                                </form>
                            </td>
                            <td class="admin-td">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" title="ویرایش"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                        <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
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
    </div>
@endforeach
@endsection
