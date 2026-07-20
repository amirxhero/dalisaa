@extends('admin.layouts.admin')
@section('title', 'ویرایش دسته‌بندی')
@section('page-title', 'ویرایش: '.$category->name)
@php
    $breadcrumbs = ['دسته‌بندی‌ها', 'ویرایش'];
@endphp

@section('content')
<div class="mx-auto max-w-lg">
    <x-admin.section title="اطلاعات دسته‌بندی">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4 p-5">
            @csrf @method('PUT')
            <div>
                <label class="admin-label">نام دسته‌بندی (فارسی) *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="admin-input">
                @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">نام انگلیسی (English Name)</label>
                <input type="text" name="name_en" value="{{ old('name_en', $category->name_en) }}" placeholder="مثال: Mobile Accessories" dir="ltr" class="admin-input text-left">
                @error('name_en') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">اسلاگ / نام مستعار یکتا (Slug)</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" dir="ltr" class="admin-input text-left font-mono">
                @error('slug') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label">دسته والد</label>
                <select name="parent_id" class="admin-select">
                    <option value="">بدون والد (دسته اصلی)</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>
                        {{ str_repeat('── ', $p->depth ?? 0) }}{{ $p->name }}
                    </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="admin-label">آیکون</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" class="admin-input">
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
