@extends('admin.layouts.admin')
@section('title', 'ویرایش مقاله')
@section('page-title', 'ویرایش مقاله')
@php $breadcrumbs = ['مقالات', 'ویرایش']; @endphp

@push('styles')
<style>
.tiptap-body h1 { font-size: 1.75rem; font-weight: 700; margin: 1rem 0 .5rem; }
.tiptap-body h2 { font-size: 1.375rem; font-weight: 700; margin: 1rem 0 .5rem; }
.tiptap-body h3 { font-size: 1.125rem; font-weight: 600; margin: .75rem 0 .375rem; }
.tiptap-body p  { margin-bottom: .75rem; line-height: 1.8; }
.tiptap-body ul, .tiptap-body ol { padding-right: 1.5rem; margin-bottom: .75rem; }
.tiptap-body ul { list-style: disc; }
.tiptap-body ol { list-style: decimal; }
.tiptap-body li { margin-bottom: .25rem; line-height: 1.7; }
.tiptap-body blockquote { border-right: 3px solid #6366f1; padding-right: 1rem; color: #6b7280; margin: 1rem 0; font-style: italic; }
.tiptap-body pre  { background: #1e1e2e; color: #cdd6f4; padding: 1rem; border-radius: .5rem; overflow-x: auto; margin-bottom: .75rem; }
.tiptap-body code { background: #f1f5f9; padding: .1em .3em; border-radius: .25rem; font-size: .85em; }
.tiptap-body pre code { background: transparent; padding: 0; }
.tiptap-body a { color: #6366f1; text-decoration: underline; }
.tiptap-body img { max-width: 100%; border-radius: .5rem; margin: .5rem 0; }
.tiptap-body mark { background: #fef08a; padding: .1em .2em; border-radius: .2em; }
.tiptap-body hr { border: none; border-top: 2px solid #e5e7eb; margin: 1.25rem 0; }
.tiptap-body p.is-editor-empty:first-child::before { content: attr(data-placeholder); color: #9ca3af; pointer-events: none; float: right; height: 0; }
.tiptap-body .ProseMirror-selectednode { outline: 2px solid #6366f1; }
.editor-tb-btn { display:inline-flex; align-items:center; justify-content:center; min-width:2rem; height:2rem; padding:0 .4rem; border-radius:.375rem; font-size:.8rem; font-weight:600; color:#374151; transition:background 100ms; cursor:pointer; }
.editor-tb-btn:hover { background:#e5e7eb; }
.editor-tb-btn.is-on { background:#e0e7ff; color:#4338ca; }
.editor-tb-sep { width:1px; height:1.25rem; background:#e5e7eb; margin:0 .25rem; }
</style>
@endpush

@section('content')
<form
    action="{{ route('admin.posts.update', $post) }}"
    method="POST"
    enctype="multipart/form-data"
    class="mx-auto max-w-6xl"
>
    @csrf @method('PUT')

    <div class="flex flex-col gap-5 lg:flex-row lg:items-start">

        {{-- ── Main column ─────────────────────────────────────── --}}
        <div class="flex-1 space-y-4">

            {{-- Title --}}
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                       placeholder="عنوان مقاله..."
                       class="w-full border-0 bg-transparent text-2xl font-bold text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-0">
                @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- TipTap Editor --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm"
                 x-data="richEditor({ value: @js(old('content', $post->content)), uploadUrl: '{{ route('admin.post-images.store') }}' })">

                {{-- Toolbar --}}
                <div class="flex flex-wrap items-center gap-0.5 border-b border-gray-100 bg-gray-50/80 px-3 py-2">
                    <button type="button" @click="cmd('undo')" class="editor-tb-btn" title="بازگشت">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                    </button>
                    <button type="button" @click="cmd('redo')" class="editor-tb-btn" title="تکرار">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14l5-5-5-5"/><path d="M20 9H9.5A5.5 5.5 0 0 0 4 14.5v0A5.5 5.5 0 0 0 9.5 20H13"/></svg>
                    </button>
                    <span class="editor-tb-sep"></span>

                    <button type="button" @click="cmd('h1')" :class="isActive('heading',{level:1}) && 'is-on'" class="editor-tb-btn">H1</button>
                    <button type="button" @click="cmd('h2')" :class="isActive('heading',{level:2}) && 'is-on'" class="editor-tb-btn">H2</button>
                    <button type="button" @click="cmd('h3')" :class="isActive('heading',{level:3}) && 'is-on'" class="editor-tb-btn">H3</button>
                    <span class="editor-tb-sep"></span>

                    <button type="button" @click="cmd('bold')"      :class="isActive('bold')      && 'is-on'" class="editor-tb-btn font-extrabold">B</button>
                    <button type="button" @click="cmd('italic')"    :class="isActive('italic')    && 'is-on'" class="editor-tb-btn italic">I</button>
                    <button type="button" @click="cmd('underline')" :class="isActive('underline') && 'is-on'" class="editor-tb-btn underline">U</button>
                    <button type="button" @click="cmd('strike')"    :class="isActive('strike')    && 'is-on'" class="editor-tb-btn line-through">S</button>
                    <button type="button" @click="cmd('highlight')" :class="isActive('highlight') && 'is-on'" class="editor-tb-btn" title="هایلایت">
                        <span style="background:#fef08a; padding:0 3px; border-radius:3px">A</span>
                    </button>
                    <span class="editor-tb-sep"></span>

                    <button type="button" @click="cmd('bullet')"  :class="isActive('bulletList')  && 'is-on'" class="editor-tb-btn" title="لیست نقطه‌ای">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                    </button>
                    <button type="button" @click="cmd('ordered')" :class="isActive('orderedList') && 'is-on'" class="editor-tb-btn" title="لیست شماره‌دار">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
                    </button>
                    <button type="button" @click="cmd('blockquote')" :class="isActive('blockquote') && 'is-on'" class="editor-tb-btn" title="نقل‌قول">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                    </button>
                    <button type="button" @click="cmd('codeBlock')" :class="isActive('codeBlock') && 'is-on'" class="editor-tb-btn font-mono text-xs" title="کد">&lt;/&gt;</button>
                    <button type="button" @click="cmd('hr')" class="editor-tb-btn" title="خط افقی">—</button>
                    <span class="editor-tb-sep"></span>

                    <button type="button" @click="cmd('alignRight')"  :class="isActive({textAlign:'right'})  && 'is-on'" class="editor-tb-btn" title="راست‌چین">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="12" x2="9" y2="12"/><line x1="21" y1="18" x2="3" y2="18"/></svg>
                    </button>
                    <button type="button" @click="cmd('alignCenter')" :class="isActive({textAlign:'center'}) && 'is-on'" class="editor-tb-btn" title="وسط‌چین">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="6" x2="3" y2="6"/><line x1="18" y1="12" x2="6" y2="12"/><line x1="21" y1="18" x2="3" y2="18"/></svg>
                    </button>
                    <button type="button" @click="cmd('alignLeft')"   :class="isActive({textAlign:'left'})   && 'is-on'" class="editor-tb-btn" title="چپ‌چین">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="6" x2="3" y2="6"/><line x1="15" y1="12" x2="3" y2="12"/><line x1="21" y1="18" x2="3" y2="18"/></svg>
                    </button>
                    <span class="editor-tb-sep"></span>

                    <button type="button" @click="cmd('link')"  :class="isActive('link') && 'is-on'" class="editor-tb-btn" title="لینک">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </button>
                    <button type="button" @click="cmd('image')" class="editor-tb-btn" title="تصویر">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </button>
                </div>

                <div x-ref="editorEl" class="text-gray-800"></div>
                <input type="hidden" name="content" x-ref="hiddenContent" value="{{ old('content', $post->content) }}">
            </div>
            @error('content')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- ── Sidebar ──────────────────────────────────────────── --}}
        <div class="w-full space-y-4 lg:w-72">

            <x-admin.section title="انتشار">
                <div class="space-y-3 p-4">
                    <div>
                        <label class="admin-label">وضعیت</label>
                        <select name="status" class="admin-input">
                            <option value="draft"     {{ old('status', $post->status) === 'draft'     ? 'selected' : '' }}>پیش‌نویس</option>
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>منتشر شده</option>
                        </select>
                    </div>
                    <x-admin.jalali-date-input name="published_at" id="published_at" label="تاریخ انتشار"
                        :value="old('published_at', \App\Support\JalaliDate::fromCarbon($post->published_at))" />
                    <div class="border-t border-gray-100 pt-3 space-y-2">
                        <button type="submit" class="admin-btn-primary w-full justify-center">
                            <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                            ذخیره تغییرات
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="admin-btn-secondary w-full justify-center">انصراف</a>
                        <a href="{{ route('blog.show', $post) }}" target="_blank"
                           class="flex items-center justify-center gap-1.5 rounded-xl px-4 py-2 text-sm text-gray-500 hover:bg-gray-100">
                            <iconify-icon icon="tabler:external-link" class="text-base"></iconify-icon>
                            مشاهده مقاله
                        </a>
                    </div>
                </div>
            </x-admin.section>

            <x-admin.section title="تصویر شاخص">
                <div class="p-4 space-y-3">
                    @if($post->cover_url)
                        <img src="{{ $post->cover_url }}" alt="" class="w-full rounded-xl object-cover">
                    @endif
                    <div class="rounded-xl border-2 border-dashed border-gray-200 p-4 text-center hover:border-indigo-200">
                        <input type="file" name="cover" accept="image/*"
                               class="mx-auto block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-indigo-700">
                        <p class="mt-1 text-xs text-gray-400">بارگذاری تصویر جدید جایگزین می‌شود</p>
                    </div>
                </div>
            </x-admin.section>

            <x-admin.section title="خلاصه مقاله">
                <div class="p-4">
                    <textarea name="excerpt" rows="3" maxlength="600"
                              placeholder="خلاصه‌ای کوتاه..."
                              class="admin-input resize-none">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>
            </x-admin.section>

            <x-admin.section title="آدرس URL">
                <div class="p-4">
                    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                           class="admin-input" dir="ltr">
                </div>
            </x-admin.section>

        </div>
    </div>
</form>
@endsection
