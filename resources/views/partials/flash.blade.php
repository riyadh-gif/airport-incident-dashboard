{{-- Session success/error are shown as toasts (see partials/toasts). --}}
{{-- This block surfaces validation errors inline above forms. --}}
@if ($errors->any())
    <div class="mb-4 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm dark:border-red-500/30 dark:bg-red-500/10">
        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300">
            <x-icon name="x-circle" class="h-4 w-4" />
        </span>
        <div>
            <p class="font-semibold text-red-800 dark:text-red-200">{{ __('Please fix the following:') }}</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5 text-red-700 dark:text-red-300/90">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
