@props([
    'as' => 'div',
    'size' => 'default', // 'default' (max-w-7xl), 'narrow' (max-w-4xl), 'fluid' (w-full)
    'class' => ''
])

@php
    $sizeClasses = match($size) {
        'narrow' => 'max-w-4xl',
        'fluid' => 'w-full',
        default => 'max-w-7xl',
    };
@endphp

<{{ $as }} {{ $attributes->merge(['class' => "{$sizeClasses} mx-auto px-4 sm:px-6 lg:px-8 {$class}"]) }}>
    {{ $slot }}
</{{ $as }}>
