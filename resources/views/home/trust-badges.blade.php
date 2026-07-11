{{-- Trust / service highlights --}}
<section class="border-t border-ink-100 bg-white py-8 sm:py-10">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-y-6 px-4 sm:grid-cols-3 lg:grid-cols-5 lg:gap-y-0 lg:px-6">
        @foreach ($trustBadges as $badge)
            <div class="flex flex-col items-center gap-2.5 text-center sm:flex-row sm:gap-3 sm:text-right lg:justify-center">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                    <x-icon :name="$badge['icon']" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-ink-700 sm:text-sm">{{ $badge['title'] }}</span>
            </div>
        @endforeach
    </div>
</section>
