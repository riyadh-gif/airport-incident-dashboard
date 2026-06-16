<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Welcome back') }}</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Sign in with your NIP to access the dashboard.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- NIP -->
        <div>
            <x-input-label for="nip" :value="__('NIP')" />
            <x-text-input id="nip" class="block w-full" type="text" name="nip" :value="old('nip')" required autofocus autocomplete="username" placeholder="{{ __('Your employee NIP') }}" />
            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:focus:ring-brand-600" name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <x-primary-button class="w-full">
            {{ __('Log in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
