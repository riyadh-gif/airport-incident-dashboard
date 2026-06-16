<x-app-layout>
    <div class="space-y-5">
        @include('partials.flash')

        {{-- Heading + date filter + summary --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Incident live map') }}</h1>
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
            {{-- Legend: marker scale + per-condition colors --}}
            <div class="mb-5 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-slate-400/30 ring-1 ring-inset ring-slate-400/50"></span>
                    <span class="inline-flex h-2.5 w-2.5 items-center justify-center rounded-full bg-slate-400/30 ring-1 ring-inset ring-slate-400/50"></span>
                    {{ __('Marker size ∝ incident count') }}
                </div>
                @foreach ($conditionMeta as $meta)
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-3 w-3 rounded-full" style="background: {{ $meta['color'] }}"></span>
                        {{ $meta['label'] }}
                    </div>
                @endforeach
            </div>

            {{-- Leaflet map container --}}
            <div
                id="incident-map"
                class="h-[28rem] w-full overflow-hidden rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 sm:h-[34rem]"
                role="region"
                aria-label="{{ __('Interactive incident map') }}"
            ></div>

            {{-- Map payload consumed by resources/js/incident-map.js --}}
            @php
                $incidentMapPayload = [
                    'config' => $mapConfig,
                    'gates' => $gates,
                    'conditionMeta' => $conditionMeta,
                ];
            @endphp
            <script type="application/json" id="incident-map-data">
                @json($incidentMapPayload)
            </script>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500">
            {{ __('Synthetic geographic map — gate coordinates are deterministic synthetic offsets around a fictional airport center; counts are computed from incident records (Eloquent). Tiles © OpenStreetMap contributors, © CARTO.') }}
        </p>
    </div>

    @push('styles')
        <style>
            /* Subtle center "terminal" label rendered as a Leaflet divIcon. */
            .incident-map-airport-label span {
                display: inline-block;
                padding: 2px 8px;
                font-size: 11px;
                font-weight: 600;
                letter-spacing: 0.02em;
                white-space: nowrap;
                color: #475569;
                background: rgba(255, 255, 255, 0.75);
                border-radius: 9999px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            }

            .dark .incident-map-airport-label span {
                color: #cbd5e1;
                background: rgba(15, 23, 42, 0.7);
            }
        </style>
    @endpush
</x-app-layout>
