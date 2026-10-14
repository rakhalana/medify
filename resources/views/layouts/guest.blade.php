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
    <body class="font-sans text-slate-900 antialiased bg-surface-canvas selection:bg-primary-500 selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 pb-12 px-4 sm:px-6">
            <div class="mb-6 flex flex-col items-center">
                <a href="/" class="flex items-center gap-3 transition-transform hover:scale-105 duration-200">
                    <x-application-logo class="w-12 h-12 text-primary-600" />
                    <span class="text-2xl font-bold tracking-tight text-slate-900 font-sans">
                        Medi<span class="text-primary-600">fy</span>
                    </span>
                </a>
                <p class="text-xs text-slate-500 mt-1 font-medium">Marketplace Alat Kesehatan Terpercaya</p>
            </div>

            <div class="w-full sm:max-w-md bg-white border border-border-subtle shadow-card rounded-xl p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>

        <x-toast />
    </body>
</html>
