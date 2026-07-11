{{-- Fixed bottom navigation, mobile only --}}
<nav 
    x-data="{ 
        isActive(tab) {
            if (tab === 'home') return !categorySheetOpen && !cartOpen && {{ request()->routeIs('home') ? 'true' : 'false' }};
            if (tab === 'category') return categorySheetOpen || {{ request()->routeIs('category.show') ? 'true' : 'false' }};
            if (tab === 'cart') return cartOpen;
            if (tab === 'wishlist') return !categorySheetOpen && !cartOpen && {{ request()->routeIs('panel.wishlist') ? 'true' : 'false' }};
            if (tab === 'account') return !categorySheetOpen && !cartOpen && {{ (request()->routeIs('panel.*') && !request()->routeIs('panel.wishlist')) ? 'true' : 'false' }};
            return false;
        }
    }"
    class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 items-center justify-items-center border-t border-ink-100/60 bg-white/90 pb-[calc(env(safe-area-inset-bottom)+0.5rem)] pt-3 shadow-[0_-8px_24px_rgba(15,0,43,0.06)] backdrop-blur-lg lg:hidden"
>
    <!-- Home -->
    <a href="{{ route('home') }}" 
       class="group flex flex-col items-center gap-1 transition-all duration-200 active:scale-95"
       :class="isActive('home') ? 'text-brand-500' : 'text-ink-400'"
    >
        <span 
            class="flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
            :class="isActive('home') ? 'bg-brand-50 scale-105 shadow-sm shadow-brand-500/5' : 'group-hover:bg-ink-50'"
        >
            <x-icon name="home" class="h-5 w-5 transition-transform group-hover:scale-110" />
        </span>
        <span class="text-[10px] font-semibold transition-colors">خانه</span>
    </a>

    <!-- Category -->
    <button type="button" 
            @click="categorySheetOpen = true" 
            class="group flex flex-col items-center gap-1 transition-all duration-200 active:scale-95"
            :class="isActive('category') ? 'text-brand-500' : 'text-ink-400'"
    >
        <span 
            class="flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
            :class="isActive('category') ? 'bg-brand-50 scale-105 shadow-sm shadow-brand-500/5' : 'group-hover:bg-ink-50'"
        >
            <x-icon name="grid" class="h-5 w-5 transition-transform group-hover:scale-110" />
        </span>
        <span class="text-[10px] font-semibold transition-colors">دسته‌بندی</span>
    </button>

    <!-- Cart (Floating Middle Button) -->
    <button type="button" 
            @click="cartOpen = true" 
            class="group relative -mt-7 flex flex-col items-center transition-all duration-300"
    >
        <span 
            class="relative flex h-13 w-13 items-center justify-center rounded-full bg-brand-500 text-white shadow-[0_8px_20px_rgba(238,39,58,0.35)] ring-4 ring-white transition-all duration-300 group-hover:scale-105 group-active:scale-95"
            :class="isActive('cart') ? 'bg-brand-600 ring-brand-50 shadow-[0_8px_24px_rgba(238,39,58,0.5)]' : ''"
        >
            <x-icon name="cart" class="h-5.5 w-5.5 transition-transform group-hover:rotate-6" />
            @if ($cart->items_count > 0)
                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-ink-900 text-[10px] font-bold text-white ring-2 ring-white">{{ $cart->items_count }}</span>
            @endif
        </span>
    </button>

    <!-- Wishlist -->
    @auth
        <a href="{{ route('panel.wishlist') }}" 
           class="group flex flex-col items-center gap-1 transition-all duration-200 active:scale-95"
           :class="isActive('wishlist') ? 'text-brand-500' : 'text-ink-400'"
        >
            <span 
                class="flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
                :class="isActive('wishlist') ? 'bg-brand-50 scale-105 shadow-sm shadow-brand-500/5' : 'group-hover:bg-ink-50'"
            >
                <x-icon name="heart" class="h-5 w-5 transition-transform group-hover:scale-110" />
            </span>
            <span class="text-[10px] font-semibold transition-colors">علاقه‌مندی</span>
        </a>
    @else
        <button type="button" 
                @click="authOpen = true" 
                class="group flex flex-col items-center gap-1 transition-all duration-200 active:scale-95"
                :class="isActive('wishlist') ? 'text-brand-500' : 'text-ink-400'"
        >
            <span 
                class="flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
                :class="isActive('wishlist') ? 'bg-brand-50 scale-105 shadow-sm shadow-brand-500/5' : 'group-hover:bg-ink-50'"
            >
                <x-icon name="heart" class="h-5 w-5 transition-transform group-hover:scale-110" />
            </span>
            <span class="text-[10px] font-semibold transition-colors">علاقه‌مندی</span>
        </button>
    @endauth

    <!-- Account -->
    @auth
        <a href="{{ route('panel.dashboard') }}" 
           class="group flex flex-col items-center gap-1 transition-all duration-200 active:scale-95"
           :class="isActive('account') ? 'text-brand-500' : 'text-ink-400'"
        >
            <span 
                class="flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
                :class="isActive('account') ? 'bg-brand-50 scale-105 shadow-sm shadow-brand-500/5' : 'group-hover:bg-ink-50'"
            >
                <x-icon name="user" class="h-5 w-5 transition-transform group-hover:scale-110" />
            </span>
            <span class="text-[10px] font-semibold transition-colors">حساب کاربری</span>
        </a>
    @else
        <button type="button" 
                @click="authOpen = true" 
                class="group flex flex-col items-center gap-1 transition-all duration-200 active:scale-95"
                :class="isActive('account') ? 'text-brand-500' : 'text-ink-400'"
        >
            <span 
                class="flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
                :class="isActive('account') ? 'bg-brand-50 scale-105 shadow-sm shadow-brand-500/5' : 'group-hover:bg-ink-50'"
            >
                <x-icon name="user" class="h-5 w-5 transition-transform group-hover:scale-110" />
            </span>
            <span class="text-[10px] font-semibold transition-colors">حساب کاربری</span>
        </button>
    @endauth
</nav>
