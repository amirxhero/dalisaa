{{-- Site header: desktop top-bar + mega menu, and a compact mobile header --}}
<header class="sticky top-0 z-40 bg-white shadow-sm">

    {{-- ============== DESKTOP ============== --}}
    <div class="hidden border-b border-ink-100 lg:block">
        <div class="mx-auto flex max-w-7xl items-center gap-8 px-6 py-4">
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="https://kaveh.moeinwp.com/1/wp-content/uploads/2022/10/demo1.svg" alt="قالب کاوه" class="h-10 w-auto">
            </a>

            <form action="#" class="flex max-w-xl flex-1 items-center overflow-hidden rounded-full border border-ink-100 bg-ink-50 focus-within:border-brand-300">
                <input type="text" placeholder="جستجوی محصول" class="w-full flex-1 bg-transparent px-5 py-2.5 text-sm text-ink-800 outline-none placeholder:text-ink-400">
                <button type="submit" class="flex h-10 w-12 shrink-0 items-center justify-center rounded-full bg-brand-500 text-white transition-colors hover:bg-brand-600">
                    <x-icon name="search" class="h-4.5 w-4.5" />
                </button>
            </form>

            <div class="flex shrink-0 items-center gap-5 text-ink-600">
                @if($siteSettings['contact_phone'])
                <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings['contact_phone']) }}" class="flex items-center gap-2 text-xs">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-50">
                        <x-icon name="phone-call" class="h-4 w-4" />
                    </span>
                    <span class="hidden flex-col leading-tight xl:flex">
                        <span class="text-ink-400">پشتیبانی سریع</span>
                        <span class="font-bold text-ink-800" dir="ltr">{{ $siteSettings['contact_phone'] }}</span>
                    </span>
                </a>
                @endif

                @auth
                    <a href="{{ route('panel.wishlist') }}" class="relative flex h-9 w-9 items-center justify-center rounded-full transition-colors hover:bg-ink-50 hover:text-brand-500" aria-label="علاقه‌مندی‌ها">
                        <x-icon name="heart" class="h-5 w-5" />
                    </a>
                @else
                    <button type="button" @click="authOpen = true" class="relative flex h-9 w-9 items-center justify-center rounded-full transition-colors hover:bg-ink-50 hover:text-brand-500" aria-label="علاقه‌مندی‌ها">
                        <x-icon name="heart" class="h-5 w-5" />
                    </button>
                @endauth

                @auth
                    <div class="group relative">
                        <button type="button" class="flex items-center gap-1.5 text-sm font-medium transition-colors hover:text-brand-500">
                            <x-icon name="user" class="h-5 w-5" />
                            {{ Str::limit(auth()->user()->name, 14) }}
                            <x-icon name="chevron-down" class="h-3.5 w-3.5" />
                        </button>
                        <div class="invisible absolute left-0 top-full z-50 w-44 translate-y-2 rounded-xl border border-ink-100 bg-white p-1.5 opacity-0 shadow-card-hover transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                            <a href="{{ route('panel.dashboard') }}" class="block rounded-lg px-3 py-2 text-xs font-medium text-ink-700 hover:bg-ink-50">حساب کاربری</a>
                            <a href="{{ route('panel.orders') }}" class="block rounded-lg px-3 py-2 text-xs font-medium text-ink-700 hover:bg-ink-50">سفارش‌های من</a>
                            <a href="{{ route('panel.wishlist') }}" class="block rounded-lg px-3 py-2 text-xs font-medium text-ink-700 hover:bg-ink-50">علاقه‌مندی‌ها</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-right text-xs font-medium text-brand-500 hover:bg-brand-50">خروج</button>
                            </form>
                        </div>
                    </div>
                @else
                    <button type="button" @click="authOpen = true" class="flex items-center gap-1.5 text-sm font-medium transition-colors hover:text-brand-500">
                        <x-icon name="user" class="h-5 w-5" />
                        ورود/ثبت نام
                    </button>
                @endauth
            </div>
        </div>

        <div class="mx-auto flex max-w-7xl items-center justify-between px-6">
            <nav class="flex items-stretch">
                <a href="{{ route('home') }}" class="flex items-center gap-2 border-b-2 border-transparent px-4 py-3.5 text-sm font-medium text-ink-800 transition-colors hover:border-brand-500 hover:text-brand-500">
                    <x-icon name="home" class="h-4.5 w-4.5" />
                    صفحه اصلی
                </a>

                <div class="group relative">
                    <button type="button" class="flex items-center gap-1.5 border-b-2 border-transparent px-4 py-3.5 text-sm font-medium text-ink-800 transition-colors group-hover:border-brand-500 group-hover:text-brand-500">
                        <x-icon name="grid" class="h-4.5 w-4.5" />
                        محصولات
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 transition-transform group-hover:rotate-180" />
                    </button>

                    {{-- Mega menu --}}
                    <div
                        x-data="{ tab: 0 }"
                        class="invisible absolute right-0 top-full z-50 w-[min(920px,90vw)] translate-y-2 rounded-2xl border border-ink-100 bg-white p-2 opacity-0 shadow-card-hover transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"
                    >
                        <div class="flex">
                            <ul class="w-56 shrink-0 border-l border-ink-100 pl-2">
                                @foreach ($megaMenu as $i => $section)
                                    <li>
                                        <button
                                            type="button"
                                            @mouseenter="tab = {{ $i }}"
                                            :class="tab === {{ $i }} ? 'bg-brand-50 text-brand-500' : 'text-ink-600 hover:bg-ink-50'"
                                            class="flex w-full items-center gap-2.5 rounded-xl px-3 py-3 text-sm font-medium transition-colors"
                                        >
                                            <x-icon :name="$section['icon']" class="h-4.5 w-4.5" />
                                            {{ $section['label'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="flex-1 p-4">
                                @foreach ($megaMenu as $i => $section)
                                    <div x-show="tab === {{ $i }}" x-cloak class="flex gap-6">
                                        @foreach ($section['columns'] as $column)
                                            <div class="flex-1">
                                                <p class="mb-3 flex items-center gap-2 text-sm font-bold text-ink-800">
                                                    {{ $column['title'] }}
                                                    @if (!empty($column['badge']))
                                                        <span class="rounded-full bg-brand-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $column['badge'] }}</span>
                                                    @endif
                                                </p>
                                                <ul class="space-y-2.5">
                                                    @foreach ($column['links'] as $link)
                                                        <li>
                                                            <a href="{{ $link['href'] }}" class="text-[13px] text-ink-600 transition-colors hover:text-brand-500">{{ $link['label'] }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach

                                        <div class="relative w-52 shrink-0 overflow-hidden rounded-xl bg-ink-50">
                                            <img src="{{ $section['promo']['image'] }}" alt="{{ $section['label'] }}" class="h-full w-full object-cover">
                                            @if (!empty($section['promo']['badge']))
                                                <span class="absolute left-2 top-2 rounded-full bg-brand-500 px-2 py-1 text-[11px] font-bold text-white">{{ $section['promo']['badge'] }}</span>
                                            @endif
                                            <a href="{{ $section['promo']['href'] ?? '#' }}" class="absolute bottom-2 right-2 left-2 flex items-center justify-center gap-1 rounded-lg bg-accent-400 py-2 text-xs font-bold text-white transition-colors hover:bg-accent-500">
                                                خرید کنید
                                                <x-icon name="chevron-left" class="h-3 w-3" />
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('blog.index') }}" class="flex items-center border-b-2 border-transparent px-4 py-3.5 text-sm font-medium text-ink-800 transition-colors hover:border-brand-500 hover:text-brand-500">وبلاگ</a>
                <a href="{{ route('about') }}" class="flex items-center border-b-2 border-transparent px-4 py-3.5 text-sm font-medium text-ink-800 transition-colors hover:border-brand-500 hover:text-brand-500">درباره ما</a>
                <a href="{{ route('contact') }}" class="flex items-center border-b-2 border-transparent px-4 py-3.5 text-sm font-medium text-ink-800 transition-colors hover:border-brand-500 hover:text-brand-500">تماس با ما</a>
            </nav>

            <button type="button" @click="cartOpen = true" class="flex items-center gap-2 rounded-full bg-accent-400 py-2 pe-4 ps-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-accent-500">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/20">
                    <x-icon name="cart" class="h-4 w-4" />
                </span>
                {{ $cart->items_count }}
            </button>
        </div>
    </div>

    {{-- ============== MOBILE ============== --}}
    <div class="lg:hidden">
        <div class="relative flex items-center justify-between px-4 py-3">
            @if($siteSettings['contact_phone'])
            <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings['contact_phone']) }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-50 text-ink-600">
                <x-icon name="phone-call" class="h-4.5 w-4.5" />
            </a>
            @else
            <span class="h-9 w-9"></span>
            @endif

            <a href="{{ route('home') }}" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
                <img src="https://kaveh.moeinwp.com/1/wp-content/uploads/2022/10/demo1.svg" alt="قالب کاوه" class="h-8 w-auto">
            </a>

            <a href="{{ route('special-offers.index') }}" class="flex h-9 w-9 min-[360px]:w-auto items-center justify-center gap-1.5 text-xs font-semibold text-brand-500 bg-brand-50 rounded-full min-[360px]:px-3 transition-all duration-200 hover:bg-brand-100 active:scale-95">
                <x-icon name="fire" class="h-4.5 w-4.5 text-brand-500 animate-pulse shrink-0" />
                <span class="hidden min-[360px]:inline">شگفت‌انگیز</span>
            </a>
        </div>

        <div class="flex items-center gap-3 px-4 pb-3">
            <button type="button" @click="cartOpen = true" class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-ink-50 text-ink-600">
                <x-icon name="cart" class="h-5 w-5" />
                @if ($cart->items_count > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-brand-500 text-[9px] font-bold text-white">{{ $cart->items_count }}</span>
                @endif
            </button>
            <a href="{{ auth()->check() ? route('panel.dashboard') : '#' }}" @if (!auth()->check()) @click.prevent="authOpen = true" @endif class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-ink-50 text-ink-600">
                <x-icon name="user" class="h-5 w-5" />
            </a>
            <form action="#" class="flex flex-1 items-center overflow-hidden rounded-full border border-ink-100 bg-ink-50">
                <input type="text" placeholder="جستجوی محصول" class="w-full flex-1 bg-transparent px-4 py-2.5 text-sm text-ink-800 outline-none placeholder:text-ink-400">
                <button type="submit" class="flex h-9 w-10 shrink-0 items-center justify-center rounded-full bg-brand-500 text-white me-0.5">
                    <x-icon name="search" class="h-4 w-4" />
                </button>
            </form>
        </div>
    </div>
</header>
