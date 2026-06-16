<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-5">
        @include('partials.flash')

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Profile') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Manage your account information and password.') }}</p>
        </div>

        <div class="card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
