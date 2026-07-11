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

<x-admin.section>
    @if($messages->isEmpty())
        <x-admin.empty-state icon="tabler:message-2" title="هنوز پیامی دریافت نشده" description="پیام‌های فرم تماس اینجا نمایش داده می‌شوند." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">فرستنده</th>
                    <th class="admin-th">خلاصه پیام</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th">تاریخ</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($messages as $message)
                <tr class="transition-colors hover:bg-gray-50/60 {{ $message->status === 'new' ? 'bg-rose-50/30' : '' }}">
                    <td class="admin-td">
                        <p class="font-medium text-gray-900">{{ $message->name }}</p>
                        <p class="text-xs text-gray-400" dir="ltr">{{ $message->email ?: $message->phone ?: '—' }}</p>
                    </td>
                    <td class="admin-td max-w-xs">
                        <p class="truncate text-gray-600">{{ $message->message }}</p>
                    </td>
                    <td class="admin-td">
                        @if($message->status === 'new')
                            <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-600">جدید</span>
                        @elseif($message->status === 'answered')
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">پاسخ داده شده</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">خوانده شده</span>
                        @endif
                    </td>
                    <td class="admin-td text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</td>
                    <td class="admin-td">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.contact-messages.show', $message) }}" title="مشاهده و پاسخ"
                               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                <iconify-icon icon="tabler:eye" class="text-sm"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST"
                                  onsubmit="return confirm('این پیام حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-rose-500 transition-colors hover:border-rose-300 hover:bg-rose-50">
                                    <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
        <div class="border-t border-gray-100 px-5 py-3">{{ $messages->links() }}</div>
    @endif
    @endif
</x-admin.section>
@endsection
