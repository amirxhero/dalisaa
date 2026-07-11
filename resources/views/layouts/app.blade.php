<!DOCTYPE html>
<html lang="fa" dir="rtl" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'دمو دیجیتال – قالب فروشگاهی کاوه')</title>
    <meta name="description" content="فروشگاه اینترنتی کاوه؛ خرید آنلاین گوشی موبایل، تبلت، ساعت هوشمند و هدفون با تضمین اصالت و ارسال سریع.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body
    class="bg-[#f7f7f9] text-ink-800 antialiased"
    x-data="{
        mobileMenuOpen: false,
        cartOpen: false,
        searchOpen: false,
        authOpen: {{ ($errors->any() || request()->boolean('login')) ? 'true' : 'false' }},
        categorySheetOpen: false,
    }"
>

    @if (request()->routeIs('product.show'))
        @include('partials.product-header')
    @else
        @include('partials.announcement-bar')
        @include('partials.site-header')
    @endif

    <main>
        @yield('content')
    </main>

    @include('partials.site-footer')

    {{-- Hide bottom nav on product, checkout, cart, and payment pages --}}
    @unless (request()->routeIs('product.show') || request()->routeIs('checkout.*') || request()->routeIs('cart.*') || request()->routeIs('payment.*'))
        @include('partials.mobile-bottom-nav')
    @endunless

    @include('partials.cart-drawer')
    @include('partials.auth-modal')
    @include('partials.mobile-menu')
    @include('partials.category-sheet')
    @include('partials.toast')
    @include('partials.story-viewer')

</body>
</html>
