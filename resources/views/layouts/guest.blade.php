<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Airport Incident') }}</title>

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

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans">
        <div class="grid min-h-full lg:grid-cols-2">
            {{-- Brand / hero panel --}}
            <div class="relative hidden overflow-hidden bg-slate-950 lg:flex lg:flex-col lg:justify-between lg:p-12">
                {{-- Decorative gradients --}}
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-brand-600/30 blur-3xl"></div>
                    <div class="absolute -bottom-32 -right-16 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.06)_1px,transparent_0)] [background-size:24px_24px]"></div>
                </div>

                <div class="relative flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-lg">
                        <x-icon name="plane" class="h-6 w-6" />
                    </span>
                    <div class="leading-tight">
                        <div class="text-base font-bold text-white">{{ __('Airport Incident') }}</div>
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-400">{{ __('Operations Dashboard') }}</div>
                    </div>
                </div>

                <div class="relative max-w-md">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                        {{ __('Track every incident across the terminal in real time.') }}
                    </h1>
                    <p class="mt-4 text-sm leading-relaxed text-slate-400">
                        {{ __('A unified control surface for incidents, flights, gates and casualty conditions — built for fast, accurate response.') }}
                    </p>

                    <div class="mt-8 flex items-center gap-6 text-slate-300">
                        <div>
                            <div class="text-2xl font-bold text-white">24/7</div>
                            <div class="text-xs text-slate-400">{{ __('Monitoring') }}</div>
                        </div>
                        <div class="h-8 w-px bg-white/10"></div>
                        <div>
                            <div class="text-2xl font-bold text-white">Live</div>
                            <div class="text-xs text-slate-400">{{ __('Gate map') }}</div>
                        </div>
                        <div class="h-8 w-px bg-white/10"></div>
                        <div>
                            <div class="text-2xl font-bold text-white">A–K</div>
                            <div class="text-xs text-slate-400">{{ __('Terminals') }}</div>
                        </div>
                    </div>
                </div>

                <p class="relative text-xs text-slate-500">© {{ date('Y') }} {{ __('Airport Incident Dashboard') }}</p>
            </div>

            {{-- Form panel --}}
            <div class="flex items-center justify-center bg-slate-100 px-4 py-12 dark:bg-slate-950">
                <div class="w-full max-w-md">
                    {{-- Mobile brand --}}
                    <div class="mb-8 flex items-center justify-center gap-2.5 lg:hidden">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-sm">
                            <x-icon name="plane" class="h-5 w-5" />
                        </span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Airport Incident') }}</span>
                    </div>

                    <div class="card p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
