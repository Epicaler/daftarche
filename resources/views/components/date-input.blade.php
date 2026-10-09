@props(['placeholder' => '1405/01/01', 'clearable' => false])

<div x-data="jdatepicker" x-modelable="value" {{ $attributes->whereStartsWith('wire:model') }}
     x-on:click.outside="open = false" x-on:keydown.escape.stop="open = false">
    <div class="relative">
        <input type="text" dir="ltr" autocomplete="off" class="input pl-10 text-right" placeholder="{{ $placeholder }}"
               x-bind:value="value" x-on:change="normalize($event)" x-on:focus="open = true">
        <button type="button" class="absolute inset-y-0 left-3 flex items-center text-muted hover:text-brand-600" x-on:click="open = !open" tabindex="-1" aria-label="تقویم">
            <x-icon name="calendar" class="size-[18px]" />
        </button>
    </div>

    {{-- In normal flow (not absolute) so it never gets clipped by a scrolling modal --}}
    <div x-show="open" x-cloak x-collapse
         class="mt-2 w-full max-w-80 rounded-3xl border border-line bg-white p-4 shadow-[0_12px_32px_-16px_rgb(20_28_23/0.3)]">
        <div class="mb-3 flex items-center justify-between">
            <button type="button" class="icon-btn size-8" x-on:click="shift(-1)" aria-label="ماه قبل"><x-icon name="chevron-right" class="size-4" /></button>
            <span class="text-sm font-semibold" x-text="title"></span>
            <button type="button" class="icon-btn size-8" x-on:click="shift(1)" aria-label="ماه بعد"><x-icon name="chevron-left" class="size-4" /></button>
        </div>

        <div class="grid grid-cols-7 gap-1 text-center">
            <template x-for="w in weekdays" :key="w">
                <span class="pb-1 text-[11px] font-medium text-muted" x-text="w"></span>
            </template>
            <template x-for="cell in cells" :key="cell.key">
                <div class="aspect-square">
                    <button type="button" x-show="cell.day !== null" x-on:click="pick(cell.day)"
                            class="size-full rounded-full text-[13px] transition"
                            x-bind:class="cell.selected ? 'bg-brand-700 text-white font-semibold' : (cell.today ? 'bg-brand-50 text-brand-700 font-semibold ring-1 ring-brand-200' : (cell.friday ? 'text-danger hover:bg-surface' : 'text-ink hover:bg-surface'))"
                            x-text="cell.day"></button>
                </div>
            </template>
        </div>

        <div class="mt-3 flex items-center justify-between border-t border-line pt-3">
            <button type="button" class="text-[13px] font-medium text-brand-700 hover:underline" x-on:click="pickToday()">امروز</button>
            @if ($clearable)
                <button type="button" class="text-[13px] text-muted hover:text-ink" x-on:click="value = ''; open = false">پاک کردن</button>
            @endif
        </div>
    </div>
</div>
