@props(['placeholder' => '0'])

<div x-data="moneyInput" x-modelable="value" {{ $attributes->whereStartsWith('wire:model') }}>
    <div class="relative">
        <input type="text" inputmode="numeric" dir="ltr" autocomplete="off"
               class="input pl-16 text-left text-base font-semibold tracking-wide"
               placeholder="{{ $placeholder }}" x-bind:value="display" x-on:input="onInput($event)"
               {{ $attributes->whereDoesntStartWith('wire:model') }}>
        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-xs text-muted">{{ config('app.currency') }}</span>
    </div>
    <p class="mt-1.5 h-4 text-xs text-brand-600" x-text="hint"></p>
</div>
