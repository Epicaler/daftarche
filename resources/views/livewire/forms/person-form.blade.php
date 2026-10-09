<div>
    <x-modal :title="$editingId ? 'ویرایش شخص' : 'افزودن شخص'" width="max-w-md">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label" for="person-name">نام</label>
                <input id="person-name" type="text" wire:model="name" class="input" placeholder="مثلاً: علی رضایی">
                @error('name') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="person-phone">شماره تماس <span class="font-normal text-muted">(اختیاری)</span></label>
                <input id="person-phone" type="tel" dir="ltr" wire:model="phone" class="input text-right" placeholder="0912...">
                @error('phone') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="person-note">یادداشت</label>
                <textarea id="person-note" wire:model="note" rows="2" class="input resize-none" placeholder="اختیاری"></textarea>
                @error('note') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled" wire:target="save">
                    <x-icon name="check" class="size-4" /> ذخیره
                </button>
                <button type="button" class="btn-ghost" x-on:click="open = false">انصراف</button>
            </div>
        </form>
    </x-modal>
</div>
