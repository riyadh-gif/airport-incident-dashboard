<x-app-layout>
    <div class="space-y-5">
        @include('partials.flash')

        {{-- Heading + date filter + summary --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Airport gate map') }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $totalIncidents }}</span> {{ __('incidents across') }}
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $activeGates }}</span> {{ __('gate(s) on') }}
                    <span class="font-medium">{{ $date }}</span>
                </p>
            </div>
            <form method="GET" action="{{ route('map') }}" class="flex items-end gap-2">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="calendar" class="h-4 w-4" />
                    </span>
                    <x-text-input id="date" name="date" type="date" class="block pl-9" :value="$date" />
                </div>
                <x-primary-button>{{ __('Apply') }}</x-primary-button>
                <a href="{{ route('map') }}" class="btn-secondary">{{ __('Today') }}</a>
            </form>
        </div>

        {{-- Map card --}}
        <div class="card p-6">
            {{-- Legend --}}
            <div class="mb-6 flex flex-wrap items-center gap-6 text-sm text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="inline-block h-3.5 w-3.5 rounded bg-slate-200 ring-1 ring-inset ring-slate-300 dark:bg-slate-800 dark:ring-slate-700"></span>
                    {{ __('No incidents') }}
                </div>
                <div class="flex items-center gap-2">
                    <span class="relative inline-flex h-3.5 w-3.5">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 animate-ping"></span>
                        <span class="relative inline-flex h-3.5 w-3.5 rounded-full bg-red-500"></span>
                    </span>
                    {{ __('Gate with incidents') }}
                </div>
            </div>

            {{-- Terminal schematic --}}
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    {{-- Terminal spine --}}
                    <div class="mb-5 flex items-center gap-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('Terminal') }}</span>
                        <div class="h-2 flex-1 rounded-full bg-gradient-to-r from-brand-200 via-brand-500 to-brand-200 dark:from-brand-900 dark:via-brand-500 dark:to-brand-900"></div>
                    </div>

                    <div class="space-y-2.5">
                        @foreach ($rows as $rowLetter => $gates)
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $rowLetter }}</div>
                                <div class="flex gap-1.5">
                                    @foreach ($gates as $gate)
                                        @php($hasIncidents = $gate['count'] > 0)
                                        <div
                                            class="relative flex h-9 w-9 items-center justify-center rounded-lg border text-[10px] font-mono font-medium transition-all duration-200 hover:scale-110 hover:z-10
                                                {{ $hasIncidents
                                                    ? 'border-red-300 bg-red-100 text-red-800 shadow-sm dark:border-red-500/40 dark:bg-red-500/20 dark:text-red-200'
                                                    : 'border-slate-200 bg-slate-50 text-slate-400 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-500' }}"
                                            title="{{ __('Gate') }} {{ $gate['code'] }}: {{ $gate['count'] }} {{ __('incident(s)') }}"
                                        >
                                            <span>{{ $gate['code'] }}</span>

                                            @if ($hasIncidents)
                                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4">
                                                    <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 animate-ping"></span>
                                                    <span class="relative inline-flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white shadow">{{ $gate['count'] }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500">
            {{ __('Synthetic terminal schematic — gate counts are computed from incident records (Eloquent groupBy location). Hover a gate to see its count.') }}
        </p>
    </div>
</x-app-layout>
