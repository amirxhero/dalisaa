@extends('admin.layouts.admin')
@section('title', 'ویرایش بنر')
@section('page-title', 'ویرایش بنر')
@php
    $breadcrumbs = ['بنرها', 'ویرایش'];
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <x-admin.section title="اطلاعات بنر">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-5">
            @csrf @method('PUT')

            <div>
                <label class="admin-label">عنوان (اختیاری)</label>
                <input type="text" name="title" value="{{ old('title', $banner->title) }}" maxlength="255" class="admin-input" placeholder="مثال: تخفیف ویژه تابستان">
                @error('title')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="admin-label">موقعیت *</label>
                    <select name="position" class="admin-input">
                        @foreach($positions as $key => $label)
                            <option value="{{ $key }}" {{ old('position', $banner->position) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('position')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">ترتیب نمایش</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0" class="admin-input">
                </div>
            </div>

            <div>
                <label class="admin-label">لینک (اختیاری)</label>
                <input type="url" name="link" value="{{ old('link', $banner->link) }}" class="admin-input" placeholder="https://..." dir="ltr">
                @error('link')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="admin-label">تصویر دسکتاپ</label>
                    @if($banner->desktop_url)
                        <img src="{{ $banner->desktop_url }}" alt="" class="mb-2 w-full rounded-xl border border-gray-100 object-cover">
                    @endif
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 p-4 text-center transition-colors hover:border-indigo-200">
                        <input type="file" name="desktop_image" accept="image/*"
                               class="mx-auto block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-400">برای جایگزینی، تصویر جدید انتخاب کنید</p>
                    </div>
                    @error('desktop_image')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">تصویر موبایل</label>
                    @if($banner->getFirstMediaUrl('mobile'))
                        <img src="{{ $banner->getFirstMediaUrl('mobile') }}" alt="" class="mb-2 w-full rounded-xl border border-gray-100 object-cover">
                    @endif
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 p-4 text-center transition-colors hover:border-indigo-200">
                        <input type="file" name="mobile_image" accept="image/*"
                               class="mx-auto block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-400">در صورت خالی بودن، تصویر دسکتاپ نمایش داده می‌شود</p>
                    </div>
                    @error('mobile_image')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                <label for="is_active" class="text-sm text-gray-700">نمایش در سایت</label>
            </div>

            <div class="flex gap-3 border-t border-gray-100 pt-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره تغییرات
                </button>
                <a href="{{ route('admin.banners.index') }}" class="admin-btn-secondary">انصراف</a>
            </div>
        </form>
    </x-admin.section>
</div>
@endsection
