<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-container>
            <div class="bg-white overflow-hidden shadow-card border border-border-subtle rounded-xl">
                <div class="p-6 text-slate-800">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </x-container>
    </div>
</x-app-layout>
