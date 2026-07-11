@extends('admin.layouts.admin')
@section('title', 'تنظیمات')
@section('page-title', 'تنظیمات سیستم')
@php
    $breadcrumbs = ['تنظیمات'];
@endphp

@section('content')

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-900">تنظیمات سیستم</h2>
    <p class="mt-0.5 text-sm text-gray-500">نرخ ارز و پیکربندی‌های عمومی فروشگاه</p>
</div>

<div class="mx-auto max-w-2xl space-y-6">

    {{-- Currency Rates --}}
    <x-admin.section title="نرخ ارز (معادل تومان)" subtitle="با ذخیره تغییرات، قیمت تومانی محصولات به‌روزرسانی می‌شود">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="divide-y divide-gray-50 px-5">
                @php
                    $currencyList = [
                        'usd_rate'  => ['label' => 'دلار آمریکا', 'symbol' => '$', 'code' => 'USD'],
                        'eur_rate'  => ['label' => 'یورو',         'symbol' => '€', 'code' => 'EUR'],
                        'usdt_rate' => ['label' => 'تتر (USDT)',   'symbol' => '₮', 'code' => 'USDT'],
                        'gbp_rate'  => ['label' => 'پوند انگلیس', 'symbol' => '£', 'code' => 'GBP'],
                    ];
                @endphp

                @foreach($currencyList as $key => $info)
                <div class="flex items-center gap-4 py-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-base font-bold text-gray-500">
                        {{ $info['symbol'] }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $info['label'] }}</p>
                        <p class="text-xs text-gray-400">{{ $info['code'] }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            type="number"
                            name="{{ $key }}"
                            value="{{ old($key, $rates[$info['code']] ?? '') }}"
                            min="1"
                            required
                            class="admin-input w-36 text-left font-mono"
                        >
                        <span class="text-sm text-gray-400">تومان</span>
                    </div>
                </div>
                @endforeach

                <div class="flex items-center gap-4 py-4 opacity-50">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-base font-bold text-gray-500">﷼</div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">تومان</p>
                        <p class="text-xs text-gray-400">IRR — ارز پایه</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="block w-36 rounded-xl border border-gray-100 bg-gray-50 px-3.5 py-2.5 text-left font-mono text-sm text-gray-400">1</span>
                        <span class="text-sm text-gray-400">تومان</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-100 px-5 py-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره و به‌روزرسانی قیمت‌ها
                </button>
            </div>
        </form>
    </x-admin.section>

    {{-- Contact Info --}}
    <x-admin.section title="اطلاعات تماس" subtitle="این اطلاعات در فوتر و صفحات سایت نمایش داده می‌شود">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            {{-- hidden currency fields so they pass validation --}}
            @foreach(['usd_rate','eur_rate','usdt_rate','gbp_rate'] as $rateKey)
                <input type="hidden" name="{{ $rateKey }}" value="{{ \App\Models\Setting::get($rateKey, 1) }}">
            @endforeach
            <div class="divide-y divide-gray-50 px-5">
                <div class="py-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">آدرس</label>
                    <textarea
                        name="contact_address"
                        rows="2"
                        class="admin-input w-full resize-none"
                        placeholder="آدرس فیزیکی فروشگاه"
                    >{{ old('contact_address', \App\Models\Setting::get('contact_address')) }}</textarea>
                </div>
                <div class="py-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">شماره تلفن</label>
                    <input
                        type="text"
                        name="contact_phone"
                        value="{{ old('contact_phone', \App\Models\Setting::get('contact_phone')) }}"
                        class="admin-input w-full"
                        placeholder="021 12345678"
                        dir="ltr"
                    >
                </div>
                <div class="py-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">ایمیل</label>
                    <input
                        type="email"
                        name="contact_email"
                        value="{{ old('contact_email', \App\Models\Setting::get('contact_email')) }}"
                        class="admin-input w-full"
                        placeholder="info@example.com"
                        dir="ltr"
                    >
                </div>
            </div>
            <div class="border-t border-gray-100 px-5 py-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره اطلاعات تماس
                </button>
            </div>
        </form>
    </x-admin.section>

    {{-- Social Media --}}
    <x-admin.section title="شبکه‌های اجتماعی" subtitle="لینک‌های شبکه‌های اجتماعی در فوتر سایت نمایش داده می‌شوند">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            {{-- hidden currency fields so they pass validation --}}
            @foreach(['usd_rate','eur_rate','usdt_rate','gbp_rate'] as $rateKey)
                <input type="hidden" name="{{ $rateKey }}" value="{{ \App\Models\Setting::get($rateKey, 1) }}">
            @endforeach
            <div class="divide-y divide-gray-50 px-5">
                @php
                    $socialList = [
                        'social_instagram' => ['label' => 'اینستاگرام', 'icon' => 'tabler:brand-instagram', 'placeholder' => 'https://instagram.com/yourpage'],
                        'social_telegram'  => ['label' => 'تلگرام',     'icon' => 'tabler:brand-telegram',  'placeholder' => 'https://t.me/yourchannel'],
                        'social_whatsapp'  => ['label' => 'واتساپ',     'icon' => 'tabler:brand-whatsapp',  'placeholder' => 'https://wa.me/989123456789'],
                    ];
                @endphp
                @foreach($socialList as $key => $info)
                <div class="flex items-center gap-4 py-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-gray-500">
                        <iconify-icon icon="{{ $info['icon'] }}" class="text-lg"></iconify-icon>
                    </div>
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-800">{{ $info['label'] }}</label>
                    </div>
                    <input
                        type="url"
                        name="{{ $key }}"
                        value="{{ old($key, \App\Models\Setting::get($key)) }}"
                        class="admin-input w-64"
                        placeholder="{{ $info['placeholder'] }}"
                        dir="ltr"
                    >
                </div>
                @endforeach
            </div>
            <div class="border-t border-gray-100 px-5 py-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره شبکه‌های اجتماعی
                </button>
            </div>
        </form>
    </x-admin.section>

    {{-- Info --}}
    <div class="flex gap-3 rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4">
        <iconify-icon icon="tabler:bulb" class="mt-0.5 shrink-0 text-lg text-amber-500"></iconify-icon>
        <div class="text-sm text-amber-800">
            <p class="mb-1 font-semibold">نکته مهم</p>
            <p>قیمت نمایش داده شده به کاربران همیشه به تومان است. وقتی محصولی با ارز خارجی (دلار، یورو و ...) قیمت‌گذاری می‌شود، سیستم بر اساس نرخ همین صفحه آن را به تومان تبدیل می‌کند. هر بار که نرخ ارز را تغییر دهید، قیمت تومانی تمام محصولات خودکار به‌روز می‌شود.</p>
        </div>
    </div>

</div>
@endsection
