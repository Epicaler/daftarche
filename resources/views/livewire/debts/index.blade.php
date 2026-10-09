<div>
    <x-page-header title="طلب و بدهی" subtitle="ببین از چه کسی چقدر طلب داری و به چه کسی بدهکاری">
        <button type="button" class="btn-primary" x-on:click="$dispatch('open-debt-form', { direction: 'receivable' })">
            <x-icon name="plus" class="size-4" /> ثبت طلب
        </button>
        <button type="button" class="btn-outline" x-on:click="$dispatch('open-debt-form', { direction: 'payable' })">
            ثبت بدهی
        </button>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card label="طلب‌های باز" :value="money($summary['receivable'], false)" icon="arrow-down-left" highlight
                     :hint="'از '.$summary['receivable_people'].' نفر'" />
        <x-stat-card label="بدهی‌های باز" :value="money($summary['payable'], false)" icon="arrow-up-right"
                     :hint="'به '.$summary['payable_people'].' نفر'" />
        <x-stat-card label="سررسید گذشته" :value="$overdue" icon="alert" :unit="'مورد'"
                     :hint="$overdue ? 'نیاز به پیگیری' : 'همه چیز مرتبه'" />
    </div>

    <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center">
        <div class="flex gap-1 rounded-full bg-line/60 p-1">
            @foreach (['' => 'همه', 'receivable' => 'طلب‌ها', 'payable' => 'بدهی‌ها'] as $value => $label)
                <button type="button" wire:click="$set('direction', '{{ $value }}')" @class(['chip', 'chip-active' => $direction === $value])>{{ $label }}</button>
            @endforeach
        </div>
        <div class="flex gap-1 rounded-full bg-line/60 p-1">
            @foreach (['open' => 'باز', 'overdue' => 'معوق', 'settled' => 'تسویه شده', 'all' => 'همه'] as $value => $label)
                <button type="button" wire:click="$set('status', '{{ $value }}')" @class(['chip', 'chip-active' => $status === $value])>{{ $label }}</button>
            @endforeach
        </div>
        <div class="relative lg:mr-auto lg:w-72">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 right-4 size-4 -translate-y-1/2 text-muted" />
            <input type="search" wire:model.live.debounce.400ms="search" class="input rounded-full pr-10" placeholder="جستجوی نام یا عنوان...">
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2" wire:loading.class="opacity-60">
        @forelse ($debts as $debt)
            <x-debt-card :debt="$debt" />
        @empty
            <div class="xl:col-span-2">
                <x-empty-state icon="scale" title="موردی پیدا نشد" text="یک طلب یا بدهی جدید ثبت کن تا اینجا نمایش داده شود." />
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $debts->links('partials.pagination') }}</div>
</div>
