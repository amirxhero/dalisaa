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

    <x-admin.section>
        @if($posts->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <iconify-icon icon="tabler:article-off" class="mb-3 text-5xl"></iconify-icon>
                <p class="text-sm">هنوز مقاله‌ای ثبت نشده</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 text-xs text-gray-400">
                    <tr>
                        <th class="py-3 pr-5 text-right font-medium">عنوان</th>
                        <th class="py-3 text-right font-medium">نویسنده</th>
                        <th class="py-3 text-right font-medium">وضعیت</th>
                        <th class="py-3 text-right font-medium">تاریخ</th>
                        <th class="py-3 pl-5 text-left font-medium">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($posts as $post)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3 pr-5">
                            <div class="flex items-center gap-3">
                                @if($post->cover_url)
                                    <img src="{{ $post->cover_url }}" alt="" class="h-10 w-16 rounded-lg object-cover">
                                @else
                                    <div class="flex h-10 w-16 items-center justify-center rounded-lg bg-gray-100 text-gray-300">
                                        <iconify-icon icon="tabler:photo" class="text-lg"></iconify-icon>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $post->title }}</p>
                                    <p class="text-xs text-gray-400">/blog/{{ $post->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-gray-600">{{ $post->user->name ?? '—' }}</td>
                        <td class="py-3">
                            @if($post->status === 'published')
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">منتشر شده</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">پیش‌نویس</span>
                            @endif
                        </td>
                        <td class="py-3 text-xs text-gray-400">{{ $post->updated_at->diffForHumans() }}</td>
                        <td class="py-3 pl-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.posts.edit', $post) }}"
                                   class="rounded-lg px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50">ویرایش</a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                      onsubmit="return confirm('حذف شود؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($posts->hasPages())
                <div class="border-t border-gray-100 px-5 py-3">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </x-admin.section>
</div>
@endsection
