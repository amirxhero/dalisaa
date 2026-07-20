@extends('admin.layouts.admin')
@section('title', 'کاربران')
@section('page-title', 'مدیریت کاربران')
@php
    $breadcrumbs = ['کاربران'];
@endphp

@section('content')

<div class="mb-5">
    <h2 class="text-lg font-bold text-gray-900">کاربران</h2>
    <p class="mt-0.5 text-sm text-gray-500">{{ $users->total() }} کاربر ثبت‌شده</p>
</div>

<form method="GET" class="admin-card mb-5 flex flex-wrap items-center gap-3 p-4">
    <div class="relative flex-1 min-w-[220px]">
        <iconify-icon icon="tabler:search" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="نام، موبایل یا ایمیل..." class="admin-input pr-10">
    </div>
    <select name="role" class="admin-select w-48">
        <option value="">همه کاربران</option>
        <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>فقط مدیران</option>
        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>فقط مشتریان</option>
    </select>
    <button type="submit" class="admin-btn-secondary">
        <iconify-icon icon="tabler:filter" class="text-base"></iconify-icon>
        اعمال
    </button>
    @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 transition-colors hover:text-gray-600">پاک کردن</a>
    @endif
</form>

<x-admin.section :padded="true">
    @if($users->isEmpty())
        <x-admin.empty-state icon="tabler:users" title="کاربری یافت نشد" />
    @else
    <div class="admin-index-grid">
        @foreach($users as $user)
        @php $isMe = $user->id === auth()->id(); @endphp
        <article class="admin-list-card {{ $user->isBlocked() ? 'bg-gray-50/70' : '' }}">
            <div class="admin-list-card-head">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-black text-indigo-600">{{ mb_substr($user->name, 0, 1) }}</span>
                    <div class="min-w-0"><h3 class="truncate text-sm font-bold text-gray-900">{{ $user->name }}</h3><p class="truncate text-[10px] text-gray-400">{{ $user->email ?? 'بدون ایمیل' }}</p></div>
                </div>
                <div class="flex gap-1.5">
                    <x-admin.badge :tone="$user->is_admin ? 'indigo' : 'gray'">{{ $user->is_admin ? 'مدیر' : 'مشتری' }}</x-admin.badge>
                    <x-admin.badge :tone="$user->isBlocked() ? 'rose' : 'emerald'">{{ $user->isBlocked() ? 'مسدود' : 'فعال' }}</x-admin.badge>
                </div>
            </div>
            <div class="admin-list-card-body">
                <div class="admin-meta-grid">
                    <div><span class="admin-meta-label">شماره موبایل</span><span class="admin-meta-value font-mono">{{ $user->mobile ?? '—' }}</span></div>
                    <div><span class="admin-meta-label">تعداد سفارش</span><x-admin.badge tone="gray">{{ $user->orders_count }} سفارش</x-admin.badge></div>
                    <div class="col-span-2"><span class="admin-meta-label">تاریخ عضویت</span><span class="admin-meta-value">{{ $user->created_at->diffForHumans() }}</span></div>
                </div>
            </div>
            <div class="admin-list-card-footer">
                <span class="text-[10px] text-gray-400">{{ $isMe ? 'حساب کاربری شما' : 'مدیریت دسترسی' }}</span>
                        @if(!$isMe)
                        <div class="flex flex-wrap items-center gap-1.5">
                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $user->is_admin ? 'حذف از مدیران' : 'تبدیل به مدیر' }}"
                                        class="admin-icon-btn">
                                    <iconify-icon icon="tabler:shield-check" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.toggle-block', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $user->isBlocked() ? 'رفع مسدودی' : 'مسدود کردن' }}"
                                        class="admin-icon-btn {{ $user->isBlocked() ? '!text-emerald-600 hover:!border-emerald-300 hover:!bg-emerald-50' : '!text-amber-600 hover:!border-amber-300 hover:!bg-amber-50' }}">
                                    <iconify-icon icon="{{ $user->isBlocked() ? 'tabler:lock-open' : 'tabler:lock' }}" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف"
                                        class="admin-icon-btn-danger">
                                    <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                        </div>
                        @else
                        <x-admin.badge tone="indigo">شما</x-admin.badge>
                        @endif
            </div>
        </article>
        @endforeach
    </div>
    @if($users->hasPages())
    <div class="mt-6 border-t border-gray-100 pt-4">
        {{ $users->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
