<x-app-layout>
    <div class="space-y-6">
        @include('partials.flash')

        {{-- Page heading + date filter --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Dashboard') }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Incident overview for') }} <span class="font-medium text-slate-700 dark:text-slate-300">{{ $date }}</span></p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-2">
                <div>
                    <x-input-label for="date" :value="__('Date')" class="sr-only" />
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-icon name="calendar" class="h-4 w-4" />
                        </span>
                        <x-text-input id="date" name="date" type="date" class="block pl-9" :value="$date" />
                    </div>
                </div>
                <x-primary-button>{{ __('Apply') }}</x-primary-button>
                <a href="{{ route('dashboard') }}" class="btn-secondary">{{ __('Today') }}</a>
            </form>
        </div>

        {{-- Metric cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-metric-card
                :label="__('Incidents (selected date)')"
                :value="$incidentsOnDate"
                icon="incidents"
                tone="brand" />
            <x-metric-card
                :label="__('Latest flight no.')"
                :value="$latestFlightNo ?? '—'"
                icon="flights"
                tone="sky"
                mono />
            <x-metric-card
                :label="__('Selected date')"
                :value="$date"
                icon="calendar"
                tone="violet" />
            <x-metric-card
                :label="__('Total this month')"
                :value="$periodTotal"
                icon="dashboard"
                tone="emerald" />
        </div>

        {{-- Condition breakdown chart --}}
        <div class="card p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ __('Incident condition breakdown') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $date }}</p>
                </div>
                <span class="badge bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-500/10 dark:text-brand-300 dark:ring-brand-500/30">
                    {{ array_sum($chartData) }} {{ __('total') }}
                </span>
            </div>

            @if (array_sum($chartData) === 0)
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 py-14 text-center dark:border-slate-700">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                        <x-icon name="info" class="h-6 w-6" />
                    </span>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ __('No incidents recorded on this date.') }}</p>
                </div>
            @else
                <div class="relative mx-auto" style="max-width: 420px; height: 320px;">
                    <canvas id="incident-condition-chart"></canvas>
                </div>
            @endif

            {{-- Chart data passed from the controller (Eloquent groupBy). --}}
            <script type="application/json" id="dashboard-chart-data">
                @json(['labels' => $chartLabels, 'data' => $chartData, 'colors' => $chartColors])
            </script>
        </div>

        {{-- Quick actions --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('incidents.index') }}" class="btn-secondary">
                <x-icon name="incidents" class="h-4 w-4" /> {{ __('View incidents') }}
            </a>
            <a href="{{ route('map') }}" class="btn-primary">
                <x-icon name="map" class="h-4 w-4" /> {{ __('Gate map') }}
            </a>
        </div>
    </div>
</x-app-layout>
