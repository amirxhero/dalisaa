@extends('admin.layouts.admin')
@section('title', 'کدهای تخفیف')
@section('page-title', 'مدیریت کدهای تخفیف')
@php
    $breadcrumbs = ['کدهای تخفیف'];
@endphp

@php
    use App\Support\JalaliDate;
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">کدهای تخفیف</h2>
        <p class="mt-0.5 text-sm text-gray-500">{{ $discounts->total() }} کد ثبت‌شده</p>
    </div>
    <a href="{{ route('admin.discounts.create') }}" class="admin-btn-primary">
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        کد جدید
    </a>
</div>

<x-admin.section :padded="true">
    @if($discounts->isEmpty())
        <x-admin.empty-state icon="tabler:ticket-off" title="هیچ کد تخفیفی ثبت نشده است" />
    @else
    <div class="admin-index-grid">
        @foreach($discounts as $d)
        @php $statusTone = $d->status_label === 'فعال' ? 'emerald' : ($d->status_label === 'منقضی' ? 'rose' : 'gray'); @endphp
        <article class="admin-list-card {{ !$d->is_active ? 'bg-gray-50/70' : '' }}">
            <div class="admin-list-card-head">
                <div><span class="text-[10px] font-semibold text-gray-400">کد تخفیف</span><h3 class="font-mono text-base font-black tracking-wider text-gray-900">{{ $d->code }}</h3></div>
                <x-admin.badge :tone="$statusTone">{{ $d->status_label }}</x-admin.badge>
            </div>
            <div class="admin-list-card-body">
                <div class="mb-4 rounded-xl bg-indigo-50 px-3 py-3 text-center"><span class="text-[10px] text-indigo-500">{{ $d->type === 'percent' ? 'تخفیف درصدی' : 'تخفیف مقداری' }}</span><p class="mt-0.5 text-lg font-black text-indigo-700">{{ $d->value_display }}</p></div>
                <div class="admin-meta-grid">
                    <div><span class="admin-meta-label">حداقل سفارش</span><span class="admin-meta-value">{{ $d->min_order ? number_format($d->min_order).' تومان' : 'بدون محدودیت' }}</span></div>
                    <div><span class="admin-meta-label">دفعات استفاده</span><span class="admin-meta-value">{{ $d->uses_count }}@if($d->max_uses) از {{ $d->max_uses }}@endif</span></div>
                    <div class="col-span-2"><span class="admin-meta-label">تاریخ انقضا</span><span class="admin-meta-value {{ $d->expires_at?->isPast() ? 'text-rose-600' : '' }}">
                        @if($d->expires_at)
                            {{ JalaliDate::fromCarbon($d->expires_at) }}
                        @else بدون انقضا
                        @endif
                    </span></div>
                </div>
            </div>
            <div class="admin-list-card-footer">
                <form action="{{ route('admin.discounts.toggle', $d) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="text-[11px] font-semibold {{ $d->is_active ? 'text-amber-600' : 'text-emerald-600' }}">{{ $d->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}</button></form>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.discounts.edit', $d) }}" title="ویرایش" class="admin-icon-btn"><iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon></a>
                    <form action="{{ route('admin.discounts.destroy', $d) }}" method="POST" onsubmit="return confirm('حذف شود؟')">@csrf @method('DELETE')<button type="submit" title="حذف" class="admin-icon-btn-danger"><iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon></button></form>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @if($discounts->hasPages())
    <div class="mt-6 border-t border-gray-100 pt-4">
        {{ $discounts->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
