{{-- Site footer --}}
<footer class="mt-10 bg-ink-900 pb-24 pt-12 text-ink-100 lg:pb-12">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-5 sm:grid-cols-2 lg:grid-cols-4 lg:px-6">

        <div>
            <img src="https://kaveh.moeinwp.com/1/wp-content/uploads/2022/10/demo1.svg" alt="قالب کاوه" class="mb-4 h-9 w-auto brightness-0 invert">
            <h3 class="mb-2 text-sm font-bold text-white">فروشگاه اینترنتی</h3>
            <p class="text-xs leading-6 text-white/50">
                فعالیت خود را از سال ۱۳۹۸ در زمینه فروش کیف پول‌های سخت‌افزاری شروع نموده و کالاهای خود را بدون واسطه و از طریق شرکت‌های اصلی سازنده تامین می‌کند و ضمانت اصالت و سلامت کالا را به تمام مشتریان ارائه می‌دهد.
            </p>
            <div class="mt-4 flex items-center gap-2">
                @if($siteSettings['social_instagram'])
                <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 text-white/70 transition-colors hover:bg-brand-500 hover:text-white">
                    <x-icon name="instagram" class="h-4.5 w-4.5" />
                </a>
                @endif
                @if($siteSettings['social_telegram'])
                <a href="{{ $siteSettings['social_telegram'] }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 text-white/70 transition-colors hover:bg-brand-500 hover:text-white">
                    <x-icon name="telegram" class="h-4.5 w-4.5" />
                </a>
                @endif
                @if($siteSettings['social_whatsapp'])
                <a href="{{ $siteSettings['social_whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 text-white/70 transition-colors hover:bg-brand-500 hover:text-white">
                    <x-icon name="whatsapp" class="h-4.5 w-4.5" />
                </a>
                @endif
            </div>
        </div>

        <div>
            <h3 class="relative mb-4 pb-3 text-sm font-bold text-white after:absolute after:bottom-0 after:right-0 after:h-0.5 after:w-8 after:bg-brand-500">دسترسی سریع</h3>
            <ul class="space-y-3 text-xs text-white/60">
                <li><a href="#" class="transition-colors hover:text-brand-400">دوره های آموزشی</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">اتاق خبر کاوه</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">فروش در کاوه</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">فرصت‌های شغلی</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">تماس با کاوه</a></li>
            </ul>
        </div>

        <div>
            <h3 class="relative mb-4 pb-3 text-sm font-bold text-white after:absolute after:bottom-0 after:right-0 after:h-0.5 after:w-8 after:bg-brand-500">خدمات پستی</h3>
            <ul class="space-y-3 text-xs text-white/60">
                <li><a href="#" class="transition-colors hover:text-brand-400">دریافت غرامت پستی</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">دریافت کدپستی</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">دریافت بسته از پستچی</a></li>
                <li><a href="#" class="transition-colors hover:text-brand-400">رهگیری مرسوله</a></li>
            </ul>
        </div>

        <div>
            <h3 class="relative mb-4 pb-3 text-sm font-bold text-white after:absolute after:bottom-0 after:right-0 after:h-0.5 after:w-8 after:bg-brand-500">تماس با ما</h3>
            <ul class="space-y-4 text-xs text-white/60">
                @if($siteSettings['contact_address'])
                <li class="flex items-start gap-2.5">
                    <x-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" />
                    {{ $siteSettings['contact_address'] }}
                </li>
                @endif
                @if($siteSettings['contact_email'])
                <li class="flex items-center gap-2.5">
                    <x-icon name="mail" class="h-4 w-4 shrink-0 text-brand-400" />
                    <a href="mailto:{{ $siteSettings['contact_email'] }}" dir="ltr" class="transition-colors hover:text-brand-400">{{ $siteSettings['contact_email'] }}</a>
                </li>
                @endif
                @if($siteSettings['contact_phone'])
                <li class="flex items-center gap-2.5">
                    <x-icon name="phone-call" class="h-4 w-4 shrink-0 text-brand-400" />
                    <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings['contact_phone']) }}" dir="ltr" class="transition-colors hover:text-brand-400">{{ $siteSettings['contact_phone'] }}</a>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="mx-auto mt-10 max-w-7xl border-t border-white/10 px-5 pt-6 text-center text-[11px] text-white/40 lg:px-6">
        کلیه حقوق این سایت متعلق به شرکت <span class="font-bold text-white/70">قالب کاوه</span> می‌باشد
    </div>
</footer>
