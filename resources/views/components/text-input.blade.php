@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-primary-500 focus:ring-primary-500 rounded-lg shadow-sm text-sm text-slate-800 disabled:bg-slate-100 disabled:text-slate-500 transition-colors']) }}>
