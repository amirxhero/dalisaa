@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-4 flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        <iconify-icon icon="tabler:circle-check" class="shrink-0 text-base"></iconify-icon>
        <span class="flex-1">{{ session('success') }}</span>
        <button @click="show = false" class="shrink-0 text-emerald-400 hover:text-emerald-600">
            <iconify-icon icon="tabler:x" class="text-base"></iconify-icon>
        </button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-4 flex items-center gap-2.5 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <iconify-icon icon="tabler:circle-x" class="shrink-0 text-base"></iconify-icon>
        <span class="flex-1">{{ session('error') }}</span>
        <button @click="show = false" class="shrink-0 text-rose-400 hover:text-rose-600">
            <iconify-icon icon="tabler:x" class="text-base"></iconify-icon>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <p class="mb-1 flex items-center gap-2 font-semibold">
            <iconify-icon icon="tabler:alert-triangle" class="text-base"></iconify-icon>
            لطفاً خطاهای زیر را برطرف کنید
        </p>
        <ul class="list-inside list-disc space-y-0.5 pr-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
