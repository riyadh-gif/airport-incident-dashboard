<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? null) ? $title.' · ' : '' }}{{ config('app.name', 'Airport Incident') }}</title>

        {{-- No-flash theme: apply the stored/system theme before paint. --}}
        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('theme');
                    var dark = stored ? stored === 'dark'
                        : window.matchMedia('(prefers-color-scheme: dark)').matches;
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="h-full font-sans">
        <div x-data="{ sidebarOpen: false }" class="min-h-full">
            {{-- Mobile sidebar backdrop --}}
            <div x-show="sidebarOpen"
                 x-transition.opacity
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm lg:hidden"
                 style="display:none"></div>

            @include('layouts.navigation')

            {{-- Main column (offset for the fixed sidebar on desktop) --}}
            <div class="lg:pl-64">
                @include('partials.topbar')

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl">
                        {{ $slot }}
                    </div>
                </main>
            </div>

            @include('partials.toasts')
        </div>
    </body>
</html>
