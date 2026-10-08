@props([
    'title' => null,
    'subtitle' => null,
    'header' => null,
    'footer' => null,
    'compact' => false,
])

<div {{ $attributes->merge(['class' => 'bg-surface-card border border-border-subtle shadow-card rounded-xl overflow-hidden transition-shadow duration-200']) }}>
    @if ($header || $title)
        <div class="px-5 py-4 border-b border-border-subtle bg-slate-50/50 flex flex-wrap items-center justify-between gap-2">
            @if ($header)
                {{ $header }}
            @else
                <div>
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                    @if ($subtitle)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $compact ? 'p-4' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-5 py-3.5 bg-slate-50/80 border-t border-border-subtle flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
    @endif
</div>
