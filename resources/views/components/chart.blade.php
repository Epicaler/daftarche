@props(['config'])
@php
    $donut = $config['type'] === 'donut';
    $height = $config['height'] ?? ($donut ? 280 : 300);
@endphp

{{-- A new key whenever the data changes makes Livewire swap the chart instead of morphing ApexCharts' SVG --}}
<div wire:key="chart-{{ md5(json_encode($config)) }}" {{ $attributes->merge(['class' => '@container']) }}>
    <div wire:ignore x-data="apexChart(@js($config))" @class(['flex flex-col', 'gap-4' => $donut, 'gap-1' => ! $donut])>
        @php
            $legend = <<<'HTML'
                <template x-for="(item, i) in legend" :key="item.name">
                    <li>
                        <button type="button" x-on:click="toggle(i)" x-bind:aria-pressed="(!item.hidden).toString()"
                                x-bind:title="item.hidden ? 'نمایش' : 'پنهان کردن'"
                                x-bind:class="item.hidden && 'opacity-40'"
                                class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-1.5 py-1 text-right text-[13px] text-ink transition hover:bg-surface">
                            <span class="size-2.5 shrink-0 rounded-full" x-bind:style="`background:${item.color}`"></span>
                            <span class="min-w-0 flex-1 truncate" x-bind:class="item.hidden && 'line-through'" x-text="item.name"></span>
                            <template x-if="item.value">
                                <span class="flex shrink-0 items-center gap-3">
                                    <span class="w-24 text-right text-muted" x-text="item.value"></span>
                                    <span class="w-9 text-left font-semibold" x-text="item.percent"></span>
                                </span>
                            </template>
                        </button>
                    </li>
                </template>
            HTML;
        @endphp

        @if (! $donut)
            <ul class="flex flex-wrap items-center gap-x-3" aria-label="راهنمای نمودار">{!! $legend !!}</ul>
        @endif

        <div x-ref="canvas" class="-mx-2" style="min-height: {{ $height }}px"></div>

        @if ($donut)
            <ul class="grid gap-x-6 gap-y-0.5 @2xl:grid-cols-2" aria-label="راهنمای نمودار">{!! $legend !!}</ul>
        @endif
    </div>
</div>
