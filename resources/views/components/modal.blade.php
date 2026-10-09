@props(['title', 'subtitle' => null, 'width' => 'max-w-lg'])

<div x-data="{ open: $wire.entangle('open').live }" x-show="open" x-cloak
     x-on:keydown.escape.window="open = false"
     class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/40 backdrop-blur-[2px]" x-on:click="open = false"></div>

    <div x-show="open" x-trap.noscroll="open"
         x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95" x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="relative flex max-h-[92vh] w-full {{ $width }} flex-col rounded-t-[28px] bg-white shadow-float sm:rounded-[28px]">
        <div class="flex items-start justify-between gap-4 px-6 pt-6">
            <div>
                <h2 class="text-lg font-bold text-ink">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="mt-1 text-[13px] text-muted">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" class="icon-btn -mt-1 -ml-2" x-on:click="open = false" aria-label="بستن">
                <x-icon name="x" />
            </button>
        </div>

        <div class="scrollbar-thin overflow-y-auto px-6 pt-5 pb-6">
            {{ $slot }}
        </div>
    </div>
</div>
