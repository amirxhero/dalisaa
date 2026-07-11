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

<x-admin.section>
    @if($users->isEmpty())
        <x-admin.empty-state icon="tabler:users" title="کاربری یافت نشد" />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="admin-th">کاربر</th>
                    <th class="admin-th">موبایل</th>
                    <th class="admin-th">سفارشات</th>
                    <th class="admin-th">تاریخ عضویت</th>
                    <th class="admin-th">نقش</th>
                    <th class="admin-th">وضعیت</th>
                    <th class="admin-th">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                @php $isMe = $user->id === auth()->id(); @endphp
                <tr class="transition-colors hover:bg-gray-50/60 {{ $user->isBlocked() ? 'opacity-60' : '' }}">
                    <td class="admin-td">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-bold text-indigo-600">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $user->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="admin-td font-mono text-gray-600">{{ $user->mobile ?? '—' }}</td>
                    <td class="admin-td"><x-admin.badge tone="gray">{{ $user->orders_count }}</x-admin.badge></td>
                    <td class="admin-td text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                    <td class="admin-td">
                        <x-admin.badge :tone="$user->is_admin ? 'indigo' : 'gray'">{{ $user->is_admin ? 'مدیر' : 'مشتری' }}</x-admin.badge>
                    </td>
                    <td class="admin-td">
                        <x-admin.badge :tone="$user->isBlocked() ? 'rose' : 'emerald'">{{ $user->isBlocked() ? 'مسدود' : 'فعال' }}</x-admin.badge>
                    </td>
                    <td class="admin-td">
                        @if(!$isMe)
                        <div class="flex flex-wrap items-center gap-1.5">
                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $user->is_admin ? 'حذف از مدیران' : 'تبدیل به مدیر' }}"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600">
                                    <iconify-icon icon="tabler:shield-check" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.toggle-block', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $user->isBlocked() ? 'رفع مسدودی' : 'مسدود کردن' }}"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 transition-colors {{ $user->isBlocked() ? 'text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50' : 'text-amber-600 hover:border-amber-300 hover:bg-amber-50' }}">
                                    <iconify-icon icon="{{ $user->isBlocked() ? 'tabler:lock-open' : 'tabler:lock' }}" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-rose-500 transition-colors hover:border-rose-300 hover:bg-rose-50">
                                    <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">شما</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="border-t border-gray-100 px-5 py-4">
        {{ $users->links('admin.partials.pagination') }}
    </div>
    @endif
    @endif
</x-admin.section>
@endsection
