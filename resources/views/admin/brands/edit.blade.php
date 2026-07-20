@extends('admin.layouts.admin')

@section('title', 'ویرایش برند')
@section('page-title', 'ویرایش: '.$brand->title)
@php
    $breadcrumbs = ['برندها', 'ویرایش'];
@endphp

@section('content')
<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf
    @method('PUT')

    <div class="admin-card space-y-5 p-6">
        <h2 class="text-base font-bold text-gray-900">ویرایش برند {{ $brand->title }}</h2>

        <div>
            <label class="admin-label">عنوان برند (فارسی) <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $brand->title) }}" required class="admin-input">
            @error('title') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">نام انگلیسی (English Title)</label>
            <input type="text" name="title_en" value="{{ old('title_en', $brand->title_en) }}" dir="ltr" class="admin-input text-left">
            @error('title_en') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">اسلاگ / URL یکتا (Slug)</label>
            <input type="text" name="slug" value="{{ old('slug', $brand->slug) }}" dir="ltr" class="admin-input text-left font-mono">
            @error('slug') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">توضیحات برند (اختیاری)</label>
            <textarea name="description" rows="3" class="admin-input">{{ old('description', $brand->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="admin-label">لوگو / تصویر برند</label>
            @if($brand->image_url)
                <div class="mb-3 flex items-center gap-3">
                    <img src="{{ $brand->image_thumb }}" alt="" class="h-14 w-14 rounded-xl border border-gray-200 bg-gray-50 object-contain p-1">
                    <span class="text-xs text-gray-500">تصویر فعلی برند</span>
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="mt-1.5 text-xs text-gray-400">در صورت انتخاب تصویر جدید، جایگزین خواهد شد.</p>
            @error('image') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                برند فعال باشد
            </label>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-5">
            <a href="{{ route('admin.brands.index') }}" class="admin-btn-secondary">انصراف</a>
            <button type="submit" class="admin-btn-primary">
                <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                ذخیره تغییرات
            </button>
        </div>
    </div>
</form>
@endsection
