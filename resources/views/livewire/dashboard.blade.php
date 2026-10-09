<div class="space-y-5">
    <x-page-header title="داشبورد" subtitle="دخل و خرجت رو ببین، برنامه‌ریزی کن و حساب‌هات رو صاف نگه دار.">
        <button type="button" class="btn-primary px-6 py-3 text-[15px]" x-on:click="$dispatch('open-transaction-form', { type: 'expense' })">
            <x-icon name="plus" class="size-[18px]" /> تراکنش جدید
        </button>
        <button type="button" class="btn-outline px-6 py-3 text-[15px]" x-on:click="$dispatch('open-debt-form')">
            ثبت طلب / بدهی
        </button>
    </x-page-header>

    {{-- KPI row --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="موجودی کل" :value="money($balance, false)" highlight :href="route('transactions')"
                     :hint="'خالص '.$monthLabel.': '.money_short($month['net'])" />
        <x-stat-card label="درآمد این ماه" :value="money($month['income'], false)" :href="route('transactions', ['type' => 'income'])"
                     :trend="$incomeTrend" :hint="$incomeTrend === null ? $monthLabel : 'نسبت به ماه قبل'" />
        <x-stat-card label="هزینه این ماه" :value="money($month['expense'], false)" :href="route('transactions', ['type' => 'expense'])"
                     :trend="$expenseTrend" :trend-good="false" :hint="$expenseTrend === null ? $monthLabel : 'نسبت به ماه قبل'" />
        <x-stat-card label="طلب خالص" :value="money($debts['receivable'] - $debts['payable'], false)" :href="route('debts')"
                     :hint="'طلب '.money_short($debts['receivable']).'، بدهی '.money_short($debts['payable'])" />
    </div>

    <div class="grid gap-5 xl:grid-cols-12">
        <div class="grid gap-5 md:grid-cols-12 xl:col-span-9">
            {{-- Weekly spending: pill bars like the reference "Project Analytics" --}}
            <div class="card md:col-span-7">
                <div class="flex items-center justify-between">
                    <p class="card-title">هزینه‌های این هفته</p>
                    <span class="text-[13px] text-muted">جمع: {{ money($week->sum('amount')) }}</span>
                </div>
                @php $max = max(1, $week->max('amount')); @endphp
                <div class="mt-6 flex h-52 items-end justify-between gap-2 sm:gap-3">
                    @foreach ($week as $day)
                        @php $empty = $day['amount'] === 0 || $day['future']; @endphp
                        <div x-data="{ tip: false }" class="relative flex h-full flex-1 flex-col items-center justify-end gap-2" x-on:mouseenter="tip = true" x-on:mouseleave="tip = false">
                            <div x-show="tip || {{ $day['today'] && ! $empty ? 'true' : 'false' }}" x-cloak
                                 class="absolute z-10 rounded-lg border border-line bg-white px-2 py-1 text-center text-[11px] whitespace-nowrap shadow-card"
                                 style="bottom: calc({{ $empty ? 72 : max(18, round($day['amount'] / $max * 100)) }}% - 0.25rem + 1.75rem)">
                                <span class="block text-muted">{{ $day['date'] }}</span>
                                <span class="font-semibold">{{ $day['amount'] ? money($day['amount']) : 'بدون هزینه' }}</span>
                            </div>
                            <div @class([
                                    'w-full max-w-14 rounded-full transition-all duration-500',
                                    'hatch' => $empty,
                                    'bg-brand-900' => ! $empty && $day['today'],
                                    'bg-brand-600 hover:bg-brand-500' => ! $empty && ! $day['today'],
                                 ])
                                 style="height: {{ $empty ? 72 : max(18, round($day['amount'] / $max * 100)) }}%"></div>
                            <span @class(['text-[13px]', 'font-bold text-ink' => $day['today'], 'text-muted' => ! $day['today']])>{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Reminder --}}
            <div class="card flex flex-col md:col-span-5">
                <p class="card-title">یادآوری</p>
                @if ($reminder)
                    <p class="mt-4 text-2xl leading-snug font-semibold text-brand-800">
                        {{ $reminder->isReceivable() ? 'دریافت طلب از' : 'پرداخت بدهی به' }} {{ $reminder->person->name }}
                    </p>
                    <p class="mt-2 text-[15px] text-muted">
                        مبلغ: <span class="font-semibold text-ink">{{ money($reminder->remaining) }}</span>
                    </p>
                    <p @class(['mt-1 text-[15px]', 'text-danger' => $reminder->due_on->isPast(), 'text-muted' => ! $reminder->due_on->isPast()])>
                        سررسید: {{ jdate_format($reminder->due_on, 'l j F') }}
                        @if ($reminder->due_on->isToday()) (امروز)
                        @elseif ($reminder->due_on->isPast()) ({{ (int) $reminder->due_on->diffInDays(today()) }} روز گذشته)
                        @else ({{ (int) today()->diffInDays($reminder->due_on) }} روز دیگر)
                        @endif
                    </p>
                    <button type="button" class="btn-primary mt-auto w-full py-3.5 text-[15px]" x-on:click="$dispatch('open-payment-form', { debt: {{ $reminder->id }} })">
                        <x-icon name="banknote" /> {{ $reminder->isReceivable() ? 'ثبت دریافت' : 'ثبت پرداخت' }}
                    </button>
                @else
                    <p class="mt-4 text-2xl leading-snug font-semibold text-brand-800">سررسید نزدیکی نداری</p>
                    <p class="mt-2 text-[15px] text-muted">برای طلب‌ها و بدهی‌هات تاریخ سررسید بذار تا اینجا یادآوری بشه.</p>
                    <button type="button" class="btn-primary mt-auto w-full py-3.5 text-[15px]" x-on:click="$dispatch('open-debt-form')">
                        <x-icon name="plus" /> ثبت طلب / بدهی
                    </button>
                @endif
            </div>

            {{-- People: modelled on "Team Collaboration" --}}
            <div class="card md:col-span-7">
                <div class="flex items-center justify-between">
                    <p class="card-title">حساب با اشخاص</p>
                    <button type="button" class="btn-outline btn-sm" x-on:click="$dispatch('open-person-form')"><x-icon name="plus" class="size-4" /> افزودن شخص</button>
                </div>
                <ul class="mt-4 space-y-1">
                    @forelse ($people as $person)
                        <li>
                            <a href="{{ route('people.show', $person) }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-2 py-2.5 transition hover:bg-surface">
                                <x-avatar :person="$person" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[15px] font-medium">{{ $person->name }}</p>
                                    <p class="truncate text-xs text-muted">مانده: <span class="font-semibold text-ink">{{ money(abs($person->net_balance)) }}</span></p>
                                </div>
                                @if ($person->net_balance > 0)
                                    <span class="badge border-brand-200 bg-brand-50 text-brand-700">طلبکارم</span>
                                @else
                                    <span class="badge border-orange-200 bg-orange-50 text-expense">بدهکارم</span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="py-8 text-center text-[13px] text-muted">حساب باز با کسی نداری</li>
                    @endforelse
                </ul>
            </div>

            {{-- Settlement gauge: modelled on "Project Progress" --}}
            @php
                $total = max(1, $debts['total']);
                $segments = [
                    ['value' => $debts['settled'] / $total * 100, 'stroke' => 'var(--color-brand-600)', 'label' => 'تسویه شده', 'amount' => $debts['settled']],
                    ['value' => $debts['receivable'] / $total * 100, 'stroke' => 'var(--color-brand-900)', 'label' => 'طلب باز', 'amount' => $debts['receivable']],
                    ['value' => $debts['payable'] / $total * 100, 'stroke' => 'url(#hatch)', 'label' => 'بدهی باز', 'amount' => $debts['payable']],
                ];
                $offset = 0;
            @endphp
            <div class="card flex flex-col md:col-span-5">
                <p class="card-title">وضعیت تسویه حساب‌ها</p>
                <div class="relative mx-auto mt-4 w-full max-w-72">
                    <svg viewBox="0 0 220 125" class="w-full" role="img" aria-label="{{ $debts['percent'] }}٪ تسویه شده">
                        <defs>
                            <pattern id="hatch" width="6" height="6" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                                <rect width="6" height="6" fill="#fff"/><rect width="2" height="6" fill="#7d8a83"/>
                            </pattern>
                        </defs>
                        <path d="M 22 112 A 88 88 0 0 1 198 112" fill="none" stroke="var(--color-surface)" stroke-width="30" stroke-linecap="round"/>
                        @if ($debts['total'] > 0)
                            @foreach ($segments as $segment)
                                @if ($segment['value'] > 0.5)
                                    <path d="M 22 112 A 88 88 0 0 1 198 112" fill="none" pathLength="100" stroke="{{ $segment['stroke'] }}" stroke-width="30"
                                          stroke-dasharray="{{ max(0.1, $segment['value'] - 1) }} 200" stroke-dashoffset="{{ -$offset }}">
                                        <title>{{ $segment['label'] }}: {{ money($segment['amount']) }}</title>
                                    </path>
                                @endif
                                @php $offset += $segment['value']; @endphp
                            @endforeach
                        @endif
                    </svg>
                    <div class="absolute inset-x-0 bottom-0 text-center">
                        <p class="text-[44px] leading-none font-bold tracking-tight">{{ $debts['percent'] }}<span class="text-3xl">٪</span></p>
                        <p class="mt-1 text-[13px] text-muted">تسویه شده</p>
                    </div>
                </div>
                <ul class="mt-auto flex flex-wrap justify-center gap-x-5 gap-y-2 pt-5 text-[13px]">
                    @foreach ($segments as $segment)
                        <li class="flex items-center gap-1.5" title="{{ money($segment['amount']) }}">
                            <span @class(['size-3.5 rounded-full', 'hatch' => $loop->last]) @if (! $loop->last) style="background: {{ $segment['stroke'] }}" @endif></span>
                            {{ $segment['label'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Right column --}}
        <div class="flex flex-col gap-5 md:flex-row xl:col-span-3 xl:flex-col">
            {{-- Open debts: modelled on "Project" list --}}
            <div class="card flex-1">
                <div class="flex items-center justify-between">
                    <p class="card-title">طلب و بدهی باز</p>
                    <button type="button" class="btn-outline btn-sm" x-on:click="$dispatch('open-debt-form')"><x-icon name="plus" class="size-4" /> جدید</button>
                </div>
                <ul class="mt-4 space-y-1">
                    @forelse ($openDebts as $debt)
                        <li>
                            <a href="{{ route('people.show', $debt->person) }}" wire:navigate class="flex items-center gap-3 rounded-2xl p-2 transition hover:bg-surface">
                                <span @class(['flex size-10 shrink-0 items-center justify-center rounded-xl', 'bg-brand-50 text-income' => $debt->isReceivable(), 'bg-orange-50 text-expense' => ! $debt->isReceivable()])>
                                    <x-icon :name="$debt->isReceivable() ? 'arrow-down-left' : 'arrow-up-right'" class="size-[18px]" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-baseline justify-between gap-2 text-sm">
                                        <span class="truncate font-medium">{{ $debt->person->name }}</span>
                                        <span class="shrink-0 text-xs font-semibold">{{ money_short($debt->remaining) }}</span>
                                    </span>
                                    <span @class(['block truncate text-[11px]', 'text-danger' => $debt->status === 'overdue', 'text-muted' => $debt->status !== 'overdue'])>
                                        {{ $debt->due_on ? 'سررسید: '.jdate_format($debt->due_on, 'j F Y') : ($debt->title ?: 'بدون سررسید') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="py-8 text-center text-[13px] text-muted">مورد بازی وجود ندارد</li>
                    @endforelse
                </ul>
            </div>

            {{-- Net worth: modelled on the dark "Time Tracker" card --}}
            <div class="relative overflow-hidden rounded-3xl bg-brand-950 p-5 text-white md:w-1/2 xl:w-auto">
                <svg class="absolute inset-0 size-full" viewBox="0 0 300 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                    <defs>
                        <radialGradient id="glow" cx="80%" cy="100%" r="80%"><stop offset="0" stop-color="#217249" stop-opacity=".9"/><stop offset="1" stop-color="#0a2418" stop-opacity="0"/></radialGradient>
                    </defs>
                    <rect width="300" height="200" fill="url(#glow)"/>
                    @foreach (range(0, 9) as $i)
                        <path d="M -20 {{ 150 + $i * 7 }} C 80 {{ 40 + $i * 9 }}, 180 {{ 230 - $i * 6 }}, 320 {{ 60 + $i * 10 }}" stroke="#4fa978" stroke-opacity="{{ 0.12 + $i * 0.02 }}" fill="none"/>
                    @endforeach
                </svg>
                <div class="relative">
                    <p class="text-[17px] font-medium">دارایی خالص</p>
                    <p class="mt-5 text-center text-[34px] leading-none font-bold tracking-tight">{{ money($netWorth, false) }}</p>
                    <p class="mt-1.5 text-center text-xs text-white/60">{{ config('app.currency') }}، موجودی + طلب − بدهی</p>
                    <div class="mt-5 flex justify-center gap-3">
                        <button type="button" class="flex size-12 cursor-pointer items-center justify-center rounded-full bg-white text-brand-950 transition hover:scale-105" x-on:click="$dispatch('open-transaction-form', { type: 'income' })" title="ثبت درآمد" aria-label="ثبت درآمد">
                            <x-icon name="arrow-down-left" />
                        </button>
                        <button type="button" class="flex size-12 cursor-pointer items-center justify-center rounded-full bg-expense text-white transition hover:scale-105" x-on:click="$dispatch('open-transaction-form', { type: 'expense' })" title="ثبت هزینه" aria-label="ثبت هزینه">
                            <x-icon name="arrow-up-right" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid gap-5 xl:grid-cols-12">
        <div class="card xl:col-span-8">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="card-title">روند ۱۲ ماه اخیر</p>
                <a href="{{ route('reports') }}" wire:navigate class="text-[13px] text-brand-700 hover:underline">گزارش کامل</a>
            </div>
            <x-chart :config="['type' => 'trend', 'height' => 380] + $series" class="mt-3" />
        </div>

        <div class="card xl:col-span-4">
            <p class="card-title">هزینه‌های {{ $monthLabel }} بر اساس دسته</p>
            @if ($categories->isNotEmpty())
                <x-chart class="mt-3" :config="['type' => 'donut', 'title' => 'جمع هزینه', 'labels' => $categories->pluck('name'), 'values' => $categories->pluck('total'), 'colors' => $categories->pluck('color')]" />
            @else
                <x-empty-state icon="chart" title="هنوز هزینه‌ای این ماه ثبت نشده" class="mt-4 border-0" />
            @endif
        </div>
    </div>

    {{-- Recent transactions --}}
    <div class="card">
        <div class="flex items-center justify-between">
            <p class="card-title">آخرین تراکنش‌ها</p>
            <a href="{{ route('transactions') }}" wire:navigate class="text-[13px] text-brand-700 hover:underline">مشاهده همه</a>
        </div>
        <ul class="mt-3 grid gap-x-8 md:grid-cols-2">
            @forelse ($recent as $tx)
                <li class="flex items-center gap-3 border-b border-line py-3 last:border-0 md:[&:nth-last-child(2)]:border-0">
                    <span @class(['flex size-10 shrink-0 items-center justify-center rounded-full', 'bg-brand-50 text-income' => $tx->isIncome(), 'bg-orange-50 text-expense' => ! $tx->isIncome()])>
                        <x-icon :name="$tx->isIncome() ? 'arrow-down-left' : 'arrow-up-right'" class="size-[18px]" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ $tx->title }}</p>
                        <p class="truncate text-xs text-muted">{{ jdate_format($tx->occurred_on, 'j F') }}{{ $tx->category ? '، '.$tx->category->name : '' }}</p>
                    </div>
                    <span class="text-sm font-bold"><span @class(['text-income' => $tx->isIncome(), 'text-expense' => ! $tx->isIncome()])>{{ $tx->isIncome() ? '+' : '−' }}</span>{{ money($tx->amount, false) }}</span>
                </li>
            @empty
                <li class="py-8 text-center text-[13px] text-muted md:col-span-2">هنوز تراکنشی ثبت نشده</li>
            @endforelse
        </ul>
    </div>
</div>
