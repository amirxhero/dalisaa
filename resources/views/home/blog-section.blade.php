{{-- Latest blog posts --}}
@if($blogPosts->isNotEmpty())
<section class="bg-white py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <x-section-heading highlight="جدیدترین" title="مقالات آموزشی" link-label="بیشتر بخوانید" :link-href="route('blog.index')" />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @foreach ($blogPosts as $post)
                <a href="{{ route('blog.show', $post) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-ink-100 shadow-card transition-shadow hover:shadow-card-hover sm:flex-row">
                    <div class="relative aspect-[16/10] w-full overflow-hidden sm:aspect-auto sm:w-48 sm:shrink-0">
                        @if($post->cover_url)
                            <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col justify-center p-4 sm:p-5">
                        <span class="mb-2 text-[11px] text-ink-400">
                            {{ ($post->published_at ?? $post->created_at)->diffForHumans() }}
                        </span>
                        <h3 class="mb-2 line-clamp-2 text-sm font-bold text-ink-900 transition-colors group-hover:text-brand-500 sm:text-base">
                            {{ $post->title }}
                        </h3>
                        @if($post->excerpt)
                            <p class="line-clamp-2 text-xs leading-6 text-ink-400">{{ $post->excerpt }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
