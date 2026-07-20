@extends('admin.layouts.admin')

@section('title', 'محصول جدید')
@section('page-title', 'افزودن محصول جدید')
@php
    $breadcrumbs = ['محصولات', 'ایجاد'];
@endphp

@section('content')
<form
    action="{{ route('admin.products.store') }}"
    method="POST"
    enctype="multipart/form-data"
    x-data='{
        step: 1,
        currency: "{{ old('price_currency', 'USD') }}",
        priceOriginal: "{{ old('price_original', '') }}",
        rates: @json($rates),
        discountType: "{{ old('discount_type', 'none') }}",
        discountValue: "{{ old('discount_value', 0) }}",
        get baseToman() {
            const n = parseFloat(this.priceOriginal);
            if (!n) return 0;
            if (this.currency === "IRR") return n;
            const rate = this.rates[this.currency] ?? 0;
            return Math.round(n * rate);
        },
        get finalToman() {
            const base = this.baseToman;
            if (!base) return 0;
            const val = parseFloat(this.discountValue) || 0;
            if (this.discountType === "percent") {
                return Math.max(0, Math.round(base * (1 - val / 100)));
            } else if (this.discountType === "amount") {
                return Math.max(0, base - val);
            }
            return base;
        },
        get tomanEquiv() {
            if (!this.finalToman) return null;
            return this.finalToman.toLocaleString("fa-IR");
        }
    }'
    class="mx-auto max-w-3xl"
>
    @csrf

    {{-- Step indicator --}}
    <div class="mb-6 flex items-center gap-0">
        @php $steps = ['اطلاعات پایه', 'قیمت‌گذاری', 'موجودی', 'تصاویر']; @endphp
        @foreach($steps as $i => $label)
        @php $n = $i + 1; @endphp
        <div class="flex items-center" :class="{ 'flex-1': {{ $n < count($steps) ? 'true' : 'false' }} }">
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold transition-colors"
                     :class="step > {{ $n }} ? 'bg-indigo-600 text-white' : (step === {{ $n }} ? 'bg-indigo-600 text-white ring-4 ring-indigo-50' : 'bg-gray-100 text-gray-400')">
                    <template x-if="step > {{ $n }}"><iconify-icon icon="tabler:check" class="text-base"></iconify-icon></template>
                    <template x-if="step <= {{ $n }}"><span>{{ $n }}</span></template>
                </div>
                <span class="hidden text-sm sm:block" :class="step === {{ $n }} ? 'font-semibold text-gray-900' : 'text-gray-400'">{{ $label }}</span>
            </div>
            @if($n < count($steps))
            <div class="mx-3 h-px flex-1 transition-colors" :class="step > {{ $n }} ? 'bg-indigo-400' : 'bg-gray-200'"></div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="admin-card p-6">

        {{-- ── STEP 1: Basic Info ──────────────────────────────────────── --}}
        <div x-show="step === 1" x-cloak>
            <h2 class="mb-5 text-base font-bold text-gray-900">اطلاعات پایه</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="admin-label">نام محصول (فارسی) <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="admin-input">
                    @error('title') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">نام انگلیسی (English Name)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="مثال: iPhone 15 Pro Max" dir="ltr" class="admin-input text-left">
                    @error('name_en') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">اسلاگ / URL یکتا (Slug)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="در صورت خالی بودن خودکار ایجاد می‌شود" dir="ltr" class="admin-input text-left font-mono">
                    @error('slug') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">برند <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" value="{{ old('brand') }}" required class="admin-input">
                </div>
                <div>
                    <label class="admin-label">دسته‌بندی <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="admin-select">
                        <option value="">انتخاب کنید...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ str_repeat('── ', $cat->depth ?? 0) }}{{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="admin-label">توضیحات <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4" required class="admin-input">{{ old('description') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <x-admin.highlights-repeater />
                </div>
            </div>
            <div class="mt-6 flex justify-end border-t border-gray-100 pt-5">
                <button type="button" @click="step = 2" class="admin-btn-primary">
                    مرحله بعد
                    <iconify-icon icon="tabler:arrow-left" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>

        {{-- ── STEP 2: Pricing ─────────────────────────────────────────── --}}
        <div x-show="step === 2" x-cloak>
            <h2 class="mb-5 text-base font-bold text-gray-900">قیمت‌گذاری</h2>
            <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm text-sky-700">
                <iconify-icon icon="tabler:info-circle" class="mt-0.5 shrink-0 text-base"></iconify-icon>
                ارز و مبلغ پایه محصول را وارد کنید. سیستم بر اساس نوع تخفیف قیمت نهایی را به تومان محاسبه می‌کند.
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="admin-label">ارز <span class="text-rose-500">*</span></label>
                    <select name="price_currency" x-model="currency" required class="admin-select">
                        @foreach($currencies as $code => $label)
                        <option value="{{ $code }}" {{ old('price_currency', 'USD') === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">مبلغ پایه <span class="text-rose-500">*</span></label>
                    <input type="number" name="price_original" x-model="priceOriginal" step="0.01" min="0" required
                           value="{{ old('price_original') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">نوع تخفیف</label>
                    <select name="discount_type" x-model="discountType" class="admin-select">
                        <option value="none">بدون تخفیف</option>
                        <option value="percent">درصدی (٪)</option>
                        <option value="amount">مقدار ثابت (تومان)</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">مقدار تخفیف</label>
                    <input type="number" name="discount_value" x-model="discountValue" min="0" class="admin-input">
                </div>

                {{-- Toman equivalent --}}
                <div class="sm:col-span-2 flex items-center rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                    <template x-if="tomanEquiv">
                        <div class="flex-1 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-indigo-500">قیمت نهایی پس از تخفیف</p>
                                <p class="text-lg font-bold text-indigo-800" x-text="tomanEquiv + ' تومان'"></p>
                            </div>
                            <div class="text-xs text-right text-indigo-500" x-show="discountType !== 'none'">
                                <p>قیمت اصلی: <span x-text="baseToman.toLocaleString('fa-IR') + ' تومان'"></span></p>
                                <p>کاهش قیمت: <span x-text="(baseToman - finalToman).toLocaleString('fa-IR') + ' تومان'"></span></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="!tomanEquiv">
                        <p class="text-sm text-indigo-400">مبلغ را وارد کنید...</p>
                    </template>
                </div>
            </div>
            <div class="mt-6 flex justify-between border-t border-gray-100 pt-5">
                <button type="button" @click="step = 1" class="admin-btn-secondary">
                    <iconify-icon icon="tabler:arrow-right" class="text-base"></iconify-icon>
                    مرحله قبل
                </button>
                <button type="button" @click="step = 3" class="admin-btn-primary">
                    مرحله بعد
                    <iconify-icon icon="tabler:arrow-left" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>

        {{-- ── STEP 3: Stock ───────────────────────────────────────────── --}}
        <div x-show="step === 3" x-cloak>
            <h2 class="mb-5 text-base font-bold text-gray-900">موجودی و وضعیت</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="admin-label">موجودی انبار <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required class="admin-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        محصول را از همان ابتدا فعال کن
                    </label>
                </div>
                <div class="sm:col-span-2 border-t border-gray-100 pt-5 mt-2">
                    <x-admin.variants-repeater />
                </div>
            </div>
            <div class="mt-6 flex justify-between border-t border-gray-100 pt-5">
                <button type="button" @click="step = 2" class="admin-btn-secondary">
                    <iconify-icon icon="tabler:arrow-right" class="text-base"></iconify-icon>
                    مرحله قبل
                </button>
                <button type="button" @click="step = 4" class="admin-btn-primary">
                    مرحله بعد
                    <iconify-icon icon="tabler:arrow-left" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>

        {{-- ── STEP 4: Images ──────────────────────────────────────────── --}}
        <div x-show="step === 4" x-cloak>
            <h2 class="mb-5 text-base font-bold text-gray-900">تصاویر محصول</h2>
            <div class="rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center transition-colors hover:border-indigo-200">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                    <iconify-icon icon="tabler:photo-plus" class="text-2xl"></iconify-icon>
                </div>
                <p class="mb-3 text-sm text-gray-500">تصاویر محصول را انتخاب کنید (اختیاری)</p>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="mx-auto block w-full max-w-sm text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-xs text-gray-400">حداکثر ۵ مگابایت برای هر تصویر · JPG, PNG, WebP</p>
            </div>

            <div class="mt-6 flex justify-between border-t border-gray-100 pt-5">
                <button type="button" @click="step = 3" class="admin-btn-secondary">
                    <iconify-icon icon="tabler:arrow-right" class="text-base"></iconify-icon>
                    مرحله قبل
                </button>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-100 transition-colors hover:bg-emerald-700">
                    <iconify-icon icon="tabler:check" class="text-base"></iconify-icon>
                    ثبت محصول
                </button>
            </div>
        </div>

    </div>
</form>
@endsection
