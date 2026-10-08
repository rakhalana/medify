@props([
    'value' => null,
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-slate-700']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="text-rose-500 font-bold ml-0.5">*</span>
    @endif
</label>
