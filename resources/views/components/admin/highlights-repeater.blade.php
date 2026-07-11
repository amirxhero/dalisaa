@props([
    'items' => [],
])

@php
    $initialItems = old('highlights', $items);

    if (empty($initialItems)) {
        $initialItems = [['title' => '', 'value' => '']];
    } else {
        $initialItems = collect($initialItems)->map(function ($item) {
            if (is_string($item)) {
                return ['title' => '', 'value' => $item];
            }

            return [
                'title' => $item['title'] ?? '',
                'value' => $item['value'] ?? '',
            ];
        })->values()->all();
    }
@endphp

<div
    x-data="{
        items: @js($initialItems),
        addItem() {
            this.items.push({ title: '', value: '' });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        }
    }"
    class="space-y-3"
>
    <label class="admin-label">ویژگی‌ها</label>

    <template x-for="(item, index) in items" :key="index">
        <div class="flex items-start gap-2">
            <div class="grid flex-1 grid-cols-1 gap-2 sm:grid-cols-2">
                <input
                    type="text"
                    :name="'highlights[' + index + '][title]'"
                    x-model="item.title"
                    placeholder="عنوان (مثال: اندازه صفحه)"
                    class="admin-input"
                >
                <input
                    type="text"
                    :name="'highlights[' + index + '][value]'"
                    x-model="item.value"
                    placeholder="مقدار (مثال: ۶.۱ اینچ)"
                    class="admin-input"
                >
            </div>
            <button
                type="button"
                @click="removeItem(index)"
                class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-200 text-gray-400 transition-colors hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500"
                title="حذف"
            >
                <iconify-icon icon="tabler:trash" class="text-base"></iconify-icon>
            </button>
        </div>
    </template>

    <button
        type="button"
        @click="addItem()"
        class="inline-flex items-center gap-1.5 rounded-xl border border-dashed border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
    >
        <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
        افزودن ویژگی
    </button>
</div>
