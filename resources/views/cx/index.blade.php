<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CX Issue Resolution Center') }}
        </h2>
    </x-slot>

    {{-- Memanggil Mesin Livewire Baru --}}
    <livewire:cx.index />

</x-app-layout>
