{{-- Login / register modal --}}
<div x-show="authOpen" x-cloak @keydown.escape.window="authOpen = false" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div
        x-show="authOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="authOpen = false"
        class="absolute inset-0 bg-ink-900/50 backdrop-blur-sm"
    ></div>

    <div
        x-data="{ mode: '{{ $errors->has('name') || $errors->has('mobile') ? 'register' : 'login' }}' }"
        x-show="authOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative max-h-[90vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl"
    >
        <button type="button" @click="authOpen = false" class="absolute left-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-ink-50 text-ink-600">
            <x-icon name="close" class="h-4 w-4" />
        </button>

        <x-logo class="mx-auto mb-4 h-9 w-auto" />

        <div class="mb-6 flex items-center justify-center gap-6 border-b border-ink-100">
            <button type="button" @click="mode = 'login'" :class="mode === 'login' ? 'border-brand-500 text-brand-500' : 'border-transparent text-ink-400'" class="border-b-2 pb-3 text-sm font-medium transition-colors">ورود</button>
            <button type="button" @click="mode = 'register'" :class="mode === 'register' ? 'border-brand-500 text-brand-500' : 'border-transparent text-ink-400'" class="border-b-2 pb-3 text-sm font-medium transition-colors">ثبت‌نام</button>
        </div>

        {{-- Login --}}
        <form x-show="mode === 'login'" x-cloak method="POST" action="{{ route('login.attempt') }}" class="space-y-3">
            @csrf
            <div class="relative">
                <x-icon name="user" class="pointer-events-none absolute right-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-400" />
                <input type="text" name="login" value="{{ old('login') }}" placeholder="شماره موبایل یا ایمیل" dir="ltr" class="w-full rounded-xl border border-ink-100 bg-ink-50 py-3 pe-3 ps-10 text-sm text-right outline-none placeholder:text-ink-400 focus:border-brand-300">
            </div>
            @error('login')
                <p class="text-xs text-brand-500">{{ $message }}</p>
            @enderror

            <input type="password" name="password" placeholder="رمز عبور" class="w-full rounded-xl border border-ink-100 bg-ink-50 px-3.5 py-3 text-sm outline-none placeholder:text-ink-400 focus:border-brand-300">
            @error('password')
                <p class="text-xs text-brand-500">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full rounded-xl bg-brand-500 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-600">ورود</button>
            <p class="text-center text-xs text-ink-400">
                حساب کاربری ندارید؟
                <button type="button" @click="mode = 'register'" class="font-bold text-brand-500">ثبت‌نام کنید</button>
            </p>
        </form>

        {{-- Register --}}
        <form x-show="mode === 'register'" x-cloak method="POST" action="{{ route('register') }}" class="space-y-3">
            @csrf
            <div class="relative">
                <x-icon name="user" class="pointer-events-none absolute right-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-400" />
                <input type="text" name="name" value="{{ old('name') }}" placeholder="نام و نام خانوادگی" class="w-full rounded-xl border border-ink-100 bg-ink-50 py-3 pe-3 ps-10 text-sm outline-none placeholder:text-ink-400 focus:border-brand-300">
            </div>
            @error('name')
                <p class="text-xs text-brand-500">{{ $message }}</p>
            @enderror

            <input type="text" name="mobile" value="{{ old('mobile') }}" placeholder="شماره موبایل (۰۹xxxxxxxxx)" dir="ltr" class="w-full rounded-xl border border-ink-100 bg-ink-50 px-3.5 py-3 text-sm text-right outline-none placeholder:text-ink-400 focus:border-brand-300">
            @error('mobile')
                <p class="text-xs text-brand-500">{{ $message }}</p>
            @enderror

            <input type="password" name="password" placeholder="رمز عبور" class="w-full rounded-xl border border-ink-100 bg-ink-50 px-3.5 py-3 text-sm outline-none placeholder:text-ink-400 focus:border-brand-300">
            @error('password')
                <p class="text-xs text-brand-500">{{ $message }}</p>
            @enderror

            <input type="password" name="password_confirmation" placeholder="تکرار رمز عبور" class="w-full rounded-xl border border-ink-100 bg-ink-50 px-3.5 py-3 text-sm outline-none placeholder:text-ink-400 focus:border-brand-300">

            <button type="submit" class="w-full rounded-xl bg-brand-500 py-3 text-sm font-bold text-white transition-colors hover:bg-brand-600">ثبت‌نام و ورود</button>
        </form>
    </div>
</div>
