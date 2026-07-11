@extends('layouts.app')

@section('title', ($address->exists ? 'ویرایش آدرس' : 'افزودن آدرس جدید').' – فروشگاه سالیکا')

@section('content')
    <x-panel-page active="addresses" :title="$address->exists ? 'ویرایش آدرس' : 'افزودن آدرس جدید'">
        <form
            action="{{ $address->exists ? route('panel.addresses.update', $address) : route('panel.addresses.store') }}"
            method="POST"
            class="max-w-xl space-y-4 rounded-2xl border border-ink-100 p-5"
        >
            @csrf
            @if ($address->exists)
                @method('PUT')
            @endif

            <div>
                <label class="mb-1.5 block text-xs font-medium text-ink-600">عنوان آدرس</label>
                <input type="text" name="title" placeholder="مثلاً خانه، محل کار" value="{{ old('title', $address->title) }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                @error('title')
                    <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-ink-600">نام گیرنده</label>
                    <input type="text" name="receiver_name" value="{{ old('receiver_name', $address->receiver_name) }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                    @error('receiver_name')
                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-ink-600">موبایل گیرنده</label>
                    <input type="text" name="receiver_mobile" dir="ltr" value="{{ old('receiver_mobile', $address->receiver_mobile) }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-left text-sm outline-none focus:border-brand-300">
                    @error('receiver_mobile')
                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-ink-600">استان</label>
                    <input type="text" name="province" value="{{ old('province', $address->province) }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                    @error('province')
                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-ink-600">شهر</label>
                    <input type="text" name="city" value="{{ old('city', $address->city) }}" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">
                    @error('city')
                        <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-ink-600">آدرس کامل</label>
                <textarea name="address_line" rows="3" class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none focus:border-brand-300">{{ old('address_line', $address->address_line) }}</textarea>
                @error('address_line')
                    <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-ink-600">کد پستی</label>
                <input type="text" name="postal_code" dir="ltr" value="{{ old('postal_code', $address->postal_code) }}" class="w-full max-w-[220px] rounded-xl border border-ink-100 bg-ink-50 p-3 text-left text-sm outline-none focus:border-brand-300">
                @error('postal_code')
                    <p class="mt-1 text-xs text-brand-500">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }} class="h-4 w-4 rounded border-ink-200 text-brand-500 focus:ring-brand-300">
                این آدرس به‌عنوان آدرس پیش‌فرض ثبت شود.
            </label>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-ink-900 px-8 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-500">
                    ذخیره آدرس
                </button>
                <a href="{{ route('panel.addresses.index') }}" class="text-sm font-medium text-ink-400 hover:text-brand-500">انصراف</a>
            </div>
        </form>
    </x-panel-page>
@endsection
