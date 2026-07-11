@props(['active' => '', 'title' => ''])

@php
$navItems = [
['key' => 'dashboard', 'route' => 'panel.dashboard', 'icon' => 'grid', 'label' => 'داشبورد'],
['key' => 'orders', 'route' => 'panel.orders', 'icon' => 'box', 'label' => 'سفارش‌های من'],
['key' => 'wishlist', 'route' => 'panel.wishlist', 'icon' => 'heart', 'label' => 'علاقه‌مندی‌ها'],
['key' => 'addresses', 'route' => 'panel.addresses.index', 'icon' => 'map-pin', 'label' => 'آدرس‌ها'],
['key' => 'profile', 'route' => 'panel.profile', 'icon' => 'user', 'label' => 'اطلاعات حساب'],
];
$activeItem = collect($navItems)->firstWhere('key', $active);
@endphp

{{-- Single root element owns the Alpine state --}}
<div x-data="{ panelNavOpen: false }">

    {{-- ===== PAGE SECTION ===== --}}
    <div class="bg-white py-6 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 lg:px-6">

            @if ($title)
            <h1 class="mb-5 text-lg font-extrabold text-ink-900 sm:text-2xl">{{ $title }}</h1>
            @endif

            <div class="flex flex-col gap-5 lg:flex-row-reverse lg:items-start lg:gap-6">

                {{-- ===== DESKTOP SIDEBAR (right side in RTL = flex-row-reverse first child) ===== --}}
                <aside class="hidden lg:sticky lg:top-6 lg:block lg:w-64 lg:shrink-0">
                    {{-- User card --}}
                    <div class="mb-3 flex items-center gap-3 rounded-2xl border border-ink-100 bg-white p-4 shadow-sm">
                        <span style="display:flex;width:2.75rem;height:2.75rem;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background-color:#0f002b;font-size:1rem;font-weight:700;color:#fff;">
                            {{ mb_substr(auth()->user()->name ?? 'ک', 0, 1) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-ink-900">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-ink-400">{{ auth()->user()->mobile }}</p>
                        </div>
                    </div>

                    {{-- Nav card --}}
                    <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-sm">
                        <nav class="p-2">
                            @foreach ($navItems as $item)
                            @php $isActive = $active === $item['key']; @endphp
                            <a
                                href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all
                                    {{ $isActive ? 'bg-brand-500 text-white shadow-sm' : 'text-ink-600 hover:bg-ink-50 hover:text-ink-900' }}">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition-colors
                                    {{ $isActive ? 'bg-white/20' : 'bg-ink-100 text-ink-500' }}">
                                    <x-icon :name="$item['icon']" class="h-3.5 w-3.5" />
                                </span>
                                <span class="flex-1">{{ $item['label'] }}</span>
                                @if ($isActive)
                                    <span class="h-1.5 w-1.5 rounded-full bg-white/70"></span>
                                @endif
                            </a>
                            @endforeach
                        </nav>

                        <div class="border-t border-ink-100 p-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-ink-400 transition-all hover:bg-red-50 hover:text-red-500">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-ink-100">
                                        <x-icon name="close" class="h-3.5 w-3.5" />
                                    </span>
                                    خروج از حساب
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                {{-- ===== MOBILE TRIGGER (hidden on desktop) ===== --}}
                <div class="lg:hidden">
                    <button
                        type="button"
                        @click="panelNavOpen = true"
                        class="flex w-full items-center gap-3 rounded-2xl border border-ink-100 bg-white px-4 py-3 shadow-sm transition-colors active:bg-ink-50">
                        <span style="display:flex;flex-shrink:0;width:2.5rem;height:2.5rem;align-items:center;justify-content:center;border-radius:9999px;background-color:#0f002b;font-size:0.875rem;font-weight:700;color:#fff;">
                            {{ mb_substr(auth()->user()->name ?? 'ک', 0, 1) }}
                        </span>
                        <div class="min-w-0 flex-1 text-right">
                            <p class="truncate text-sm font-bold text-ink-900">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-ink-400">{{ $activeItem['label'] ?? 'منوی حساب' }}</p>
                        </div>
                        <span class="flex shrink-0 items-center gap-1 rounded-full bg-ink-50 px-2.5 py-1 text-[11px] font-medium text-ink-500">
                            منوی کاربری
                            <x-icon name="chevron-down" class="h-3 w-3" />
                        </span>
                    </button>
                </div>

                {{-- ===== PAGE CONTENT ===== --}}
                <div class="min-w-0 flex-1">
                    {{ $slot }}
                </div>

            </div>
        </div>
    </div>

    {{-- ===== MOBILE BOTTOM SHEET (inside same x-data scope) ===== --}}
    <div
        x-show="panelNavOpen"
        x-cloak
        class="fixed inset-0 z-[60] lg:hidden"
        @keydown.escape.window="panelNavOpen = false">
        {{-- Backdrop --}}
        <div
            x-show="panelNavOpen"
            x-transition:enter="transition-opacity duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="panelNavOpen = false"
            class="absolute inset-0 bg-ink-900/50 backdrop-blur-sm"></div>

        {{-- Sheet panel --}}
        <div
            x-show="panelNavOpen"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute bottom-0 left-0 right-0 max-h-[90dvh] overflow-hidden rounded-t-3xl bg-white">
            {{-- Drag handle --}}
            <div class="flex justify-center pb-2 pt-3">
                <div class="h-1 w-10 rounded-full bg-ink-200"></div>
            </div>

            {{-- Sheet header --}}
            <div class="flex items-center gap-3 border-b border-ink-100 px-6 py-4">
                {{-- Avatar — first DOM child = rightmost in RTL, px-6 gives 24px clearance from corner --}}
                <span style="display:flex;width:2.75rem;height:2.75rem;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background-color:#0f002b;font-size:1rem;font-weight:700;color:#fff;">
                    {{ mb_substr(auth()->user()->name ?? 'ک', 0, 1) }}
                </span>

                {{-- Name + phone — takes remaining space --}}
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-ink-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-ink-400">{{ auth()->user()->mobile }}</p>
                </div>

                {{-- Close button — last DOM child = leftmost in RTL --}}
                <button
                    type="button"
                    @click="panelNavOpen = false"
                    class="flex shrink-0 items-center justify-center rounded-full transition-colors"
                    style="width:2rem;height:2rem;background-color:#e7e8ec;color:#6b6d78;">
                    <x-icon name="close" class="h-3.5 w-3.5" />
                </button>
            </div>

            {{-- Nav items --}}
            <nav class="p-2">
                @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @click="panelNavOpen = false"
                    class="flex items-center gap-3 rounded-2xl px-3 py-3 transition-colors {{ $active === $item['key'] ? 'bg-brand-50 text-brand-600' : 'text-ink-700 hover:bg-ink-50' }}">
                    <span
                        class="flex shrink-0 items-center justify-center rounded-xl"
                        style="width:2rem;height:2rem;{{ $active === $item['key'] ? 'background-color:#ee273a;color:#fff;' : 'background-color:#e7e8ec;color:#6b6d78;' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                    </span>
                    <span class="flex-1 text-sm font-medium leading-none">{{ $item['label'] }}</span>
                    @if ($active === $item['key'])
                    <span
                        class="flex items-center justify-center rounded-full"
                        style="width:1.25rem;height:1.25rem;background-color:#ee273a;">
                        <x-icon name="check" class="h-3 w-3 text-white" />
                    </span>
                    @else
                    <x-icon name="chevron-left" class="h-4 w-4 text-ink-200" />
                    @endif
                </a>
                @endforeach
            </nav>

            {{-- Logout --}}
            <div class="border-t border-ink-100 p-2 pb-safe" style="padding-bottom: max(2rem, env(safe-area-inset-bottom))">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium text-red-500 transition-colors hover:bg-red-50">
                        <span
                            class="flex shrink-0 items-center justify-center rounded-xl"
                            style="width:2rem;height:2rem;background-color:#fef2f2;color:#f87171;">
                            <x-icon name="close" class="h-4 w-4" />
                        </span>
                        خروج از حساب
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>