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

<x-admin.section>
    @if($discounts->isEmpty())
        <x-admin.empty-state icon="tabler:ticket-off" title="هیچ کد تخفیفی ثبت نشده است" />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">کد</th>
                    <th class="admin-th">نوع</th>
                    <th class="admin-th">مقدار</th>
                    <th class="admin-th">حداقل سفارش</th>
                    <th class="admin-th">استفاده</th>
                    <th class="admin-th">انقضا</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($discounts as $d)
                @php
                    $statusTone = $d->status_label === 'فعال' ? 'emerald' : ($d->status_label === 'منقضی' ? 'rose' : 'gray');
                @endphp
                <tr class="transition-colors hover:bg-gray-50/60 {{ !$d->is_active ? 'opacity-60' : '' }}">
                    <td class="admin-td font-mono font-bold text-gray-900">{{ $d->code }}</td>
                    <td class="admin-td text-gray-600">{{ $d->type === 'percent' ? 'درصدی' : 'مقداری' }}</td>
                    <td class="admin-td font-semibold text-indigo-700">{{ $d->value_display }}</td>
                    <td class="admin-td text-gray-500">{{ $d->min_order ? number_format($d->min_order).' ت' : '—' }}</td>
                    <td class="admin-td">
                        <span class="text-gray-700">{{ $d->uses_count }}</span>
                        @if($d->max_uses)<span class="text-gray-400"> / {{ $d->max_uses }}</span>@endif
                    </td>
                    <td class="admin-td text-xs text-gray-500">
                        @if($d->expires_at)
                            <span class="{{ $d->expires_at->isPast() ? 'text-rose-500' : '' }}">{{ JalaliDate::fromCarbon($d->expires_at) }}</span>
                        @else —
                        @endif
                    </td>
                    <td class="admin-td">
                        <x-admin.badge :tone="$statusTone">{{ $d->status_label }}</x-admin.badge>
                    </td>
                    <td class="admin-td">
                        <div class="flex items-center gap-1.5">
                            <form action="{{ route('admin.discounts.toggle', $d) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $d->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 transition-colors {{ $d->is_active ? 'text-amber-600 hover:border-amber-300 hover:bg-amber-50' : 'text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50' }}">
                                    <iconify-icon icon="{{ $d->is_active ? 'tabler:player-pause' : 'tabler:player-play' }}" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                            <a href="{{ route('admin.discounts.edit', $d) }}" title="ویرایش"
                               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                <iconify-icon icon="tabler:pencil" class="text-sm"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.discounts.destroy', $d) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
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
    @if($discounts->hasPages())
    <div class="border-t border-gray-100 px-5 py-4">
        {{ $discounts->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
