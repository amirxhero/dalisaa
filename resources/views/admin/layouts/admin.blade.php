<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت') — سالیکا</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js" defer></script>
    @stack('styles')
</head>
<body class="h-full bg-[#F7F8FA] text-gray-800 antialiased" x-data="{ sidebarOpen: false }">

@php
    $navGroups = [
        [
            'label' => 'مدیریت',
            'items' => [
                ['route' => 'admin.dashboard', 'label' => 'داشبورد', 'icon' => 'tabler:layout-dashboard'],
            ],
        ],
        [
            'label' => 'فروشگاه',
            'items' => [
                ['route' => 'admin.categories.index',        'label' => 'دسته‌بندی‌ها',        'icon' => 'tabler:folder'],
                ['route' => 'admin.products.index',          'label' => 'محصولات',              'icon' => 'tabler:box'],
                ['route' => 'admin.special-products.index',  'label' => 'محصولات شگفت‌انگیز',  'icon' => 'tabler:bolt'],
                ['route' => 'admin.stories.index',           'label' => 'استوری‌ها',            'icon' => 'tabler:photo'],
                ['route' => 'admin.banners.index',           'label' => 'بنرها',                'icon' => 'tabler:layout-board'],
            ],
        ],
        [
            'label' => 'فروش',
            'items' => [
                ['route' => 'admin.orders.index',    'label' => 'سفارشات',       'icon' => 'tabler:shopping-cart'],
                ['route' => 'admin.discounts.index', 'label' => 'کدهای تخفیف', 'icon' => 'tabler:ticket'],
                ['route' => 'admin.users.index',     'label' => 'کاربران',        'icon' => 'tabler:users'],
            ],
        ],
        [
            'label' => 'محتوا',
            'items' => [
                ['route' => 'admin.posts.index', 'label' => 'مقالات', 'icon' => 'tabler:article'],
                ['route' => 'admin.contact-messages.index', 'label' => 'پیام‌های تماس', 'icon' => 'tabler:mail', 'badge' => \App\Models\ContactMessage::new()->count()],
            ],
        ],
        [
            'label' => 'پیکربندی',
            'items' => [
                ['route' => 'admin.settings.index', 'label' => 'تنظیمات', 'icon' => 'tabler:settings'],
                ['route' => 'admin.account.edit',   'label' => 'حساب کاربری', 'icon' => 'tabler:user-cog'],
            ],
        ],
    ];
@endphp

{{-- Mobile overlay --}}
<div
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/30 lg:hidden"
></div>

{{-- ── Sidebar ────────────────────────────────────────────────────────────── --}}
<aside
    class="fixed inset-y-0 right-0 z-40 flex w-64 flex-col border-l border-gray-100 bg-white transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
>
    {{-- Logo --}}
    <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-gray-100 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">س</div>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-gray-900">سالیکا</p>
            <p class="text-[11px] text-gray-400">پنل مدیریت</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-5">
        @foreach($navGroups as $group)
        <div>
            <p class="mb-1.5 px-3 text-[10px] font-semibold tracking-widest text-gray-400">{{ $group['label'] }}</p>
            <div class="space-y-0.5">
                @foreach($group['items'] as $item)
                @php $active = request()->routeIs($item['route'].'*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm transition-colors
                          {{ $active ? 'bg-indigo-50 font-semibold text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                    <iconify-icon icon="{{ $item['icon'] }}" class="shrink-0 text-lg {{ $active ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"></iconify-icon>
                    <span class="truncate">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span class="mr-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[11px] font-bold text-white">{{ $item['badge'] }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </nav>

    {{-- User --}}
    <div class="shrink-0 border-t border-gray-100 p-3">
        <div class="flex items-center gap-2.5 rounded-2xl bg-gray-50 px-3 py-3">
            <a href="{{ route('admin.account.edit') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-white bg-indigo-100 text-sm font-bold text-indigo-600 shadow-sm transition-colors hover:bg-indigo-200">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </a>
            <a href="{{ route('admin.account.edit') }}" class="min-w-0 flex-1 transition-colors hover:text-indigo-600">
                <p class="truncate text-xs font-bold leading-tight text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-[10px] leading-tight text-gray-400">مدیر سیستم</p>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="خروج"
                        class="flex h-7 w-7 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-gray-200 hover:text-rose-500">
                    <iconify-icon icon="tabler:logout" class="text-[15px]"></iconify-icon>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── Main ─────────────────────────────────────────────────────────────── --}}
<div class="flex min-h-screen flex-col lg:mr-64">

    {{-- Topbar --}}
    <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-3 border-b border-gray-100 bg-white px-4 lg:px-6">
        <h1 class="flex-1 truncate text-sm font-bold text-gray-900">@yield('page-title', 'پنل مدیریت')</h1>

        <div class="hidden items-center gap-1 sm:flex">
            <button class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                <iconify-icon icon="tabler:bell" class="text-lg"></iconify-icon>
            </button>
        </div>

        <div class="h-7 w-px shrink-0 bg-gray-100"></div>

        <a href="{{ route('admin.account.edit') }}" class="hidden items-center gap-2 transition-colors hover:text-indigo-600 sm:flex">
            <div class="text-right leading-tight">
                <p class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-indigo-500">مدیر سیستم</p>
            </div>
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-600">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
        </a>

        <button @click="sidebarOpen = !sidebarOpen"
                class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 transition-colors hover:bg-gray-100 lg:hidden">
            <iconify-icon icon="tabler:menu-2" class="text-lg"></iconify-icon>
        </button>
    </header>

    {{-- Breadcrumb bar --}}
    <div class="flex shrink-0 items-center gap-1.5 border-b border-gray-100 bg-white px-4 py-2.5 text-xs lg:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 transition-colors hover:text-indigo-600">داشبورد</a>
        @isset($breadcrumbs)
            @foreach($breadcrumbs as $i => $crumb)
            <iconify-icon icon="tabler:chevron-left" class="text-[10px] text-gray-300"></iconify-icon>
            <span class="{{ $i === count($breadcrumbs) - 1 ? 'font-medium text-gray-700' : 'text-gray-500' }}">{{ $crumb }}</span>
            @endforeach
        @endisset
    </div>

    {{-- Content --}}
    <main class="flex-1 p-4 lg:p-6">
        @include('admin.partials.flash')
        @yield('content')
    </main>
</div>

</body>
</html>
