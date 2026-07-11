@extends('admin.layouts.admin')

@section('title', 'ویرایش محصول')
@section('page-title', 'ویرایش: '.$product->title)
@php
    $breadcrumbs = ['محصولات', 'ویرایش'];
@endphp

@section('content')
<form
    action="{{ route('admin.products.update', $product) }}"
    method="POST"
    enctype="multipart/form-data"
    x-data='{
        currency: "{{ old('price_currency', $product->price_currency) }}",
        priceOriginal: "{{ old('price_original', $product->price_original) }}",
        rates: @json($rates),
        discountType: "{{ old('discount_type', $product->discount_type ?? 'none') }}",
        discountValue: "{{ old('discount_value', $product->discount_value ?? 0) }}",
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
>
    @csrf @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Main --}}
        <div class="space-y-6 lg:col-span-2">
            <x-admin.section title="اطلاعات پایه">
                <div class="space-y-4 p-5">
                    <div>
                        <label class="admin-label">نام محصول *</label>
                        <input type="text" name="title" value="{{ old('title', $product->title) }}" required class="admin-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="admin-label">برند *</label>
                            <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" required class="admin-input">
                        </div>
                        <div>
                            <label class="admin-label">کد SKU *</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="admin-input font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="admin-label">دسته‌بندی *</label>
                        <select name="category_id" required class="admin-select">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->parent ? '└ ' : '' }}{{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">توضیحات *</label>
                        <textarea name="description" rows="4" required class="admin-input">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div>
                        <x-admin.highlights-repeater :items="$product->highlights ?? []" />
                    </div>
                </div>
            </x-admin.section>

            <x-admin.section title="قیمت‌گذاری">
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                    <div>
                        <label class="admin-label">ارز *</label>
                        <select name="price_currency" x-model="currency" required class="admin-select">
                            @foreach($currencies as $code => $label)
                            <option value="{{ $code }}" {{ old('price_currency', $product->price_currency) === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">مبلغ پایه *</label>
                        <input type="number" name="price_original" x-model="priceOriginal" step="0.01" min="0" required
                               value="{{ old('price_original', $product->price_original) }}" class="admin-input">
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
                        <input type="number" name="discount_value" x-model="discountValue" min="0" value="{{ old('discount_value', $product->discount_value ?? 0) }}" class="admin-input">
                    </div>
                    <div class="sm:col-span-2 flex items-center rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                        <template x-if="tomanEquiv">
                            <div class="flex-1 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-indigo-500">قیمت نهایی پس از تخفیف (جدید)</p>
                                    <p class="text-lg font-bold text-indigo-800" x-text="tomanEquiv + ' تومان'"></p>
                                </div>
                                <div class="text-xs text-right text-indigo-500" x-show="discountType !== 'none'">
                                    <p>قیمت اصلی: <span x-text="baseToman.toLocaleString('fa-IR') + ' تومان'"></span></p>
                                    <p>کاهش قیمت: <span x-text="(baseToman - finalToman).toLocaleString('fa-IR') + ' تومان'"></span></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!tomanEquiv">
                            <div>
                                <p class="text-xs text-indigo-500">قیمت فعلی</p>
                                <p class="text-lg font-bold text-indigo-800">{{ number_format($product->price) }} تومان</p>
                            </div>
                        </template>
                    </div>
                </div>
            </x-admin.section>

            {{-- Variants --}}
            <x-admin.section title="تنوع متغیرها">
                <div class="p-5">
                    <x-admin.variants-repeater :items="$product->variants ?? []" />
                </div>
            </x-admin.section>

            {{-- Images --}}
            <x-admin.section title="تصاویر">
                <div class="p-5">
                    @if($product->getMedia('gallery')->isNotEmpty())
                    <div class="mb-4 flex flex-wrap gap-2">
                        @foreach($product->getMedia('gallery') as $media)
                        <img src="{{ $media->getUrl('thumb') }}" alt="" class="h-20 w-20 rounded-xl border border-gray-200 object-cover">
                        @endforeach
                    </div>
                    <p class="mb-3 flex items-center gap-1.5 text-xs text-amber-600">
                        <iconify-icon icon="tabler:alert-triangle"></iconify-icon>
                        آپلود تصاویر جدید، تصاویر قبلی را جایگزین می‌کند.
                    </p>
                    @endif
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </x-admin.section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <x-admin.section title="وضعیت">
                <div class="p-5">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        محصول فعال است
                    </label>
                </div>
            </x-admin.section>
            <x-admin.section title="موجودی">
                <div class="p-5">
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="admin-input">
                </div>
            </x-admin.section>
            <div class="flex flex-col gap-2">
                <button type="submit" class="admin-btn-primary py-3">
                    <iconify-icon icon="tabler:device-floppy" class="text-base"></iconify-icon>
                    ذخیره تغییرات
                </button>
                <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary py-3">انصراف</a>
            </div>
        </div>
    </div>
</form>
@endsection
