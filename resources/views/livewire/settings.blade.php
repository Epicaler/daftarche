<div>
    <x-page-header title="تنظیمات" subtitle="اطلاعات حساب کاربری و امنیت" />

    <div class="grid max-w-4xl gap-5 lg:grid-cols-2">
        <form wire:submit="saveProfile" class="card space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-brand-50 text-brand-700"><x-icon name="user" /></span>
                <p class="card-title">پروفایل</p>
            </div>
            <div>
                <label class="label" for="s-name">نام</label>
                <input id="s-name" type="text" wire:model="name" class="input">
                @error('name') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="s-email">ایمیل</label>
                <input id="s-email" type="email" dir="ltr" wire:model="email" class="input text-right">
                @error('email') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary"><x-icon name="check" class="size-4" /> ذخیره</button>
        </form>

        <form wire:submit="savePassword" class="card space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-brand-50 text-brand-700"><x-icon name="lock" /></span>
                <p class="card-title">تغییر رمز عبور</p>
            </div>
            <div>
                <label class="label" for="s-cur">رمز فعلی</label>
                <input id="s-cur" type="password" wire:model="current_password" class="input" autocomplete="current-password">
                @error('current_password') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="s-new">رمز جدید</label>
                <input id="s-new" type="password" wire:model="password" class="input" autocomplete="new-password">
                @error('password') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="s-conf">تکرار رمز جدید</label>
                <input id="s-conf" type="password" wire:model="password_confirmation" class="input" autocomplete="new-password">
            </div>
            <button type="submit" class="btn-primary"><x-icon name="lock" class="size-4" /> تغییر رمز</button>
        </form>

        <div class="card lg:col-span-2">
            <p class="card-title">خروجی داده‌ها</p>
            <p class="mt-1 text-[13px] text-muted">فایل CSV سازگار با اکسل (UTF-8) دانلود کن.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('export', 'transactions') }}" class="btn-outline"><x-icon name="download" class="size-4" /> تراکنش‌ها</a>
                <a href="{{ route('export', 'debts') }}" class="btn-outline"><x-icon name="download" class="size-4" /> طلب و بدهی‌ها</a>
            </div>
        </div>
    </div>
</div>
