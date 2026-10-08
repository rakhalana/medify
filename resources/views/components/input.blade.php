@props([
    'disabled' => false,
    'error' => false,
])

@php
$baseClasses = 'w-full rounded-lg text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:bg-slate-100 disabled:cursor-not-allowed disabled:text-slate-500';

$stateClasses = $error
    ? 'border-rose-300 text-rose-900 placeholder:text-rose-300 focus:border-rose-500 focus:ring-rose-200'
    : 'border-slate-300 text-slate-800 placeholder:text-slate-400 focus:border-primary-500 focus:ring-primary-200';
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => "{$baseClasses} {$stateClasses}"]) }}>
