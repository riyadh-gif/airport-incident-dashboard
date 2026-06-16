@props([
    'label',
    'value',
    'icon' => 'dashboard',
    'tone' => 'brand',
    'mono' => false,
])

@php
    $tones = [
        'brand'   => ['glow' => 'bg-brand-500/20',   'icon' => 'text-brand-600 ring-brand-500/20 dark:text-brand-300'],
        'sky'     => ['glow' => 'bg-sky-500/20',      'icon' => 'text-sky-600 ring-sky-500/20 dark:text-sky-300'],
        'violet'  => ['glow' => 'bg-violet-500/20',   'icon' => 'text-violet-600 ring-violet-500/20 dark:text-violet-300'],
        'emerald' => ['glow' => 'bg-emerald-500/20',  'icon' => 'text-emerald-600 ring-emerald-500/20 dark:text-emerald-300'],
    ];
    $t = $tones[$tone] ?? $tones['brand'];
@endphp

<div class="group card relative overflow-hidden p-5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-soft-lg">
    <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full {{ $t['glow'] }} blur-2xl"></div>
    <div class="relative flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p @class([
                'mt-2 truncate text-3xl font-bold text-slate-900 dark:text-white',
                'font-mono text-2xl' => $mono,
            ])>{{ $value }}</p>
        </div>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white ring-1 ring-inset {{ $t['icon'] }} dark:bg-slate-800">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
</div>
