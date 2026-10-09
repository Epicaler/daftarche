@props(['label', 'value', 'hint' => null, 'icon' => 'arrow-up-left', 'highlight' => false, 'href' => null, 'unit' => null, 'trend' => null, 'trendGood' => true])
@php
    $unit ??= config('app.currency');
    $trendUp = $trend !== null && $trend > 0;
    $good = $trend === null ? null : ($trend === 0 || $trendUp === $trendGood);
@endphp

<div @class([
    'relative overflow-hidden rounded-3xl p-5 shadow-card',
    'bg-linear-to-br from-brand-600 via-brand-700 to-brand-900 text-white' => $highlight,
    'bg-white text-ink' => ! $highlight,
])>
    @if ($highlight)
        <svg class="pointer-events-none absolute -bottom-10 -left-10 size-48 opacity-20" viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="48" fill="none" stroke="white" stroke-width="1"/><circle cx="50" cy="50" r="34" fill="none" stroke="white" stroke-width="1"/><circle cx="50" cy="50" r="20" fill="none" stroke="white" stroke-width="1"/></svg>
    @endif

    <div class="relative flex items-start justify-between gap-3">
        <p class="text-[17px] font-medium">{{ $label }}</p>
        @if ($href)
            <a href="{{ $href }}" wire:navigate @class(['flex size-9 shrink-0 items-center justify-center rounded-full transition', 'bg-white text-brand-900 hover:scale-105' => $highlight, 'border border-ink/70 hover:bg-ink hover:text-white' => ! $highlight]) aria-label="{{ $label }}">
                <x-icon :name="$icon" class="size-4" />
            </a>
        @else
            <span @class(['flex size-9 shrink-0 items-center justify-center rounded-full', 'bg-white text-brand-900' => $highlight, 'border border-ink/70' => ! $highlight])>
                <x-icon :name="$icon" class="size-4" />
            </span>
        @endif
    </div>

    <p class="relative mt-4 flex items-baseline gap-1.5">
        <span class="truncate text-[28px] leading-none font-bold tracking-tight xl:text-[32px]">{{ $value }}</span>
        <span @class(['text-xs', 'text-white/70' => $highlight, 'text-muted' => ! $highlight])>{{ $unit }}</span>
    </p>

    <div @class(['relative mt-4 flex items-center gap-2 text-[13px]', 'text-brand-100' => $highlight, 'text-brand-700' => ! $highlight])>
        @if ($trend !== null)
            <span @class([
                'inline-flex items-center gap-0.5 rounded-md border px-1.5 py-px text-[11px] font-semibold',
                'border-white/40' => $highlight,
                'border-brand-300 text-brand-700' => ! $highlight && $good,
                'border-red-200 text-danger' => ! $highlight && ! $good,
            ])>
                {{ abs($trend) }}٪
                @if ($trend !== 0)
                    <x-icon :name="$trendUp ? 'arrow-up' : 'arrow-down'" class="size-3" />
                @endif
            </span>
        @endif
        @if ($hint)
            <span @class(['truncate', 'text-muted' => ! $highlight && $trend === null])>{{ $hint }}</span>
        @endif
    </div>
</div>
