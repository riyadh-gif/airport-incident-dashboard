@php
    $navItems = [
        ['route' => 'dashboard',        'active' => 'dashboard',    'label' => __('Dashboard'), 'icon' => 'dashboard'],
        ['route' => 'incidents.index',  'active' => 'incidents.*',  'label' => __('Incidents'), 'icon' => 'incidents'],
        ['route' => 'flights.index',    'active' => 'flights.*',    'label' => __('Flights'),   'icon' => 'flights'],
        ['route' => 'map',              'active' => 'map',          'label' => __('Map'),       'icon' => 'map'],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-in-out dark:border-slate-800 dark:bg-slate-900 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-slate-200 px-5 dark:border-slate-800">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
            <x-icon name="plane" class="h-5 w-5" />
        </span>
        <div class="leading-tight">
            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Airport Incident') }}</div>
            <div class="text-[11px] font-medium uppercase tracking-wide text-slate-400">{{ __('Dashboard') }}</div>
        </div>
        <button @click="sidebarOpen = false" class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden">
            <x-icon name="close" class="h-5 w-5" />
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div>
            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('Operations') }}</p>
            <div class="space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @class(['nav-item', 'nav-item-active' => request()->routeIs($item['active'])])>
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if (Auth::user()->isAdmin())
            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('Administration') }}</p>
                <div class="space-y-1">
                    <a href="{{ route('users.index') }}"
                       @class(['nav-item', 'nav-item-active' => request()->routeIs('users.*')])>
                        <x-icon name="users" class="h-5 w-5 shrink-0" />
                        <span>{{ __('Users') }}</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>

    {{-- Footer user card --}}
    <div class="border-t border-slate-200 p-3 dark:border-slate-800">
        <div class="flex items-center gap-3 rounded-xl px-2 py-2">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-xs font-bold text-white">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </span>
            <div class="min-w-0 leading-tight">
                <div class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ Auth::user()->name }}</div>
                <div class="truncate text-xs text-slate-400">NIP {{ Auth::user()->nip }}</div>
            </div>
        </div>
    </div>
</aside>
