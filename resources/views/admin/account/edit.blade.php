@extends('admin.layouts.admin')

@section('title', 'حساب کاربری')
@section('page-title', 'تنظیمات حساب کاربری')
@php
    $breadcrumbs = ['حساب کاربری'];
@endphp

@section('content')

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-900">تنظیمات حساب کاربری</h2>
    <p class="mt-0.5 text-sm text-gray-500">ویرایش اطلاعات شخصی و رمز عبور مدیر</p>
</div>

<div class="mx-auto max-w-2xl">
    <x-admin.section title="اطلاعات شخصی">
        <form action="{{ route('admin.account.update') }}" method="POST" class="space-y-5 p-5">
            @csrf
            @method('PUT')

            <div>
                <label class="admin-label">نام و نام خانوادگی <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="admin-input">
            </div>

            <div>
                <label class="admin-label">شماره موبایل <span class="text-rose-500">*</span></label>
                <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" dir="ltr" required
                       placeholder="09123456789" class="admin-input text-left font-mono">
            </div>

            <div>
                <label class="admin-label">ایمیل (اختیاری)</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr"
                       class="admin-input text-left">
            </div>

            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50/60 p-4">
                <p class="mb-4 text-sm font-medium text-gray-700">تغییر رمز عبور</p>
                <p class="mb-4 text-xs text-gray-500">در صورت تمایل به تغییر رمز عبور، فیلدهای زیر را تکمیل کنید.</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="admin-label">رمز عبور جدید</label>
                        <input type="password" name="password" autocomplete="new-password" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">تکرار رمز عبور</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="admin-input">
                    </div>
                </div>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-5">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </x-admin.section>
</div>

@endsection
