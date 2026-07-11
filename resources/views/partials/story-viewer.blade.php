{{-- Instagram-style full-screen story viewer, driven by the global Alpine $store.storyViewer --}}
<div
    x-show="$store.storyViewer.isOpen"
    x-cloak
    x-transition:enter="transition-opacity duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="$store.storyViewer.close()"
    class="fixed inset-0 z-[80] flex items-center justify-center bg-black"
>
    {{-- Desktop prev button --}}
    <button type="button"
            @click="$store.storyViewer.prev()"
            aria-label="قبلی"
            class="z-20 mr-4 hidden h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/35 lg:flex">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    {{-- Story card --}}
    <div class="relative flex h-full w-full max-w-md flex-col" dir="ltr">

        {{-- Progress bars --}}
        <div class="absolute inset-x-0 top-0 z-10 flex gap-1 p-2.5">
            <template x-for="(slide, i) in ($store.storyViewer.currentStory ? $store.storyViewer.currentStory.slides : [])" :key="i">
                <div class="h-0.5 flex-1 overflow-hidden rounded-full bg-white/30">
                    <div
                        class="h-full rounded-full bg-white"
                        :style="{
                            width: i < $store.storyViewer.slideIndex
                                ? '100%'
                                : i === $store.storyViewer.slideIndex
                                    ? $store.storyViewer.progress + '%'
                                    : '0%'
                        }"
                    ></div>
                </div>
            </template>
        </div>

        {{-- Header --}}
        <div class="absolute inset-x-0 top-5 z-10 flex items-center justify-between px-3">
            <div class="flex items-center gap-2">
                <img :src="$store.storyViewer.currentStory ? $store.storyViewer.currentStory.image : ''"
                     alt="" class="h-8 w-8 rounded-full border-2 border-white/70 object-cover">
                <span class="text-sm font-bold text-white"
                      x-text="$store.storyViewer.currentStory ? $store.storyViewer.currentStory.title : ''"></span>
            </div>
            <button type="button" @click="$store.storyViewer.close()"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-white shadow backdrop-blur-sm transition hover:bg-white/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- Slide image --}}
        <div class="flex h-full w-full items-center justify-center">
            <img :src="$store.storyViewer.currentSlide ?? ''" alt="" class="max-h-full max-w-full object-contain">
        </div>

        {{-- Mobile tap zones (invisible) --}}
        <button type="button" class="absolute inset-y-0 left-0 w-1/2 lg:hidden" @click="$store.storyViewer.prev()" aria-label="قبلی"></button>
        <button type="button" class="absolute inset-y-0 right-0 w-1/2 lg:hidden" @click="$store.storyViewer.next()" aria-label="بعدی"></button>

        {{-- Link CTA --}}
        <template x-if="$store.storyViewer.currentStory && $store.storyViewer.currentStory.link">
            <a :href="$store.storyViewer.currentStory.link" target="_blank" rel="noopener noreferrer"
               class="absolute inset-x-4 bottom-5 z-10 flex items-center justify-center gap-1.5 rounded-xl bg-white/95 py-2.5 text-sm font-semibold text-ink-900">
                مشاهده
                <iconify-icon icon="tabler:arrow-up-left" class="text-base"></iconify-icon>
            </a>
        </template>

    </div>

    {{-- Desktop next button --}}
    <button type="button"
            @click="$store.storyViewer.next()"
            aria-label="بعدی"
            class="z-20 ml-4 hidden h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/35 lg:flex">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>

</div>
