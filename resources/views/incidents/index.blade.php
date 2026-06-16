<x-app-layout>
    <div class="space-y-5">
        @include('partials.flash')

        {{-- Heading --}}
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Incidents') }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Casualty incidents across all gates.') }}</p>
            </div>
            <a href="{{ route('incidents.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" /> {{ __('Add incidents') }}
            </a>
        </div>

        {{-- Table card --}}
        <div class="card overflow-hidden">
            {{-- Search toolbar --}}
            <div class="border-b border-slate-200 p-4 dark:border-slate-800">
                <form method="GET" action="{{ route('incidents.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="relative min-w-[220px] flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <x-text-input id="search" name="search" type="text" class="block w-full pl-9" :value="$search" placeholder="{{ __('Search name, gate, flight, hospital, condition…') }}" />
                    </div>
                    <x-primary-button>{{ __('Search') }}</x-primary-button>
                    @if ($search !== '')
                        <a href="{{ route('incidents.index') }}" class="btn-secondary">{{ __('Clear') }}</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Condition') }}</th>
                            <th class="px-4 py-3">{{ __('Hospital') }}</th>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3">{{ __('Gate') }}</th>
                            <th class="px-4 py-3">{{ __('Flight') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                        @forelse ($incidents as $incident)
                            @php($cond = \App\Enums\IncidentCondition::tryFromValue($incident->condition))
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $incident->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $cond?->badgeClasses() ?? 'bg-slate-100 text-slate-600 ring-slate-300/50 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700' }}">
                                        {{ $cond?->label() ?? ucfirst($incident->condition) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $incident->hospital }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $incident->occurred_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $incident->location }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $incident->flight_no }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('incidents.edit', $incident) }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 transition hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10">
                                            <x-icon name="edit" class="h-3.5 w-3.5" /> {{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('incidents.destroy', $incident) }}" onsubmit="return confirm('Delete this incident?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <x-icon name="trash" class="h-3.5 w-3.5" /> {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-16">
                                    <div class="flex flex-col items-center text-center">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                                            <x-icon name="incidents" class="h-6 w-6" />
                                        </span>
                                        <p class="mt-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('No incidents found') }}</p>
                                        <p class="text-sm text-slate-400">{{ $search !== '' ? __('Try a different search term.') : __('Add your first incident to get started.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $incidents->links() }}</div>
    </div>
</x-app-layout>
