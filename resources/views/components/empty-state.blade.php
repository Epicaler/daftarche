@props(['icon' => 'inbox', 'title', 'text' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-line bg-white/50 px-6 py-12 text-center']) }}>
    <span class="flex size-14 items-center justify-center rounded-full bg-white text-brand-600 shadow-card"><x-icon :name="$icon" class="size-6" /></span>
    <p class="mt-4 font-semibold">{{ $title }}</p>
    @if ($text)
        <p class="mt-1 max-w-xs text-[13px] text-muted">{{ $text }}</p>
    @endif
    {{ $slot }}
</div>
