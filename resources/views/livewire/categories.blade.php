<div>
    <x-page-header title="دسته‌بندی‌ها" subtitle="درآمدها و هزینه‌هات رو دسته‌بندی کن تا گزارش‌ها دقیق‌تر بشن" />

    <div class="grid gap-5 lg:grid-cols-[22rem_1fr]">
        <form wire:submit="save" class="card h-fit space-y-4 lg:sticky lg:top-4">
            <p class="card-title">{{ $editingId ? 'ویرایش دسته‌بندی' : 'دسته‌بندی جدید' }}</p>

            <div class="grid grid-cols-2 gap-1 rounded-full bg-surface p-1">
                @foreach (\App\Enums\TransactionType::cases() as $case)
                    <button type="button" wire:click="$set('type', '{{ $case->value }}')" @class(['chip text-center', 'chip-active' => $type === $case->value])>{{ $case->label() }}</button>
                @endforeach
            </div>

            <div>
                <label class="label" for="cat-name">نام</label>
                <input id="cat-name" type="text" wire:model="name" class="input" placeholder="مثلاً: حمل و نقل">
                @error('name') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <span class="label">رنگ</span>
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Livewire\Categories::COLORS as $swatch)
                        <button type="button" wire:click="$set('color', '{{ $swatch }}')" aria-label="رنگ {{ $loop->iteration }}"
                                @class(['size-8 cursor-pointer rounded-full ring-offset-2 transition', 'ring-2 ring-ink' => $color === $swatch])
                                style="background: {{ $swatch }}"></button>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 pt-1">
                <button type="submit" class="btn-primary flex-1"><x-icon name="check" class="size-4" /> {{ $editingId ? 'ذخیره' : 'افزودن' }}</button>
                @if ($editingId)
                    <button type="button" class="btn-ghost" wire:click="cancel">انصراف</button>
                @endif
            </div>
        </form>

        <div class="grid gap-5 md:grid-cols-2">
            @foreach (\App\Enums\TransactionType::cases() as $case)
                <div class="card">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="card-title">{{ $case === \App\Enums\TransactionType::Income ? 'دسته‌های درآمد' : 'دسته‌های هزینه' }}</p>
                        <span class="text-xs text-muted">{{ ($groups[$case->value] ?? collect())->count() }} مورد</span>
                    </div>
                    <ul class="space-y-1">
                        @forelse ($groups[$case->value] ?? [] as $category)
                            <li wire:key="cat-{{ $category->id }}" @class(['group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition hover:bg-surface', 'bg-brand-50' => $editingId === $category->id])>
                                <span class="flex size-9 items-center justify-center rounded-xl" style="background: {{ $category->color }}1f; color: {{ $category->color }}"><x-icon name="tag" class="size-4" /></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $category->name }}</p>
                                    <p class="text-[11px] text-muted">{{ $category->transactions_count }} تراکنش، {{ money_short($category->transactions_sum_amount ?? 0) }}</p>
                                </div>
                                <div class="flex opacity-100 transition md:opacity-0 md:group-hover:opacity-100">
                                    <button type="button" class="icon-btn size-8" wire:click="edit({{ $category->id }})" aria-label="ویرایش"><x-icon name="pencil" class="size-4" /></button>
                                    <button type="button" class="icon-btn size-8 hover:text-danger" wire:click="delete({{ $category->id }})" wire:confirm="«{{ $category->name }}» حذف شود؟ تراکنش‌هایش بدون دسته می‌شوند." aria-label="حذف"><x-icon name="trash" class="size-4" /></button>
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-center text-[13px] text-muted">دسته‌ای وجود ندارد</li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</div>
