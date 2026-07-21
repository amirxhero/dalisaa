@extends('layouts.app')
@section('title', $category->name)

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 lg:px-6">

    {{-- Breadcrumb --}}
    <nav class="mb-4 flex items-center gap-2 text-xs text-ink-400">
        <a href="{{ route('home') }}" class="hover:text-brand-500 transition-colors">خانه</a>
        @if($category->parent)
            <span>/</span>
            <a href="{{ route('category.show', $category->parent) }}" class="hover:text-brand-500 transition-colors">{{ $category->parent->name }}</a>
        @endif
        <span>/</span>
        <span class="text-ink-600 font-semibold">{{ $category->name }}</span>
    </nav>

    {{-- Sub-categories Chips (Shown above main title) --}}
    @if($subcategories->isNotEmpty())
        <div class="mb-5">
            <div class="no-scrollbar -mx-4 flex items-center gap-2 overflow-x-auto px-4 sm:mx-0 sm:flex-wrap sm:px-0">
                @foreach($subcategories as $sub)
                    @php $isActive = $sub->id === $category->id; @endphp
                    <a href="{{ route('category.show', $sub) }}"
                       class="group flex shrink-0 items-center gap-2 rounded-2xl border px-3.5 py-2 text-xs font-bold transition-all duration-200
                              {{ $isActive ? 'border-brand-500 bg-brand-500 text-white shadow-sm shadow-brand-500/20' : 'border-ink-100 bg-white text-ink-700 hover:border-brand-300 hover:bg-brand-50/50 hover:text-brand-600' }}">
                        @if($sub->image_thumb)
                            <img src="{{ $sub->image_thumb }}" alt="" class="h-5 w-5 rounded-lg object-contain p-0.5 bg-ink-50 shrink-0">
                        @elseif($sub->icon)
                            <span class="text-sm shrink-0">{{ $sub->icon }}</span>
                        @endif
                        <span>{{ $sub->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Category Header --}}
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-extrabold text-ink-900 sm:text-2xl">{{ $category->name }}</h1>
        <p class="text-sm font-medium text-ink-500">{{ $products->total() }} محصول</p>
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
