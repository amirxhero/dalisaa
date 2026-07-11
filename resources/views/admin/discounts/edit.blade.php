@extends('admin.layouts.admin')
@section('title', 'ویرایش کد تخفیف')
@section('page-title', 'ویرایش: '.$discount->code)
@php
    $breadcrumbs = ['کدهای تخفیف', 'ویرایش'];
@endphp

@php
    use App\Support\JalaliDate;
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <x-admin.section title="اطلاعات کد تخفیف">
        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="space-y-5 p-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="admin-label">کد تخفیف *</label>
                    <input type="text" name="code" value="{{ old('code', $discount->code) }}" required style="text-transform:uppercase"
                           class="admin-input font-mono uppercase">
                </div>
                <div>
                    <label class="admin-label">نوع تخفیف *</label>
                    <select name="type" required class="admin-select">
                        <option value="percent" {{ old('type', $discount->type) === 'percent' ? 'selected' : '' }}>درصدی (٪)</option>
                        <option value="fixed"   {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>مقداری (تومان)</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">مقدار *</label>
                    <input type="number" name="value" value="{{ old('value', $discount->value) }}" required step="0.01" min="0" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">حداقل مبلغ سفارش (تومان)</label>
                    <input type="number" name="min_order" value="{{ old('min_order', $discount->min_order) }}" min="0" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">حداکثر استفاده</label>
                    <input type="number" name="max_uses" value="{{ old('max_uses', $discount->max_uses) }}" min="1" class="admin-input">
                </div>
                <div></div>
                <div>
                    <x-admin.jalali-date-input name="starts_at" id="starts_at" label="تاریخ شروع"
                        :value="old('starts_at', JalaliDate::fromCarbon($discount->starts_at))" />
                </div>
                <div>
                    <x-admin.jalali-date-input name="expires_at" id="expires_at" label="تاریخ انقضا"
                        :value="old('expires_at', JalaliDate::fromCarbon($discount->expires_at))" min-date="#starts_at" />
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        فعال باشد
                    </label>
                </div>
            </div>
            <div class="flex gap-3 border-t border-gray-100 pt-5">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره
                </button>
                <a href="{{ route('admin.discounts.index') }}" class="admin-btn-secondary">انصراف</a>
            </div>
        </form>
    </x-admin.section>
</div>
@endsection
