<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-5">
        @include('partials.flash')

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Edit incident') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Update the casualty record below.') }}</p>
        </div>

        <div class="card p-6 sm:p-8">
            <form method="POST" action="{{ route('incidents.update', $incident) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $incident->name)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="condition" :value="__('Condition')" />
                        <select id="condition" name="condition" class="form-select">
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition->value }}" @selected(old('condition', $incident->condition) === $condition->value)>{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-1.5" :messages="$errors->get('condition')" />
                    </div>

                    <div>
                        <x-input-label for="hospital" :value="__('Hospital')" />
                        <x-text-input id="hospital" name="hospital" type="text" class="block w-full" :value="old('hospital', $incident->hospital)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('hospital')" />
                    </div>

                    <div>
                        <x-input-label for="occurred_at" :value="__('Date')" />
                        <x-text-input id="occurred_at" name="occurred_at" type="date" class="block w-full" :value="old('occurred_at', $incident->occurred_at->format('Y-m-d'))" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('occurred_at')" />
                    </div>

                    <div>
                        <x-input-label for="location" :value="__('Gate / location')" />
                        <x-text-input id="location" name="location" type="text" class="block w-full" :value="old('location', $incident->location)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('location')" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="flight_no" :value="__('Flight no.')" />
                        <x-text-input id="flight_no" name="flight_no" type="text" class="block w-full" :value="old('flight_no', $incident->flight_no)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('flight_no')" />
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <x-primary-button>{{ __('Update') }}</x-primary-button>
                    <a href="{{ route('incidents.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
