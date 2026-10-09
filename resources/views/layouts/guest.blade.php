<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a5c3b">
    <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="relative flex min-h-screen items-center justify-center overflow-hidden bg-canvas p-4 font-sans text-ink antialiased">
    <div class="pointer-events-none absolute -top-40 -left-40 size-[480px] rounded-full bg-brand-300/30 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-32 -bottom-40 size-[420px] rounded-full bg-brand-600/20 blur-3xl"></div>
    <div class="relative flex w-full justify-center">{{ $slot }}</div>
    @livewireScriptConfig
</body>
</html>
