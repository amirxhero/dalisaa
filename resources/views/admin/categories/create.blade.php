@extends('admin.layouts.admin')
@section('title', 'دسته‌بندی جدید')
@section('page-title', 'افزودن دسته‌بندی')
@php
    $breadcrumbs = ['دسته‌بندی‌ها', 'ایجاد'];
@endphp

@section('content')
<div class="mx-auto max-w-lg">
    <x-admin.section title="اطلاعات دسته‌بندی">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="admin-label">نام دسته‌بندی (فارسی) *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="admin-input">
                @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">نام انگلیسی (English Name)</label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="مثال: Mobile Accessories" dir="ltr" class="admin-input text-left">
                @error('name_en') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">اسلاگ / نام مستعار یکتا (Slug)</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="در صورت خالی بودن خودکار ایجاد می‌شود" dir="ltr" class="admin-input text-left font-mono">
                @error('slug') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">دسته والد (اختیاری)</label>
                <select name="parent_id" class="admin-select">
                    <option value="">بدون والد (دسته اصلی)</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                        {{ str_repeat('── ', $p->depth ?? 0) }}{{ $p->name }}
                    </option>
                    @endforeach
                </select>
                @error('parent_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">آیکون (اختیاری)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="مثال: 📱" class="admin-input">
            </div>
            <div>
                <label class="admin-label">تصویر دسته‌بندی (اختیاری)</label>
                <input type="file" name="image" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1.5 text-xs text-gray-400">فرمت‌های مجاز: JPG, PNG, WebP, SVG · حداکثر ۵ مگابایت</p>
                @error('image') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="admin-input">
            </div>
            <div class="flex gap-3 border-t border-gray-100 pt-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره
                </button>
                <a href="{{ route('admin.categories.index') }}" class="admin-btn-secondary">انصراف</a>
            </div>
        </form>
    </x-admin.section>
</div>
@endsection
