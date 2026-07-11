@extends('layouts.app')

@section('title', 'علاقه‌مندی‌های من')

@section('content')
    <x-panel-page active="wishlist" title="علاقه‌مندی‌های من">
        @if ($wishlists->isEmpty())
            <div class="flex flex-col items-center gap-4 rounded-2xl border border-ink-100 py-16 text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-ink-50 text-ink-300">
                    <x-icon name="heart" class="h-10 w-10" />
                </div>
                <h6 class="text-sm font-bold text-ink-600">لیست علاقه‌مندی‌های شما خالی است</h6>
                <a href="{{ route('home') }}" class="rounded-full bg-brand-500 px-6 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-600">
                    مشاهده محصولات
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                @foreach ($wishlists as $wishlist)
                    <x-product-card :product="$wishlist->product" />
                @endforeach
            </div>
        @endif
    </x-panel-page>
@endsection
