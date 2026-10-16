@props(['wide' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 overflow-hidden" style="background: #eef1e7;">

            {{-- Decorative radial glow (mirrors dashboard banner ::after) --}}
            <div class="pointer-events-none absolute rounded-full" style="width: 220px; height: 220px; right: -40px; top: -60px; background: rgba(255,255,255,0.05);"></div>
            <div class="pointer-events-none absolute rounded-full" style="width: 260px; height: 260px; left: -70px; bottom: -90px; background: rgba(255,255,255,0.04);"></div>

            <div class="relative flex flex-col items-center gap-1 mb-2">
                <a href="/" class="inline-flex flex-col items-center gap-1">
                    @php
                        $storeNameAuth = $storeSettings['store_name'] ?? config('app.name', 'Store');
                        $storeLogoAuth = $storeSettings['store_logo'] ?? null;
                    @endphp
                    @if ($storeLogoAuth)
                        <img src="{{ asset('storage/' . $storeLogoAuth) }}" alt="{{ $storeNameAuth }}"
                             class="object-contain h-12 w-auto">
                    @else
                        <x-application-logo class="w-16 h-16 fill-current text-indigo-500" />
                        <span class="text-lg font-bold text-gray-800 tracking-tight">{{ $storeNameAuth }}</span>
                    @endif
                </a>
            </div>

            <div class="relative w-full {{ ($wide ?? false) ? 'sm:max-w-full sm:px-10' : 'sm:max-w-md' }} mt-6 px-6 py-4 bg-white shadow-2xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
