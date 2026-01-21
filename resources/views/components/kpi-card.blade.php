@props(['title', 'value', 'icon', 'color' => 'teal', 'href' => null])

@php
    $colorClasses = match($color) {
        'teal' => 'from-teal-500 to-teal-600',
        'orange' => 'from-orange-500 to-orange-600',
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'red' => 'from-red-500 to-red-600',
        'purple' => 'from-purple-500 to-purple-600',
        default => 'from-gray-500 to-gray-600',
    };
@endphp

<div class="bg-gradient-to-br {{ $colorClasses }} rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 {{ $href ? 'cursor-pointer hover:scale-105' : '' }}"
     @if($href) onclick="window.location='{{ $href }}'" @endif>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="text-4xl font-black mb-2">{{ $value }}</div>
            <div class="text-sm font-semibold opacity-90 uppercase tracking-wide">{{ $title }}</div>
        </div>
        <div class="text-5xl opacity-20">
            {!! $icon !!}
        </div>
    </div>
</div>
