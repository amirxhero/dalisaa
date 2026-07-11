@extends('admin.layouts.admin')

@section('title', 'داشبورد')
@section('page-title', 'داشبورد')

@section('content')

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-900">خوش آمدید، {{ auth()->user()->name }} 👋</h2>
        <p class="mt-0.5 text-sm text-gray-500">نمای کلی وضعیت فروشگاه شما در یک نگاه</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">
            <iconify-icon icon="tabler:plus" class="text-base"></iconify-icon>
            محصول جدید
        </a>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-admin.stat-card icon="tabler:coin" tone="indigo" label="درآمد کل (پرداخت‌شده)" value="{{ number_format($stats['total_revenue']) }} ت" />
    <x-admin.stat-card icon="tabler:clock-hour-4" tone="amber" label="سفارشات در جریان" value="{{ number_format($stats['pending_orders']) }}" />
    <x-admin.stat-card icon="tabler:users" tone="sky" label="کاربران" value="{{ number_format($stats['users_count']) }}" />
    <x-admin.stat-card icon="tabler:box" tone="emerald" label="محصولات فعال" value="{{ number_format($stats['active_products']) }} / {{ number_format($stats['products_count']) }}" />
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Recent Orders --}}
    <x-admin.section title="آخرین سفارشات" subtitle="{{ $stats['orders_count'] }} سفارش ثبت‌شده" class="lg:col-span-2">
        <x-slot:actions>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                مشاهده همه
                <iconify-icon icon="tabler:arrow-left" class="text-sm"></iconify-icon>
            </a>
        </x-slot:actions>

        @if($recentOrders->isEmpty())
            <x-admin.empty-state icon="tabler:shopping-cart-off" title="هنوز سفارشی ثبت نشده است" />
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/60">
                        <th class="admin-th">شماره سفارش</th>
                        <th class="admin-th">مشتری</th>
                        <th class="admin-th">مبلغ</th>
                        <th class="admin-th">وضعیت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentOrders as $order)
                    @php
                        $toneMap = ['pending'=>'gray','processing'=>'amber','paid'=>'sky','shipped'=>'indigo','delivered'=>'emerald','cancelled'=>'rose','failed'=>'rose'];
                    @endphp
                    <tr class="transition-colors hover:bg-gray-50/60">
                        <td class="admin-td">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs font-semibold text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                        </td>
                        <td class="admin-td text-gray-600">{{ $order->user?->name ?? '—' }}</td>
                        <td class="admin-td font-semibold text-gray-900">{{ number_format($order->total) }} ت</td>
                        <td class="admin-td">
                            <x-admin.badge :tone="$toneMap[$order->status] ?? 'gray'">{{ $order->status_label }}</x-admin.badge>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </x-admin.section>

    {{-- Right column --}}
    <div class="space-y-6">

        {{-- Currency Rates --}}
        <x-admin.section title="نرخ ارز" subtitle="پایه محاسبه قیمت تومانی">
            <x-slot:actions>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                    ویرایش
                    <iconify-icon icon="tabler:pencil" class="text-xs"></iconify-icon>
                </a>
            </x-slot:actions>
            <div class="divide-y divide-gray-50 px-5">
                @foreach(['USD' => ['نام' => 'دلار آمریکا', 'sym' => '$', 'icon' => 'twemoji:flag-united-states'], 'EUR' => ['نام' => 'یورو', 'sym' => '€', 'icon' => 'twemoji:flag-european-union'], 'USDT' => ['نام' => 'تتر', 'sym' => '₮', 'icon' => 'cryptocurrency-color:usdt'], 'GBP' => ['نام' => 'پوند انگلیس', 'sym' => '£', 'icon' => 'twemoji:flag-united-kingdom']] as $cur => $info)
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-50 text-sm font-bold text-gray-500">{{ $info['sym'] }}</span>
                        <span class="text-sm text-gray-600">{{ $info['نام'] }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($rates[$cur] ?? 0) }} <span class="font-normal text-gray-400">ت</span></span>
                </div>
                @endforeach
            </div>
        </x-admin.section>

        {{-- Low Stock --}}
        <x-admin.section title="هشدار موجودی کم" subtitle="نیازمند تأمین مجدد">
            @if($lowStockProducts->isEmpty())
                <div class="flex items-center gap-2.5 px-5 py-6 text-sm text-gray-400">
                    <iconify-icon icon="tabler:circle-check" class="text-lg text-emerald-400"></iconify-icon>
                    همه محصولات موجودی کافی دارند.
                </div>
            @else
            <div class="divide-y divide-gray-50 px-5">
                @foreach($lowStockProducts as $p)
                <div class="flex items-center justify-between gap-3 py-3">
                    <span class="truncate text-sm text-gray-700">{{ $p->title }}</span>
                    <x-admin.badge :tone="$p->stock === 0 ? 'rose' : 'amber'">{{ $p->stock }} عدد</x-admin.badge>
                </div>
                @endforeach
            </div>
            @endif
        </x-admin.section>
    </div>
</div>

{{-- Order status breakdown --}}
<div class="mt-6">
    <x-admin.section title="توزیع وضعیت سفارشات" subtitle="سهم هر وضعیت از کل سفارشات">
        @php
            $totalOrders = max(array_sum($orderStatusCounts), 1);
            $statusMeta = [
                'pending'    => ['label' => 'در انتظار پرداخت', 'color' => 'bg-gray-300'],
                'processing' => ['label' => 'در حال پردازش',   'color' => 'bg-amber-400'],
                'paid'       => ['label' => 'پرداخت‌شده',       'color' => 'bg-sky-400'],
                'shipped'    => ['label' => 'ارسال‌شده',        'color' => 'bg-indigo-400'],
                'delivered'  => ['label' => 'تحویل داده‌شده',  'color' => 'bg-emerald-400'],
                'cancelled'  => ['label' => 'لغو‌شده',          'color' => 'bg-rose-300'],
                'failed'     => ['label' => 'ناموفق',            'color' => 'bg-rose-400'],
            ];
        @endphp
        <div class="p-5">
            <div class="flex h-3 w-full overflow-hidden rounded-full bg-gray-50">
                @foreach($statusMeta as $key => $meta)
                    @if(($orderStatusCounts[$key] ?? 0) > 0)
                    <div class="{{ $meta['color'] }}" style="width: {{ ($orderStatusCounts[$key] / $totalOrders) * 100 }}%" title="{{ $meta['label'] }}: {{ $orderStatusCounts[$key] }}"></div>
                    @endif
                @endforeach
            </div>
            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2">
                @foreach($statusMeta as $key => $meta)
                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                    <span class="h-2 w-2 rounded-full {{ $meta['color'] }}"></span>
                    {{ $meta['label'] }}
                    <span class="font-semibold text-gray-800">({{ $orderStatusCounts[$key] ?? 0 }})</span>
                </div>
                @endforeach
            </div>
        </div>
    </x-admin.section>
</div>

@endsection
