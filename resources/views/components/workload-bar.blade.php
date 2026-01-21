@props(['label', 'count', 'max' => 50, 'href' => null])

@php
    $percentage = $max > 0 ? min(($count / $max) * 100, 100) : 0;
    $color = $percentage > 80 ? 'bg-red-500' : ($percentage > 50 ? 'bg-yellow-500' : 'bg-teal-500');
@endphp

<div class="mb-4 {{ $href ? 'cursor-pointer hover:bg-gray-50' : '' }} p-3 rounded-lg transition-colors"
     @if($href) onclick="window.location='{{ $href }}'" @endif>
    <div class="flex justify-between items-center mb-2">
        <span class="font-bold text-gray-800">{{ $label }}</span>
        <span class="text-sm font-semibold text-gray-600">{{ $count }} orders</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
        <div class="{{ $color }} h-3 rounded-full transition-all duration-500 ease-out shadow-sm" 
             style="width: {{ $percentage }}%"></div>
    </div>
</div>
