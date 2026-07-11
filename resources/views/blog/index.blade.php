@extends('layouts.app')
@section('title', 'وبلاگ')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 lg:px-6">

    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-ink-900">وبلاگ</h1>
        <p class="mt-2 text-sm text-ink-400">آخرین مقالات و اخبار</p>
    </div>

    @if($posts->isEmpty())
        <div class="py-24 text-center text-ink-300">
            <p class="text-lg">هنوز مقاله‌ای منتشر نشده</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
            <a href="{{ route('blog.show', $post) }}"
               class="group flex flex-col overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-card transition-shadow hover:shadow-card-hover">
                <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                    @if($post->cover_url)
                        <img src="{{ $post->cover_url }}" alt="{{ $post->title }}"
                             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <p class="mb-2 text-xs text-ink-400">
                        {{ $post->published_at?->diffForHumans() ?? $post->created_at->diffForHumans() }}
                        @if($post->user) · {{ $post->user->name }} @endif
                    </p>
                    <h2 class="mb-2 line-clamp-2 font-bold text-ink-900 transition-colors group-hover:text-brand-500">
                        {{ $post->title }}
                    </h2>
                    @if($post->excerpt)
                        <p class="line-clamp-3 text-sm leading-6 text-ink-400">{{ $post->excerpt }}</p>
                    @endif
                    <div class="mt-auto pt-4">
                        <span class="text-xs font-semibold text-brand-500">بیشتر بخوانید ←</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        @if($posts->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $posts->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
