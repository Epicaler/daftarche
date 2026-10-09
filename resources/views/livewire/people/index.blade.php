<div>
    <x-page-header title="اشخاص" subtitle="خلاصه حساب تو با هر نفر، یک‌جا">
        <button type="button" class="btn-primary" x-on:click="$dispatch('open-person-form')">
            <x-icon name="plus" class="size-4" /> افزودن شخص
        </button>
    </x-page-header>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex gap-1 overflow-x-auto rounded-full bg-line/60 p-1">
            @foreach (['' => 'همه', 'owes-me' => 'به من بدهکارند', 'i-owe' => 'من بدهکارم', 'clear' => 'تسویه'] as $value => $label)
                <button type="button" wire:click="$set('filter', '{{ $value }}')" @class(['chip', 'chip-active' => $filter === $value])>{{ $label }}</button>
            @endforeach
        </div>
        <div class="relative sm:mr-auto sm:w-72">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 right-4 size-4 -translate-y-1/2 text-muted" />
            <input type="search" wire:model.live.debounce.400ms="search" class="input rounded-full pr-10" placeholder="جستجوی نام...">
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        @forelse ($people as $person)
            <a href="{{ route('people.show', $person) }}" wire:navigate wire:key="person-{{ $person->id }}"
               class="group rounded-3xl bg-white p-5 shadow-card transition hover:-translate-y-0.5 hover:shadow-[0_16px_40px_-20px_rgb(20_28_23/0.35)]">
                <div class="flex items-center gap-3">
                    <x-avatar :person="$person" size="size-12" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold group-hover:text-brand-700">{{ $person->name }}</p>
                        <p class="text-xs text-muted" dir="{{ $person->phone ? 'ltr' : 'rtl' }}">{{ $person->phone ?: $person->debts_count.' مورد ثبت شده' }}</p>
                    </div>
                    <span class="flex size-9 items-center justify-center rounded-full border border-line text-muted transition group-hover:border-ink group-hover:bg-ink group-hover:text-white"><x-icon name="arrow-up-left" class="size-4" /></span>
                </div>
                <div class="mt-5 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-xs text-muted">
                            @if ($person->net_balance > 0) به من بدهکار است
                            @elseif ($person->net_balance < 0) من بدهکارم
                            @else حساب صاف است
                            @endif
                        </p>
                        <p class="mt-1 text-xl font-bold">{{ money(abs($person->net_balance), false) }} <span class="text-xs font-normal text-muted">{{ config('app.currency') }}</span></p>
                    </div>
                    @if ($person->net_balance > 0)
                        <span class="badge border-brand-200 bg-brand-50 text-brand-700">طلبکارم</span>
                    @elseif ($person->net_balance < 0)
                        <span class="badge border-orange-200 bg-orange-50 text-expense">بدهکارم</span>
                    @else
                        <span class="badge border-line bg-surface text-muted">تسویه</span>
                    @endif
                </div>
                @if ($person->receivable_left > 0 && $person->payable_left > 0)
                    <p class="mt-3 border-t border-line pt-3 text-[11px] text-muted">طلب {{ money($person->receivable_left) }}، بدهی {{ money($person->payable_left) }}</p>
                @endif
            </a>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 2xl:col-span-4">
                <x-empty-state icon="users" title="هنوز کسی اضافه نشده" text="با ثبت اولین طلب یا بدهی، شخص به‌صورت خودکار ساخته می‌شود." />
            </div>
        @endforelse
    </div>
</div>
