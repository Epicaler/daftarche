<div>
    <x-modal :title="$editingId ? 'ویرایش تراکنش' : 'ثبت تراکنش جدید'" subtitle="دخل یا خرجت رو با جزئیات ثبت کن">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-1 rounded-full bg-surface p-1">
                @foreach (\App\Enums\TransactionType::cases() as $case)
                    <button type="button" wire:click="$set('type', '{{ $case->value }}')"
                            @class([
                                'flex cursor-pointer items-center justify-center gap-2 rounded-full py-2 text-sm font-medium transition',
                                'bg-white text-ink shadow-card' => $type === $case->value,
                                'text-muted hover:text-ink' => $type !== $case->value,
                            ])>
                        <x-icon :name="$case === \App\Enums\TransactionType::Income ? 'arrow-down-left' : 'arrow-up-right'"
                                @class(['size-4', 'text-income' => $case === \App\Enums\TransactionType::Income, 'text-expense' => $case === \App\Enums\TransactionType::Expense]) />
                        {{ $case->label() }}
                    </button>
                @endforeach
            </div>

            <div>
                <label class="label">مبلغ</label>
                <x-money-input wire:model="amount" />
                @error('amount') <p class="input-error -mt-3">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="tx-title">عنوان</label>
                <input id="tx-title" type="text" wire:model="title" class="input" placeholder="{{ $type === 'income' ? 'مثلاً: حقوق مهر' : 'مثلاً: خرید هفتگی' }}">
                @error('title') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="tx-category">دسته‌بندی</label>
                    <select id="tx-category" wire:model="category_id" class="input">
                        <option value="">بدون دسته</option>
                        @foreach ($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">تاریخ</label>
                    <x-date-input wire:model="date" />
                    @error('date') <p class="input-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label" for="tx-desc">توضیحات</label>
                <textarea id="tx-desc" wire:model="description" rows="2" class="input resize-none" placeholder="اختیاری"></textarea>
                @error('description') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled" wire:target="save">
                    <x-icon name="check" class="size-4" />
                    {{ $editingId ? 'ذخیره تغییرات' : 'ثبت تراکنش' }}
                </button>
                <button type="button" class="btn-ghost" x-on:click="open = false">انصراف</button>
            </div>
        </form>
    </x-modal>
</div>
