<div>
    <x-modal :title="$editingId ? 'ویرایش طلب / بدهی' : 'ثبت طلب یا بدهی'" subtitle="مشخص کن چه کسی، چقدر و از چه تاریخی">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-1 rounded-full bg-surface p-1">
                <button type="button" wire:click="$set('direction', 'receivable')"
                        @class(['flex cursor-pointer items-center justify-center gap-2 rounded-full py-2 text-sm font-medium transition', 'bg-white text-ink shadow-card' => $direction === 'receivable', 'text-muted hover:text-ink' => $direction !== 'receivable'])>
                    <x-icon name="arrow-down-left" class="size-4 text-income" /> طلب دارم
                </button>
                <button type="button" wire:click="$set('direction', 'payable')"
                        @class(['flex cursor-pointer items-center justify-center gap-2 rounded-full py-2 text-sm font-medium transition', 'bg-white text-ink shadow-card' => $direction === 'payable', 'text-muted hover:text-ink' => $direction !== 'payable'])>
                    <x-icon name="arrow-up-right" class="size-4 text-expense" /> بدهکارم
                </button>
            </div>

            <div>
                <label class="label" for="debt-person">{{ $direction === 'receivable' ? 'از چه کسی طلب داری؟' : 'به چه کسی بدهکاری؟' }}</label>
                <input id="debt-person" type="text" wire:model="person_name" list="people-list" class="input" placeholder="نام شخص (اگر جدید باشد ساخته می‌شود)" autocomplete="off">
                <datalist id="people-list">
                    @foreach ($this->people as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                @error('person_name') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">مبلغ</label>
                <x-money-input wire:model="amount" />
                @error('amount') <p class="input-error -mt-3">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="debt-title">بابت</label>
                <input id="debt-title" type="text" wire:model="title" class="input" placeholder="مثلاً: قرض برای خرید گوشی">
                @error('title') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">تاریخ</label>
                    <x-date-input wire:model="date" />
                    @error('date') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">سررسید <span class="font-normal text-muted">(اختیاری)</span></label>
                    <x-date-input wire:model="due_date" placeholder="بدون سررسید" clearable />
                    @error('due_date') <p class="input-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label" for="debt-desc">توضیحات</label>
                <textarea id="debt-desc" wire:model="description" rows="2" class="input resize-none" placeholder="اختیاری"></textarea>
                @error('description') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled" wire:target="save">
                    <x-icon name="check" class="size-4" />
                    {{ $editingId ? 'ذخیره تغییرات' : 'ثبت' }}
                </button>
                <button type="button" class="btn-ghost" x-on:click="open = false">انصراف</button>
            </div>
        </form>
    </x-modal>
</div>
