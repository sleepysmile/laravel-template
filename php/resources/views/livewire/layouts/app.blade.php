<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="/favicon.ico"/>
    @livewireStyles
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
<main>
    <nav>
        <a href="{{ route("home") }}" wire:navigate>Home</a>
        <a href="{{ route("test") }}" wire:navigate>Test</a>
    </nav>

    {{ $slot }}
</main>
@livewireScripts
@livewireScriptConfig
</body>
</html>
