@php
    $myReview = auth()->check() ? $product->reviews->firstWhere('user_id', auth()->id()) : null;
@endphp

<div class="flex max-w-3xl flex-col items-start gap-4 rounded-2xl border border-ink-100 p-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="mb-1 flex items-baseline gap-2">
            <span class="text-3xl font-extrabold text-ink-900">{{ number_format($product->rating_cache, 1) }}</span>
            <span class="text-sm text-ink-400">از ۵</span>
        </div>
        <x-rating-stars :rating="$product->rating_cache" size="h-4 w-4" />
        <p class="mt-2 text-xs text-ink-400">بر اساس {{ number_format($product->reviews_count_cache) }} نظر ثبت شده</p>
    </div>

    @auth
        @unless ($myReview)
            <button type="button" @click="$refs.reviewForm.classList.toggle('hidden')" class="shrink-0 rounded-full bg-ink-900 px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-500">
                ثبت نظر شما
            </button>
        @endunless
    @else
        <button type="button" @click="authOpen = true" class="shrink-0 rounded-full bg-ink-900 px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-500">
            ثبت نظر شما
        </button>
    @endauth
</div>

@auth
    @unless ($myReview)
        <form x-ref="reviewForm" action="{{ route('reviews.store', $product) }}" method="POST" class="{{ $errors->has('rating') || $errors->has('comment') ? '' : 'hidden' }} mt-4 max-w-3xl space-y-3 rounded-2xl border border-ink-100 p-5" x-data="{ rating: {{ old('rating', 5) }} }">
            @csrf
            <div>
                <p class="mb-2 text-sm font-medium text-ink-800">امتیاز شما</p>
                <div class="flex items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" @click="rating = {{ $i }}">
                            <x-icon name="star" class="h-6 w-6" x-bind:class="{{ $i }} <= rating ? 'fill-amber-400 text-amber-400' : 'fill-none text-ink-100'" />
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" x-bind:value="rating">
            </div>
            <textarea name="comment" rows="3" placeholder="نظر خود را درباره این محصول بنویسید..." class="w-full rounded-xl border border-ink-100 bg-ink-50 p-3 text-sm outline-none placeholder:text-ink-400 focus:border-brand-300">{{ old('comment') }}</textarea>
            @error('comment')
                <p class="text-xs text-brand-500">{{ $message }}</p>
            @enderror
            <button type="submit" class="rounded-full bg-brand-500 px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-brand-600">ثبت نظر</button>
        </form>
    @endunless
@endauth

@if ($product->reviews->isNotEmpty())
    <div class="mt-6 max-w-3xl space-y-4">
        @foreach ($product->reviews as $review)
            <div class="rounded-2xl border border-ink-100 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ink-50 text-xs font-bold text-ink-600">{{ mb_substr($review->user->name, 0, 1) }}</span>
                        <span class="text-sm font-bold text-ink-800">{{ $review->user->name }}</span>
                    </div>
                    <span class="text-[11px] text-ink-400">{{ jdate($review->created_at)->format('%d %B %Y') }}</span>
                </div>
                <x-rating-stars :rating="$review->rating" size="h-3.5 w-3.5" />
                <p class="mt-2 text-sm leading-7 text-ink-600">{{ $review->comment }}</p>
            </div>
        @endforeach
    </div>
@else
    <p class="mt-6 text-sm text-ink-400">هنوز نظری برای این محصول ثبت نشده است. اولین نفری باشید که نظر می‌دهد!</p>
@endif
