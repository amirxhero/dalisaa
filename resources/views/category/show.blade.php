@extends('layouts.app')
@section('title', $category->name)

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">

    {{-- Breadcrumb --}}
    <nav class="mb-5 flex items-center gap-2 text-xs text-ink-400">
        <a href="{{ route('home') }}" class="hover:text-brand-500">خانه</a>
        <span>/</span>
        <span class="text-ink-600">{{ $category->name }}</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-extrabold text-ink-900 sm:text-2xl">{{ $category->name }}</h1>
        <p class="text-sm text-ink-500">{{ $products->total() }} محصول</p>
    </div>

    @if($products->isEmpty())
        <div class="py-20 text-center text-ink-300">
            <p class="text-lg">محصولی در این دسته‌بندی موجود نیست</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block rounded-full bg-ink-900 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-brand-500">بازگشت به فروشگاه</a>
        </div>
    @else
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        @if($products->hasPages())
            <div class="mt-8">{{ $products->links() }}</div>
        @endif
    @endif
</div>
@endsection
