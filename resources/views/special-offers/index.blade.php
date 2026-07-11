@extends('layouts.app')
@section('title', 'پیشنهادهای شگفت‌انگیز')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">

    {{-- Hero header --}}
    <div class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-l from-rose-50 via-purple-50 to-indigo-50 px-6 py-8 text-center ring-1 ring-black/5 sm:py-10">
        <img src="{{ asset('static/media/percent.webp') }}" alt="تخفیف" class="mx-auto mb-3 h-16 w-16 object-contain drop-shadow-sm">
        <h1 class="text-2xl font-extrabold text-ink-900 sm:text-3xl">
            <span class="text-brand-500">پیشنهادهای</span> شگفت‌انگیز
        </h1>
        <p class="mt-2 text-sm text-ink-500">تخفیف‌های ویژه و محصولات منتخب فروشگاه</p>
    </div>

    @if($products->isEmpty())
        <div class="py-20 text-center text-ink-300">
            <p class="text-lg">در حال حاضر پیشنهاد ویژه‌ای موجود نیست</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block rounded-full bg-ink-900 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-brand-500">بازگشت به فروشگاه</a>
        </div>
    @else
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm text-ink-500">{{ $products->total() }} محصول</p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
