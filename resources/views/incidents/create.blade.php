<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-5">
        @include('partials.flash')

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Add incidents') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Add one or more casualty rows, then submit them all at once.') }}</p>
        </div>

        <div class="card p-6 sm:p-8"
             x-data="{
                 rows: [{ name: '', condition: '{{ $conditions[0]->value }}', hospital: '', occurred_at: '{{ now()->toDateString() }}', location: '', flight_no: '' }],
                 add() { this.rows.push({ name: '', condition: '{{ $conditions[0]->value }}', hospital: '', occurred_at: '{{ now()->toDateString() }}', location: '', flight_no: '' }); },
                 remove(i) { if (this.rows.length > 1) this.rows.splice(i, 1); }
             }">
            <form method="POST" action="{{ route('incidents.store') }}">
                @csrf

                <div class="space-y-4">
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="relative rounded-2xl border border-slate-200 bg-slate-50/50 p-4 transition dark:border-slate-800 dark:bg-slate-800/30">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300" x-text="i + 1"></span>
                                    {{ __('Casualty') }}
                                </span>
                                <button type="button" @click="remove(i)" x-show="rows.length > 1"
                                        class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                    <x-icon name="trash" class="h-3.5 w-3.5" /> {{ __('Remove') }}
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                                <div class="md:col-span-2">
                                    <label class="form-label">{{ __('Name') }}</label>
                                    <input type="text" :name="`rows[${i}][name]`" x-model="row.name" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Condition') }}</label>
                                    <select :name="`rows[${i}][condition]`" x-model="row.condition" class="form-select" required>
                                        @foreach ($conditions as $condition)
                                            <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Hospital') }}</label>
                                    <input type="text" :name="`rows[${i}][hospital]`" x-model="row.hospital" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Date') }}</label>
                                    <input type="date" :name="`rows[${i}][occurred_at]`" x-model="row.occurred_at" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Gate') }}</label>
                                    <input type="text" :name="`rows[${i}][location]`" x-model="row.location" placeholder="A12" class="form-input" required>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="form-label">{{ __('Flight') }}</label>
                                    <input type="text" :name="`rows[${i}][flight_no]`" x-model="row.flight_no" placeholder="GA-431" class="form-input" required>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="add()"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 py-3 text-sm font-medium text-slate-500 transition hover:border-brand-400 hover:bg-brand-50/50 hover:text-brand-600 dark:border-slate-700 dark:text-slate-400 dark:hover:border-brand-500/50 dark:hover:bg-brand-500/5 dark:hover:text-brand-300">
                    <x-icon name="plus" class="h-4 w-4" /> {{ __('Add another row') }}
                </button>

                <div class="mt-5 flex items-center gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <x-primary-button>
                        <x-icon name="check" class="h-4 w-4" />
                        {{ __('Save all') }} <span x-text="`(${rows.length})`"></span>
                    </x-primary-button>
                    <a href="{{ route('incidents.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
