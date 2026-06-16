@php
    // Route-derived page title + breadcrumb crumbs.
    $crumbs = match (true) {
        request()->routeIs('dashboard')   => ['Dashboard'],
        request()->routeIs('incidents.create') => ['Incidents', 'Add'],
        request()->routeIs('incidents.edit')   => ['Incidents', 'Edit'],
        request()->routeIs('incidents.*') => ['Incidents'],
        request()->routeIs('flights.create')   => ['Flights', 'Add'],
        request()->routeIs('flights.edit')     => ['Flights', 'Edit'],
        request()->routeIs('flights.*')   => ['Flights'],
        request()->routeIs('map')         => ['Gate Map'],
        request()->routeIs('users.create')     => ['Users', 'Add'],
        request()->routeIs('users.edit')       => ['Users', 'Edit'],
        request()->routeIs('users.*')     => ['Users'],
        request()->routeIs('profile.*')   => ['Profile'],
        default                           => ['Airport Incident'],
    };
@endphp

<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/80 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/80">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
        {{-- Mobile menu toggle --}}
        <button @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden">
            <x-icon name="menu" class="h-6 w-6" />
        </button>

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm" aria-label="Breadcrumb">
            @foreach ($crumbs as $i => $crumb)
                @if ($i > 0)
                    <span class="text-slate-300 dark:text-slate-600">/</span>
                @endif
                <span @class([
                    'font-semibold text-slate-900 dark:text-white' => $loop->last,
                    'text-slate-400' => ! $loop->last,
                ])>{{ __($crumb) }}</span>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-2">
            {{-- Dark-mode toggle --}}
            <button
                @click="window.darkMode.toggle()"
                x-data="{ dark: document.documentElement.classList.contains('dark') }"
                @theme-changed.window="dark = $event.detail.dark"
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200"
                :title="dark ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
                aria-label="{{ __('Toggle dark mode') }}"
            >
                <span x-show="!dark"><x-icon name="moon" class="h-5 w-5" /></span>
                <span x-show="dark" style="display:none"><x-icon name="sun" class="h-5 w-5" /></span>
            </button>

            {{-- User menu --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                <button @click="open = !open" type="button"
                        class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-2.5 text-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-[11px] font-bold text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </span>
                    <span class="hidden text-left leading-tight sm:block">
                        <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">{{ Auth::user()->name }}</span>
                        <span class="block text-[11px] text-slate-400">NIP {{ Auth::user()->nip }}</span>
                    </span>
                    <x-icon name="chevron" class="h-4 w-4 text-slate-400" />
                </button>

                <div x-show="open" style="display:none"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute right-0 mt-2 w-56 origin-top-right overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-soft-lg dark:border-slate-700 dark:bg-slate-800">
                    <div class="border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-slate-400">NIP {{ Auth::user()->nip }} · {{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700">
                        <x-icon name="user" class="h-4 w-4 text-slate-400" /> {{ __('Profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                            <x-icon name="logout" class="h-4 w-4" /> {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
