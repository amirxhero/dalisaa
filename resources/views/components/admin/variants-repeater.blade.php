@props([
    'items' => [],
])

@php
    $initialItems = old('variants', $items);

    if (empty($initialItems)) {
        $initialItems = [];
    } else {
        $initialItems = collect($initialItems)->map(function ($item) {
            // If regular_price is not null/zero, it represents the base price before discount.
            // Otherwise, price is the base price.
            $basePrice = $item['regular_price'] ?? $item['price'] ?? '';
            return [
                'id' => $item['id'] ?? null,
                'color_name' => $item['color_name'] ?? '',
                'color_hex' => $item['color_hex'] ?? '',
                'base_price' => $basePrice,
                'discount_type' => $item['discount_type'] ?? 'none',
                'discount_value' => $item['discount_value'] ?? 0,
                'stock' => $item['stock'] ?? 0,
                'is_default' => (bool) ($item['is_default'] ?? false),
            ];
        })->values()->all();
    }
@endphp

<div
    x-data="{
        items: @js($initialItems),
        addItem() {
            this.items.push({
                id: null,
                color_name: '',
                color_hex: '',
                base_price: '',
                discount_type: 'none',
                discount_value: 0,
                stock: 0,
                is_default: this.items.length === 0
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
            // Ensure at least one item is marked default if list is not empty
            if (this.items.length > 0 && !this.items.some(item => item.is_default)) {
                this.items[0].is_default = true;
            }
        },
        setDefault(index) {
            this.items.forEach((item, i) => {
                item.is_default = (i === index);
            });
        }
    }"
    class="space-y-4"
>
    <div class="flex items-center justify-between">
        <label class="admin-label !mb-0">تنوع متغیرها و قیمت</label>
        <button
            type="button"
            @click="addItem()"
            class="inline-flex items-center gap-1.5 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 px-3 py-2 text-xs font-semibold text-indigo-600 transition-colors hover:border-indigo-300 hover:bg-indigo-50"
        >
            <iconify-icon icon="tabler:plus" class="text-sm"></iconify-icon>
            افزودن متغیر جدید
        </button>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-100 bg-white">
        <table class="w-full border-collapse text-right text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/75">
                    <th class="px-4 py-3 font-semibold text-gray-600 w-12 text-center">پیش‌فرض</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">نام متغیر * (مثلا سایز L، حافظه ۲۵۶ گیگابایت، قرمز)</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 w-24">کد رنگ (اختیاری)</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 w-32">قیمت پایه (تومان، اختیاری)</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 w-32">نوع تخفیف</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 w-28">مقدار تخفیف</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 w-24">موجودی *</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 w-12 text-center">حذف</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in items" :key="index">
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/20">
                        <td class="px-4 py-3 text-center">
                            <input
                                type="radio"
                                name="default_variant"
                                :value="index"
                                :checked="item.is_default"
                                @change="setDefault(index)"
                                class="h-4 w-4 rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <input type="hidden" :name="'variants[' + index + '][is_default]'" :value="item.is_default ? 1 : 0">
                        </td>
                        <td class="px-4 py-3">
                            <input type="hidden" :name="'variants[' + index + '][id]'" :value="item.id">
                            <input
                                type="text"
                                :name="'variants[' + index + '][color_name]'"
                                x-model="item.color_name"
                                placeholder="مثال: سایز L"
                                required
                                class="admin-input py-1.5 text-xs w-full"
                            >
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <input
                                    type="color"
                                    :value="item.color_hex || '#ffffff'"
                                    @input="item.color_hex = $event.target.value"
                                    class="h-7 w-7 shrink-0 cursor-pointer rounded-lg border border-gray-200 p-0"
                                >
                                <input
                                    type="text"
                                    :name="'variants[' + index + '][color_hex]'"
                                    x-model="item.color_hex"
                                    placeholder="بدون رنگ"
                                    pattern="^#[0-9a-fA-F]{6}$|^$"
                                    class="admin-input py-1.5 px-2 text-xs font-mono text-center w-20"
                                >
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                type="number"
                                :name="'variants[' + index + '][base_price]'"
                                x-model="item.base_price"
                                placeholder="پیش‌فرض محصول"
                                min="0"
                                class="admin-input py-1.5 px-2 text-xs w-28"
                            >
                        </td>
                        <td class="px-4 py-3">
                            <select
                                :name="'variants[' + index + '][discount_type]'"
                                x-model="item.discount_type"
                                class="admin-select py-1.5 px-2 text-xs w-32"
                            >
                                <option value="none">بدون تخفیف</option>
                                <option value="percent">درصدی (٪)</option>
                                <option value="amount">مقدار ثابت (تومان)</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                type="number"
                                :name="'variants[' + index + '][discount_value]'"
                                x-model="item.discount_value"
                                min="0"
                                class="admin-input py-1.5 px-2 text-xs w-24"
                            >
                        </td>
                        <td class="px-4 py-3">
                            <input
                                type="number"
                                :name="'variants[' + index + '][stock]'"
                                x-model="item.stock"
                                required
                                min="0"
                                class="admin-input py-1.5 px-2 text-xs w-20"
                            >
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                @click="removeItem(index)"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-100 text-gray-400 transition-colors hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500"
                            >
                                <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                            </button>
                        </td>
                    </tr>
                </template>
                <template x-if="items.length === 0">
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-400 text-xs">
                            هیچ متغیری ثبت نشده است. این محصول فاقد متغیر خواهد بود.
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
