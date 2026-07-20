@extends('admin.layouts.admin')
@section('title', 'پیام‌های تماس')
@section('page-title', 'پیام‌های تماس')
@php
    $breadcrumbs = ['پیام‌های تماس'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">پیام‌های فرم تماس</h2>
        <p class="mt-0.5 text-sm text-gray-500">پیام‌های ارسال‌شده از صفحه «تماس با ما»</p>
    </div>
    @if($newCount)
        <span class="rounded-full bg-rose-50 px-3 py-1 text-sm font-medium text-rose-600">{{ $newCount }} پیام جدید</span>
    @endif
</div>

<x-admin.section :padded="true">
    @if($messages->isEmpty())
        <x-admin.empty-state icon="tabler:message-2" title="هنوز پیامی دریافت نشده" description="پیام‌های فرم تماس اینجا نمایش داده می‌شوند." />
    @else
    <div class="admin-index-grid">
        @foreach($messages as $message)
        <article class="admin-list-card {{ $message->status === 'new' ? 'border-rose-200 bg-rose-50/20' : '' }}">
            <div class="admin-list-card-head">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-black text-indigo-600">{{ mb_substr($message->name, 0, 1) }}</span>
                    <div class="min-w-0"><h3 class="truncate text-sm font-bold text-gray-900">{{ $message->name }}</h3><p class="truncate text-[10px] text-gray-400" dir="ltr">{{ $message->email ?: $message->phone ?: '—' }}</p></div>
                </div>
                        @if($message->status === 'new')
                    <x-admin.badge tone="rose">جدید</x-admin.badge>
                        @elseif($message->status === 'answered')
                    <x-admin.badge tone="emerald">پاسخ داده شده</x-admin.badge>
                        @else
                    <x-admin.badge tone="gray">خوانده شده</x-admin.badge>
                        @endif
            </div>
            <div class="admin-list-card-body">
                <p class="line-clamp-3 min-h-14 text-xs leading-5 text-gray-600">{{ $message->message }}</p>
            </div>
            <div class="admin-list-card-footer">
                <span class="text-[10px] text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.contact-messages.show', $message) }}" title="مشاهده و پاسخ" class="admin-icon-btn"><iconify-icon icon="tabler:eye" class="text-sm"></iconify-icon></a>
                    <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" onsubmit="return confirm('این پیام حذف شود؟')">
                        @csrf @method('DELETE')
                        <button type="submit" title="حذف" class="admin-icon-btn-danger"><iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon></button>
                    </form>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @if($messages->hasPages())
        <div class="mt-6 border-t border-gray-100 pt-4">{{ $messages->links() }}</div>
    @endif
    @endif
</x-admin.section>
@endsection
