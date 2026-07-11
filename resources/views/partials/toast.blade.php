{{-- ===== Cart success toast ===== --}}
@if (session('cart_success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        x-cloak
        class="fixed bottom-24 left-4 right-4 z-[60] mx-auto max-w-sm lg:bottom-6 lg:left-auto lg:right-6"
    >
        <div class="flex items-start gap-3 rounded-2xl bg-white p-4 shadow-[0_8px_32px_rgba(15,0,43,0.18)] ring-1 ring-ink-100">

            {{-- Green check icon --}}
            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent-500">
                <x-icon name="check" class="h-4.5 w-4.5 text-white" />
            </div>

            {{-- Text --}}
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-ink-900">به سبد خرید اضافه شد</p>
                <p class="mt-0.5 truncate text-xs text-ink-400">{{ session('cart_success') }}</p>
            </div>

            {{-- View cart button --}}
            <a
                href="{{ route('cart.index') }}"
                class="shrink-0 rounded-xl bg-ink-900 px-3 py-2 text-xs font-bold text-white transition-colors hover:bg-brand-500"
            >
                مشاهده سبد
            </a>
        </div>

        {{-- Auto-dismiss progress bar --}}
        <div class="mt-1.5 h-0.5 w-full overflow-hidden rounded-full bg-ink-100">
            <div
                class="h-full bg-accent-500"
                x-init="$el.style.width = '100%'; $el.style.transition = 'width 5s linear'; requestAnimationFrame(() => $el.style.width = '0%')"
            ></div>
        </div>
    </div>
@endif

{{-- ===== Generic success / error toast ===== --}}
@if (session('success') || session('error'))
    @php $isError = (bool) session('error'); @endphp
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        x-cloak
        class="fixed bottom-24 left-4 right-4 z-[60] mx-auto max-w-sm lg:bottom-6 lg:left-auto lg:right-6"
    >
        <div class="flex items-center gap-3 rounded-2xl p-4 shadow-[0_8px_32px_rgba(15,0,43,0.18)] {{ $isError ? 'bg-ink-900' : 'bg-white ring-1 ring-ink-100' }}">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isError ? 'bg-white/10' : 'bg-accent-500' }}">
                <x-icon name="{{ $isError ? 'close' : 'check' }}" class="h-4 w-4 {{ $isError ? 'text-white' : 'text-white' }}" />
            </div>
            <p class="text-sm font-medium {{ $isError ? 'text-white' : 'text-ink-900' }}">
                {{ session('error') ?? session('success') }}
            </p>
        </div>
    </div>
@endif
