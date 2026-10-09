<div>
    <x-modal title="ثبت پرداخت" subtitle="بخشی یا تمام مبلغ را ثبت کن؛ با پرداخت کامل، حساب خودکار تسویه می‌شود" width="max-w-md">
        @if ($debt)
            <div class="mb-5 flex items-center gap-3 rounded-2xl bg-surface p-4">
                <x-avatar :person="$debt->person" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ $debt->isReceivable() ? 'طلب از' : 'بدهی به' }} {{ $debt->person->name }}</p>
                    <p class="truncate text-xs text-muted">{{ $debt->title ?: 'بدون عنوان' }}</p>
                </div>
                <div class="text-left">
                    <p class="text-[11px] text-muted">مانده</p>
                    <p class="text-sm font-bold">{{ money($debt->remaining) }}</p>
                </div>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="label">{{ $debt->isReceivable() ? 'مبلغ دریافتی' : 'مبلغ پرداختی' }}</label>
                    <x-money-input wire:model="amount" />
                    @error('amount') <p class="input-error -mt-3">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">تاریخ</label>
                    <x-date-input wire:model="date" />
                    @error('date') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="pay-note">یادداشت</label>
                    <input id="pay-note" type="text" wire:model="note" class="input" placeholder="مثلاً: کارت به کارت">
                    @error('note') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled" wire:target="save">
                        <x-icon name="check" class="size-4" /> ثبت پرداخت
                    </button>
                    <button type="button" class="btn-ghost" x-on:click="open = false">انصراف</button>
                </div>
            </form>
        @endif
    </x-modal>
</div>
