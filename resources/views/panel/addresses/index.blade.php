@extends('layouts.app')

@section('title', 'آدرس‌های من – فروشگاه سالیکا')

@section('content')
    <x-panel-page active="addresses">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-lg font-extrabold text-ink-900 sm:text-2xl">آدرس‌های من</h1>
            <a href="{{ route('panel.addresses.create') }}" class="flex items-center gap-1.5 rounded-full bg-brand-500 px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-600">
                <x-icon name="plus" class="h-3.5 w-3.5" />
                افزودن آدرس جدید
            </a>
        </div>

        @if ($addresses->isEmpty())
            <div class="flex flex-col items-center gap-4 rounded-2xl border border-ink-100 py-16 text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-ink-50 text-ink-300">
                    <x-icon name="map-pin" class="h-10 w-10" />
                </div>
                <h6 class="text-sm font-bold text-ink-600">هنوز آدرسی ثبت نکرده‌اید</h6>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($addresses as $address)
                    <div class="relative rounded-2xl border border-ink-100 p-4">
                        @if ($address->is_default)
                            <span class="absolute left-4 top-4 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600">پیش‌فرض</span>
                        @endif
                        <div class="mb-2 flex items-center gap-2">
                            <x-icon name="map-pin" class="h-4 w-4 text-brand-500" />
                            <span class="text-sm font-bold text-ink-900">{{ $address->title }}</span>
                        </div>
                        <p class="text-sm text-ink-700">{{ $address->receiver_name }}</p>
                        <p class="mt-1 text-xs text-ink-400" dir="ltr">{{ $address->receiver_mobile }}</p>
                        <p class="mt-2 text-sm leading-6 text-ink-600">{{ $address->full_address }}</p>
                        <p class="mt-1 text-xs text-ink-400">کد پستی: {{ $address->postal_code }}</p>

                        <div class="mt-4 flex items-center gap-4 border-t border-dashed border-ink-100 pt-3">
                            <a href="{{ route('panel.addresses.edit', $address) }}" class="text-xs font-bold text-ink-600 hover:text-brand-500">ویرایش</a>
                            <form action="{{ route('panel.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('آیا از حذف این آدرس مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-ink-400 hover:text-brand-500">حذف</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-panel-page>
@endsection
