<div class="w-full max-w-md">
    <div class="mb-8 flex items-center justify-center gap-3">
        <x-logo class="size-12" />
        <span class="text-3xl font-bold tracking-tight">{{ config('app.name') }}</span>
    </div>

    <form wire:submit="login" class="rounded-[32px] bg-white p-7 shadow-float sm:p-9">
        <h1 class="text-2xl font-bold">خوش برگشتی 👋</h1>
        <p class="mt-1.5 text-sm text-muted">برای دیدن دخل و خرجت وارد شو.</p>

        <div class="mt-7 space-y-4">
            <div>
                <label class="label" for="email">ایمیل</label>
                <input id="email" type="email" dir="ltr" wire:model="email" class="input text-right" autocomplete="username" autofocus>
                @error('email') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="password">رمز عبور</label>
                <input id="password" type="password" wire:model="password" class="input" autocomplete="current-password">
                @error('password') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <label class="flex cursor-pointer items-center gap-2 text-sm text-ink/80">
                <input type="checkbox" wire:model="remember" class="size-4 rounded accent-brand-700"> مرا به خاطر بسپار
            </label>
        </div>

        <button type="submit" class="btn-primary mt-7 w-full py-3 text-[15px]" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">ورود</span>
            <span wire:loading wire:target="login">در حال ورود...</span>
        </button>
    </form>
</div>
