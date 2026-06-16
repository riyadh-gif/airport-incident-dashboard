{{-- Flash-message toasts (Alpine, auto-dismiss). Validation errors stay inline in forms. --}}
<div class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center gap-3 px-4 sm:items-end sm:px-6"
     x-data="{
        toasts: [],
        push(toast) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, ...toast });
            setTimeout(() => this.dismiss(id), 4500);
        },
        dismiss(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
     }"
     x-init="
        @if (session('success')) push({ type: 'success', message: @js(session('success')) }); @endif
        @if (session('error'))   push({ type: 'error',   message: @js(session('error')) });   @endif
        @if (session('status') && session('status') !== 'profile-updated')
            push({ type: 'success', message: @js(session('status')) });
        @endif
     "
     @toast.window="push($event.detail)">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-[-12px] sm:translate-y-0 sm:translate-x-4"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 translate-x-4"
             class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-2xl border bg-white p-4 shadow-soft-lg dark:bg-slate-800"
             :class="toast.type === 'error'
                ? 'border-red-200 dark:border-red-500/30'
                : 'border-emerald-200 dark:border-emerald-500/30'">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                  :class="toast.type === 'error'
                    ? 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300'
                    : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300'">
                <template x-if="toast.type === 'error'"><x-icon name="x-circle" class="h-4 w-4" /></template>
                <template x-if="toast.type !== 'error'"><x-icon name="check" class="h-4 w-4" /></template>
            </span>
            <p class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-200" x-text="toast.message"></p>
            <button @click="dismiss(toast.id)" class="text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200">
                <x-icon name="close" class="h-4 w-4" />
            </button>
        </div>
    </template>
</div>
