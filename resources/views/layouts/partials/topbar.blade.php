<header class="flex items-center gap-3 rounded-[28px] bg-surface px-3 py-3 sm:px-5">
    <button type="button" class="icon-btn bg-white lg:hidden" x-on:click="nav = true" aria-label="منو">
        <x-icon name="menu" />
    </button>

    <form x-data="{ q: '' }" x-on:submit.prevent="Livewire.navigate('{{ route('transactions') }}?search=' + encodeURIComponent(q))"
          x-on:keydown.ctrl.k.window.prevent="$refs.q.focus()" x-on:keydown.meta.k.window.prevent="$refs.q.focus()"
          class="relative w-full max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute top-1/2 right-4 size-[18px] -translate-y-1/2 text-muted" />
        <input x-ref="q" x-model="q" type="search" placeholder="جستجوی تراکنش..."
               class="w-full rounded-full bg-white py-3 pr-11 pl-16 text-sm outline-none placeholder:text-muted focus:ring-4 focus:ring-brand-100">
        <kbd class="absolute top-1/2 left-3 hidden -translate-y-1/2 rounded-md bg-surface px-2 py-0.5 font-sans text-[11px] text-muted sm:block" dir="ltr">Ctrl K</kbd>
    </form>

    <div class="mr-auto flex items-center gap-2 sm:gap-3">
        <a href="{{ route('debts') }}" wire:navigate class="hidden size-11 items-center justify-center rounded-full bg-white text-ink transition hover:text-brand-700 sm:flex" aria-label="طلب و بدهی">
            <x-icon name="mail" />
        </a>

        <div x-data="{ open: false }" class="relative" x-on:click.outside="open = false">
            <button type="button" x-on:click="open = !open" class="relative flex size-11 cursor-pointer items-center justify-center rounded-full bg-white text-ink transition hover:text-brand-700" aria-label="یادآوری‌ها">
                <x-icon name="bell" />
                @if ($alerts->isNotEmpty())
                    <span class="absolute top-2.5 right-3 size-2 rounded-full bg-danger ring-2 ring-white"></span>
                @endif
            </button>
            <div x-show="open" x-cloak x-transition.origin.top.left
                 class="absolute top-full left-0 z-40 mt-2 w-80 rounded-3xl border border-line bg-white p-2 shadow-float">
                <p class="px-3 pt-2 pb-3 text-sm font-semibold">سررسیدهای نزدیک</p>
                @forelse ($alerts as $debt)
                    <a href="{{ route('people.show', $debt->person_id) }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-2.5 hover:bg-surface">
                        <span @class(['flex size-9 items-center justify-center rounded-full', 'bg-brand-50 text-brand-700' => $debt->isReceivable(), 'bg-orange-50 text-expense' => ! $debt->isReceivable()])>
                            <x-icon :name="$debt->isReceivable() ? 'arrow-down-left' : 'arrow-up-right'" class="size-4" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-[13px] font-medium">{{ $debt->isReceivable() ? 'طلب از' : 'بدهی به' }} {{ $debt->person->name }}</span>
                            <span @class(['block text-[11px]', 'text-danger' => $debt->due_on->isPast(), 'text-muted' => ! $debt->due_on->isPast()])>
                                {{ $debt->due_on->isPast() ? 'سررسید گذشته' : 'سررسید' }}، {{ jdate_format($debt->due_on) }}
                            </span>
                        </span>
                        <span class="text-[13px] font-semibold">{{ money_short($debt->remaining) }}</span>
                    </a>
                @empty
                    <p class="px-3 pb-4 text-[13px] text-muted">سررسید نزدیکی نداری 🎉</p>
                @endforelse
            </div>
        </div>

        <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 rounded-full py-1 pr-1 pl-2 transition hover:bg-white">
            <span class="flex size-11 items-center justify-center rounded-full bg-linear-to-br from-brand-200 to-brand-400 text-base font-bold text-brand-950">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </span>
            <span class="hidden leading-tight md:block">
                <span class="block text-[15px] font-semibold">{{ auth()->user()->name }}</span>
                <span class="block text-[13px] text-muted">{{ auth()->user()->email }}</span>
            </span>
        </a>
    </div>
</header>
