@extends('layouts.app')
@section('title', 'تماس با ما')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 lg:px-6">

    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-ink-900">تماس با ما</h1>
        <p class="mt-2 text-sm text-ink-400">خوشحال می‌شویم صدای شما را بشنویم؛ از راه‌های زیر با ما در ارتباط باشید.</p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

        {{-- ── Left: illustration + contact details ─────────────── --}}
        <div class="space-y-6">
            <div class="flex items-center justify-center rounded-3xl bg-gradient-to-br from-brand-50 to-ink-50 p-6">
                <img src="{{ asset('static/media/contact.webp') }}" alt="تماس با ما" class="w-full max-w-sm object-contain">
            </div>

            <div class="space-y-3">
                @if($siteSettings['contact_phone'])
                <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings['contact_phone']) }}"
                   class="flex items-center gap-4 rounded-2xl border border-ink-100 bg-white p-4 transition-shadow hover:shadow-card">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500">
                        <x-icon name="phone-call" class="h-5 w-5" />
                    </span>
                    <span class="flex flex-col">
                        <span class="text-xs text-ink-400">شماره تماس</span>
                        <span class="font-bold text-ink-800" dir="ltr">{{ $siteSettings['contact_phone'] }}</span>
                    </span>
                </a>
                @endif

                @if($siteSettings['contact_email'])
                <a href="mailto:{{ $siteSettings['contact_email'] }}"
                   class="flex items-center gap-4 rounded-2xl border border-ink-100 bg-white p-4 transition-shadow hover:shadow-card">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500">
                        <x-icon name="mail" class="h-5 w-5" />
                    </span>
                    <span class="flex flex-col">
                        <span class="text-xs text-ink-400">ایمیل</span>
                        <span class="font-bold text-ink-800" dir="ltr">{{ $siteSettings['contact_email'] }}</span>
                    </span>
                </a>
                @endif

                @if($siteSettings['contact_address'])
                <div class="flex items-center gap-4 rounded-2xl border border-ink-100 bg-white p-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500">
                        <x-icon name="map-pin" class="h-5 w-5" />
                    </span>
                    <span class="flex flex-col">
                        <span class="text-xs text-ink-400">آدرس</span>
                        <span class="font-medium leading-6 text-ink-800">{{ $siteSettings['contact_address'] }}</span>
                    </span>
                </div>
                @endif
            </div>

            {{-- Social links --}}
            @if($siteSettings['social_instagram'] || $siteSettings['social_telegram'] || $siteSettings['social_whatsapp'])
            <div class="flex items-center gap-3">
                <span class="text-sm text-ink-500">ما را دنبال کنید:</span>
                @if($siteSettings['social_instagram'])
                <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener noreferrer"
                   class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-50 text-ink-500 transition-colors hover:bg-brand-500 hover:text-white">
                    <x-icon name="instagram" class="h-5 w-5" />
                </a>
                @endif
                @if($siteSettings['social_telegram'])
                <a href="{{ $siteSettings['social_telegram'] }}" target="_blank" rel="noopener noreferrer"
                   class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-50 text-ink-500 transition-colors hover:bg-brand-500 hover:text-white">
                    <x-icon name="telegram" class="h-5 w-5" />
                </a>
                @endif
                @if($siteSettings['social_whatsapp'])
                <a href="{{ $siteSettings['social_whatsapp'] }}" target="_blank" rel="noopener noreferrer"
                   class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-50 text-ink-500 transition-colors hover:bg-brand-500 hover:text-white">
                    <x-icon name="whatsapp" class="h-5 w-5" />
                </a>
                @endif
            </div>
            @endif
        </div>

        {{-- ── Right: contact form ──────────────────────────────── --}}
        <div class="rounded-3xl border border-ink-100 bg-white p-6 shadow-card sm:p-8">
            <h2 class="mb-1 text-lg font-bold text-ink-900">ارسال پیام</h2>
            <p class="mb-6 text-sm text-ink-400">فرم زیر را پر کنید تا کارشناسان ما با شما تماس بگیرند.</p>

            @if(session('success'))
                <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink-700">نام و نام خانوادگی <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-ink-100 bg-ink-50 px-4 py-2.5 text-sm text-ink-800 outline-none transition-colors focus:border-brand-300 focus:bg-white"
                           placeholder="نام شما">
                    @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ink-700">ایمیل</label>
                        <input type="email" name="email" value="{{ old('email') }}" dir="ltr"
                               class="w-full rounded-xl border border-ink-100 bg-ink-50 px-4 py-2.5 text-sm text-ink-800 outline-none transition-colors focus:border-brand-300 focus:bg-white"
                               placeholder="you@example.com">
                        @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ink-700">شماره تماس</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr"
                               class="w-full rounded-xl border border-ink-100 bg-ink-50 px-4 py-2.5 text-sm text-ink-800 outline-none transition-colors focus:border-brand-300 focus:bg-white"
                               placeholder="0912...">
                        @error('phone')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink-700">پیام شما <span class="text-rose-500">*</span></label>
                    <textarea name="message" rows="5" required
                              class="w-full resize-none rounded-xl border border-ink-100 bg-ink-50 px-4 py-2.5 text-sm text-ink-800 outline-none transition-colors focus:border-brand-300 focus:bg-white"
                              placeholder="متن پیام...">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-600">
                    ارسال پیام
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
