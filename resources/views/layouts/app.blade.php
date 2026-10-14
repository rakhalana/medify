<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Medify') }}</title>

        <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-canvas text-slate-900 selection:bg-primary-500 selection:text-white">
        <div class="min-h-screen flex flex-col bg-surface-canvas">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/90 backdrop-blur-md border-b border-border-subtle sticky top-0 z-30 shadow-card">
                    <x-container class="py-5">
                        {{ $header }}
                    </x-container>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        <x-toast />
    </body>
</html>
