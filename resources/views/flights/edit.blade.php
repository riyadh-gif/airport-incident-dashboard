<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-5">
        @include('partials.flash')

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Edit flight') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Update the flight record below.') }}</p>
        </div>

        <div class="card p-6 sm:p-8">
            <form method="POST" action="{{ route('flights.update', $flight) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="flight_no" :value="__('Flight no.')" />
                        <x-text-input id="flight_no" name="flight_no" type="text" class="block w-full" :value="old('flight_no', $flight->flight_no)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('flight_no')" />
                    </div>

                    <div>
                        <x-input-label for="occurred_at" :value="__('Date')" />
                        <x-text-input id="occurred_at" name="occurred_at" type="date" class="block w-full" :value="old('occurred_at', $flight->occurred_at->format('Y-m-d'))" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('occurred_at')" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="location" :value="__('Gate / location')" />
                        <x-text-input id="location" name="location" type="text" class="block w-full" :value="old('location', $flight->location)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('location')" />
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <x-primary-button>{{ __('Update') }}</x-primary-button>
                    <a href="{{ route('flights.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
