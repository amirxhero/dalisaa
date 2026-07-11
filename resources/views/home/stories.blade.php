{{-- Instagram-style story reel --}}
@if(count($stories))
<section
    class="border-b border-ink-100 bg-white py-4"
    x-data="carousel({ slidesPerView: 'auto', spaceBetween: 14, freeMode: true })"
    x-init="init(); $store.storyViewer.setStories(@js($stories))"
>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <div class="swiper no-scrollbar" x-ref="track">
            <div class="swiper-wrapper">
                @foreach ($stories as $i => $story)
                    <div class="swiper-slide !w-20 text-center">
                        <button type="button" @click="$store.storyViewer.open({{ $i }})" class="group flex w-full flex-col items-center gap-1.5">
                            <span
                                class="relative flex h-16 w-16 items-center justify-center rounded-full p-[2.5px] transition-colors"
                                :class="$store.storyViewer.isSeen({{ $story['id'] }}) ? 'bg-gray-200' : 'bg-gradient-to-tr from-brand-500 via-brand-400 to-accent-400'"
                            >
                                <span class="flex h-full w-full items-center justify-center rounded-full bg-white p-[2px]">
                                    <img src="{{ $story['image'] }}" alt="{{ $story['title'] }}" class="h-full w-full rounded-full object-cover">
                                </span>
                                @if ($story['badge'])
                                    <span @class([
                                        'absolute -bottom-1 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full px-1.5 py-0.5 text-[8px] font-bold text-white shadow',
                                        'bg-accent-400' => $story['badge'] !== 'ویدیو',
                                        'bg-ink-900' => $story['badge'] === 'ویدیو',
                                    ])>{{ $story['badge'] }}</span>
                                @endif
                            </span>
                            <span class="text-[11px] font-medium text-ink-600 group-hover:text-brand-500">{{ $story['title'] }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
