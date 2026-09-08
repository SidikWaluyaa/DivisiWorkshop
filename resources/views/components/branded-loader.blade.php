@props([
    'text' => 'Memuat Data...',
    'size' => 'md',
    'overlay' => true,
    'fullscreen' => false,
])

@php
    $sizeClasses = match($size) {
        'sm' => [
            'card' => 'p-4 rounded-2xl gap-2.5',
            'outer_ring' => 'w-12 h-12',
            'logo' => 'w-7 h-7',
            'text' => 'text-[10px]',
        ],
        'lg' => [
            'card' => 'p-8 rounded-[2.5rem] gap-5',
            'outer_ring' => 'w-24 h-24',
            'logo' => 'w-14 h-14',
            'text' => 'text-sm',
        ],
        default => [
            'card' => 'p-6 rounded-3xl gap-3.5',
            'outer_ring' => 'w-16 h-16',
            'logo' => 'w-10 h-10',
            'text' => 'text-xs',
        ],
    };
@endphp

<div @class([
    'fixed inset-0 bg-slate-950/40 dark:bg-black/60 backdrop-blur-md z-[9999] flex items-center justify-center transition-all duration-300' => $fullscreen,
    'absolute inset-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur-[3px] z-30 flex items-center justify-center rounded-2xl transition-all duration-300' => ($overlay && !$fullscreen),
    'flex items-center justify-center p-4' => (!$overlay && !$fullscreen),
]) {{ $attributes }}>
    <div class="flex flex-col items-center bg-white/95 dark:bg-gray-800/95 {{ $sizeClasses['card'] }} shadow-[0_20px_50px_rgba(0,0,0,0.12)] border border-white/80 dark:border-gray-700/80 backdrop-blur-xl transition-transform transform scale-100">
        {{-- Logo with Orbit Animation --}}
        <div class="relative flex items-center justify-center {{ $sizeClasses['outer_ring'] }}">
            {{-- Outer Glowing Pulse Halo --}}
            <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-teal-500/20 to-emerald-400/20 blur-md animate-pulse"></div>

            {{-- Spinning Orbit Ring --}}
            <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-teal-500 border-r-emerald-400 animate-spin" style="animation-duration: 1.2s;"></div>
            <div class="absolute inset-1 rounded-full border-2 border-dashed border-teal-300/40 animate-spin" style="animation-duration: 3s; animation-direction: reverse;"></div>

            {{-- Center Logo Badge with Breathing Scale --}}
            <div class="relative z-10 flex items-center justify-center bg-white dark:bg-gray-700 rounded-full p-1.5 shadow-sm">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="Loading..." 
                     class="{{ $sizeClasses['logo'] }} object-contain animate-pulse drop-shadow-sm select-none pointer-events-none">
            </div>
        </div>

        {{-- Loading Text with Animated Dots --}}
        @if($text)
            <div class="flex items-center gap-1 {{ $sizeClasses['text'] }} font-black uppercase tracking-wider text-gray-700 dark:text-gray-200 select-none">
                <span>{{ $text }}</span>
                <span class="inline-flex gap-0.5">
                    <span class="w-1 h-1 rounded-full bg-teal-500 animate-bounce" style="animation-delay: 0ms;"></span>
                    <span class="w-1 h-1 rounded-full bg-teal-500 animate-bounce" style="animation-delay: 150ms;"></span>
                    <span class="w-1 h-1 rounded-full bg-teal-500 animate-bounce" style="animation-delay: 300ms;"></span>
                </span>
            </div>
        @endif
    </div>
</div>
