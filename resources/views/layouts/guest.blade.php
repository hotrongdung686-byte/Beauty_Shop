<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BeautyShop') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=karla:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-karla text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0 bg-cream-100">
            <a href="/" class="font-karla font-extrabold text-3xl tracking-tight text-ink">
                {{ config('app.name', 'BeautyShop') }}
            </a>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-white border border-cream-300 sm:rounded-sm">
                {{ $slot }}
            </div>

            <a href="/" class="mt-6 text-xs uppercase tracking-widest text-ink/40 hover:text-clay-600 transition">&larr; Về trang chủ</a>
        </div>
    </body>
</html>
