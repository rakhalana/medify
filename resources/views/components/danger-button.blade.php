<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-rose-700 active:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm disabled:opacity-50']) }}>
    {{ $slot }}
</button>
