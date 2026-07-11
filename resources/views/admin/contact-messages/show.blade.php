@extends('admin.layouts.admin')
@section('title', 'پیام تماس')
@section('page-title', 'مشاهده پیام')
@php
    $breadcrumbs = ['پیام‌های تماس', $message->name];
@endphp

@section('content')
<div class="mx-auto max-w-2xl space-y-5">

    <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600">
        <iconify-icon icon="tabler:arrow-right" class="text-base"></iconify-icon>
        بازگشت به لیست
    </a>

    {{-- Sender + message --}}
    <x-admin.section title="اطلاعات فرستنده">
        <div class="space-y-4 p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-xs text-gray-400">نام</p>
                    <p class="font-medium text-gray-900">{{ $message->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">ایمیل</p>
                    <p class="font-medium text-gray-900" dir="ltr">{{ $message->email ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">تلفن</p>
                    <p class="font-medium text-gray-900" dir="ltr">{{ $message->phone ?: '—' }}</p>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-4">
                <p class="mb-1.5 text-xs text-gray-400">متن پیام</p>
                <p class="whitespace-pre-line leading-7 text-gray-700">{{ $message->message }}</p>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-100 pt-3 text-xs text-gray-400">
                <span>{{ $message->created_at->format('Y/m/d H:i') }}</span>
                @if($message->email)
                    <a href="mailto:{{ $message->email }}" class="inline-flex items-center gap-1 text-indigo-600 hover:underline">
                        <iconify-icon icon="tabler:mail"></iconify-icon>
                        پاسخ با ایمیل
                    </a>
                @endif
            </div>
        </div>
    </x-admin.section>

    {{-- Reply / answer --}}
    <x-admin.section title="پاسخ مدیر">
        <form action="{{ route('admin.contact-messages.reply', $message) }}" method="POST" class="space-y-4 p-5">
            @csrf

            @if($message->answered_at)
                <p class="rounded-xl bg-emerald-50 px-4 py-2.5 text-xs text-emerald-700">
                    آخرین پاسخ در {{ $message->answered_at->format('Y/m/d H:i') }} ثبت شده است.
                </p>
            @endif

            <div>
                <label class="admin-label">متن پاسخ</label>
                <textarea name="admin_reply" rows="5" required
                          class="admin-input resize-none"
                          placeholder="پاسخ خود را بنویسید...">{{ old('admin_reply', $message->admin_reply) }}</textarea>
                @error('admin_reply')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 border-t border-gray-100 pt-4">
                <button type="submit" class="admin-btn-primary">
                    <iconify-icon icon="tabler:send" class="text-base"></iconify-icon>
                    ذخیره پاسخ
                </button>
            </div>
        </form>
    </x-admin.section>

</div>
@endsection
