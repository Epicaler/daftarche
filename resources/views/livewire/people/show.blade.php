<div>
    <a href="{{ route('people') }}" wire:navigate class="mb-4 inline-flex items-center gap-1 text-[13px] text-muted hover:text-ink">
        <x-icon name="chevron-right" class="size-4" /> همه اشخاص
    </a>

    <div class="flex flex-col gap-5 rounded-3xl bg-white p-5 shadow-card sm:p-6 lg:flex-row lg:items-center">
        <div class="flex flex-1 items-center gap-4">
            <x-avatar :person="$person" size="size-16 text-xl" />
            <div class="min-w-0">
                <h1 class="text-2xl font-bold">{{ $person->name }}</h1>
                <p class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-[13px] text-muted">
                    @if ($person->phone)
                        <a href="tel:{{ $person->phone }}" class="inline-flex items-center gap-1 hover:text-brand-700" dir="ltr"><x-icon name="phone" class="size-3.5" /> {{ $person->phone }}</a>
                    @endif
                    <span>{{ $debts->count() }} مورد ثبت شده</span>
                </p>
                @if ($person->note)
                    <p class="mt-2 text-[13px] text-ink/70">{{ $person->note }}</p>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="btn-primary" x-on:click="$dispatch('open-debt-form', { direction: 'receivable', person: {{ $person->id }} })"><x-icon name="plus" class="size-4" /> طلب جدید</button>
            <button type="button" class="btn-outline" x-on:click="$dispatch('open-debt-form', { direction: 'payable', person: {{ $person->id }} })">بدهی جدید</button>
            <button type="button" class="icon-btn bg-surface" x-on:click="$dispatch('open-person-form', { id: {{ $person->id }} })" aria-label="ویرایش"><x-icon name="pencil" class="size-4" /></button>
            <button type="button" class="icon-btn bg-surface hover:text-danger" wire:click="deletePerson" wire:confirm="«{{ $person->name }}» و همه طلب‌ها/بدهی‌هایش حذف شود؟" aria-label="حذف"><x-icon name="trash" class="size-4" /></button>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="خالص حساب" :value="money(abs($balances->net_balance), false)" icon="scale" highlight
                     :hint="$balances->net_balance > 0 ? 'به من بدهکار است' : ($balances->net_balance < 0 ? 'من بدهکارم' : 'حساب صاف است')" />
        <x-stat-card label="طلب باقی‌مانده" :value="money($balances->receivable_left, false)" icon="arrow-down-left" :hint="'از مجموع '.money($totalReceivable)" />
        <x-stat-card label="بدهی باقی‌مانده" :value="money($balances->payable_left, false)" icon="arrow-up-right" :hint="'از مجموع '.money($totalPayable)" />
    </div>

    <h2 class="mt-8 mb-4 text-lg font-semibold">سوابق</h2>
    <div class="grid gap-4 xl:grid-cols-2">
        @forelse ($debts as $debt)
            <x-debt-card :debt="$debt" :show-person="false" />
        @empty
            <div class="xl:col-span-2">
                <x-empty-state icon="scale" title="هنوز سابقه‌ای ثبت نشده" text="از دکمه‌های بالا یک طلب یا بدهی برای این شخص ثبت کن." />
            </div>
        @endforelse
    </div>
</div>
