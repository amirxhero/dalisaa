@extends('layouts.app')
@section('title', 'درباره ما')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 lg:px-6">

    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-ink-900">درباره ما</h1>
        <p class="mt-2 text-sm text-ink-400">با فروشگاه ما و ارزش‌هایی که به آن پایبندیم بیشتر آشنا شوید.</p>
    </div>

    {{-- Intro: illustration + text --}}
    <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2">
        <div class="flex items-center justify-center rounded-3xl bg-gradient-to-br from-brand-50 to-ink-50 p-6">
            <img src="{{ asset('static/media/store.webp') }}" alt="فروشگاه ما" class="w-full max-w-md object-contain">
        </div>

        <div class="space-y-4 text-ink-600">
            <h2 class="text-xl font-bold text-ink-900">فروشگاهی برای خرید مطمئن</h2>
            <p class="leading-8">
                ما با هدف ارائه تجربه‌ای آسان، سریع و مطمئن از خرید آنلاین فعالیت خود را آغاز کردیم.
                تمرکز ما بر تضمین اصالت کالا، قیمت منصفانه و پشتیبانی صمیمانه است تا شما با خیال راحت خرید کنید.
            </p>
            <p class="leading-8">
                تیم ما همواره در تلاش است تا با گسترش سبد محصولات و بهبود خدمات، نیازهای متنوع مشتریان را
                پاسخ دهد و رضایت شما را جلب کند.
            </p>
        </div>
    </div>

    {{-- Value cards --}}
    <div class="mt-12 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $values = [
                ['icon' => 'shield', 'title' => 'تضمین اصالت کالا', 'text' => 'تمام محصولات با ضمانت اصل بودن و گارانتی معتبر عرضه می‌شوند.'],
                ['icon' => 'truck', 'title' => 'ارسال سریع', 'text' => 'سفارش شما در کوتاه‌ترین زمان ممکن بسته‌بندی و ارسال می‌شود.'],
                ['icon' => 'headset', 'title' => 'پشتیبانی همیشگی', 'text' => 'کارشناسان ما برای پاسخ به سوالات شما همیشه در دسترس هستند.'],
                ['icon' => 'return', 'title' => 'ضمانت بازگشت', 'text' => 'در صورت عدم رضایت، امکان بازگشت کالا تا هفت روز فراهم است.'],
                ['icon' => 'cash', 'title' => 'پرداخت امن', 'text' => 'درگاه پرداخت امن و امکان پرداخت در محل برای اطمینان بیشتر شما.'],
                ['icon' => 'heart', 'title' => 'رضایت مشتری', 'text' => 'رضایت شما مهم‌ترین سرمایه و اولویت اصلی مجموعه ماست.'],
            ];
        @endphp
        @foreach($values as $value)
        <div class="rounded-2xl border border-ink-100 bg-white p-5 transition-shadow hover:shadow-card">
            <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-500">
                <x-icon :name="$value['icon']" class="h-5 w-5" />
            </span>
            <h3 class="mb-1.5 font-bold text-ink-900">{{ $value['title'] }}</h3>
            <p class="text-sm leading-7 text-ink-400">{{ $value['text'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- CTA --}}
    <div class="mt-12 flex flex-col items-center gap-4 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-10 text-center text-white">
        <h2 class="text-xl font-bold">سوالی دارید؟</h2>
        <p class="text-sm text-white/80">تیم ما آماده پاسخگویی به شماست. همین حالا با ما در تماس باشید.</p>
        <a href="{{ route('contact') }}"
           class="rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-brand-600 transition-colors hover:bg-white/90">
            تماس با ما
        </a>
    </div>

</div>
@endsection
