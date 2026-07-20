@extends('admin.layouts.admin')
@section('title', 'مقالات')
@section('page-title', 'مدیریت مقالات')
@php $breadcrumbs = ['مقالات']; @endphp

@section('content')
<div class="space-y-4">
    {{-- Header bar --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $posts->total() }} مقاله</p>
        <a href="{{ route('admin.posts.create') }}" class="admin-btn-primary">
            <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
            مقاله جدید
        </a>
    </div>

    <x-admin.section :padded="true">
        @if($posts->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <iconify-icon icon="tabler:article-off" class="mb-3 text-5xl"></iconify-icon>
                <p class="text-sm">هنوز مقاله‌ای ثبت نشده</p>
            </div>
        @else
            <div class="admin-index-grid">
                @foreach($posts as $post)
                <article class="group admin-list-card">
                    <div class="h-32 overflow-hidden bg-gray-100">
                                @if($post->cover_url)
                            <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                            <div class="flex h-full items-center justify-center text-gray-300"><iconify-icon icon="tabler:article" class="text-4xl"></iconify-icon></div>
                                @endif
                    </div>
                    <div class="admin-list-card-body">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            @if($post->status === 'published')
                                <x-admin.badge tone="emerald">منتشر شده</x-admin.badge>
                            @else
                                <x-admin.badge tone="amber">پیش‌نویس</x-admin.badge>
                            @endif
                            <span class="text-[10px] text-gray-400">{{ $post->updated_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-gray-900">{{ $post->title }}</h3>
                        <p class="mt-2 truncate font-mono text-[10px] text-gray-400" dir="ltr">/blog/{{ $post->slug }}</p>
                        <p class="mt-3 text-xs text-gray-500">نویسنده: {{ $post->user->name ?? '—' }}</p>
                    </div>
                    <div class="admin-list-card-footer">
                        <span class="text-[10px] text-gray-400">مدیریت مقاله</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.posts.edit', $post) }}" title="ویرایش" class="admin-icon-btn"><iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon></a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف" class="admin-icon-btn-danger"><iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon></button>
                            </form>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @if($posts->hasPages())
                <div class="mt-6 border-t border-gray-100 pt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </x-admin.section>
</div>
@endsection
