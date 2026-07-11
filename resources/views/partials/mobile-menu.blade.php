{{-- Mobile off-canvas navigation drawer --}}
<div x-show="mobileMenuOpen" x-cloak @keydown.escape.window="mobileMenuOpen = false" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="absolute inset-0 bg-ink-900/50"
    ></div>

    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="absolute inset-y-0 right-0 flex w-[85%] max-w-sm flex-col bg-white shadow-2xl"
    >
        <div class="flex items-center justify-between border-b border-ink-100 px-4 py-4">
            <img src="https://kaveh.moeinwp.com/1/wp-content/uploads/2022/10/demo1.svg" alt="قالب کاوه" class="h-8 w-auto">
            <button type="button" @click="mobileMenuOpen = false" class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-50 text-ink-600">
                <x-icon name="close" class="h-4.5 w-4.5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto p-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-ink-800 hover:bg-ink-50">
                <x-icon name="home" class="h-5 w-5 text-brand-500" />
                صفحه اصلی
            </a>

            <div x-data="{ open: false }" class="border-t border-ink-100 pt-1">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-3 text-sm font-medium text-ink-800 hover:bg-ink-50">
                    <span class="flex items-center gap-3">
                        <x-icon name="grid" class="h-5 w-5 text-brand-500" />
                        محصولات
                    </span>
                    <span class="inline-flex transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                        <x-icon name="chevron-down" class="h-4 w-4" />
                    </span>
                </button>
                <ul x-show="open" x-cloak x-transition class="space-y-1 py-1 ps-11">
                    @foreach ($megaMenu as $section)
                        <li>
                            <a href="#" class="flex items-center gap-2 rounded-lg px-2 py-2 text-[13px] text-ink-600 hover:text-brand-500">
                                <x-icon :name="$section['icon']" class="h-4 w-4" />
                                {{ $section['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('blog.index') }}" class="flex items-center gap-3 rounded-xl border-t border-ink-100 px-3 py-3 pt-4 text-sm font-medium text-ink-800 hover:bg-ink-50">وبلاگ</a>
            <a href="{{ route('about') }}" class="flex items-center gap-3 px-3 py-3 text-sm font-medium text-ink-800 hover:bg-ink-50">درباره ما</a>
            <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-3 text-sm font-medium text-ink-800 hover:bg-ink-50">تماس با ما</a>
        </nav>

        <div class="border-t border-ink-100 p-4">
            <button type="button" @click="mobileMenuOpen = false; authOpen = true" class="flex w-full items-center justify-center gap-2 rounded-xl bg-ink-900 py-3 text-sm font-bold text-white">
                <x-icon name="user" class="h-4.5 w-4.5" />
                ورود / ثبت‌نام
            </button>
        </div>
    </div>
</div>
