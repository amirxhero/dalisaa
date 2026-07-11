@extends('admin.layouts.admin')
@section('title', 'استوری جدید')
@section('page-title', 'افزودن استوری')
@php
    $breadcrumbs = ['استوری‌ها', 'ایجاد'];
@endphp

@section('content')
<div class="mx-auto max-w-lg">
    <x-admin.section title="اطلاعات استوری">
        <form action="{{ route('admin.stories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="admin-label">عنوان *</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="50" class="admin-input" placeholder="مثال: شگفت‌انگیز">
            </div>
            <div>
                <label class="admin-label">برچسب (اختیاری)</label>
                <input type="text" name="badge" value="{{ old('badge') }}" maxlength="20" class="admin-input" placeholder="مثال: جدید، ویدیو، تخفیف ویژه">
            </div>
            <div>
                <label class="admin-label">لینک (اختیاری)</label>
                <input type="url" name="link" value="{{ old('link') }}" class="admin-input" placeholder="https://..." dir="ltr">
            </div>
            <div>
                <label class="admin-label">ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="admin-input">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                <label for="is_active" class="text-sm text-gray-700">نمایش در سایت</label>
            </div>

            <div>
                <label class="admin-label">تصاویر استوری *</label>
                <div class="rounded-2xl border-2 border-dashed border-gray-200 p-8 text-center transition-colors hover:border-indigo-200">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                        <iconify-icon icon="tabler:photo-plus" class="text-xl"></iconify-icon>
                    </div>
                    <p class="mb-3 text-sm text-gray-500">تصویر اول به عنوان آواتار استوری استفاده می‌شود</p>
                    <input type="file" name="images[]" multiple accept="image/*" required
                           class="mx-auto block w-full max-w-sm text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-2 text-xs text-gray-400">می‌توانید چند تصویر برای چند اسلاید انتخاب کنید · حداکثر ۵ مگابایت هر تصویر</p>
                </div>
            </div>

            <div class="flex gap-3 border-t border-gray-100 pt-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره
                </button>
                <a href="{{ route('admin.stories.index') }}" class="admin-btn-secondary">انصراف</a>
            </div>
        </form>
    </x-admin.section>
</div>
@endsection
