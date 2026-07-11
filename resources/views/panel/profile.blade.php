@extends('layouts.app')

@section('title', 'اطلاعات حساب کاربری')

@section('content')
    <x-panel-page active="profile" title="اطلاعات حساب کاربری">
        <form action="{{ route('panel.profile.update') }}" method="POST" class="max-w-xl space-y-4 rounded-2xl border border-ink-100 p-5">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1.5 block text-xs font-medium text-ink-600">نام و نام خانوادگی</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                @error('name')
                    <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-ink-600">شماره موبایل</label>
                <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" dir="ltr" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-left text-sm outline-none focus:border-brand-300">
                @error('mobile')
                    <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-ink-600">ایمیل (اختیاری)</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-left text-sm outline-none focus:border-brand-300">
                @error('email')
                    <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-dashed border-ink-100 pt-4">
                <p class="mb-3 text-xs font-medium text-ink-500">در صورت تمایل به تغییر رمز عبور، فیلدهای زیر را تکمیل کنید.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-ink-600">رمز عبور جدید</label>
                        <input type="password" name="password" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                        @error('password')
                            <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-ink-600">تکرار رمز عبور</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-ink-900 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-500 sm:w-auto sm:px-8">
                ذخیره تغییرات
            </button>
        </form>
    </x-panel-page>
@endsection
