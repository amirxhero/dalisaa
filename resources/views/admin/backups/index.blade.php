@extends('admin.layouts.admin')
@section('title', 'پشتیبان‌گیری (بکاپ)')
@section('page-title', 'مدیریت پشتیبان‌گیری دیتابیس و سیستم')

@php
    $breadcrumbs = ['پشتیبان‌گیری'];

    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
@endphp

@section('content')

<div class="space-y-6">

    {{-- Top Banner & Quick Actions --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        {{-- DB Backup Card --}}
        <div class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/60 via-white to-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-indigo-600">
                        <iconify-icon icon="tabler:database" class="text-2xl"></iconify-icon>
                        <h3 class="font-bold text-gray-900">بکاپ فوری دیتابیس</h3>
                    </div>
                    <p class="text-xs text-gray-500">پشتیبان‌گیری از تمامی جداول و اطلاعات دیتابیس (سریع و سبک)</p>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-2">
                {{-- Generate & Instant Download --}}
                <form action="{{ route('admin.backups.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="option" value="only-db">
                    <input type="hidden" name="download" value="1">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-indigo-700 hover:shadow-indigo-100">
                        <iconify-icon icon="tabler:download" class="text-base"></iconify-icon>
                        ایجاد و دانلود مستقیم دیتابیس
                    </button>
                </form>

                {{-- Generate to storage --}}
                <form action="{{ route('admin.backups.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="option" value="only-db">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                        <iconify-icon icon="tabler:device-floppy" class="text-base text-gray-400"></iconify-icon>
                        ذخیره در سرور
                    </button>
                </form>
            </div>
        </div>

        {{-- Full Backup Card --}}
        <div class="relative overflow-hidden rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50/60 via-white to-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-purple-600">
                        <iconify-icon icon="tabler:archive" class="text-2xl"></iconify-icon>
                        <h3 class="font-bold text-gray-900">بکاپ کامل سیستم</h3>
                    </div>
                    <p class="text-xs text-gray-500">پشتیبان‌گیری از دیتابیس + تمام فایل‌ها و سورس کدهای پروژه</p>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-2">
                {{-- Generate & Instant Download --}}
                <form action="{{ route('admin.backups.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="option" value="full">
                    <input type="hidden" name="download" value="1">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow-purple-100">
                        <iconify-icon icon="tabler:download" class="text-base"></iconify-icon>
                        ایجاد و دانلود مستقیم بکاپ کامل
                    </button>
                </form>

                {{-- Generate to storage --}}
                <form action="{{ route('admin.backups.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="option" value="full">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                        <iconify-icon icon="tabler:device-floppy" class="text-base text-gray-400"></iconify-icon>
                        ذخیره در سرور
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Existing Backups List --}}
    <x-admin.section title="لیست فایل‌های پشتیبان موجود در سرور" :padded="false">
        @if($backups->isEmpty())
            <div class="p-8 text-center">
                <x-admin.empty-state
                    icon="tabler:database-off"
                    title="هیچ فایل بکاپی در سرور موجود نیست"
                    description="می‌توانید با دکمه‌های بالا اولین پشتیبان خود را ایجاد و دانلود کنید."
                />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs text-gray-400 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 font-semibold">نام فایل ZIP</th>
                            <th class="px-5 py-3 font-semibold">حجم فایل</th>
                            <th class="px-5 py-3 font-semibold">تاریخ ایجاد</th>
                            <th class="px-5 py-3 font-semibold text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($backups as $backup)
                            <tr class="transition-colors hover:bg-gray-50/60">
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-gray-800 dir-ltr text-right">
                                    <div class="flex items-center gap-2">
                                        <iconify-icon icon="tabler:file-zip" class="text-lg text-amber-500 shrink-0"></iconify-icon>
                                        <span>{{ $backup['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-600">
                                    {{ formatBytes($backup['size']) }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{ route('admin.backups.download', $backup['name']) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs font-bold text-indigo-600 transition-colors hover:bg-indigo-100"
                                            title="دانلود فایل"
                                        >
                                            <iconify-icon icon="tabler:download" class="text-sm"></iconify-icon>
                                            دانلود
                                        </a>

                                        <form action="{{ route('admin.backups.destroy', $backup['name']) }}" method="POST" onsubmit="return confirm('آیا از حذف این فایل بکاپ اطمینان دارید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                                                title="حذف"
                                            >
                                                <iconify-icon icon="tabler:trash" class="text-base"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-admin.section>

    {{-- Automated Cron Information --}}
    <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-4 text-xs text-blue-800">
        <div class="flex items-center gap-2 font-bold text-blue-900 mb-1">
            <iconify-icon icon="tabler:clock" class="text-base text-blue-600"></iconify-icon>
            پشتیبان‌گیری خودکار (Scheduled Backups)
        </div>
        <p class="leading-relaxed">
            سیستم طبق زمان‌بندی روزانه (ساعت ۰۱:۳۰ بامداد) از دیتابیس و به‌صورت هفتگی (دوشنبه‌ها ساعت ۰۲:۰۰ بامداد) از کل سیستم به صورت خودکار پشتیبان تهیه می‌کند.
            برای فعال‌سازی پشتیبان‌گیری خودکار در سرور پروداکشن، کرون جاب <code>* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</code> را تنظیم کنید.
        </p>
    </div>

</div>

@endsection
