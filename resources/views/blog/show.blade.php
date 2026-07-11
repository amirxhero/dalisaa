@extends('layouts.app')
@section('title', $post->title)

@push('styles')
<style>
.blog-content h1 { font-size: 1.875rem; font-weight: 700; margin: 1.5rem 0 .75rem; color: #111827; }
.blog-content h2 { font-size: 1.5rem;   font-weight: 700; margin: 1.5rem 0 .75rem; color: #111827; }
.blog-content h3 { font-size: 1.25rem;  font-weight: 600; margin: 1.25rem 0 .5rem; color: #111827; }
.blog-content p  { margin-bottom: 1rem; line-height: 1.9; color: #374151; }
.blog-content ul, .blog-content ol { padding-right: 1.5rem; margin-bottom: 1rem; color: #374151; }
.blog-content ul { list-style: disc; }
.blog-content ol { list-style: decimal; }
.blog-content li { margin-bottom: .4rem; line-height: 1.8; }
.blog-content blockquote { border-right: 4px solid #6366f1; padding: .75rem 1rem; background: #f5f3ff; border-radius: 0 .5rem .5rem 0; margin: 1.25rem 0; color: #4b5563; font-style: italic; }
.blog-content pre  { background: #1e1e2e; color: #cdd6f4; padding: 1.25rem; border-radius: .75rem; overflow-x: auto; margin: 1.25rem 0; }
.blog-content code { background: #f1f5f9; padding: .15em .4em; border-radius: .25rem; font-size: .875em; color: #6366f1; }
.blog-content pre code { background: transparent; color: inherit; padding: 0; }
.blog-content a { color: #6366f1; text-decoration: underline; }
.blog-content img { max-width: 100%; border-radius: .75rem; margin: 1rem auto; display: block; }
.blog-content mark { background: #fef08a; padding: .1em .3em; border-radius: .25rem; }
.blog-content hr { border: none; border-top: 2px solid #e5e7eb; margin: 2rem 0; }
.blog-content strong { font-weight: 700; color: #111827; }
</style>
@endpush

@section('content')
<article class="mx-auto max-w-3xl px-4 py-10 lg:px-6">

    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-xs text-ink-400">
        <a href="{{ route('home') }}" class="hover:text-brand-500">خانه</a>
        <span>/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-brand-500">وبلاگ</a>
        <span>/</span>
        <span class="text-ink-600">{{ Str::limit($post->title, 40) }}</span>
    </nav>

    {{-- Cover --}}
    @if($post->cover_url)
        <div class="mb-8 overflow-hidden rounded-2xl">
            <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" class="w-full object-cover">
        </div>
    @endif

    {{-- Meta --}}
    <div class="mb-4 flex flex-wrap items-center gap-3 text-xs text-ink-400">
        @if($post->user)
            <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ $post->user->name }}
            </span>
        @endif
        <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ ($post->published_at ?? $post->created_at)->diffForHumans() }}
        </span>
    </div>

    {{-- Title --}}
    <h1 class="mb-8 text-2xl font-bold leading-snug text-ink-900 sm:text-3xl">{{ $post->title }}</h1>

    {{-- Content --}}
    <div class="blog-content" dir="rtl">
        {!! $post->content !!}
    </div>

    {{-- Recent posts --}}
    @if($recent->isNotEmpty())
        <div class="mt-12 border-t border-ink-100 pt-8">
            <h2 class="mb-5 text-lg font-bold text-ink-900">مقالات مرتبط</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach($recent as $r)
                <a href="{{ route('blog.show', $r) }}"
                   class="group overflow-hidden rounded-xl border border-ink-100 bg-white hover:border-brand-200">
                    @if($r->cover_url)
                        <img src="{{ $r->cover_url }}" alt="{{ $r->title }}"
                             class="aspect-[16/9] w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @endif
                    <div class="p-3">
                        <p class="line-clamp-2 text-sm font-medium text-ink-800 group-hover:text-brand-500">{{ $r->title }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    @endif

</article>
@endsection
