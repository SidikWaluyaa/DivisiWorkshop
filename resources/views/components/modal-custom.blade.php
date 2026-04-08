@props([
    'title' => '',
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
][$maxWidth] ?? 'max-w-2xl';
@endphp

<div
    x-cloak
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0']) }}
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all"
        @click="$dispatch('close')"
    >
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    </div>

    {{-- Modal Content --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 bg-white/95 backdrop-blur-xl rounded-[2.5rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)] transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto border border-white"
    >
        @if($title)
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight italic">{{ $title }}</h3>
                <button @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-xl transition-all">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="p-8">
            {{ $slot }}
        </div>
    </div>
</div>
