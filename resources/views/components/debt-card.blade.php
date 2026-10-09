@props(['debt', 'showPerson' => true])
@php
    $receivable = $debt->isReceivable();
    $status = $debt->status;
    $statusMap = [
        'settled' => ['تسویه شده', 'border-brand-200 bg-brand-50 text-brand-700'],
        'overdue' => ['سررسید گذشته', 'border-red-200 bg-red-50 text-danger'],
        'partial' => ['پرداخت جزئی', 'border-amber-200 bg-amber-50 text-amber-700'],
        'open' => ['باز', 'border-line bg-surface text-muted'],
    ];
@endphp

<div x-data="{ more: false }" wire:key="debt-{{ $debt->id }}" class="rounded-3xl bg-white p-4 shadow-card sm:p-5">
    <div class="flex flex-wrap items-start gap-3 sm:flex-nowrap sm:gap-4">
        @if ($showPerson)
            <a href="{{ route('people.show', $debt->person) }}" wire:navigate><x-avatar :person="$debt->person" /></a>
        @else
            <span @class(['flex size-11 shrink-0 items-center justify-center rounded-full', 'bg-brand-50 text-income' => $receivable, 'bg-orange-50 text-expense' => ! $receivable])>
                <x-icon :name="$receivable ? 'arrow-down-left' : 'arrow-up-right'" />
            </span>
        @endif

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                @if ($showPerson)
                    <a href="{{ route('people.show', $debt->person) }}" wire:navigate class="font-semibold hover:text-brand-700">{{ $debt->person->name }}</a>
                @endif
                <span @class(['badge', 'border-brand-200 bg-white text-income' => $receivable, 'border-orange-200 bg-white text-expense' => ! $receivable])>
                    <x-icon :name="$receivable ? 'arrow-down-left' : 'arrow-up-right'" class="size-3" />
                    {{ $receivable ? 'طلب' : 'بدهی' }}
                </span>
                <span class="badge {{ $statusMap[$status][1] }}">{{ $statusMap[$status][0] }}</span>
            </div>
            <p class="mt-1 text-sm text-ink/80">{{ $debt->title ?: 'بدون عنوان' }}</p>
            <p class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted">
                <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="size-3.5" /> {{ jdate_format($debt->occurred_on, 'j F Y') }}</span>
                @if ($debt->due_on)
                    <span @class(['inline-flex items-center gap-1', 'text-danger' => $status === 'overdue'])><x-icon name="clock" class="size-3.5" /> سررسید {{ jdate_format($debt->due_on, 'j F Y') }}</span>
                @endif
            </p>
        </div>

        <div class="w-full text-right sm:w-auto sm:text-left">
            <p class="text-lg font-bold">{{ money($debt->amount, false) }} <span class="text-xs font-normal text-muted">{{ config('app.currency') }}</span></p>
            @if ($debt->paid_amount > 0 && ! $debt->settled_at)
                <p class="text-xs text-muted">مانده: <span class="font-semibold text-ink">{{ money($debt->remaining) }}</span></p>
            @endif
        </div>
    </div>

    <div class="mt-4 flex items-center gap-3">
        <div class="h-2 flex-1 overflow-hidden rounded-full bg-surface">
            <div @class(['h-full rounded-full transition-all', 'bg-brand-600' => $receivable, 'bg-expense' => ! $receivable]) style="width: {{ $debt->progress }}%"></div>
        </div>
        <span class="w-10 text-left text-xs font-medium text-muted">{{ $debt->progress }}٪</span>
    </div>

    @if ($debt->description)
        <p class="mt-3 rounded-2xl bg-surface px-4 py-2.5 text-[13px] leading-6 text-ink/70">{{ $debt->description }}</p>
    @endif

    <div class="mt-4 flex flex-wrap items-center gap-2">
        @unless ($debt->settled_at)
            <button type="button" class="btn-primary btn-sm" wire:click="$dispatch('open-payment-form', { debt: {{ $debt->id }} })">
                <x-icon name="banknote" class="size-4" /> {{ $receivable ? 'ثبت دریافت' : 'ثبت پرداخت' }}
            </button>
            <button type="button" class="btn-outline btn-sm" wire:click="settle({{ $debt->id }})" wire:confirm="کل مانده ({{ money($debt->remaining) }}) به عنوان پرداخت ثبت و حساب تسویه شود؟">
                <x-icon name="check" class="size-4" /> تسویه کامل
            </button>
        @endunless
        @if ($debt->payments->isNotEmpty())
            <button type="button" class="btn-ghost btn-sm" x-on:click="more = !more">
                پرداخت‌ها ({{ $debt->payments->count() }})
                <x-icon name="chevron-down" class="size-4 transition" x-bind:class="more && 'rotate-180'" />
            </button>
        @endif
        <div class="mr-auto flex items-center">
            <button type="button" class="icon-btn" wire:click="$dispatch('open-debt-form', { id: {{ $debt->id }} })" aria-label="ویرایش"><x-icon name="pencil" class="size-4" /></button>
            <button type="button" class="icon-btn hover:text-danger" wire:click="deleteDebt({{ $debt->id }})" wire:confirm="این مورد و همه پرداخت‌هایش حذف شود؟" aria-label="حذف"><x-icon name="trash" class="size-4" /></button>
        </div>
    </div>

    @if ($debt->payments->isNotEmpty())
        <div x-show="more" x-collapse x-cloak>
            <ul class="mt-3 divide-y divide-line rounded-2xl border border-line">
                @foreach ($debt->payments->sortByDesc('paid_on') as $payment)
                    <li class="flex items-center gap-3 px-4 py-2.5 text-[13px]" wire:key="payment-{{ $payment->id }}">
                        <x-icon name="check-circle" class="size-4 text-brand-600" />
                        <span class="text-muted">{{ jdate_format($payment->paid_on, 'j F Y') }}</span>
                        <span class="flex-1 truncate text-ink/70">{{ $payment->note }}</span>
                        <span class="font-semibold">{{ money($payment->amount) }}</span>
                        <button type="button" class="text-muted hover:text-danger" wire:click="deletePayment({{ $payment->id }})" wire:confirm="این پرداخت حذف شود؟" aria-label="حذف پرداخت"><x-icon name="x" class="size-4" /></button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
