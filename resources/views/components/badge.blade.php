@props([
    'variant' => 'neutral', // success, warning, info, danger, neutral, teal, indigo
    'size' => 'md', // sm, md
    'dot' => false,
])

@php
$sizeClasses = match ($size) {
    'sm' => 'px-2 py-0.5 text-[11px]',
    default => 'px-2.5 py-0.5 text-xs',
};

$variantClasses = match ($variant) {
    'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
    'info' => 'bg-blue-50 text-blue-800 border-blue-200',
    'danger' => 'bg-rose-50 text-rose-800 border-rose-200',
    'teal' => 'bg-teal-50 text-teal-800 border-teal-200',
    'indigo' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200',
};

$dotClasses = match ($variant) {
    'success' => 'bg-emerald-500',
    'warning' => 'bg-amber-500',
    'info' => 'bg-blue-500',
    'danger' => 'bg-rose-500',
    'teal' => 'bg-teal-500',
    'indigo' => 'bg-indigo-500',
    default => 'bg-slate-400',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded-full border {$sizeClasses} {$variantClasses}"]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
    @endif
    {{ $slot }}
</span>
