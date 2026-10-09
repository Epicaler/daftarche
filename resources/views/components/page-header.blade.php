@props(['title', 'subtitle' => null])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold tracking-tight sm:text-[40px] sm:leading-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1.5 text-[15px] text-muted">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-3">{{ $slot }}</div>
    @endif
</div>
