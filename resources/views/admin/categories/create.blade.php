@extends('admin.layouts.admin')
@section('title', 'دسته‌بندی جدید')
@section('page-title', 'افزودن دسته‌بندی')
@php
    $breadcrumbs = ['دسته‌بندی‌ها', 'ایجاد'];
@endphp

@section('content')
<div class="mx-auto max-w-lg">
    <x-admin.section title="اطلاعات دسته‌بندی">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="admin-label">نام دسته‌بندی *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">دسته والد (اختیاری)</label>
                <select name="parent_id" class="admin-select">
                    <option value="">بدون والد (دسته اصلی)</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">آیکون (اختیاری)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="مثال: 📱" class="admin-input">
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
