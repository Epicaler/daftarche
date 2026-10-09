<!DOCTYPE html>
<html lang="fa" dir="rtl">
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
<body class="relative flex min-h-screen items-center justify-center overflow-hidden bg-canvas p-4 font-sans text-ink antialiased">
    {{-- Decorative glows, clipped so they never widen the page (an RTL page would scroll sideways) --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 -left-40 size-[480px] rounded-full bg-brand-300/30 blur-3xl"></div>
        <div class="absolute -right-32 -bottom-40 size-[420px] rounded-full bg-brand-600/20 blur-3xl"></div>
    </div>
    <div class="relative flex w-full flex-col items-center gap-6">
        {{ $slot }}
        @include('partials.footer')
    </div>
    @livewireScriptConfig
</body>
</html>
