@props([
    'variant' => 'primary', // primary, secondary, outline, danger
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'loading' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none active:scale-[0.98]';

$sizeClasses = match ($size) {
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'lg' => 'px-5 py-2.5 text-base gap-2.5',
    default => 'px-4 py-2 text-sm gap-2',
};

$variantClasses = match ($variant) {
    'secondary' => 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-slate-900 focus:ring-primary-500 shadow-sm',
    'outline' => 'bg-transparent border border-primary-600 text-primary-700 hover:bg-primary-50 focus:ring-primary-500',
    'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500 shadow-sm',
    default => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-sm',
};

$classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    <span class="inline-flex items-center gap-2">
        {{ $slot }}
    </span>
</button>
