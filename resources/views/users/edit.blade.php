<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-5">
        @include('partials.flash')

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Edit user') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Update account details and role.') }}</p>
        </div>

        <div class="card p-6 sm:p-8">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus />
                    <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="nip" :value="__('NIP')" />
                        <x-text-input id="nip" name="nip" type="text" class="block w-full" :value="old('nip', $user->nip)" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('nip')" />
                    </div>

                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" class="form-select">
                            <option value="operator" @selected(old('role', $user->role) === 'operator')>Operator</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        </select>
                        <x-input-error class="mt-1.5" :messages="$errors->get('role')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="password" :value="__('New password')" />
                        <x-text-input id="password" name="password" type="password" class="block w-full" autocomplete="new-password" placeholder="{{ __('Leave blank to keep current') }}" />
                        <x-input-error class="mt-1.5" :messages="$errors->get('password')" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm new password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <x-primary-button>{{ __('Update') }}</x-primary-button>
                    <a href="{{ route('users.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
