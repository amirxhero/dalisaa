@extends('layouts.app')

@section('title', $product->title)

@section('content')
    <div
        x-data="{
            activeImage: 0,
            activeColor: 0,
            qty: 1,
            tab: 'description',
            gallery: @js($product->gallery_urls),
            variants: @js($product->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->color_name,
                'hex' => $v->color_hex,
                'price' => $v->price ?? $product->price,
                'regular_price' => $v->regular_price ?? $product->regular_price
            ])),
            maxQty: {{ max(1, (int) $product->stock) }},
            inc() { if (this.qty < this.maxQty) this.qty++ },
            dec() { if (this.qty > 1) this.qty-- },
            formatPrice(val) {
                return new Intl.NumberFormat('en-US').format(val);
            },
            getPrice() {
                return this.variants[this.activeColor]?.price ?? {{ $product->price }};
            },
            getRegularPrice() {
                return this.variants[this.activeColor]?.regular_price ?? {{ $product->regular_price ?? 'null' }};
            },
            getDiscount() {
                const reg = this.getRegularPrice();
                const pr = this.getPrice();
                if (!reg || reg <= pr) return 0;
                return Math.round(((reg - pr) / reg) * 100);
            }
        }"
        class="pb-24 lg:pb-0"
    >
        {{-- Breadcrumb --}}
        <div class="border-b border-ink-100 bg-white">
            <div class="mx-auto flex max-w-7xl items-center gap-1.5 px-4 py-3 text-xs text-ink-400 lg:px-6">
                <a href="{{ route('home') }}" class="transition-colors hover:text-brand-500">خانه</a>
                <x-icon name="chevron-left" class="h-3 w-3" />
                <span class="text-ink-600">{{ $product->category->name }}</span>
                <x-icon name="chevron-left" class="h-3 w-3" />
                <span class="max-w-[55vw] truncate text-ink-800 sm:max-w-xs">{{ $product->title }}</span>
            </div>
        </div>

        {{-- Main product section --}}
        <section class="bg-white py-6 sm:py-10">
            <div class="mx-auto max-w-7xl px-4 lg:px-6">
                <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">

                    {{-- Gallery --}}
                    <div>
                        <div class="relative mb-4 aspect-square overflow-hidden rounded-2xl border border-ink-100 bg-ink-50">
                            @if ($product->discount_percent > 0)
                                <span class="absolute right-3 top-3 z-10 rounded-full bg-brand-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm">
                                    {{ $product->discount_percent }}% تخفیف
                                </span>
                            @endif

                            @auth
                                <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute left-3 top-3 z-10">
                                    @csrf
                                    <button type="submit" aria-label="افزودن به علاقه‌مندی" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 {{ auth()->user()->hasWishlisted($product->id) ? 'text-brand-500' : 'text-ink-600' }} shadow-sm backdrop-blur transition-colors hover:text-brand-500">
                                        <x-icon name="heart" class="h-4.5 w-4.5 {{ auth()->user()->hasWishlisted($product->id) ? 'fill-brand-500' : '' }}" />
                                    </button>
                                </form>
                            @else
                                <button type="button" @click="authOpen = true" aria-label="افزودن به علاقه‌مندی" class="absolute left-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-ink-600 shadow-sm backdrop-blur transition-colors hover:text-brand-500">
                                    <x-icon name="heart" class="h-4.5 w-4.5" />
                                </button>
                            @endauth

                            <template x-for="(img, i) in gallery" :key="i">
                                <img :src="img" x-show="activeImage === i" x-cloak alt="{{ $product->title }}" class="h-full w-full object-contain p-8">
                            </template>
                        </div>

                        @if (count($product->gallery_urls) > 1)
                            <div class="flex gap-3 overflow-x-auto no-scrollbar">
                                <template x-for="(img, i) in gallery" :key="i">
                                    <button
                                        type="button"
                                        @click="activeImage = i"
                                        :class="activeImage === i ? 'border-brand-500' : 'border-ink-100 hover:border-brand-300'"
                                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border-2 bg-ink-50 p-1.5 transition-colors sm:h-20 sm:w-20"
                                    >
                                        <img :src="img" class="h-full w-full object-contain">
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="rounded-full bg-ink-50 px-2.5 py-1 text-[11px] font-medium text-ink-600">{{ $product->brand }}</span>
                            <span class="rounded-full bg-ink-50 px-2.5 py-1 text-[11px] font-medium text-ink-600">{{ $product->category->name }}</span>
                        </div>

                        <h1 class="mb-3 text-xl font-extrabold leading-snug text-ink-900 sm:text-2xl">
                            {{ $product->title }}
                        </h1>

                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <x-rating-stars :rating="$product->rating_cache" :count="$product->reviews_count_cache" />
                            <span class="text-ink-100">|</span>
                            <span class="text-xs text-ink-400">کد محصول: <span dir="ltr" class="text-ink-600">{{ $product->sku }}</span></span>
                        </div>

                        {{-- Price block --}}
                        <div class="mb-5 rounded-2xl bg-ink-50 p-4">
                            <span class="block text-sm text-ink-400 line-through" x-show="getDiscount() > 0" x-text="formatPrice(getRegularPrice()) + ' تومان'"></span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-extrabold text-brand-500 sm:text-3xl" x-text="formatPrice(getPrice())"></span>
                                <span class="text-sm text-ink-600">تومان</span>
                                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-bold text-brand-600" x-show="getDiscount() > 0" x-text="getDiscount() + '٪ تخفیف'"></span>
                            </div>
                        </div>

                        {{-- Stock status --}}
                        <div class="mb-5 flex items-center gap-2 text-sm">
                            @if ($product->in_stock)
                                <span class="h-2 w-2 shrink-0 rounded-full bg-accent-500"></span>
                                <span class="text-ink-700">موجود در انبار</span>
                                <span class="text-ink-400">({{ $product->stock }} عدد باقی مانده)</span>
                            @else
                                <span class="h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                                <span class="text-ink-700">ناموجود</span>
                            @endif
                        </div>

                        {{-- Variants --}}
                        @if ($product->variants->isNotEmpty())
                            <div class="mb-5">
                                <p class="mb-2.5 text-sm font-medium text-ink-800">
                                    انتخاب گزینه: <span class="font-bold text-brand-500" x-text="variants[activeColor]?.name ?? ''"></span>
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach ($product->variants as $i => $variant)
                                        @if ($variant->color_hex)
                                            <button
                                                type="button"
                                                @click="activeColor = {{ $i }}"
                                                :class="activeColor === {{ $i }} ? 'ring-2 ring-brand-500 ring-offset-2 scale-105' : 'ring-1 ring-ink-100 hover:scale-105'"
                                                class="h-8.5 w-8.5 shrink-0 rounded-full transition-all duration-150"
                                                style="background-color: {{ $variant->color_hex }}"
                                                aria-label="{{ $variant->color_name }}"
                                                title="{{ $variant->color_name }}"
                                            ></button>
                                        @else
                                            <button
                                                type="button"
                                                @click="activeColor = {{ $i }}"
                                                :class="activeColor === {{ $i }} ? 'border-brand-500 bg-brand-50/40 text-brand-600 ring-1 ring-brand-500' : 'border-ink-150 bg-white text-ink-700 hover:border-brand-300'"
                                                class="shrink-0 rounded-xl border px-4 py-2 text-xs font-bold transition-all duration-150"
                                                aria-label="{{ $variant->color_name }}"
                                            >
                                                {{ $variant->color_name }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Quantity + actions --}}
                        <form action="{{ route('cart.store') }}" method="POST" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-stretch">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            @if ($product->variants->isNotEmpty())
                                <input type="hidden" name="product_variant_id" :value="variants[activeColor]?.id">
                            @endif
                            <input type="hidden" name="quantity" x-bind:value="qty">

                            <div class="flex h-13 w-full shrink-0 items-center justify-between rounded-xl border border-ink-100 px-3 sm:w-32">
                                <button type="button" @click="dec()" class="flex h-8 w-8 items-center justify-center rounded-lg text-ink-600 transition-colors hover:bg-ink-50">
                                    <x-icon name="minus" class="h-4 w-4" />
                                </button>
                                <span class="w-6 text-center text-sm font-bold text-ink-900" x-text="qty"></span>
                                <button type="button" @click="inc()" class="flex h-8 w-8 items-center justify-center rounded-lg text-ink-600 transition-colors hover:bg-ink-50">
                                    <x-icon name="plus" class="h-4 w-4" />
                                </button>
                            </div>

                            <button
                                type="submit"
                                {{ $product->in_stock ? '' : 'disabled' }}
                                class="flex h-13 w-full items-center justify-center gap-2 rounded-xl bg-ink-900 px-6 text-sm font-bold text-white shadow-sm transition-colors hover:bg-brand-500 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto sm:flex-1"
                            >
                                <x-icon name="cart" class="h-5 w-5" />
                                افزودن به سبد خرید
                            </button>

                            @guest
                                <button type="button" @click="authOpen = true" aria-label="افزودن به علاقه‌مندی" class="flex h-13 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-ink-100 text-ink-600 transition-colors hover:border-brand-500 hover:text-brand-500 sm:w-13">
                                    <x-icon name="heart" class="h-5 w-5" />
                                    <span class="text-sm font-medium sm:hidden">افزودن به علاقه‌مندی</span>
                                </button>
                            @endguest
                        </form>

                        {{-- Highlights --}}
                        @if (!empty($product->highlights))
                            <ul class="mb-6 space-y-2.5 border-t border-ink-100 pt-5">
                                @foreach ($product->highlights as $point)
                                    @php
                                        $highlightTitle = is_array($point) ? trim($point['title'] ?? '') : '';
                                        $highlightValue = is_array($point) ? trim($point['value'] ?? '') : trim((string) $point);
                                    @endphp
                                    <li class="flex items-start gap-2.5 text-sm text-ink-600">
                                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-accent-50 text-accent-600">
                                            <x-icon name="check" class="h-3 w-3" />
                                        </span>
                                        @if ($highlightTitle !== '')
                                            <span><span class="font-medium text-ink-800">{{ $highlightTitle }}:</span> {{ $highlightValue }}</span>
                                        @else
                                            {{ $highlightValue }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Trust strip --}}
                        <div class="grid grid-cols-3 gap-2 rounded-2xl bg-ink-50 p-4 text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                <x-icon name="truck" class="h-5 w-5 text-ink-600" />
                                <span class="text-[11px] text-ink-600">ارسال اکسپرس</span>
                            </div>
                            <div class="flex flex-col items-center gap-1.5 border-x border-ink-100">
                                <x-icon name="return" class="h-5 w-5 text-ink-600" />
                                <span class="text-[11px] text-ink-600">۷ روز ضمانت بازگشت</span>
                            </div>
                            <div class="flex flex-col items-center gap-1.5">
                                <x-icon name="shield" class="h-5 w-5 text-ink-600" />
                                <span class="text-[11px] text-ink-600">گارانتی اصالت کالا</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabs: description / specs / reviews --}}
                <div class="mt-10 sm:mt-14">
                    <div class="flex gap-2 overflow-x-auto border-b border-ink-100 no-scrollbar">
                        <button
                            type="button"
                            @click="tab = 'description'"
                            :class="tab === 'description' ? 'border-brand-500 text-brand-500' : 'border-transparent text-ink-400 hover:text-ink-800'"
                            class="shrink-0 border-b-2 px-4 py-3 text-sm font-bold transition-colors"
                        >
                            توضیحات محصول
                        </button>
                        <button
                            type="button"
                            @click="tab = 'specs'"
                            :class="tab === 'specs' ? 'border-brand-500 text-brand-500' : 'border-transparent text-ink-400 hover:text-ink-800'"
                            class="shrink-0 border-b-2 px-4 py-3 text-sm font-bold transition-colors"
                        >
                            مشخصات فنی
                        </button>
                        <button
                            type="button"
                            @click="tab = 'reviews'"
                            :class="tab === 'reviews' ? 'border-brand-500 text-brand-500' : 'border-transparent text-ink-400 hover:text-ink-800'"
                            class="shrink-0 border-b-2 px-4 py-3 text-sm font-bold transition-colors"
                        >
                            نظرات کاربران ({{ $product->reviews_count_cache }})
                        </button>
                    </div>

                    <div class="py-6">
                        <div x-show="tab === 'description'" x-cloak>
                            <p class="max-w-3xl text-sm leading-8 text-ink-600">{{ $product->description }}</p>
                        </div>

                        <div x-show="tab === 'specs'" x-cloak>
                            <dl class="grid max-w-3xl divide-y divide-ink-100 overflow-hidden rounded-2xl border border-ink-100">
                                @foreach ($product->specs ?? [] as $label => $value)
                                    <div class="grid grid-cols-2 gap-3 px-4 py-3 odd:bg-ink-50/60">
                                        <dt class="text-sm text-ink-400">{{ $label }}</dt>
                                        <dd class="text-sm font-medium text-ink-800">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>

                        <div x-show="tab === 'reviews'" x-cloak>
                            @include('product.partials.reviews')
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Related products --}}
        @if ($related->isNotEmpty())
            <section class="bg-white py-8 sm:py-10">
                <div class="mx-auto max-w-7xl px-4 lg:px-6">
                    <x-section-heading title="محصولات مشابه" highlight="پیشنهاد ما" subtitle="بر اساس این دسته‌بندی، این محصولات را هم ببینید" />

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                        @foreach ($related as $item)
                            <x-product-card :product="$item" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Sticky price + buy bar, mobile only --}}
        <div
            class="fixed inset-x-0 bottom-0 z-40 flex items-center gap-3 border-t border-ink-100 bg-white px-4 py-3 shadow-[0_-6px_20px_rgba(15,0,43,0.08)] lg:hidden"
            style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))"
        >
            <div class="flex flex-col leading-tight">
                <span class="text-[11px] text-ink-400 line-through" x-show="getDiscount() > 0" x-text="formatPrice(getRegularPrice())"></span>
                <span class="whitespace-nowrap text-base font-extrabold text-brand-500">
                    <span x-text="formatPrice(getPrice())"></span>
                    <span class="text-[11px] font-normal text-ink-500">تومان</span>
                </span>
            </div>

            <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if ($product->variants->isNotEmpty())
                    <input type="hidden" name="product_variant_id" :value="variants[activeColor]?.id">
                @endif
                <input type="hidden" name="quantity" x-bind:value="qty">
                <button
                    type="submit"
                    {{ $product->in_stock ? '' : 'disabled' }}
                    class="flex h-13 w-full items-center justify-center gap-2 rounded-xl bg-ink-900 text-sm font-bold text-white shadow-sm transition-colors hover:bg-brand-500 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <x-icon name="cart" class="h-5 w-5" />
                    افزودن به سبد خرید
                </button>
            </form>
        </div>
    </div>
@endsection
