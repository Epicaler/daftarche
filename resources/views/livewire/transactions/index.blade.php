<div>
    <x-page-header title="دخل و خرج" subtitle="همه درآمدها و هزینه‌هات، مرتب و قابل جستجو">
        <button type="button" class="btn-primary" x-on:click="$dispatch('open-transaction-form', { type: 'income' })">
            <x-icon name="plus" class="size-4" /> ثبت درآمد
        </button>
        <button type="button" class="btn-outline" x-on:click="$dispatch('open-transaction-form', { type: 'expense' })">
            <x-icon name="plus" class="size-4" /> ثبت هزینه
        </button>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card label="جمع درآمد" :value="money($income, false)" icon="arrow-down-left" highlight :hint="$month ? $this->months[$month] ?? '' : 'کل دوره'" />
        <x-stat-card label="جمع هزینه" :value="money($expense, false)" icon="arrow-up-right" :hint="$month ? $this->months[$month] ?? '' : 'کل دوره'" />
        <x-stat-card label="خالص" :value="money($income - $expense, false)" icon="coins" :hint="$count.' تراکنش'" />
    </div>

    <div class="mt-6 rounded-3xl bg-white p-4 shadow-card sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="flex gap-1 rounded-full bg-surface p-1">
                @foreach (['' => 'همه', 'income' => 'درآمد', 'expense' => 'هزینه'] as $value => $label)
                    <button type="button" wire:click="$set('type', '{{ $value }}')" @class(['chip', 'chip-active' => $type === $value])>{{ $label }}</button>
                @endforeach
            </div>

            <div class="grid flex-1 gap-3 sm:grid-cols-3">
                <select wire:model.live="month" class="input rounded-full">
                    <option value="">همه ماه‌ها</option>
                    @foreach ($this->months as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="category" class="input rounded-full">
                    <option value="">همه دسته‌ها</option>
                    <option value="none">بدون دسته</option>
                    @foreach ($this->categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}{{ $type ? '' : ' ('.$cat->type->label().')' }}</option>
                    @endforeach
                </select>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 right-4 size-4 -translate-y-1/2 text-muted" />
                    <input type="search" wire:model.live.debounce.400ms="search" class="input rounded-full pr-10" placeholder="جستجو در عنوان و توضیحات">
                </div>
            </div>

            @if ($search || $type || $category || $month)
                <button type="button" class="btn-ghost btn-sm" wire:click="clearFilters"><x-icon name="x" class="size-4" /> حذف فیلترها</button>
            @endif
        </div>

        <div class="mt-5 hidden grid-cols-[1fr_10rem_8rem_10rem_5rem] gap-4 border-b border-line px-3 pb-3 text-xs font-medium text-muted md:grid">
            <span>عنوان</span><span>دسته‌بندی</span><span>تاریخ</span><span class="text-left">مبلغ</span><span></span>
        </div>

        <ul class="divide-y divide-line" wire:loading.class="opacity-60">
            @forelse ($transactions as $tx)
                <li wire:key="tx-{{ $tx->id }}" class="group grid grid-cols-[auto_1fr_auto] items-center gap-x-3 gap-y-1 px-1 py-3.5 md:grid-cols-[1fr_10rem_8rem_10rem_5rem] md:gap-4 md:px-3">
                    <div class="contents md:flex md:items-center md:gap-3">
                        <span @class(['row-span-2 flex size-10 shrink-0 items-center justify-center rounded-full md:row-span-1', 'bg-brand-50 text-income' => $tx->isIncome(), 'bg-orange-50 text-expense' => ! $tx->isIncome()])>
                            <x-icon :name="$tx->isIncome() ? 'arrow-down-left' : 'arrow-up-right'" class="size-[18px]" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ $tx->title }}</p>
                            @if ($tx->description)
                                <p class="truncate text-xs text-muted" title="{{ $tx->description }}">{{ $tx->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="hidden md:block">
                        @if ($tx->category)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-surface px-2.5 py-1 text-xs">
                                <span class="size-2 rounded-full" style="background: {{ $tx->category->color }}"></span>{{ $tx->category->name }}
                            </span>
                        @else
                            <span class="text-xs text-muted">—</span>
                        @endif
                    </div>
                    <span class="col-start-2 text-xs text-muted md:col-start-auto md:text-[13px]">{{ jdate_format($tx->occurred_on, 'j F Y') }}<span class="md:hidden">{{ $tx->category ? '، '.$tx->category->name : '' }}</span></span>
                    <span class="col-start-3 row-start-1 text-left text-sm font-bold md:col-start-auto md:row-start-auto">
                        <span @class(['text-income' => $tx->isIncome(), 'text-expense' => ! $tx->isIncome()])>{{ $tx->isIncome() ? '+' : '−' }}</span>{{ money($tx->amount, false) }}
                    </span>
                    <div class="col-start-3 flex justify-end opacity-100 transition md:col-start-auto md:opacity-0 md:group-hover:opacity-100 md:focus-within:opacity-100">
                        <button type="button" class="icon-btn size-8" wire:click="$dispatch('open-transaction-form', { id: {{ $tx->id }} })" aria-label="ویرایش"><x-icon name="pencil" class="size-4" /></button>
                        <button type="button" class="icon-btn size-8 hover:text-danger" wire:click="delete({{ $tx->id }})" wire:confirm="«{{ $tx->title }}» حذف شود؟" aria-label="حذف"><x-icon name="trash" class="size-4" /></button>
                    </div>
                </li>
            @empty
                <li class="py-6">
                    <x-empty-state icon="wallet" title="تراکنشی پیدا نشد" text="اولین درآمد یا هزینه‌ات رو ثبت کن." class="border-0" />
                </li>
            @endforelse
        </ul>

        <div class="mt-4">{{ $transactions->links('partials.pagination') }}</div>
    </div>
</div>
