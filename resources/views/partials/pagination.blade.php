@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3" aria-label="صفحه‌بندی">
        <p class="text-[13px] text-muted">
            <span class="hidden sm:inline">{{ $paginator->firstItem() }} تا {{ $paginator->lastItem() }} از {{ $paginator->total() }} مورد</span>
            <span class="sm:hidden">صفحه {{ $paginator->currentPage() }} از {{ $paginator->lastPage() }}</span>
        </p>
        <div class="flex items-center gap-1">
            <button type="button" class="icon-btn bg-white disabled:opacity-40" wire:click="previousPage('{{ $paginator->getPageName() }}')" @disabled($paginator->onFirstPage()) aria-label="قبلی">
                <x-icon name="chevron-right" class="size-4" />
            </button>
            {{-- Page numbers don't fit on phones; previous/next is enough there --}}
            <div class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-muted">…</span>
                @else
                    @foreach ($element as $page => $url)
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                @class(['size-9 cursor-pointer rounded-full text-[13px] font-medium transition', 'bg-brand-800 text-white' => $page == $paginator->currentPage(), 'bg-white hover:bg-line' => $page != $paginator->currentPage()])>{{ $page }}</button>
                    @endforeach
                @endif
            @endforeach
            </div>
            <button type="button" class="icon-btn bg-white disabled:opacity-40" wire:click="nextPage('{{ $paginator->getPageName() }}')" @disabled(! $paginator->hasMorePages()) aria-label="بعدی">
                <x-icon name="chevron-left" class="size-4" />
            </button>
        </div>
    </nav>
@endif
