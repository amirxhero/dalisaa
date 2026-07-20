@extends('admin.layouts.admin')

@section('title', 'افزودن برند جدید')
@section('page-title', 'افزودن برند جدید')
@php
    $breadcrumbs = ['برندها', 'ایجاد'];
@endphp

@section('content')
<form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf

    <div class="admin-card space-y-5 p-6">
        <h2 class="text-base font-bold text-gray-900">اطلاعات برند</h2>

        <div>
            <label class="admin-label">عنوان برند (فارسی) <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="مثال: سامسونگ" class="admin-input">
            @error('title') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">نام انگلیسی (English Title)</label>
            <input type="text" name="title_en" value="{{ old('title_en') }}" placeholder="مثال: Samsung" dir="ltr" class="admin-input text-left">
            @error('title_en') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">اسلاگ / URL یکتا (Slug)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="در صورت خالی بودن خودکار ایجاد می‌شود" dir="ltr" class="admin-input text-left font-mono">
            @error('slug') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">توضیحات برند (اختیاری)</label>
            <textarea name="description" rows="3" placeholder="توضیحات کوتاهی درباره برند..." class="admin-input">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">لوگو / تصویر برند (اختیاری)</label>
            <input type="file" name="image" accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="mt-1.5 text-xs text-gray-400">فرمت‌های مجاز: JPG, PNG, WebP, SVG · حداکثر ۵ مگابایت</p>
            @error('image') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                برند فعال باشد
            </label>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-5">
            <a href="{{ route('admin.brands.index') }}" class="admin-btn-secondary">انصراف</a>
            <button type="submit" class="admin-btn-primary">
                <iconify-icon icon="tabler:check" class="text-base"></iconify-icon>
                ذخیره برند
            </button>
        </div>
    </div>
</form>
@endsection
