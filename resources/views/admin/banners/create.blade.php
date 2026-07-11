@extends('admin.layouts.admin')
@section('title', 'بنر جدید')
@section('page-title', 'افزودن بنر')
@php
    $breadcrumbs = ['بنرها', 'ایجاد'];
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <x-admin.section title="اطلاعات بنر">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-5">
            @csrf

            <div>
                <label class="admin-label">عنوان (اختیاری)</label>
                <input type="text" name="title" value="{{ old('title') }}" maxlength="255" class="admin-input" placeholder="مثال: تخفیف ویژه تابستان">
                @error('title')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="admin-label">موقعیت *</label>
                    <select name="position" class="admin-input">
                        @foreach($positions as $key => $label)
                            <option value="{{ $key }}" {{ old('position') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('position')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">ترتیب نمایش</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="admin-input">
                </div>
            </div>

            <div>
                <label class="admin-label">لینک (اختیاری)</label>
                <input type="url" name="link" value="{{ old('link') }}" class="admin-input" placeholder="https://..." dir="ltr">
                @error('link')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="admin-label">تصویر دسکتاپ *</label>
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 p-5 text-center transition-colors hover:border-indigo-200">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-400">
                            <iconify-icon icon="tabler:device-desktop" class="text-lg"></iconify-icon>
                        </div>
                        <input type="file" name="desktop_image" accept="image/*" required
                               class="mx-auto block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-2 text-xs text-gray-400">نسبت پهن · حداکثر ۵ مگابایت</p>
                    </div>
                    @error('desktop_image')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="admin-label">تصویر موبایل (اختیاری)</label>
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 p-5 text-center transition-colors hover:border-indigo-200">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-400">
                            <iconify-icon icon="tabler:device-mobile" class="text-lg"></iconify-icon>
                        </div>
                        <input type="file" name="mobile_image" accept="image/*"
                               class="mx-auto block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-2 text-xs text-gray-400">در صورت خالی بودن، تصویر دسکتاپ نمایش داده می‌شود</p>
                    </div>
                    @error('mobile_image')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                <label for="is_active" class="text-sm text-gray-700">نمایش در سایت</label>
            </div>

            <div class="flex gap-3 border-t border-gray-100 pt-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره
                </button>
                <a href="{{ route('admin.banners.index') }}" class="admin-btn-secondary">انصراف</a>
            </div>
        </form>
    </x-admin.section>
</div>
@endsection
