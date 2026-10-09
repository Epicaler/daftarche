<!DOCTYPE html>
<html lang="fa" dir="rtl" data-currency="{{ config('app.currency') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a5c3b">
    <meta name="author" content="Hesam (t.me/MREpicaler)">
    <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-canvas font-sans text-ink antialiased">
    <div x-data="{ nav: false }" class="min-h-screen lg:p-4 xl:p-6">
        <div class="mx-auto flex min-h-screen max-w-[1640px] gap-3 bg-white p-2 sm:p-3 lg:min-h-[calc(100vh-2rem)] lg:rounded-[36px] lg:shadow-[0_30px_80px_-40px_rgb(20_28_23/0.35)] xl:min-h-[calc(100vh-3rem)]">

            {{-- Mobile backdrop --}}
            <div x-show="nav" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-ink/40 lg:hidden" x-on:click="nav = false"></div>

            @include('layouts.partials.sidebar')

            <div class="flex min-w-0 flex-1 flex-col gap-3">
                @include('layouts.partials.topbar')

                <main class="flex-1 rounded-[28px] bg-surface p-4 sm:p-6">
                    {{ $slot }}
                </main>

                @include('partials.footer')
            </div>
        </div>
    </div>

    <livewire:forms.transaction-form />
    <livewire:forms.debt-form />
    <livewire:forms.payment-form />
    <livewire:forms.person-form />

    {{-- Toasts --}}
    <div x-data="toasts" x-on:toast.window="add($event.detail)" class="pointer-events-none fixed bottom-5 left-1/2 z-[60] flex w-full max-w-sm -translate-x-1/2 flex-col gap-2 px-4">
        <template x-for="toast in items" :key="toast.id">
            <div x-transition class="pointer-events-auto flex items-center gap-3 rounded-2xl bg-brand-950 px-4 py-3 text-sm text-white shadow-float"
                 x-bind:class="toast.type === 'error' && '!bg-danger'">
                <x-icon name="check-circle" class="size-5 text-brand-300" />
                <span class="flex-1" x-text="toast.message"></span>
                <button type="button" class="text-white/60 hover:text-white" x-on:click="remove(toast.id)"><x-icon name="x" class="size-4" /></button>
            </div>
        </template>
    </div>
    @if (session('toast'))
        <div x-data x-init="$nextTick(() => $dispatch('toast', { message: @js(session('toast')) }))"></div>
    @endif

    @livewireScriptConfig
</body>
</html>
