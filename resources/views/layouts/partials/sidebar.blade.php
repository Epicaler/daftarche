@php
    $menu = [
        ['route' => 'dashboard', 'label' => 'داشبورد', 'icon' => 'grid'],
        ['route' => 'transactions', 'label' => 'دخل و خرج', 'icon' => 'wallet'],
        ['route' => 'debts', 'label' => 'طلب و بدهی', 'icon' => 'scale', 'badge' => $openDebtCount],
        ['route' => 'people', 'label' => 'اشخاص', 'icon' => 'users', 'match' => 'people*'],
        ['route' => 'reports', 'label' => 'گزارش‌ها', 'icon' => 'chart'],
    ];
    $general = [
        ['route' => 'categories', 'label' => 'دسته‌بندی‌ها', 'icon' => 'tag'],
        ['route' => 'settings', 'label' => 'تنظیمات', 'icon' => 'settings'],
    ];
@endphp

<aside x-bind:data-open="nav ? 'true' : 'false'"
       class="fixed inset-y-0 right-0 z-50 flex w-[272px] shrink-0 translate-x-full flex-col data-[open=true]:translate-x-0 bg-surface px-5 py-6 transition-transform duration-300 lg:sticky lg:top-3 lg:h-[calc(100vh-2rem-1.5rem)] lg:translate-x-0 lg:rounded-[28px] xl:h-[calc(100vh-3rem-1.5rem)]">
    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-2">
        <x-logo />
        <span class="text-[22px] font-bold tracking-tight">{{ config('app.name') }}</span>
    </a>

    <nav class="scrollbar-thin mt-9 flex-1 overflow-y-auto">
        <p class="mb-3 px-3 text-xs font-medium text-muted">منو</p>
        <ul class="space-y-1">
            @foreach ($menu as $item)
                @php $active = request()->routeIs($item['match'] ?? $item['route']); @endphp
                <li class="relative">
                    @if ($active)
                        <span class="absolute top-1/2 -right-5 h-9 w-1.5 -translate-y-1/2 rounded-l-full bg-brand-700"></span>
                    @endif
                    <a href="{{ route($item['route']) }}" wire:navigate
                       @class([
                           'flex items-center gap-3 rounded-2xl px-3 py-2.5 text-[15px] transition',
                           'font-semibold text-ink' => $active,
                           'text-muted hover:bg-white hover:text-ink' => ! $active,
                       ])>
                        <x-icon :name="$item['icon']" @class(['size-[22px]', 'text-brand-700' => $active]) />
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if (! empty($item['badge']))
                            <span class="rounded-md bg-brand-900 px-1.5 py-0.5 text-[10px] font-semibold text-white">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>

        <p class="mt-9 mb-3 px-3 text-xs font-medium text-muted">عمومی</p>
        <ul class="space-y-1">
            @foreach ($general as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <li class="relative">
                    @if ($active)
                        <span class="absolute top-1/2 -right-5 h-9 w-1.5 -translate-y-1/2 rounded-l-full bg-brand-700"></span>
                    @endif
                    <a href="{{ route($item['route']) }}" wire:navigate
                       @class([
                           'flex items-center gap-3 rounded-2xl px-3 py-2.5 text-[15px] transition',
                           'font-semibold text-ink' => $active,
                           'text-muted hover:bg-white hover:text-ink' => ! $active,
                       ])>
                        <x-icon :name="$item['icon']" @class(['size-[22px]', 'text-brand-700' => $active]) />
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full cursor-pointer items-center gap-3 rounded-2xl px-3 py-2.5 text-[15px] text-muted transition hover:bg-white hover:text-danger">
                        <x-icon name="logout" class="size-[22px]" />
                        <span>خروج</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    {{-- Export promo, modelled on the "Download our app" card --}}
    <div class="relative mt-6 overflow-hidden rounded-3xl bg-brand-950 p-5 text-white">
        <svg class="absolute inset-0 size-full opacity-40" viewBox="0 0 220 180" preserveAspectRatio="none" aria-hidden="true">
            <path d="M-20 140 C 60 60, 120 200, 240 80" stroke="#2d8c5b" stroke-width="1.2" fill="none"/>
            <path d="M-20 120 C 60 40, 130 190, 240 60" stroke="#2d8c5b" stroke-width="1.2" fill="none"/>
            <path d="M-20 100 C 60 20, 140 180, 240 40" stroke="#2d8c5b" stroke-width="1.2" fill="none"/>
            <circle cx="190" cy="30" r="60" fill="#217249" opacity=".45"/>
        </svg>
        <div class="relative">
            <span class="flex size-8 items-center justify-center rounded-full bg-white text-brand-900"><x-icon name="download" class="size-4" /></span>
            <p class="mt-3 text-lg leading-snug font-semibold">خروجی اکسل</p>
            <p class="mt-1 text-xs text-white/60">همه تراکنش‌ها در یک فایل CSV</p>
            <a href="{{ route('export', 'transactions') }}" class="mt-4 flex w-full items-center justify-center rounded-full bg-brand-600 py-2.5 text-sm font-medium transition hover:bg-brand-500">دانلود</a>
        </div>
    </div>
</aside>
