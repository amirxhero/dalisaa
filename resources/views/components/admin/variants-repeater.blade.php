@props([
    'items' => [],
])

@php
    $allCurrencies = [
        'IRR'  => 'تومان',
        'USD'  => 'دلار ($)',
        'EUR'  => 'یورو (€)',
        'USDT' => 'تتر (₮)',
        'GBP'  => 'پوند (£)',
    ];

    $initialItems = old('variants', $items);

    if (empty($initialItems)) {
        $initialItems = [];
    } else {
        $initialItems = collect($initialItems)->map(function ($item) {
            // Support both Eloquent models (toArray) and plain arrays (old() post data)
            $arr = is_object($item) ? $item->toArray() : (array) $item;
            $basePrice = $arr['regular_price'] ?? $arr['price'] ?? '';
            return [
                'id'             => $arr['id'] ?? null,
                'color_name'     => $arr['color_name'] ?? '',
                'color_hex'      => $arr['color_hex'] ?? '',
                'price_currency' => $arr['price_currency'] ?? 'IRR',
                'base_price'     => $basePrice,
                'discount_type'  => $arr['discount_type'] ?? 'none',
                'discount_value' => $arr['discount_value'] ?? 0,
                'stock'          => $arr['stock'] ?? 0,
                'is_default'     => (bool) ($arr['is_default'] ?? false),
            ];
        })->values()->all();
    }
@endphp

<div
    x-data="{
        items: @js($initialItems),
        currencies: @js($allCurrencies),
        addItem() {
            this.items.push({
                id: null,
                color_name: '',
                color_hex: '',
                price_currency: 'IRR',
                base_price: '',
                discount_type: 'none',
                discount_value: 0,
                stock: 0,
                is_default: this.items.length === 0
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
            if (this.items.length > 0 && !this.items.some(item => item.is_default)) {
                this.items[0].is_default = true;
            }
        },
        setDefault(index) {
            this.items.forEach((item, i) => {
                item.is_default = (i === index);
            });
        },
        currencySymbol(code) {
            const map = { IRR: 'تومان', USD: '$', EUR: '€', USDT: '₮', GBP: '£' };
            return map[code] || code;
        }
    }"
    class="space-y-4"
>
    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <label class="admin-label !mb-0">تنوع متغیرها</label>
            <p class="mt-0.5 text-xs text-gray-400 leading-relaxed">هر متغیر می‌تواند قیمت، ارز و موجودی مستقل داشته باشد</p>
        </div>
        <button
            type="button"
            @click="addItem()"
            class="shrink-0 inline-flex items-center gap-1.5 rounded-xl border border-dashed border-indigo-300 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 whitespace-nowrap transition-all hover:border-indigo-400 hover:bg-indigo-100 hover:shadow-sm"
        >
            <iconify-icon icon="tabler:plus" class="text-sm shrink-0"></iconify-icon>
            افزودن متغیر
        </button>
    </div>

    {{-- Empty state --}}
    <template x-if="items.length === 0">
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 py-10 text-center">
            <iconify-icon icon="tabler:stack-2" class="mb-2 text-3xl text-gray-300"></iconify-icon>
            <p class="text-sm font-medium text-gray-400">هیچ متغیری ثبت نشده</p>
            <p class="mt-1 text-xs text-gray-300">این محصول بدون متغیر ثبت خواهد شد</p>
        </div>
    </template>

    {{-- Variant Cards --}}
    <div class="space-y-3">
        <template x-for="(item, index) in items" :key="index">
            <div
                class="relative rounded-2xl border bg-white shadow-sm transition-all duration-200"
                :class="item.is_default ? 'border-indigo-200 shadow-indigo-50' : 'border-gray-200 hover:border-gray-300'"
            >
                {{-- Default badge + delete --}}
                <div class="flex items-center justify-between border-b px-4 py-2.5"
                     :class="item.is_default ? 'border-indigo-100 bg-indigo-50/60' : 'border-gray-100 bg-gray-50/50'">
                    <div class="flex items-center gap-2">
                        <input type="hidden" :name="'variants[' + index + '][is_default]'" :value="item.is_default ? 1 : 0">
                        <input type="hidden" :name="'variants[' + index + '][id]'" :value="item.id">
                        <label class="flex cursor-pointer items-center gap-2 text-xs font-medium select-none"
                               :class="item.is_default ? 'text-indigo-700' : 'text-gray-500'">
                            <input
                                type="radio"
                                name="default_variant"
                                :value="index"
                                :checked="item.is_default"
                                @change="setDefault(index)"
                                class="h-3.5 w-3.5 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span x-text="item.is_default ? '✓ متغیر پیش‌فرض' : 'تنظیم به‌عنوان پیش‌فرض'"></span>
                        </label>
                    </div>
                    <button
                        type="button"
                        @click="removeItem(index)"
                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-rose-50 hover:text-rose-500"
                    >
                        <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                    </button>
                </div>

                {{-- Fields grid --}}
                <div class="space-y-3 p-4">

                    {{-- Row 1: Color / Name (full width) --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">
                            نام متغیر <span class="text-rose-400">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                type="color"
                                :value="item.color_hex || '#e5e7eb'"
                                @input="item.color_hex = $event.target.value"
                                class="h-9 w-9 shrink-0 cursor-pointer rounded-lg border border-gray-200 p-0.5 shadow-sm"
                                title="کد رنگ"
                            >
                            <input
                                type="hidden"
                                :name="'variants[' + index + '][color_hex]'"
                                x-model="item.color_hex"
                            >
                            <input
                                type="text"
                                :name="'variants[' + index + '][color_name]'"
                                x-model="item.color_name"
                                placeholder="مثال: قرمز، سایز XL، ۲۵۶ گیگابایت..."
                                required
                                class="admin-input flex-1 py-2 text-sm"
                            >
                        </div>
                        <p class="mt-1 text-[10px] font-mono text-gray-400"
                           x-show="item.color_hex"
                           x-text="'# ' + (item.color_hex || '').replace('#','')">
                        </p>
                    </div>

                    {{-- Row 2: Stock + Currency (50/50) --}}
                    <div class="grid grid-cols-2 gap-3">

                        {{-- Stock --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">
                                موجودی <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    :name="'variants[' + index + '][stock]'"
                                    x-model="item.stock"
                                    required
                                    min="0"
                                    class="admin-input py-2 text-center text-sm font-semibold"
                                >
                            </div>
                        </div>

                        {{-- Price Currency --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">نوع ارز</label>
                            <select
                                :name="'variants[' + index + '][price_currency]'"
                                x-model="item.price_currency"
                                class="admin-select py-2 text-sm"
                            >
                                <template x-for="(label, code) in currencies" :key="code">
                                    <option :value="code" x-text="label" :selected="item.price_currency === code"></option>
                                </template>
                            </select>
                        </div>

                    </div>

                    {{-- Row 3: Base Price (full width) --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">
                            قیمت پایه
                            <span class="font-normal text-gray-400">(اختیاری)</span>
                        </label>
                        <p class="mb-1.5 text-[10px] text-gray-400">اگر خالی بماند، قیمت اصلی محصول استفاده می‌شود</p>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                :name="'variants[' + index + '][base_price]'"
                                x-model="item.base_price"
                                placeholder="0"
                                min="0"
                                class="admin-input flex-1 py-2 text-sm"
                            >
                            <span
                                class="shrink-0 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-2 text-xs font-semibold text-gray-600 whitespace-nowrap min-w-[3rem] text-center"
                                x-text="currencySymbol(item.price_currency)"
                            ></span>
                        </div>
                    </div>

                    {{-- Row 4: Discount Type + Discount Value (50/50 or full) --}}
                    <div class="grid gap-3"
                         :class="item.discount_type !== 'none' ? 'grid-cols-2' : 'grid-cols-1'">

                        {{-- Discount Type --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">نوع تخفیف</label>
                            <select
                                :name="'variants[' + index + '][discount_type]'"
                                x-model="item.discount_type"
                                class="admin-select py-2 text-sm"
                            >
                                <option value="none">بدون تخفیف</option>
                                <option value="percent">درصدی (٪)</option>
                                <option value="amount">مبلغ ثابت</option>
                            </select>
                        </div>

                        {{-- Discount Value --}}
                        <div x-show="item.discount_type !== 'none'" x-transition>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">
                                مقدار
                                <span class="font-normal text-gray-400" x-text="item.discount_type === 'percent' ? '(٪)' : '(تومان)'"></span>
                            </label>
                            <input
                                type="number"
                                :name="'variants[' + index + '][discount_value]'"
                                x-model="item.discount_value"
                                min="0"
                                class="admin-input py-2 text-sm text-center"
                            >
                        </div>

                    </div>

                </div>
            </div>
        </template>
    </div>
</div>
